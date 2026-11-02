<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProgressReportComponentMarksModeTest extends TestCase
{
    public function test_progress_report_exposes_minimal_and_detailed_component_mark_modes(): void
    {
        $results = file_get_contents(resource_path('views/pages/progress-report/results.blade.php'));
        $print = file_get_contents(resource_path('views/pages/progress-report/print.blade.php'));
        $filter = file_get_contents(resource_path('views/pages/progress-report/_filter.blade.php'));

        $this->assertStringContainsString('name="component_marks_mode" value="minimal"', $results);
        $this->assertStringContainsString('name="component_marks_mode" value="detailed"', $results);
        $this->assertStringContainsString('name="component_marks_mode" value="{{ $filters[\'component_marks_mode\'] ?? \'minimal\' }}"', $filter);
        $this->assertStringContainsString('<th>CQ</th>', $results);
        $this->assertStringContainsString('<th>MCQ</th>', $results);
        $this->assertStringContainsString('<th>Practical</th>', $results);
        $this->assertStringContainsString('<th>Viva</th>', $results);
        $this->assertStringContainsString('<th>CQ</th>', $print);
        $this->assertStringContainsString('<th>MCQ</th>', $print);
        $this->assertStringContainsString('<th>Practical</th>', $print);
        $this->assertStringContainsString('<th>Viva</th>', $print);
    }
}
