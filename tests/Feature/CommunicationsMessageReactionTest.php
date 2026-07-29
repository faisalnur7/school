<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationsMessageReactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_message_reaction_can_be_added_and_removed_via_ajax(): void
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
            'sender_id' => $recipient->id,
            'body' => 'Hello from the other side',
            'is_system' => false,
        ]);

        $addReaction = $this->actingAs($sender)->postJson(route('communications.messages.reactions.toggle', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]), [
            'reaction' => 'love',
        ]);

        $addReaction->assertOk();
        $addReaction->assertJsonPath('message.reactions.mine', 'love');
        $addReaction->assertJsonPath('message.reactions.summary.0.reaction', 'love');
        $addReaction->assertJsonPath('message.reactions.summary.0.count', 1);

        $this->assertDatabaseHas('chat_message_reactions', [
            'chat_message_id' => $message->id,
            'user_id' => $sender->id,
            'reaction' => 'love',
        ]);

        $removeReaction = $this->actingAs($sender)->postJson(route('communications.messages.reactions.toggle', [
            'conversation' => $conversation->id,
            'message' => $message->id,
        ]), [
            'reaction' => 'love',
        ]);

        $removeReaction->assertOk();
        $removeReaction->assertJsonPath('message.reactions.mine', null);
        $removeReaction->assertJsonPath('message.reactions.total_count', 0);

        $this->assertDatabaseMissing('chat_message_reactions', [
            'chat_message_id' => $message->id,
            'user_id' => $sender->id,
            'reaction' => 'love',
        ]);
    }
}
