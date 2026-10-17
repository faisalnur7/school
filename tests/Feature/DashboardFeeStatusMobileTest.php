<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeeStatusMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_fee_status_tiles_cannot_overflow_on_mobile(): void
    {
        $view = file_get_contents(resource_path('views/dashboard.blade.php'));

        $this->assertStringContainsString('fee-status-card', $view);
        $this->assertStringContainsString('fee-status-grid', $view);
        $this->assertStringContainsString('.fee-status-grid {', $view);
        $this->assertStringContainsString('.fee-status-grid .attend-item {', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr));', $view);
        $this->assertStringContainsString('min-width: 0;', $view);
    }
}
