<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsMessageUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_message_owner_can_update_message_body_via_ajax(): void
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
            'body' => 'Original message',
            'is_system' => false,
        ]);

        $response = $this->actingAs($sender)->patchJson(route('communications.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]), [
            'body' => 'Edited message body',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message.body', 'Edited message body');
        $response->assertJsonPath('message.mine', true);
        $response->assertJsonPath('conversation.id', $conversation->id);

        $this->assertDatabaseHas(ChatMessage::class, [
            'id' => $message->id,
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Edited message body',
        ]);
    }

    public function test_chat_message_cannot_be_updated_by_other_participant(): void
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
            'body' => 'Original message',
            'is_system' => false,
        ]);

        $response = $this->actingAs($recipient)->patchJson(route('communications.messages.update', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]), [
            'body' => 'Trying to edit someone else\'s message',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas(ChatMessage::class, [
            'id' => $message->id,
            'body' => 'Original message',
        ]);
    }
}
