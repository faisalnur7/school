<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentListOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_list_grouping_preserves_database_order_for_print_output(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));

        $oldSession = AcademicSession::create([
            'name_en' => '2025',
            'name_bn' => '২০২৫',
            'status' => 1,
        ]);
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);
        $currentClass = SchoolClass::create(['name_en' => 'Class 9', 'name_bn' => 'Class 9', 'status' => 1]);
        $oldClass = SchoolClass::create(['name_en' => 'Class 2', 'name_bn' => 'Class 2', 'status' => 1]);
        $section = Section::create([
            'school_class_id' => $currentClass->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $alice = Student::create(['full_name_en' => 'Alice Student', 'student_cid' => '000001', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $alice->id,
            'academic_session_id' => $oldSession->id,
            'school_class_id' => $oldClass->id,
            'section_id' => $section->id,
            'roll' => '99',
        ]);
        StudentAcademicInformation::create([
            'student_id' => $alice->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $currentClass->id,
            'section_id' => $section->id,
            'roll' => '1',
        ]);

        $bob = Student::create(['full_name_en' => 'Bob Student', 'student_cid' => '000002', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $bob->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $oldClass->id,
            'section_id' => $section->id,
            'roll' => '2',
        ]);

        $students = Student::with([
            'academicInformations.schoolClass',
            'academicInformations.section',
        ])->whereIn('id', [$alice->id, $bob->id])->get();

        $controller = app(\App\Http\Controllers\StudentController::class);
        $method = new \ReflectionMethod($controller, 'groupStudentsByClassAndSection');
        $method->setAccessible(true);
        $groupedStudents = $method->invoke($controller, $students);

        $this->assertSame(
            [$alice->id, $bob->id],
            $groupedStudents->flatMap(fn ($classGroup) => $classGroup['sections'])
                ->flatMap(fn ($sectionGroup) => $sectionGroup['students'])
                ->pluck('id')
                ->all()
        );
    }
}
