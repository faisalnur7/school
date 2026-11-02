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
use App\Models\SubjectClassAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TutorialReportHighestMarksTest extends TestCase
{
    use RefreshDatabase;

    public function test_highest_marks_are_scoped_to_the_selected_section(): void
    {
        $user = User::factory()->create();
        $session = AcademicSession::create(['name_en' => '2026', 'name_bn' => '২০২৬', 'status' => 1]);
        $class = SchoolClass::create(['name_en' => 'Class 11', 'name_bn' => 'Class 11', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_en' => 'A', 'name_bn' => 'A']);
        $otherSection = Section::create(['school_class_id' => $class->id, 'name_en' => 'B', 'name_bn' => 'B']);
        $exam = Exam::create([
            'name' => 'Final Tutorial Exam',
            'type' => Exam::TYPE_TUTORIAL,
            'academic_session_id' => $session->id,
            'year' => 2026,
            'status' => Exam::STATUS_PUBLISHED,
        ]);
        $subject = Subject::create([
            'name' => 'Bangla 1st',
            'tutorial_marks' => 20,
            'include_in_tutorial_exam' => true,
            'is_active' => true,
        ]);
        SubjectClassAssignment::create([
            'subject_id' => $subject->id,
            'school_class_id' => $class->id,
            'is_active' => true,
            'gender' => 'all',
            'religion' => 'all',
        ]);

        $student = Student::create(['full_name_en' => 'Section A Student', 'student_cid' => 'A-001', 'status' => 1]);
        $outsideStudent = Student::create(['full_name_en' => 'Section B Student', 'student_cid' => 'B-001', 'status' => 1]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
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

        ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'tutorial_marks' => 16,
            'total' => 16,
            'is_absent' => false,
        ]);
        ExamMark::create([
            'exam_id' => $exam->id,
            'student_id' => $outsideStudent->id,
            'subject_id' => $subject->id,
            'tutorial_marks' => 20,
            'total' => 20,
            'is_absent' => false,
        ]);

        $response = $this->withoutMiddleware()->actingAs($user)->get(route('result.tutorial-report.show', [
            'session_id' => $session->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'exam_id' => $exam->id,
            'student_id' => '',
        ]));

        $response->assertOk();
        $response->assertSeeInOrder([
            '>Bangla 1st</td>',
            '>20.0</td>',
            '>16.0</td>',
            '>16.0</td>',
        ]);
    }
}
