<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\SubjectClassConfig;
use Tests\TestCase;

class SubjectMarksTotalTest extends TestCase
{
    public function test_subject_totals_exclude_tutorial_marks(): void
    {
        $subject = new Subject([
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'tutorial_marks' => 20,
        ]);

        $config = new SubjectClassConfig([
            'creative_marks' => 70,
            'mcq_marks' => 30,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'tutorial_marks' => 20,
        ]);

        $this->assertSame(100.0, $subject->total_marks);
        $this->assertSame(100.0, $config->total_marks);
    }

    public function test_subject_form_total_calculation_does_not_include_tutorial_marks(): void
    {
        $html = file_get_contents(resource_path('views/pages/subjects/edit.blade.php'));

        $this->assertStringContainsString("$('.marks-input').each(function ()", $html);
        $this->assertStringNotContainsString("$('.marks-input, #tutorial_marks').each(function ()", $html);
        $this->assertStringNotContainsString("$(document).on('input', '.marks-input, #tutorial_marks', updateTotalMarks)", $html);
    }
}
