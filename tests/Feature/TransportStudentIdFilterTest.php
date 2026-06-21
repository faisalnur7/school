<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermission;
use App\Models\AcademicSession;
use App\Models\FeeCategory;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\Transport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransportStudentIdFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_filters_by_student_id_text(): void
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

        $feeCategory = FeeCategory::create([
            'name_en' => 'Transport Fee',
            'status' => 1,
            'is_transport' => 1,
        ]);

        $studentOne = Student::create([
            'full_name_en' => 'Alpha Student',
            'student_cid' => '0309',
            'status' => 1,
        ]);

        $infoOne = StudentAcademicInformation::create([
            'student_id' => $studentOne->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        Transport::create([
            'student_id' => $studentOne->id,
            'student_academic_information_id' => $infoOne->id,
            'academic_session_id' => $session->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 370,
            'status' => Transport::STATUS_ACTIVE,
        ]);

        $studentTwo = Student::create([
            'full_name_en' => 'Beta Student',
            'student_cid' => '0310',
            'status' => 1,
        ]);

        $infoTwo = StudentAcademicInformation::create([
            'student_id' => $studentTwo->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        Transport::create([
            'student_id' => $studentTwo->id,
            'student_academic_information_id' => $infoTwo->id,
            'academic_session_id' => $session->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 420,
            'status' => Transport::STATUS_ACTIVE,
        ]);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->get(route('transports.index', [
                'student_cid' => '0309',
            ]));

        $response->assertOk();
        $response->assertSee('0309');
        $response->assertDontSee('0310');
        $response->assertSee('title="Reset"', false);
    }
}
