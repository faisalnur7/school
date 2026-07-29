@auth
    @php
        $chatUser = auth()->user();
        $chatWidgetConversations = \App\Models\ChatConversation::query()
            ->forUser($chatUser->id)
            ->with([
                'participants' => function ($query) {
                    $query->select('users.id', 'users.name', 'users.image');
                },
                'latestMessage.sender',
            ])
            ->withCount('messages')
            ->orderByDesc('last_message_at')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();
        $chatWidgetActiveConversation = $chatWidgetConversations->first();
        $chatWidgetMessages = $chatWidgetActiveConversation
            ? $chatWidgetActiveConversation->messages()->with(['sender', 'reactions.user'])->orderBy('created_at')->limit(40)->get()
            : collect();
        $chatReactionOptions = [
            'like' => ['emoji' => '👍', 'label' => 'Like'],
            'love' => ['emoji' => '❤️', 'label' => 'Love'],
            'haha' => ['emoji' => '😂', 'label' => 'Haha'],
            'wow' => ['emoji' => '😮', 'label' => 'Wow'],
            'sad' => ['emoji' => '😢', 'label' => 'Sad'],
            'angry' => ['emoji' => '😡', 'label' => 'Angry'],
        ];
    @endphp
@endauth
