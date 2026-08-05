<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatConversation extends Model
{
    protected $fillable = [
        'name',
        'is_group',
        'created_by_user_id',
        'last_message_at',
        'last_message_preview',
    ];

    protected function casts(): array
    {
        return [
            'is_group' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_conversation_participants')
            ->withPivot(['is_admin', 'muted_at', 'last_read_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_conversation_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class, 'chat_conversation_id')->latestOfMany();
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('participants', function (Builder $participantQuery) use ($userId) {
            $participantQuery->where('users.id', $userId);
        });
    }

    public function titleFor(User $user): string
    {
        if ($this->is_group) {
            return $this->name ?: 'Group conversation';
        }

        $otherParticipant = $this->participants
            ->firstWhere('id', '!=', $user->id);

        return $otherParticipant?->name ?: 'Direct chat';
    }

    public function subtitleFor(User $user): string
    {
        if ($this->is_group) {
            return $this->participants
                ->pluck('name')
                ->filter()
                ->take(3)
                ->implode(' · ');
        }

        $otherParticipant = $this->participants
            ->firstWhere('id', '!=', $user->id);

        return $otherParticipant?->role?->name
            ?? $otherParticipant?->email
            ?? 'Private conversation';
    }

    public function avatarLabelFor(User $user): string
    {
        if ($this->is_group) {
            return collect(explode(' ', trim((string) ($this->name ?: 'Group'))))
                ->filter()
                ->take(2)
                ->map(fn (string $part) => mb_substr($part, 0, 1))
                ->implode('');
        }

        $otherParticipant = $this->participants
            ->firstWhere('id', '!=', $user->id);

        return collect(explode(' ', trim((string) ($otherParticipant?->name ?: 'Chat'))))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_substr($part, 0, 1))
            ->implode('');
    }
}
