<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarksEntryStudentWiseToolbarMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_wise_toolbar_has_mobile_layout_hooks(): void
    {
        $view = file_get_contents(resource_path('views/pages/exams/marks-entry.blade.php'));

        $this->assertStringContainsString('student-wise-toolbar', $view);
        $this->assertStringContainsString('student-wise-toolbar-status', $view);
        $this->assertStringContainsString('student-wise-toolbar-actions', $view);
        $this->assertStringContainsString('.marks-entry-page .student-wise-toolbar {', $view);
        $this->assertStringContainsString('.marks-entry-page .student-wise-toolbar-actions .btn {', $view);
        $this->assertStringContainsString(".marks-entry-page .student-wise-toolbar-actions .autosave-switch {\n                grid-column: 1;", $view);
        $this->assertStringContainsString(".marks-entry-page .student-wise-toolbar-actions .student-wise-toolbar-status {\n                grid-column: 2;", $view);
    }
}
