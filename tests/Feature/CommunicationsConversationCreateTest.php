<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsConversationCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_force_new_creates_a_new_direct_conversation_even_if_one_exists(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $existingConversation = ChatConversation::create([
            'name' => null,
            'is_group' => false,
            'created_by_user_id' => $sender->id,
        ]);

        $existingConversation->participants()->attach([
            $sender->id => [
                'is_admin' => true,
                'last_read_at' => now(),
            ],
            $recipient->id => [
                'is_admin' => false,
                'last_read_at' => now(),
            ],
        ]);

        $response = $this->actingAs($sender)->postJson(route('communications.conversations.store'), [
            'participant_id' => $recipient->id,
            'force_new' => 1,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('conversation.id', fn ($id) => (int) $id !== (int) $existingConversation->id);

        $this->assertDatabaseCount('chat_conversations', 2);
    }
}
