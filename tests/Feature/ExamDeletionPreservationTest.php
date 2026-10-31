<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamDeletionPreservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_exam_is_archived_and_its_marks_are_preserved(): void
    {
        $exam = $this->createExam(Exam::STATUS_PUBLISHED);
        $mark = $this->createMark($exam);
        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->delete(route('exams.destroy', $exam))
            ->assertRedirect(route('exams.index'))
            ->assertSessionHas('success', 'Exam archived. Student marks and historical records were preserved.');

        $this->assertSame(Exam::STATUS_ARCHIVED, $exam->fresh()->status);
        $this->assertDatabaseHas('exam_marks', ['id' => $mark->id, 'exam_id' => $exam->id]);
    }

    public function test_draft_exam_with_marks_is_archived_and_its_marks_are_preserved(): void
    {
        $exam = $this->createExam(Exam::STATUS_DRAFT);
        $mark = $this->createMark($exam);
        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->delete(route('exams.destroy', $exam));

        $this->assertSame(Exam::STATUS_ARCHIVED, $exam->fresh()->status);
        $this->assertDatabaseHas('exam_marks', ['id' => $mark->id, 'exam_id' => $exam->id]);
    }

    public function test_empty_draft_exam_can_be_deleted(): void
    {
        $exam = $this->createExam(Exam::STATUS_DRAFT);

        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->delete(route('exams.destroy', $exam))
            ->assertRedirect(route('exams.index'))
            ->assertSessionHas('success', 'Draft exam deleted.');

        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_archived_exams_are_hidden_from_the_exam_list(): void
    {
        $archived = $this->createExam(Exam::STATUS_ARCHIVED, 'Archived exam');
        $active = $this->createExam(Exam::STATUS_DRAFT, 'Active exam');

        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->get(route('exams.index'))
            ->assertOk()
            ->assertDontSee($archived->name)
            ->assertSee($active->name);
    }

    public function test_archived_exams_can_be_restored_as_drafts_without_losing_marks(): void
    {
        $exam = $this->createExam(Exam::STATUS_ARCHIVED, 'Archived with marks');
        $mark = $this->createMark($exam);

        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->get(route('exams.index', ['status' => Exam::STATUS_ARCHIVED]))
            ->assertOk()
            ->assertSee($exam->name)
            ->assertSee('Restore');

        $this->actingAs(User::factory()->create(['is_super_admin' => true]))
            ->post(route('exams.restore', $exam))
            ->assertRedirect(route('exams.index'))
            ->assertSessionHas('success', 'Exam restored as draft. Review the marks before publishing.');

        $this->assertSame(Exam::STATUS_DRAFT, $exam->fresh()->status);
        $this->assertDatabaseHas('exam_marks', ['id' => $mark->id, 'exam_id' => $exam->id]);
    }

    private function createExam(string $status, string $name = 'Exam'): Exam
    {
        return Exam::create([
            'name' => $name,
            'type' => Exam::TYPE_TUTORIAL,
            'exam_category' => 'tutorial',
            'year' => 2026,
            'status' => $status,
        ]);
    }

    private function createMark(Exam $exam): ExamMark
    {
        $student = Student::create([
            'full_name_en' => 'Exam Student',
            'student_cid' => 'EXAM-001',
            'status' => 1,
            'religion' => Student::ISLAM,
        ]);
        $subject = Subject::create(['name' => 'Bangla']);

        return ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'total' => 15,
            'is_absent' => false,
        ]);
    }
}
