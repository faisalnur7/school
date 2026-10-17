<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stats_use_two_columns_on_mobile(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertStringContainsString('.stats-grid {', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $view);
    }
}
