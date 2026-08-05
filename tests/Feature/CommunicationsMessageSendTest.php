<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsMessageSendTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_message_store_returns_json_payload_for_ajax_sends(): void
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

        $response = $this->actingAs($sender)->postJson(route('communications.messages.store', $conversation->id), [
            'body' => 'Hello from the composer',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message.body', 'Hello from the composer');
        $response->assertJsonPath('message.mine', true);
        $response->assertJsonPath('conversation.id', $conversation->id);
        $response->assertJsonPath('conversation.latest_message.body', 'Hello from the composer');

        $this->assertDatabaseHas(ChatMessage::class, [
            'chat_conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => 'Hello from the composer',
        ]);
    }
}
