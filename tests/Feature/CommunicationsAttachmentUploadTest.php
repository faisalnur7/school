<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CommunicationsAttachmentUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_image_attachment_is_saved_in_public_chat_images_folder(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-27 10:15:30', 'UTC'));

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

        $response = $this->actingAs($sender)->post(route('communications.messages.store', $conversation->id), [
            'body' => 'Hello',
            'attachment' => UploadedFile::fake()->image('photo.jpg', 800, 600),
        ]);

        $response->assertRedirect();

        $message = ChatMessage::query()->latest('id')->firstOrFail();

        $this->assertStringStartsWith(
            'chat/images/' . $sender->id . '_1785147330/',
            $message->attachment_path
        );
        $this->assertFileExists(public_path($message->attachment_path));
        $this->assertSame(asset($message->attachment_path), $message->attachment_url);
    }

    public function test_missing_chat_attachment_is_hidden_from_the_response_payload(): void
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
            'body' => 'Broken attachment',
            'attachment_path' => 'chat-attachments/missing.webp',
            'attachment_name' => 'missing.webp',
            'attachment_mime' => 'image/webp',
            'is_system' => false,
        ]);

        $response = $this->actingAs($sender)->getJson(route('communications.conversations.show', [
            'conversation' => $conversation->id,
        ]));

        $response->assertOk();
        $response->assertJsonPath('messages.0.id', $message->id);
        $response->assertJsonPath('messages.0.attachment.missing', true);
        $response->assertJsonPath('messages.0.attachment.url', null);
    }
}
