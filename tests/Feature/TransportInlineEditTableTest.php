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

class TransportInlineEditTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_renders_inline_edit_controls(): void
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

        $student = Student::create([
            'full_name_en' => 'Transport Student',
            'student_cid'   => 'TRN-001',
            'status'        => 1,
        ]);

        $academicInfo = StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '1',
            'academic_status'     => 'active',
            'is_current'          => true,
        ]);

        $feeCategory = FeeCategory::create([
            'name_en' => 'Transport Fee',
            'status' => 1,
            'is_transport' => 1,
        ]);

        Transport::create([
            'student_id' => $student->id,
            'student_academic_information_id' => $academicInfo->id,
            'academic_session_id' => $session->id,
            'fee_category_id' => $feeCategory->id,
            'amount' => 370,
            'status' => Transport::STATUS_ACTIVE,
        ]);

        $response = $this->withoutMiddleware(CheckPermission::class)
            ->actingAs($user)
            ->get(route('transports.index'));

        $response->assertOk();
        $response->assertSee('transport-edit-btn', false);
        $response->assertSee('transportUpdateForm', false);
        $response->assertSee('transport-update-btn', false);
        $response->assertSee('Update', false);
        $response->assertDontSee('fa-trash', false);
    }
}
