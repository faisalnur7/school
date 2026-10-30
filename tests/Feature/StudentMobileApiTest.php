<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Database\Seeders\MobileAppFeatureSeeder;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentMobileApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--class' => MobileAppFeatureSeeder::class, '--quiet' => true]);
    }

    public function test_student_api_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/v1/student/dashboard')
            ->assertUnauthorized();
    }

    public function test_student_routine_pdf_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/v1/student/routine/pdf')
            ->assertUnauthorized();
    }

    public function test_student_login_requires_credentials(): void
    {
        $this->postJson('/api/v1/student/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['login', 'password']);
    }

    public function test_student_fees_stop_at_the_end_of_the_present_month(): void
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create([
            'student_cid' => 'FEE-001',
            'full_name_en' => 'Fee Student',
            'full_name_bn' => 'Fee Student',
            'status' => 1,
        ]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
            'promotion_status' => 'new_admission',
        ]);
        $user = User::factory()->create(['student_id' => $student->id, 'is_active' => true]);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id]);
        $today = CarbonImmutable::today();
        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 100,
            'scholarship_discount' => 0,
            'paid_amount' => 0,
            'due_date' => $today->endOfMonth()->toDateString(),
            'is_active' => true,
        ]);
        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 200,
            'scholarship_discount' => 0,
            'paid_amount' => 0,
            'due_date' => $today->endOfMonth()->addDay()->toDateString(),
            'is_active' => true,
        ]);
        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 999,
            'scholarship_discount' => 0,
            'paid_amount' => 0,
            'due_date' => $today->endOfMonth()->toDateString(),
            'is_active' => false,
        ]);

        Sanctum::actingAs($user, ['student']);

        $this->getJson('/api/v1/student/fees')
            ->assertOk()
            ->assertJsonPath('summary.due', 100)
            ->assertJsonPath('due_till', $today->endOfMonth()->toDateString())
            ->assertJsonCount(1, 'fees');
    }

}
