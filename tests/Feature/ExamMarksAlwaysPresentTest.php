<?php

namespace Tests\Feature;

use App\Http\Controllers\ExamController;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class ExamMarksAlwaysPresentTest extends TestCase
{
    use RefreshDatabase;

    public function test_marks_entry_view_has_no_absent_input_controls(): void
    {
        $view = file_get_contents(resource_path('views/pages/exams/marks-entry.blade.php'));

        $this->assertStringNotContainsString('name="marks[{{ $i }}][is_absent]"', $view);
        $this->assertStringNotContainsString('[is_present]', $view);
        $this->assertStringNotContainsString('name="show_absent"', $view);
        $this->assertStringNotContainsString('>Absent</th>', $view);
    }

    public function test_exam_mark_input_ignores_absent_flag_and_saves_marks_as_present(): void
    {
        $exam = Exam::create([
            'name' => 'Terminal Exam',
            'type' => Exam::TYPE_TERMINAL,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $student = Student::create([
            'full_name_en' => 'Marks Student',
            'student_cid' => 'MARKS-001',
            'status' => 1,
        ]);
        $subject = Subject::create(['name' => 'Bangla']);
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);

        $method = new ReflectionMethod(ExamController::class, 'persistExamMark');
        $method->setAccessible(true);
        $method->invoke(app(ExamController::class), $exam, $subject, $subject->id, $class->id, [
            'creative_marks' => 100,
            'mcq_marks' => 0,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'total_marks' => 100,
        ], [
            'student_id' => $student->id,
            'cq_marks' => 80,
            'is_absent' => 1,
        ], false);

        $mark = ExamMark::query()->firstOrFail();

        $this->assertFalse($mark->is_absent);
        $this->assertSame('80.00', $mark->cq_marks);
        $this->assertSame('80.00', $mark->total);
        $this->assertNotSame('AB', $mark->letter_grade);
    }

    public function test_terminal_csv_excludes_tutorial_marks_and_exposes_component_limits(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ExamController.php'));
        $exporter = substr($controller, strpos($controller, 'public function exportMarksCsv'), strpos($controller, 'public function importMarksCsv') - strpos($controller, 'public function exportMarksCsv'));

        $this->assertStringNotContainsString("'tutorial_marks', 'cq_marks'", $exporter);
        $this->assertStringContainsString("'cq_full_marks'", $exporter);
        $this->assertStringContainsString("'mcq_full_marks'", $exporter);
        $this->assertStringContainsString("'viva_full_marks'", $exporter);
        $this->assertStringContainsString("'practical_full_marks'", $exporter);
    }

    public function test_csv_marks_cannot_exceed_configured_component_full_marks(): void
    {
        $exam = Exam::create([
            'name' => 'Terminal Exam',
            'type' => Exam::TYPE_TERMINAL,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $student = Student::create([
            'full_name_en' => 'CSV Student',
            'student_cid' => 'CSV-001',
            'status' => 1,
        ]);
        $subject = Subject::create([
            'name' => 'Bangla',
            'creative_marks' => 80,
            'mcq_marks' => 20,
            'viva_marks' => 0,
            'practical_marks' => 0,
        ]);

        $method = new ReflectionMethod(ExamController::class, 'preflightCsvRows');
        $method->setAccessible(true);
        [$rows, $errors] = $method->invoke(app(ExamController::class), [[
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'cq_marks' => 81,
            'mcq_marks' => 20,
            'viva_marks' => '',
            'practical_marks' => '',
        ]], $exam, [
            'classId' => 1,
            'students' => new Collection([$student]),
            'subjects' => new Collection([$subject]),
            'eligibility' => [$subject->id => [$student->id]],
        ]);

        $this->assertNotEmpty($rows[0]['_errors']);
        $this->assertStringContainsString('CQ must be between 0 and 80', $errors[0]);
    }
}
