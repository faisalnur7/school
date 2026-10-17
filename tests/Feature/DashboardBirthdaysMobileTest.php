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
}
