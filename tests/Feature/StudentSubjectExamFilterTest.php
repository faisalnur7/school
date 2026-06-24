<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\StudentSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSubjectExamFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_lookup_endpoint_returns_only_session_exams(): void
    {
        $sessionA = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'  => 1,
        ]);

        $sessionB = AcademicSession::create([
            'name_en' => '2027',
            'name_bn' => '২০২৭',
            'status'  => 1,
        ]);

        $examA = Exam::create([
            'name'                => 'First Term',
            'type'                => Exam::TYPE_TERMINAL,
            'academic_session_id' => $sessionA->id,
            'year'                => 2026,
            'status'              => Exam::STATUS_PUBLISHED,
        ]);

        $examB = Exam::create([
            'name'                => 'Second Term',
            'type'                => Exam::TYPE_TERMINAL,
            'academic_session_id' => $sessionB->id,
            'year'                => 2027,
            'status'              => Exam::STATUS_PUBLISHED,
        ]);

        $response = $this->withoutMiddleware()
            ->getJson(route('student-subjects.ajax.exams-by-session', ['session_id' => $sessionA->id]));

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $examA->id,
            'name' => $examA->name,
        ]);
        $response->assertJsonMissing([
            'id' => $examB->id,
            'name' => $examB->name,
        ]);
    }

    public function test_index_filters_students_by_exam_subjects(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'  => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Play',
            'name_bn' => 'Play',
            'status'  => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $exam = Exam::create([
            'name'                => 'Terminal Exam',
            'type'                => Exam::TYPE_TERMINAL,
            'academic_session_id' => $session->id,
            'year'                => 2026,
            'status'              => Exam::STATUS_PUBLISHED,
        ]);

        $subject1 = Subject::create([
            'name' => 'Math',
            'code' => 'MATH-001',
            'type' => 'mandatory',
            'has_multiple_papers' => false,
            'combine_papers_for_result' => false,
            'is_parent' => false,
            'is_paper' => false,
            'creative_marks' => 100,
            'mcq_marks' => 0,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'pass_mark' => 33,
            'is_active' => true,
        ]);

        $subject2 = Subject::create([
            'name' => 'English',
            'code' => 'ENG-001',
            'type' => 'mandatory',
            'has_multiple_papers' => false,
            'combine_papers_for_result' => false,
            'is_parent' => false,
            'is_paper' => false,
            'creative_marks' => 100,
            'mcq_marks' => 0,
            'practical_marks' => 0,
            'viva_marks' => 0,
            'pass_mark' => 33,
            'is_active' => true,
        ]);

        ExamSubject::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject1->id,
            'full_marks' => 100,
        ]);

        $studentA = Student::create([
            'full_name_en' => 'Student A',
            'student_cid' => 'STU-001',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $studentA->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        StudentSubject::create([
            'student_id' => $studentA->id,
            'subject_id' => $subject1->id,
            'school_class_id' => $class->id,
            'academic_session_id' => $session->id,
            'is_optional' => false,
            'is_mandatory' => true,
        ]);

        $studentB = Student::create([
            'full_name_en' => 'Student B',
            'student_cid' => 'STU-002',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $studentB->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);
        StudentSubject::create([
            'student_id' => $studentB->id,
            'subject_id' => $subject2->id,
            'school_class_id' => $class->id,
            'academic_session_id' => $session->id,
            'is_optional' => false,
            'is_mandatory' => true,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('student-subjects.index', [
                'session_id' => $session->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'exam_id' => $exam->id,
            ]));

        $response->assertOk();
        $response->assertSee($studentA->full_name_en);
        $response->assertDontSee($studentB->full_name_en);
    }
}
