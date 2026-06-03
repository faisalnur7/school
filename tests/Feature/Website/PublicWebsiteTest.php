<?php

namespace Tests\Feature\Website;

use App\Models\ContactMessage;
use App\Models\WebsitePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_page_is_visible_on_about_page(): void
    {
        WebsitePage::factory()->create([
            'title' => 'About Us',
            'slug' => 'about',
            'status' => 'published',
            'content' => 'Published content',
            'published_at' => now()->subMinute(),
        ]);

        $response = $this->get(route('website.about'));

        $response->assertOk()->assertSee('Published content');
    }

    public function test_draft_page_is_not_visible_on_about_page(): void
    {
        WebsitePage::factory()->create([
            'title' => 'About Draft',
            'slug' => 'about',
            'status' => 'draft',
            'content' => 'Draft content',
        ]);

        $response = $this->get(route('website.about'));

        $response->assertOk()->assertDontSee('Draft content');
    }

    public function test_contact_form_persists_message(): void
    {
        $response = $this->post(route('website.contact.submit'), [
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'phone' => '01700000000',
            'subject' => 'Admission Query',
            'message' => 'Please share admission details.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('contact_messages', ['email' => 'parent@example.com', 'subject' => 'Admission Query']);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('website.contact.submit'), [
                'name' => 'Rate Test',
                'email' => 'rate@example.com',
                'subject' => 'Subject',
                'message' => 'Message',
            ]);
        }

        $last = $this->post(route('website.contact.submit'), [
            'name' => 'Rate Test',
            'email' => 'rate@example.com',
            'subject' => 'Subject',
            'message' => 'Message',
        ]);

        $last->assertStatus(429);
        $this->assertGreaterThanOrEqual(5, ContactMessage::count());
    }
}
