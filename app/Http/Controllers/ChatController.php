<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatMessageReaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private const REACTION_OPTIONS = [
        'like' => ['label' => 'Like', 'emoji' => '👍'],
        'love' => ['label' => 'Love', 'emoji' => '❤️'],
        'haha' => ['label' => 'Haha', 'emoji' => '😂'],
        'wow' => ['label' => 'Wow', 'emoji' => '😮'],
        'sad' => ['label' => 'Sad', 'emoji' => '😢'],
        'angry' => ['label' => 'Angry', 'emoji' => '😡'],
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 403);

        $conversations = $this->conversationListFor($user);
        $defaultConversationId = $conversations->first()['id'] ?? null;
        $activeConversationId = (int) $request->query('conversation', $defaultConversationId);
        $activeConversation = $activeConversationId
            ? ChatConversation::query()
                ->forUser($user->id)
                ->with([
                    'participants' => function ($query) {
                        $query->select('users.id', 'users.name', 'users.email', 'users.image')
                            ->with(['employee:id,user_id,phone']);
                    },
                    'latestMessage.sender',
                ])
                ->find($activeConversationId)
            : null;

        if ($activeConversation) {
            $this->syncConversationReadState($activeConversation, $user->id);
            $conversations = $conversations->map(function (array $conversation) use ($activeConversation) {
                if ((int) $conversation['id'] === (int) $activeConversation->id) {
                    $conversation['unread_count'] = 0;
                }

                return $conversation;
            });
        }

        if (! $activeConversation && $defaultConversationId) {
            $activeConversation = ChatConversation::query()
                ->forUser($user->id)
                ->with([
                    'participants' => function ($query) {
                        $query->select('users.id', 'users.name', 'users.email', 'users.image')
                            ->with(['employee:id,user_id,phone']);
                    },
                    'latestMessage.sender',
                ])
                ->find($defaultConversationId);
        }

        if ($activeConversation) {
            $activeConversation->load([
                'participants' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.image')
                        ->with(['employee:id,user_id,phone']);
                },
                'latestMessage.sender',
            ]);
        }

        $messages = $activeConversation
            ? $this->messageListFor($activeConversation->id)
            : collect();

        $users = User::query()
            ->with(['employee:id,user_id,phone'])
            ->whereKeyNot($user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'image']);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $users = $users->filter(function (User $candidate) use ($search) {
                $phone = $candidate->employee?->phone ?? '';

                return str_contains(strtolower($candidate->name ?? ''), strtolower($search))
                    || str_contains(strtolower($candidate->email ?? ''), strtolower($search))
                    || str_contains(strtolower($phone), strtolower($search));
            })->values();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'conversations' => $conversations->values()->all(),
                'activeConversation' => $activeConversation ? $this->conversationPayload($activeConversation, $user) : null,
                'messages' => $messages ? $this->messagePayloadCollection($messages, $user->id) : [],
                'users' => $users->map(function (User $candidate) {
                    return [
                        'id' => $candidate->id,
                        'name' => $candidate->name,
                        'email' => $candidate->email,
                        'phone' => $candidate->employee?->phone,
                        'image_url' => $candidate->image_url,
                    ];
                })->values()->all(),
            ]);
        }

        return view('pages.chat.index', [
            'hubRoute' => 'communications.index',
            'routeName' => $request->route()?->getName(),
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'users' => $users,
            'searchQuery' => $search,
        ]);
    }

    public function show(Request $request, ChatConversation $conversation)
    {
        $user = $request->user();
        $this->ensureConversationAccess($conversation, $user->id);
        $afterMessageId = (int) $request->query('after_id', 0);

        $conversation->load([
            'participants' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.image')
                    ->with(['employee:id,user_id,phone']);
            },
            'latestMessage.sender',
        ]);

        $this->syncConversationReadState($conversation, $user->id);

        if ($request->expectsJson()) {
            $messages = $afterMessageId > 0
                ? $this->messageListFor($conversation->id, $afterMessageId)
                : $this->messageListFor($conversation->id);

            return response()->json([
                'conversation' => $this->conversationPayload($conversation, $user),
                'messages' => $this->messagePayloadCollection($messages, $user->id),
            ]);
        }

        return redirect()->route('communications.index', ['conversation' => $conversation->id]);
    }

    public function storeConversation(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 403);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'participant_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'participant_ids' => ['nullable', 'array', 'min:1'],
            'participant_ids.*' => ['integer', Rule::exists('users', 'id')],
            'force_new' => ['nullable', 'boolean'],
        ]);

        $participantIds = collect($data['participant_ids'] ?? [])
            ->push($data['participant_id'] ?? null)
            ->filter()
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->reject(fn ($participantId) => $participantId === $user->id)
            ->values();
        $forceNewConversation = (bool) ($data['force_new'] ?? false);

        if ($participantIds->isEmpty()) {
            throw ValidationException::withMessages([
                'participant_ids' => 'Select at least one person to start a conversation.',
            ]);
        }

        $isGroup = $participantIds->count() > 1 || filled($data['name'] ?? null);
        $conversation = DB::transaction(function () use ($user, $participantIds, $data, $isGroup, $forceNewConversation) {
            if (! $isGroup && ! $forceNewConversation) {
                $existingConversation = ChatConversation::query()
                    ->where('is_group', false)
                    ->whereHas('participants', function ($query) use ($user) {
                        $query->where('users.id', $user->id);
                    })
                    ->whereHas('participants', function ($query) use ($participantIds) {
                        $query->where('users.id', $participantIds->first());
                    })
                    ->withCount('participants')
                    ->having('participants_count', '=', 2)
                    ->first();

                if ($existingConversation) {
                    return $existingConversation->load([
                        'participants' => function ($query) {
                            $query->select('users.id', 'users.name', 'users.email', 'users.image')
                                ->with(['employee:id,user_id,phone']);
                        },
                        'latestMessage.sender',
                    ]);
                }
            }

            $conversation = ChatConversation::create([
                'name' => $isGroup ? ($data['name'] ?? 'New group') : null,
                'is_group' => $isGroup,
                'created_by_user_id' => $user->id,
            ]);

            $conversation->participants()->attach(
                $participantIds
                    ->prepend($user->id)
                    ->unique()
                    ->mapWithKeys(fn ($participantId) => [
                        $participantId => [
                            'is_admin' => $participantId === $user->id,
                            'last_read_at' => now(),
                        ],
                    ])
                    ->all()
            );

            return $conversation;
        });

        if ($request->expectsJson()) {
            $conversation->load([
                'participants' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.image')
                        ->with(['employee:id,user_id,phone']);
                },
                'latestMessage.sender',
            ]);

            return response()->json([
                'conversation' => $this->conversationPayload($conversation, $user),
            ], 201);
        }

        return redirect()->route('communications.index', ['conversation' => $conversation->id]);
    }

    public function storeMessage(Request $request, ChatConversation $conversation)
    {
        $user = $request->user();

        abort_unless($user, 403);
        $this->ensureConversationAccess($conversation, $user->id);

        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        if (blank($data['body'] ?? null) && ! $request->hasFile('attachment')) {
            throw ValidationException::withMessages([
                'body' => 'Type a message or attach an image.',
            ]);
        }

        $attachment = $request->file('attachment');
        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;

        if ($attachment) {
            $attachmentName = $attachment->getClientOriginalName();
            $attachmentMime = $attachment->getClientMimeType();
            $attachmentSize = $attachment->getSize();
            $attachmentPath = $this->storeChatAttachment($attachment, $user->id);
        }

        $message = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => $data['body'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
            'is_system' => false,
        ]);

        $message->load(['sender', 'reactions.user']);

        $conversation->forceFill([
            'last_message_at' => $message->created_at,
            'last_message_preview' => $this->messagePreview($message),
        ])->save();

        $this->syncConversationReadState($conversation, $user->id);

        $message->load('sender');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->messagePayload($message, $user->id),
                'conversation' => $this->conversationPayload(
                    $conversation->fresh(['participants.employee', 'latestMessage.sender', 'latestMessage.reactions.user']),
                    $user
                ),
            ], 201);
        }

        return back();
    }

    public function updateMessage(Request $request, ChatConversation $conversation, ChatMessage $message): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);
        $this->ensureConversationAccess($conversation, $user->id);
        abort_unless((int) $message->chat_conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message->forceFill([
            'body' => trim((string) $data['body']),
        ])->save();

        $isLatestMessage = (int) $conversation->messages()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->value('id') === (int) $message->id;

        $conversation->forceFill([
            'last_message_preview' => $isLatestMessage
                ? $this->messagePreview($message)
                : $conversation->last_message_preview,
        ])->save();

        $message->load(['sender', 'reactions.user']);
        $conversation->load([
            'participants' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.image')
                    ->with(['employee:id,user_id,phone']);
            },
            'latestMessage.sender',
            'latestMessage.reactions.user',
        ]);

        return response()->json([
            'message' => $this->messagePayload($message, $user->id),
            'conversation' => $this->conversationPayload($conversation, $user),
        ]);
    }

    public function destroyMessage(Request $request, ChatConversation $conversation, ChatMessage $message): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);
        $this->ensureConversationAccess($conversation, $user->id);
        abort_unless((int) $message->chat_conversation_id === (int) $conversation->id, 404);
        abort_unless((int) $message->sender_id === (int) $user->id, 403);

        DB::transaction(function () use ($conversation, $message) {
            $message->delete();

            $latestMessage = $conversation->messages()
                ->with(['sender', 'reactions.user'])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();

            $conversation->forceFill([
                'last_message_at' => $latestMessage?->created_at,
                'last_message_preview' => $this->messagePreview($latestMessage),
            ])->save();
        });

        $conversation->load([
            'participants' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.image')
                    ->with(['employee:id,user_id,phone']);
            },
            'latestMessage.sender',
            'latestMessage.reactions.user',
        ]);

        return response()->json([
            'ok' => true,
            'conversation' => $this->conversationPayload($conversation, $user),
        ]);
    }

    public function markConversationRead(Request $request, ChatConversation $conversation): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);
        $this->ensureConversationAccess($conversation, $user->id);

        $this->syncConversationReadState($conversation, $user->id);

        return response()->json(['ok' => true]);
    }

    public function toggleMessageReaction(Request $request, ChatConversation $conversation, ChatMessage $message): JsonResponse
    {
        $user = $request->user();

        abort_unless($user, 403);
        $this->ensureConversationAccess($conversation, $user->id);
        abort_unless((int) $message->chat_conversation_id === (int) $conversation->id, 404);

        $data = $request->validate([
            'reaction' => ['required', Rule::in(array_keys(self::REACTION_OPTIONS))],
        ]);

        $reaction = trim((string) $data['reaction']);

        DB::transaction(function () use ($conversation, $message, $user, $reaction) {
            $existingReaction = ChatMessageReaction::query()
                ->where('chat_message_id', $message->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingReaction && $existingReaction->reaction === $reaction) {
                $existingReaction->delete();
            } elseif ($existingReaction) {
                $existingReaction->update(['reaction' => $reaction]);
            } else {
                ChatMessageReaction::create([
                    'chat_message_id' => $message->id,
                    'user_id' => $user->id,
                    'reaction' => $reaction,
                ]);
            }

            $conversation->touch();
        });

        $message->load(['sender', 'reactions.user']);
        $conversation->load([
            'participants' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email', 'users.image')
                    ->with(['employee:id,user_id,phone']);
            },
            'latestMessage.sender',
            'latestMessage.reactions.user',
        ]);

        return response()->json([
            'message' => $this->messagePayload($message, $user->id),
            'conversation' => $this->conversationPayload($conversation, $user),
        ]);
    }

    private function conversationListFor(User $user)
    {
        return ChatConversation::query()
            ->forUser($user->id)
            ->with([
                'participants' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.email', 'users.image')
                        ->with(['employee:id,user_id,phone']);
                },
                'latestMessage.sender',
            ])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(12)
            ->get()
            ->map(function (ChatConversation $conversation) use ($user) {
                return $this->conversationPayload($conversation, $user);
            });
    }

    private function messageListFor(int $conversationId, int $afterMessageId = 0)
    {
        return ChatMessage::query()
            ->where('chat_conversation_id', $conversationId)
            ->when($afterMessageId > 0, function ($query) use ($afterMessageId) {
                $query->where('id', '>', $afterMessageId);
            })
            ->with(['sender', 'reactions.user'])
            ->orderBy('created_at')
            ->limit(200)
            ->get();
    }

    private function conversationPayload(ChatConversation $conversation, ?User $user): array
    {
        $latestMessage = $conversation->latestMessage;
        $participants = $conversation->participants->map(function (User $participant) {
            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'phone' => $participant->employee?->phone,
                'image_url' => $participant->image_url,
            ];
        })->values();

        $searchText = collect([
            $conversation->titleFor($user),
            $conversation->subtitleFor($user),
            $conversation->last_message_preview ?: $this->messagePreview($latestMessage),
            $participants->pluck('name')->implode(' '),
            $participants->pluck('email')->implode(' '),
            $participants->pluck('phone')->implode(' '),
        ])
            ->filter()
            ->implode(' ');

        return [
            'id' => $conversation->id,
            'name' => $user ? $conversation->titleFor($user) : ($conversation->name ?: 'Conversation'),
            'subtitle' => $user ? $conversation->subtitleFor($user) : 'Conversation',
            'avatar_label' => $user ? $conversation->avatarLabelFor($user) : 'C',
            'is_group' => $conversation->is_group,
            'last_message_at' => optional($conversation->last_message_at)->toIso8601String(),
            'updated_at' => optional($conversation->updated_at)->toIso8601String(),
            'last_message_preview' => $conversation->last_message_preview ?: $this->messagePreview($latestMessage),
            'messages_count' => (int) ($conversation->messages_count ?? 0),
            'unread_count' => $this->unreadCountFor($conversation, $user),
            'search_text' => str($searchText)->squish()->lower()->toString(),
            'participants' => $participants->map(function (array $participant) {
                return [
                    'id' => $participant['id'],
                    'name' => $participant['name'],
                    'email' => $participant['email'],
                    'phone' => $participant['phone'],
                    'image_url' => $participant['image_url'],
                ];
            })->values()->all(),
            'latest_message' => $latestMessage ? $this->messagePayload($latestMessage, $user?->id ?? 0) : null,
        ];
    }

    private function messagePayloadCollection($messages, int $userId): array
    {
        return $messages->map(fn (ChatMessage $message) => $this->messagePayload($message, $userId))->values()->all();
    }

    private function messagePayload(ChatMessage $message, int $userId): array
    {
        $attachmentUrl = $message->attachment_url;
        $reactionSummary = $this->reactionSummaryFor($message, $userId);

        return [
            'id' => $message->id,
            'body' => $message->body,
            'mine' => (int) $message->sender_id === $userId,
            'is_system' => (bool) $message->is_system,
            'created_at' => optional($message->created_at)->toIso8601String(),
            'updated_at' => optional($message->updated_at)->toIso8601String(),
            'sender' => $message->sender ? [
                'id' => $message->sender->id,
                'name' => $message->sender->name,
                'image_url' => $message->sender->image_url,
            ] : null,
            'attachment' => $message->attachment_path ? [
                'name' => $message->attachment_name ?: basename($message->attachment_path),
                'url' => $attachmentUrl,
                'mime' => $message->attachment_mime,
                'size' => $message->attachment_size,
                'is_image' => $message->is_image_attachment,
                'missing' => blank($attachmentUrl),
            ] : null,
            'reactions' => $reactionSummary,
        ];
    }

    private function reactionSummaryFor(ChatMessage $message, int $userId): array
    {
        $reactions = $message->relationLoaded('reactions')
            ? $message->reactions
            : $message->reactions()->with('user')->get();

        $summary = $reactions
            ->groupBy('reaction')
            ->map(function ($group, string $reaction) {
                $meta = self::REACTION_OPTIONS[$reaction] ?? ['label' => ucfirst($reaction), 'emoji' => '✨'];

                return [
                    'reaction' => $reaction,
                    'label' => $meta['label'],
                    'emoji' => $meta['emoji'],
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->values()
            ->all();

        return [
            'mine' => $reactions->firstWhere('user_id', $userId)?->reaction,
            'summary' => $summary,
            'total_count' => $reactions->count(),
        ];
    }

    private function messagePreview(?ChatMessage $message): ?string
    {
        if (! $message) {
            return null;
        }

        if ($message->attachment_path && ! blank($message->body)) {
            return trim((string) $message->body);
        }

        if ($message->attachment_path) {
            return 'Shared an image';
        }

        return str($message->body ?? '')->limit(90)->toString();
    }

    private function ensureConversationAccess(ChatConversation $conversation, int $userId): void
    {
        abort_unless(
            $conversation->participants()->where('users.id', $userId)->exists(),
            403
        );
    }

    private function syncConversationReadState(ChatConversation $conversation, int $userId): void
    {
        $conversation->participants()
            ->updateExistingPivot($userId, ['last_read_at' => now()]);
    }

    private function storeChatAttachment(UploadedFile $attachment, int $userId): string
    {
        $folder = $userId . '_' . now()->timestamp;
        $directory = public_path('chat/images/' . $folder);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = $attachment->getClientOriginalExtension();
        $filename = Str::uuid()->toString();

        if ($extension) {
            $filename .= '.' . $extension;
        }

        $attachment->move($directory, $filename);

        return 'chat/images/' . $folder . '/' . $filename;
    }

    private function unreadCountFor(ChatConversation $conversation, ?User $user): int
    {
        if (! $user) {
            return 0;
        }

        $lastReadAt = $conversation->participants
            ->firstWhere('id', $user->id)
            ?->pivot
            ?->last_read_at;

        return $conversation->messages()
            ->where('sender_id', '!=', $user->id)
            ->when($lastReadAt, function ($query) use ($lastReadAt) {
                $query->where('created_at', '>', $lastReadAt);
            })
            ->count();
    }
}
