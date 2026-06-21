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

class TransportStudentsListTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_students_excludes_checked_out_students(): void
    {
        $user = User::create([
            'name' => 'Transport Admin',
            'email' => 'transport@example.com',
            'password' => bcrypt('password'),
        ]);

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status'  => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status'  => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
        ]);

        $activeStudent = Student::create([
            'full_name_en' => 'Active Student',
            'student_cid'   => 'ACT-001',
            'status'        => 1,
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
            'student_cid'   => 'OUT-001',
            'status'        => 1,
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

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->getJson(route('transports.get-students', [
                'academic_session_id' => $session->id,
                'school_class_id'     => $class->id,
                'section_id'          => $section->id,
            ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'student_id' => $activeStudent->id,
            'name'       => $activeStudent->full_name_en,
        ]);
        $response->assertJsonMissing([
            'student_id' => $checkedOutStudent->id,
            'name'       => $checkedOutStudent->full_name_en,
        ]);
    }
}
