<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamsIndexMobileLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_exams_index_has_mobile_header_and_filter_layout_hooks(): void
    {
        $view = file_get_contents(resource_path('views/pages/exams/index.blade.php'));

        $this->assertStringContainsString('exams-mobile-header', $view);
        $this->assertStringContainsString('exams-mobile-filters', $view);
        $this->assertStringContainsString('.exams-mobile-header {', $view);
        $this->assertStringContainsString('.exams-mobile-filters > div {', $view);
        $this->assertStringContainsString('grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);', $view);
    }
}
