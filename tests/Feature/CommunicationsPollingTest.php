<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsPollingTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_show_returns_only_messages_after_the_given_id_for_polling(): void
    {
        $viewer = User::factory()->create();
        $sender = User::factory()->create();

        $conversation = ChatConversation::create([
            'name' => null,
            'is_group' => false,
            'created_by_user_id' => $viewer->id,
        ]);

        $conversation->participants()->attach([
            $viewer->id => [
                'is_admin' => true,
                'last_read_at' => now(),
            ],
            $sender->id => [
                'is_admin' => false,
                'last_read_at' => now(),
            ],
        ]);

        $firstMessage = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'First message',
            'is_system' => false,
        ]);

        $secondMessage = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Second message',
            'is_system' => false,
        ]);

        $response = $this->actingAs($viewer)->getJson(route('communications.conversations.show', [
            'conversation' => $conversation->id,
            'after_id' => $firstMessage->id,
        ]));

        $response->assertOk();
        $response->assertJsonCount(1, 'messages');
        $response->assertJsonPath('messages.0.id', $secondMessage->id);
        $response->assertJsonPath('conversation.id', $conversation->id);
    }
}
