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
use Tests\TestCase;

class ResultReportExcludesCheckedOutStudentsTest extends TestCase
{
    use RefreshDatabase;

    private function scaffold(): array
    {
        $user = User::factory()->create();
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'   => 1,
        ]);
        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status'   => 1,
        ]);
        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $activeStudent = Student::create([
            'full_name_en' => 'Active Student',
            'student_cid'  => 'ACT-001',
            'status'       => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id'          => $activeStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '1',
            'academic_status'     => 'active',
            'is_current'          => true,
        ]);

        $checkedOutStudent = Student::create([
            'full_name_en' => 'Checked Out Student',
            'student_cid'  => 'OUT-001',
            'status'       => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id'          => $checkedOutStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '2',
            'academic_status'     => 'withdrawn',
            'is_current'          => false,
        ]);

        $tutorialExam = Exam::create([
            'name'                => 'Tutorial 1',
            'type'                => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year'                => 2026,
            'status'              => Exam::STATUS_PUBLISHED,
        ]);

        $terminalExam = Exam::create([
            'name'                => 'Terminal 1',
            'type'                => Exam::TYPE_TERMINAL,
            'academic_session_id' => $session->id,
            'year'                => 2026,
            'status'              => Exam::STATUS_PUBLISHED,
        ]);

        return compact('user', 'session', 'class', 'section', 'activeStudent', 'checkedOutStudent', 'tutorialExam', 'terminalExam');
    }

    public function test_tutorial_report_skips_checked_out_students(): void
    {
        ['user' => $user, 'session' => $session, 'class' => $class, 'section' => $section, 'activeStudent' => $activeStudent, 'checkedOutStudent' => $checkedOutStudent, 'tutorialExam' => $tutorialExam] = $this->scaffold();

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('result.tutorial-report.show', [
                'session_id' => $session->id,
                'class_id'   => $class->id,
                'section_id' => $section->id,
                'exam_id'    => $tutorialExam->id,
            ]));

        $response->assertOk();
        $response->assertSee($activeStudent->full_name_en);
        $response->assertDontSee($checkedOutStudent->full_name_en);
    }

    public function test_progress_report_skips_checked_out_students(): void
    {
        ['user' => $user, 'session' => $session, 'class' => $class, 'section' => $section, 'activeStudent' => $activeStudent, 'checkedOutStudent' => $checkedOutStudent, 'terminalExam' => $terminalExam] = $this->scaffold();

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('result.progress-report.show'), [
                'session_id' => $session->id,
                'class_id'   => $class->id,
                'section_id' => $section->id,
                'exam_id'    => $terminalExam->id,
            ]);

        $response->assertOk();
        $response->assertSee($activeStudent->full_name_en);
        $response->assertDontSee($checkedOutStudent->full_name_en);
    }

    public function test_progress_report_displays_student_group_under_class(): void
    {
        ['user' => $user, 'session' => $session, 'class' => $class, 'section' => $section, 'activeStudent' => $activeStudent, 'terminalExam' => $terminalExam] = $this->scaffold();

        $group = Group::create([
            'name_en' => 'Science Group',
            'name_bn' => 'বিজ্ঞান',
            'status' => 1,
        ]);

        StudentAcademicInformation::where('student_id', $activeStudent->id)
            ->where('academic_session_id', $session->id)
            ->update(['group_id' => $group->id]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('result.progress-report.show', [
                'session_id' => $session->id,
                'class_id'   => $class->id,
                'section_id' => $section->id,
                'exam_id'    => $terminalExam->id,
            ]));

        $response->assertOk();
        $response->assertSee('Group');
        $response->assertSee('Science Group');
    }

    public function test_progress_report_highest_marks_are_scoped_to_the_selected_section(): void
    {
        ['user' => $user, 'session' => $session, 'class' => $class, 'section' => $section, 'activeStudent' => $activeStudent, 'terminalExam' => $terminalExam] = $this->scaffold();
        $otherSection = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'B',
            'name_bn' => 'B',
        ]);
        $outsideStudent = Student::create([
            'full_name_en' => 'Other Section Student',
            'student_cid' => 'OUT-002',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $outsideStudent->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $otherSection->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        $subject = Subject::create([
            'name' => 'Mathematics',
            'creative_marks' => 100,
            'pass_mark' => 33,
            'is_active' => true,
        ]);
        SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'is_active' => true,
            'gender' => 'all',
            'religion' => 'all',
        ]);
        ExamMark::create([
            'exam_id' => $terminalExam->id,
            'student_id' => $activeStudent->id,
            'subject_id' => $subject->id,
            'total' => 80,
            'is_absent' => false,
            'letter_grade' => 'A+',
            'gpa' => 5,
        ]);
        ExamMark::create([
            'exam_id' => $terminalExam->id,
            'student_id' => $outsideStudent->id,
            'subject_id' => $subject->id,
            'total' => 100,
            'is_absent' => false,
            'letter_grade' => 'A+',
            'gpa' => 5,
        ]);

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('result.progress-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $terminalExam->id,
        ]));

        $response->assertOk();
        $response->assertSeeInOrder([
            '>Mathematics</td>',
            '>100</td>',
            '>80</td>',
            '>80</td>',
        ]);
    }
}
