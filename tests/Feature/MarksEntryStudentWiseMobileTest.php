<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarksEntryStudentWiseMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_wise_table_has_mobile_scroll_and_compact_sticky_columns(): void
    {
        $view = file_get_contents(resource_path('views/pages/exams/marks-entry.blade.php'));

        $this->assertStringContainsString('student-wise-mobile-scroll', $view);
        $this->assertStringContainsString('student-wise-mobile-table', $view);
        $this->assertStringContainsString('.marks-entry-page .student-wise-mobile-scroll', $view);
        $this->assertStringContainsString('.marks-entry-page .student-wise-mobile-table .student-name-col {', $view);
        $this->assertStringContainsString('left: 56px;', $view);
        $this->assertStringContainsString('width: 58px !important;', $view);
    }
}
