<?php

namespace Tests\Feature\Website;

use App\Models\AcademicCalendar;
use App\Models\WebsitePage;
use App\Models\WebsiteSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsiteSectionsAndCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_sections_for_published_about_page(): void
    {
        $page = WebsitePage::factory()->create([
            'title' => 'About',
            'slug' => 'about',
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'content' => 'Main about body',
        ]);

        WebsiteSection::create([
            'website_page_id' => $page->id,
            'title' => 'Mission',
            'section_key' => 'mission',
            'content' => 'Our mission content',
            'sort_order' => 1,
        ]);

        $response = $this->get(route('website.about'));

        $response->assertOk()->assertSee('Mission')->assertSee('Our mission content');
    }

    public function test_academic_calendar_page_shows_only_published_items(): void
    {
        AcademicCalendar::create([
            'title' => 'Class Starts',
            'description' => 'First class day',
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-01',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        AcademicCalendar::create([
            'title' => 'Draft Event',
            'description' => 'Hidden event',
            'start_date' => '2026-06-10',
            'is_published' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('website.academic-calendar'));

        $response->assertOk()->assertSee('Class Starts')->assertDontSee('Draft Event');
    }
}
