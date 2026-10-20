<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardBirthdaysMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_birthday_list_has_compact_mobile_spacing(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertStringContainsString('birthday-mobile-list', $view);
        $this->assertStringContainsString('.birthday-mobile-list .birthday-item {', $view);
        $this->assertStringContainsString('.birthday-mobile-list .birthday-avatar {', $view);
        $this->assertStringContainsString('.birthday-mobile-list .birthday-meta {', $view);
    }

    public function test_dashboard_card_headings_use_the_short_labels(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertStringContainsString("__('Classwise Attendance')", $view);
        $this->assertStringContainsString("__('Recent Birthdays')", $view);
        $this->assertStringNotContainsString("Today's Classwise Attendance", $view);
        $this->assertStringNotContainsString('Birthdays Today & Next 5 Days', $view);
    }
}
