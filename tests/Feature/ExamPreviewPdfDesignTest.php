<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPreviewPdfDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_pdf_does_not_render_pass_or_fail_labels(): void
    {
        $pdfView = file_get_contents(resource_path('views/pages/exams/pdf/preview.blade.php'));

        $this->assertStringNotContainsString('Passed:', $pdfView);
        $this->assertStringNotContainsString('Failed:', $pdfView);
        $this->assertStringNotContainsString('<th>Status</th>', $pdfView);
        $this->assertStringNotContainsString('badge-pass', $pdfView);
        $this->assertStringNotContainsString('badge-fail', $pdfView);
        $this->assertStringContainsString('<th>Roll / ID</th>', $pdfView);
        $this->assertStringNotContainsString('<th>Grade</th>', $pdfView);
        $this->assertStringNotContainsString('<th>GPA</th>', $pdfView);
        $this->assertStringContainsString('class="school-header"', $pdfView);
        $controller = file_get_contents(app_path('Http/Controllers/ExamController.php'));
        $this->assertStringContainsString('$school = SchoolSetting::current();', $controller);
        $this->assertStringContainsString('<table class="stats">', $pdfView);
        $this->assertStringNotContainsString('<div class="stats">', $pdfView);
        $this->assertStringContainsString('border-bottom: 2px solid #2563eb;', $pdfView);
        $this->assertStringContainsString('text-align: center;', $pdfView);
        $this->assertStringContainsString('margin-bottom: 14px;', $pdfView);
        $this->assertStringContainsString('class="school-details"', $pdfView);
        $this->assertStringContainsString('class="school-spacer"', $pdfView);
        $this->assertStringContainsString('$highestMarks', $pdfView);
        $this->assertStringContainsString('Highest Marks:', $pdfView);
        $this->assertStringNotContainsString('Pass Mark:', $pdfView);
    }
}
