<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialReportPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_tutorial_pdf_uses_prominent_typography_and_signature_spacing(): void
    {
        $layout = file_get_contents(resource_path('views/pages/tutorial-report/_print-layout.blade.php'));
        $controller = file_get_contents(app_path('Http/Controllers/TutorialReportController.php'));

        $this->assertStringContainsString('border: 1px solid #cbd6d2;', $layout);
        $this->assertStringContainsString('border-radius: 8px;', $layout);
        $this->assertStringContainsString('background: transparent;', $layout);
        $this->assertStringContainsString('font-size: 18px;', $layout);
        $this->assertStringContainsString('font-size: 14px;', $layout);
        $this->assertStringContainsString('font-size: 12px;', $layout);
        $this->assertStringContainsString('.tutorial-print-student thead th { height: 25px; background: transparent; color: #000; font-size: 14px;', $layout);
        $this->assertStringContainsString('white-space: nowrap;', $layout);
        $this->assertStringContainsString('.tutorial-print-student tbody td { height: 24px; color: #000; font-size: 14px; font-weight: 700;', $layout);
        $this->assertStringContainsString('.tutorial-print-total td { height: 27px; background: transparent !important; border-bottom: 0 !important; color: #000 !important; font-size: 14px;', $layout);
        $this->assertStringContainsString('.tutorial-print-summary-label { color: #000; font-size: 14px;', $layout);
        $this->assertStringContainsString('.tutorial-print-summary-value { margin-top: 2px; color: #000; font-size: 14px;', $layout);
        $this->assertStringContainsString('.tutorial-print-comment { display: inline-block; padding: 9px 18px; border: 0; border-radius: 999px; background: transparent; color: #000; font-size: 14px;', $layout);
        $this->assertStringContainsString('.tutorial-print-comment-label { display: inline-block; margin-right: 10px; padding-right: 2px; color: #000; font-size: 14px;', $layout);
        $this->assertStringContainsString('<span class="tutorial-print-comment-label">Comments -</span>', $layout);
        $this->assertStringContainsString("return 'Needs Improvement';", file_get_contents(app_path('Http/Controllers/TutorialReportController.php')));
        $this->assertStringNotContainsString('Bar: obtained · Tick: highest', $layout);
        $this->assertStringNotContainsString('tutorial-print-legend-cell', $layout);
        $this->assertStringNotContainsString('tutorial-print-legend', $layout);
        $this->assertMatchesRegularExpression('/\.tutorial-print-signature\s*\{[^}]*margin: 0;/s', $layout);
        $this->assertMatchesRegularExpression('/\.tutorial-print-signature\s*\{[^}]*font-size: 12px;/s', $layout);
        $this->assertStringContainsString('border-bottom: 3px solid #c9a13b;', $layout);
        $this->assertStringContainsString('padding: 8px 12px 0;', $layout);
        $this->assertStringContainsString('.tutorial-print-summary', $layout);
        $this->assertStringContainsString('.tutorial-print-total', $layout);
        $this->assertStringContainsString('.tutorial-print-comment', $layout);
        $this->assertStringContainsString('$totalFullMarks', $layout);
        $this->assertStringContainsString('color: #000;', $layout);
        $this->assertStringNotContainsString('color: #8a6a16;', $layout);
        $this->assertStringNotContainsString('color: #6b7b76;', $layout);
        $this->assertStringNotContainsString('style="color: {{ $performanceColor }};"', $layout);
        $this->assertStringContainsString('width: 96%;', $layout);
        $this->assertStringContainsString('style="background: transparent; color: #000;"', $layout);
        $this->assertStringNotContainsString('background: #0f3d2e;', $layout);
        $this->assertStringNotContainsString('background: #e8f0ec;', $layout);
        $this->assertStringNotContainsString('background: #f4f7f6;', $layout);
        $this->assertStringContainsString('.tutorial-print-signature-space', $layout);
        $this->assertStringContainsString('height: 1in;', $layout);
        $this->assertStringContainsString('<div class="tutorial-print-signature-space"></div>', $layout);
        $this->assertStringContainsString('.tutorial-print-signature-row', $layout);
        $this->assertStringContainsString('colspan="3"', $layout);
        $this->assertStringContainsString('.tutorial-print-signature-table', $layout);
        $this->assertStringContainsString('<table class="tutorial-print-signature-table" align="right">', $layout);
        $this->assertStringNotContainsString('>Performance</th>', $layout);
        $this->assertStringNotContainsString('class="performance-column"', $layout);
        $this->assertStringContainsString('>Full Marks</th>', $layout);
        $this->assertStringContainsString('>Obtained Marks</th>', $layout);
        $this->assertStringContainsString('>Highest Marks</th>', $layout);
        $this->assertStringNotContainsString('>Full</th>', $layout);
        $this->assertStringNotContainsString('>Obtained</th>', $layout);
        $this->assertStringNotContainsString('>Highest</th>', $layout);
        $this->assertStringNotContainsString('class="number-column"', $layout);
        $this->assertStringNotContainsString('>#</th>', $layout);
        $this->assertStringContainsString('<tr><td colspan="4"', $layout);
        $this->assertStringContainsString('<tr class="tutorial-print-total"><td style="background: transparent; color: #000;">Total</td>', $layout);
        $paperFirstSort = 'sprintf(\'%d:%s\', $m->subject->is_paper ? 0 : 1';
        $this->assertSame(3, substr_count($controller, $paperFirstSort));
        $this->assertSame(2, substr_count($controller, '&& $m->subject->include_in_tutorial_exam'));
    }

    public function test_tutorial_pdf_is_portrait_and_fits_multiple_student_cards_on_a_page(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);
        $class = SchoolClass::create([
            'name_en' => 'Nursery',
            'name_bn' => 'Nursery',
            'status' => 1,
        ]);
        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);
        $student = Student::create([
            'full_name_en' => 'PDF Student',
            'student_cid' => 'PDF-001',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        $exam = Exam::create([
            'name' => 'First Tutorial Exam',
            'type' => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $subject = Subject::create([
            'name' => 'Bangla',
            'tutorial_marks' => 20,
            'is_active' => true,
        ]);
        ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'tutorial_marks' => 15,
            'total' => 15,
            'is_absent' => false,
        ]);
        $secondStudent = Student::create([
            'full_name_en' => 'Second PDF Student',
            'student_cid' => 'PDF-002',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $secondStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $secondStudent->id,
            'subject_id' => $subject->id,
            'tutorial_marks' => 16,
            'total' => 16,
            'is_absent' => false,
        ]);
        foreach ([
            ['Third PDF Student', 'PDF-003', '3', 17],
            ['Fourth PDF Student', 'PDF-004', '4', 18],
        ] as [$name, $cid, $roll, $mark]) {
            $extraStudent = Student::create([
                'full_name_en' => $name,
                'student_cid' => $cid,
                'status' => 1,
            ]);
            StudentAcademicInformation::create([
                'student_id' => $extraStudent->id,
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'roll' => $roll,
                'academic_status' => 'active',
                'is_current' => true,
            ]);
            ExamMark::create([
                'exam_id' => $exam->id,
                'student_id' => $extraStudent->id,
                'subject_id' => $subject->id,
                'tutorial_marks' => $mark,
                'total' => $mark,
                'is_absent' => false,
            ]);
        }

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('result.tutorial-report.pdf', [
                'session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'exam_id' => $exam->id,
                'student_id' => '',
            ]));

        $response->assertOk();
        $pdf = $response->getContent();

        $this->assertStringStartsWith('%PDF-', $pdf);
        preg_match_all('/\/Type \/Page\\b/', $pdf, $pageObjects);
        $this->assertCount(2, $pageObjects[0]);
        $this->assertStringContainsString('/MediaBox [0 0 595.280 841.890]', $pdf);
        $this->assertGreaterThan(1000, strlen($pdf));
    }
}
