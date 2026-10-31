<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamResultSelectorDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_class_selector_uses_terminal_result_layout_hooks(): void
    {
        $terminal = file_get_contents(resource_path('views/pages/exams/terminal-result.blade.php'));
        $preview = file_get_contents(resource_path('views/pages/exams/preview.blade.php'));

        $this->assertStringContainsString('class="d-flex flex-wrap"', $terminal);
        $this->assertStringContainsString('class="d-flex flex-wrap"', $preview);
        $this->assertStringContainsString('preview-page', $preview);
        $this->assertStringContainsString('.preview-page .btn-sm { padding: 5px 10px; }', $preview);
    }
}
