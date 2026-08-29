<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\AdmissionApplication;
use App\Models\AdmissionExam;
use App\Models\AdmissionExamClassSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AdmissionConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdmissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_application_is_created_under_the_active_exam(): void
    {
        [$session, $class] = $this->examSetup();

        $this->get(route('public.admission.form'))->assertOk()->assertSee('New Admission');

        $response = $this->post(route('public.admission.store'), [
            'school_class_id' => $class->id,
            'full_name_en' => 'Applicant One',
            'date_of_birth' => '2015-01-01',
            'gender' => 1,
            'religion' => 1,
            'father_name' => 'Parent One',
            'mother_name' => 'Parent Two',
            'father_phone' => '01700000000',
            'guardian_phone' => '01700000000',
            'present_address' => 'Dhaka',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admission_applications', ['full_name_en' => 'Applicant One', 'school_class_id' => $class->id, 'payment_status' => 'unpaid']);
        $this->assertDatabaseHas('admission_payments', ['amount' => 500, 'status' => 'pending']);

        $application = AdmissionApplication::where('full_name_en', 'Applicant One')->firstOrFail();
        $this->get(route('public.admission.application-pdf', $application))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_public_application_requires_a_father_or_mother_phone(): void
    {
        [, $class] = $this->examSetup();

        $this->from(route('public.admission.form'))
            ->post(route('public.admission.store'), [
                'school_class_id' => $class->id,
                'full_name_en' => 'Applicant Without Parent Phone',
                'gender' => 1,
                'religion' => 1,
                'father_name' => 'Parent One',
                'mother_name' => 'Parent Two',
            ])
            ->assertSessionHasErrors(['father_phone', 'mother_phone']);
    }

    public function test_conversion_creates_student_academic_record_and_normal_fees_path(): void
    {
        [$session, $class] = $this->examSetup();
        $application = AdmissionApplication::create([
            'admission_exam_id' => AdmissionExam::first()->id,
            'application_number' => 'APP-TEST-001',
            'application_no' => 'APP-TEST-001',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'class_id' => $class->id,
            'full_name_en' => 'Approved Applicant',
            'applicant_data' => ['full_name_en' => 'Approved Applicant', 'date_of_birth' => '2015-01-01', 'gender' => 1, 'father_name' => 'Parent'],
            'review_status' => 'approved',
        ]);

        app(AdmissionConversionService::class)->convert($application);

        $this->assertDatabaseHas('admission_applications', ['id' => $application->id, 'conversion_status' => 'converted']);
        $this->assertDatabaseHas('students', ['full_name_en' => 'Approved Applicant']);
        $this->assertDatabaseHas('student_academic_information', ['academic_session_id' => $session->id, 'school_class_id' => $class->id, 'roll' => '1']);
        $this->assertDatabaseCount('admission_conversions', 1);
    }

    public function test_public_application_can_be_found_by_parent_mobile_or_birth_certificate(): void
    {
        [$session, $class] = $this->examSetup();
        AdmissionApplication::create([
            'admission_exam_id' => AdmissionExam::first()->id,
            'application_number' => 'APP-SEARCH-001',
            'application_no' => 'APP-SEARCH-001',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'class_id' => $class->id,
            'full_name_en' => 'Search Applicant',
            'applicant_data' => ['full_name_en' => 'Search Applicant'],
            'father_phone' => '01700-111222',
            'mother_phone' => '01800 333444',
            'birth_certificate_number' => 'BC-SEARCH-001',
            'submitted_at' => now(),
        ]);

        $this->get(route('public.admission.search', ['search' => '01700111222']))
            ->assertOk()
            ->assertSee('APP-SEARCH-001');
        $this->get(route('public.admission.search', ['search' => '01800 333444']))
            ->assertOk()
            ->assertSee('APP-SEARCH-001');
        $this->get(route('public.admission.search', ['search' => 'BC-SEARCH-001']))
            ->assertOk()
            ->assertSee('APP-SEARCH-001');
    }

    private function examSetup(): array
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'One', 'name_en' => 'Class One', 'status' => 1]);
        $exam = AdmissionExam::create(['name' => 'Admission Test 2026', 'academic_session_id' => $session->id, 'exam_date' => '2026-12-01', 'form_fee' => 500, 'status' => true]);
        AdmissionExamClassSetting::create(['admission_exam_id' => $exam->id, 'school_class_id' => $class->id, 'pass_mark' => 40]);
        return [$session, $class];
    }
}
