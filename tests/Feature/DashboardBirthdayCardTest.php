<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardBirthdayCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_todays_birthday_row_shows_today_message_and_links_to_card_preview(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $cardPreviewUrl = route('students.birthdays.card.preview', 126);
        $birthdays = [[
            'name' => 'Today Birthday Student',
            'date' => now()->toImmutable(),
            'days_until' => '0',
            'label' => 'In 0 days',
            'class' => 'Three',
            'section' => 'A',
            'link' => $cardPreviewUrl,
        ]];

        $response = $this->actingAs($user)->view('dashboard', [
            'upcoming_birthdays' => $birthdays,
        ]);

        $response
            ->assertSee('Today is birthday')
            ->assertSee('href="' . $cardPreviewUrl . '"', false)
            ->assertDontSee('In 0 days');
    }
}
