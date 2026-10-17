<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialReportResultsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_screen_tutorial_table_matches_the_pdf_marks_table(): void
    {
        $view = file_get_contents(resource_path('views/pages/tutorial-report/results.blade.php'));

        $this->assertStringContainsString('>Full Marks</th>', $view);
        $this->assertStringContainsString('>Obtained Marks</th>', $view);
        $this->assertStringContainsString('>Highest Marks</th>', $view);
        $this->assertStringContainsString('tutorial-report-total', $view);
        $this->assertStringContainsString('{{ number_format($totalFullMarks, 1) }}', $view);
        $this->assertStringContainsString('{{ number_format($overallPercentage, 1) }}%', $view);
        $this->assertStringNotContainsString('GPA:', $view);
        $this->assertStringNotContainsString('{{ $data[\'grade\'] }}', $view);
        $this->assertStringContainsString('id="tutorialToggleSubjects"', $view);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr));', $view);
        $this->assertStringContainsString('.tutorial-report-subject-filter.is-mobile-collapsed', $view);
        $this->assertStringContainsString('tutorial-report-subject-filter__action', $view);
        $this->assertStringContainsString('background: #111827;', $view);
        $this->assertStringContainsString('appearance: none;', $view);
        $this->assertStringContainsString('border-radius: 999px;', $view);
        $this->assertStringContainsString('width: 1.05rem;', $view);
        $this->assertStringContainsString('gap: 0.95rem 1rem;', $view);
        $this->assertStringContainsString('background-image: url("data:image/svg+xml', $view);
        $this->assertStringContainsString('.tutorial-report-subject-option > span', $view);
        $this->assertStringContainsString('margin-left: 1.5rem;', $view);
        $this->assertStringContainsString('value="religion"', $view);
        $this->assertStringContainsString('>Religion</span>', $view);
    }
}
