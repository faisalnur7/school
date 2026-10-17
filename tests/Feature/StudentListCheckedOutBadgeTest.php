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

class StudentListCheckedOutBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_checked_out_student_has_badge_after_name_in_directory(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);
        $class = SchoolClass::create([
            'name_en' => 'Class 12',
            'name_bn' => 'Class 12',
            'status' => 1,
        ]);
        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);
        $student = Student::create([
            'full_name_en' => 'Checked Out Student',
            'student_cid' => 'OUT-001',
            'status' => 0,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'withdrawn',
            'is_current' => false,
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('students.index', [
                'academic_session_id' => $session->id,
                'school_class_id' => $class->id,
                'section_id' => $section->id,
                'status' => '0',
            ]));

        $response->assertOk();
        $response->assertSee($student->full_name_en);
        $response->assertSeeInOrder([$student->full_name_en, 'Checked out']);
    }
}
