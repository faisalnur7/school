<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Group;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Subject;
use App\Models\SubjectClassAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SubjectTutorialExamInclusionTest extends TestCase
{
    use RefreshDatabase;

    public function test_subjects_default_to_included_in_tutorial_exams_and_can_be_changed(): void
    {
        $this->assertTrue(Schema::hasColumn('subjects', 'include_in_tutorial_exam'));

        $created = Subject::create(['name' => 'Included Subject', 'type' => 'mandatory']);
        $this->assertTrue($created->fresh()->include_in_tutorial_exam);

        $excluded = Subject::create([
            'name' => 'Excluded Subject',
            'type' => 'optional',
            'include_in_tutorial_exam' => false,
        ]);
        $this->assertFalse($excluded->fresh()->include_in_tutorial_exam);
    }

    public function test_tutorial_exam_inclusion_can_be_toggled_from_subject_list(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $subject = Subject::create(['name' => 'Toggle Subject', 'include_in_tutorial_exam' => true]);

        $this->actingAs($user)
            ->post(route('subjects.toggle-tutorial-exam', $subject))
            ->assertRedirect();

        $this->assertFalse($subject->fresh()->include_in_tutorial_exam);

        $this->actingAs($user)
            ->post(route('subjects.toggle-tutorial-exam', $subject))
            ->assertRedirect();

        $this->assertTrue($subject->fresh()->include_in_tutorial_exam);
    }

    public function test_subject_tables_expose_tutorial_exam_toggle_column(): void
    {
        $table = file_get_contents(resource_path('views/pages/subjects/table.blade.php'));
        $classwise = file_get_contents(resource_path('views/pages/subjects/index_classwise.blade.php'));
        $toggle = file_get_contents(resource_path('views/pages/subjects/_tutorial-exam-toggle.blade.php'));

        $this->assertStringContainsString('Tutorial Exam', $table);
        $this->assertStringContainsString('_tutorial-exam-toggle', $table);
        $this->assertStringContainsString('Tutorial Exam', $classwise);
        $this->assertStringContainsString('_tutorial-exam-toggle', $classwise);
        $this->assertStringContainsString('subjects.toggle-tutorial-exam', $toggle);
    }

    public function test_tutorial_report_excludes_subjects_disabled_for_tutorial_exams(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A']);
        $student = Student::create([
            'full_name_en' => 'Tutorial Student',
            'student_cid' => 'T-001',
            'status' => 1,
            'religion' => Student::ISLAM,
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
            'name' => 'Tutorial 1',
            'type' => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $included = Subject::create(['name' => 'Islam Religion', 'tutorial_marks' => 20]);
        $excluded = Subject::create(['name' => 'Hindu Religion', 'tutorial_marks' => 20, 'include_in_tutorial_exam' => false]);
        SubjectClassAssignment::create(['subject_id' => $included->id, 'school_class_id' => $class->id, 'religion' => 'islam']);
        SubjectClassAssignment::create(['subject_id' => $excluded->id, 'school_class_id' => $class->id, 'religion' => 'hindu']);
        foreach ([[$included, 15], [$excluded, 20]] as [$subject, $mark]) {
            ExamMark::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'tutorial_marks' => $mark,
                'total' => $mark,
                'is_absent' => false,
            ]);
        }

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('result.tutorial-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
        ]));

        $response->assertOk();
        $response->assertSee('value="religion"', false);
        $response->assertDontSee('value="religion" disabled', false);
        $response->assertSee('>Religion</span>', false);
        $response->assertDontSee('Islam Religion');
        $response->assertDontSee('Hindu Religion');
        $response->assertSee('15.0');
        $rows = $response->viewData('studentsData')->first()['rows'];
        $this->assertSame(['Religion'], $rows->pluck('subject_name')->all());
    }

    public function test_tutorial_report_can_filter_to_selected_assigned_subjects(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 1', 'name_bn' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A']);
        $student = Student::create(['full_name_en' => 'Filtered Student', 'student_cid' => 'T-002', 'status' => 1]);
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
            'name' => 'Tutorial 1',
            'type' => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $first = Subject::create(['name' => 'First Subject', 'tutorial_marks' => 20]);
        $second = Subject::create(['name' => 'Second Subject', 'tutorial_marks' => 20]);
        SubjectClassAssignment::create(['subject_id' => $first->id, 'school_class_id' => $class->id]);
        SubjectClassAssignment::create(['subject_id' => $second->id, 'school_class_id' => $class->id]);

        foreach ([[$first, 15], [$second, 18]] as [$subject, $mark]) {
            ExamMark::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'tutorial_marks' => $mark,
                'total' => $mark,
                'is_absent' => false,
            ]);
        }

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('result.tutorial-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
            'subject_filter' => 1,
            'subject_ids' => [$first->id],
        ]));

        $response->assertOk();
        $response->assertSee('name="subject_ids[]"', false);
        $response->assertSee('value="' . $first->id . '"', false);
        $response->assertSee('value="' . $second->id . '"', false);
        $data = $response->viewData('studentsData')->first();
        $this->assertSame(['First Subject'], $data['rows']->pluck('subject_name')->all());
        $this->assertSame([$first->id], $response->viewData('filters')['subject_ids']);

        $clearedResponse = $this->withoutMiddleware()->actingAs($user)->get(route('result.tutorial-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
            'subject_filter' => 1,
        ]));

        $clearedResponse->assertOk();
        $this->assertCount(0, $clearedResponse->viewData('studentsData')->first()['rows']);
    }

    public function test_tutorial_report_shows_only_subjects_assigned_to_each_students_group(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 12', 'name_bn' => 'Class 12', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A']);
        $science = Group::create(['name_en' => 'Science', 'name_bn' => 'Science', 'status' => 1]);
        $business = Group::create(['name_en' => 'Business', 'name_bn' => 'Business', 'status' => 1]);
        $students = collect([
            ['name' => 'Science Student', 'group_id' => $science->id],
            ['name' => 'Business Student', 'group_id' => $business->id],
        ])->map(function (array $data, int $index) use ($session, $class, $section) {
            $student = Student::create([
                'full_name_en' => $data['name'],
                'student_cid' => 'GROUP-' . $index,
                'status' => 1,
            ]);
            StudentAcademicInformation::create([
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'group_id' => $data['group_id'],
                'roll' => (string) ($index + 1),
                'academic_status' => 'active',
                'is_current' => true,
            ]);

            return $student;
        });
        $exam = Exam::create([
            'name' => 'Tutorial 1',
            'type' => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $scienceSubject = Subject::create(['name' => 'Physics', 'tutorial_marks' => 20]);
        $businessSubject = Subject::create(['name' => 'Accounting', 'tutorial_marks' => 20]);
        SubjectClassAssignment::create(['subject_id' => $scienceSubject->id, 'school_class_id' => $class->id, 'group_id' => $science->id]);
        SubjectClassAssignment::create(['subject_id' => $businessSubject->id, 'school_class_id' => $class->id, 'group_id' => $business->id]);

        foreach ($students as $student) {
            foreach ([$scienceSubject, $businessSubject] as $subject) {
                ExamMark::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'tutorial_marks' => 15,
                    'total' => 15,
                    'is_absent' => false,
                ]);
            }
        }

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('result.tutorial-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
            'subject_filter' => 1,
            'subject_ids' => [$scienceSubject->id, $businessSubject->id],
        ]));

        $response->assertOk();
        $response->assertSee('class="tutorial-print-summary-group"', false);
        $response->assertSee('>Science</div>', false);
        $rowsByStudent = $response->viewData('studentsData')->keyBy(fn ($data) => $data['student']->full_name_en);
        $this->assertSame(['Physics'], $rowsByStudent['Science Student']['rows']->pluck('subject_name')->all());
        $this->assertSame(['Accounting'], $rowsByStudent['Business Student']['rows']->pluck('subject_name')->all());
    }
}
