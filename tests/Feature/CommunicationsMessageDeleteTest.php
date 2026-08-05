<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsMessageDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_message_owner_can_delete_message_via_ajax(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $conversation = ChatConversation::create([
            'name' => null,
            'is_group' => false,
            'created_by_user_id' => $sender->id,
        ]);

        $conversation->participants()->attach([
            $sender->id => [
                'is_admin' => true,
                'last_read_at' => now(),
            ],
            $recipient->id => [
                'is_admin' => false,
                'last_read_at' => now(),
            ],
        ]);

        $message = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Message to remove',
            'is_system' => false,
        ]);

        $response = $this->actingAs($sender)->deleteJson(route('communications.messages.destroy', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]));

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonPath('conversation.id', $conversation->id);

        $this->assertDatabaseMissing(ChatMessage::class, [
            'id' => $message->id,
            'chat_conversation_id' => $conversation->id,
        ]);
    }

    public function test_chat_message_cannot_be_deleted_by_other_participant(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $conversation = ChatConversation::create([
            'name' => null,
            'is_group' => false,
            'created_by_user_id' => $sender->id,
        ]);

        $conversation->participants()->attach([
            $sender->id => [
                'is_admin' => true,
                'last_read_at' => now(),
            ],
            $recipient->id => [
                'is_admin' => false,
                'last_read_at' => now(),
            ],
        ]);

        $message = ChatMessage::create([
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Message to keep',
            'is_system' => false,
        ]);

        $response = $this->actingAs($recipient)->deleteJson(route('communications.messages.destroy', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]));

        $response->assertForbidden();
        $this->assertDatabaseHas(ChatMessage::class, [
            'id' => $message->id,
            'body' => 'Message to keep',
        ]);
    }
}
