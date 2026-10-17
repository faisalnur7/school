<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarksEntryMobileDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_entry_has_mobile_only_compact_layout_hooks(): void
    {
        $view = file_get_contents(resource_path('views/pages/exams/marks-entry.blade.php'));

        $this->assertStringContainsString('marks-entry-hero-header', $view);
        $this->assertStringContainsString('marks-entry-details', $view);
        $this->assertStringContainsString('marks-entry-class-grid', $view);
        $this->assertStringContainsString('marks-entry-cohort-summary', $view);
        $this->assertStringContainsString('@media (max-width: 576px)', $view);
        $this->assertStringContainsString('.marks-entry-page .marks-entry-class-grid', $view);
    }
}
