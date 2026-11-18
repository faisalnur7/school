<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\AdmissionApplication;
use App\Models\AdmissionExam;
use App\Models\AdmissionExamClassSetting;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\HandCash;
use App\Models\Income;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\AdmissionConversionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
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
            'date_of_birth' => '16/08/2000',
            'gender' => 1,
            'religion' => 1,
            'father_name' => 'Parent One',
            'mother_name' => 'Parent Two',
            'father_phone' => '01700000000',
            'guardian_phone' => '01700000000',
            'present_address' => 'Dhaka',
        ]);

        $response->assertRedirect();
        $previewUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/admission/preview/', $previewUrl);
        $this->assertDatabaseCount('admission_applications', 0);
        $this->get($previewUrl)->assertOk()->assertSee('Check your application')->assertSee('Applicant One');
        $draftToken = basename(parse_url($previewUrl, PHP_URL_PATH));
        $this->get(route('public.admission.form', ['draft' => $draftToken]))->assertOk()->assertSee('Applicant One')->assertSee('draft_token');
        $confirmResponse = $this->post($previewUrl . '/confirm');
        $confirmResponse->assertRedirect(route('public.admission.search', [
            'application_number' => '0001',
            'phone' => '01700000000',
        ]));
        $this->get($confirmResponse->headers->get('Location'))->assertOk()->assertSee('Applicant One')->assertSee('Export as PDF');
        $this->assertDatabaseHas('admission_applications', ['full_name_en' => 'Applicant One', 'school_class_id' => $class->id, 'payment_status' => 'unpaid']);
        $application = AdmissionApplication::where('full_name_en', 'Applicant One')->firstOrFail();
        $this->assertSame('0001', $application->application_number);
        $this->assertSame($application->application_number, $application->application_no);
        $this->assertDatabaseHas('admission_payments', ['amount' => 500, 'status' => 'pending']);

        $statusPage = $this->get($confirmResponse->headers->get('Location'));
        $this->get($this->signedLinkFrom($statusPage->getContent(), 'pdf'))
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

    public function test_other_guardian_requires_guardian_details(): void
    {
        [, $class] = $this->examSetup();

        $this->from(route('public.admission.form'))
            ->post(route('public.admission.store'), [
                'school_class_id' => $class->id,
                'full_name_en' => 'Applicant With Other Guardian',
                'gender' => 1,
                'religion' => 1,
                'father_name' => 'Parent One',
                'mother_name' => 'Parent Two',
                'father_phone' => '01700000000',
                'guardian_type' => 3,
            ])
            ->assertSessionHasErrors(['guardian_name', 'guardian_relation', 'guardian_phone']);
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

        $this->get(route('public.admission.search', ['application_number' => 'APP-SEARCH-001', 'phone' => '01700111222']))
            ->assertOk()
            ->assertSee('APP-SEARCH-001');
        $this->get(route('public.admission.search', ['application_number' => 'APP-SEARCH-001', 'phone' => '01800 333444']))
            ->assertOk()
            ->assertSee('APP-SEARCH-001');
        $this->get(route('public.admission.search', ['application_number' => 'APP-SEARCH-001']))
            ->assertOk()
            ->assertSee('Enter the father, mother, guardian, or other contact phone used in the application.');
    }

    public function test_clearing_a_saved_mark_resets_the_result_to_not_entered(): void
    {
        [, $class] = $this->examSetup();
        $exam = AdmissionExam::first();
        $application = $this->makeApplication(['payment_status' => 'paid']);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admissions.marks.batch', $exam), [
            'school_class_id' => $class->id,
            'marks' => [$application->id => 55],
        ])->assertRedirect();
        $this->assertSame('passed', $application->fresh()->result_status);

        $this->actingAs($admin)->post(route('admissions.marks.batch', $exam), [
            'school_class_id' => $class->id,
            'marks' => [$application->id => null],
        ])->assertRedirect();

        $application->refresh();
        $this->assertNull($application->total_marks);
        $this->assertNull($application->pass_mark_snapshot);
        $this->assertSame('not_entered', $application->result_status);
    }

    public function test_collecting_payment_without_an_active_hand_cash_account_shows_a_clear_error(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'unpaid']);

        $this->actingAs($this->admin())
            ->from(route('admissions.applications'))
            ->post(route('admissions.applications.collect-payment', $application))
            ->assertRedirect(route('admissions.applications'))
            ->assertSessionHasErrors(['payment' => 'No active Hand Cash account. Create one under Accounts → Hand Cash first.']);

        $this->assertSame('unpaid', $application->fresh()->payment_status);
        $this->assertSame(0, Income::count());
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_collecting_payment_records_income_into_the_active_hand_cash_account(): void
    {
        $this->examSetup();
        $this->activeHandCash();
        $application = $this->makeApplication(['payment_status' => 'unpaid']);

        $this->actingAs($this->admin())
            ->post(route('admissions.applications.collect-payment', $application), ['discount_amount' => 100])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('paid', $application->fresh()->payment_status);
        $this->assertDatabaseHas('admission_payments', [
            'admission_application_id' => $application->id,
            'status' => 'paid',
            'gross_amount' => 500,
            'discount_amount' => 100,
            'total_amount' => 400,
        ]);
        $this->assertSame(400.0, (float) Income::sole()->amount);
    }

    public function test_public_downloads_require_a_signed_link(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid']);

        $this->get(route('public.admission.application-pdf', $application))->assertForbidden();
        $this->get(route('public.admission.admit-card', $application))->assertForbidden();

        $this->get(URL::temporarySignedRoute('public.admission.application-pdf', now()->addHours(2), $application))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->get(URL::temporarySignedRoute('public.admission.admit-card', now()->addHours(2), $application))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_public_download_links_expire_after_two_hours(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid']);
        $page = $this->get(route('public.admission.search', ['application_number' => $application->application_number, 'phone' => '01700000000']));
        $link = $this->signedLinkFrom($page->getContent(), 'pdf');

        $this->travel(2)->hours();
        $this->travel(1)->minutes();

        $this->get($link)->assertForbidden();
    }

    public function test_status_page_only_shows_signed_links_after_the_phone_matches(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid']);
        $application->admitCard()->create(['admit_card_number' => 'ADM-TEST-000001']);

        $matched = $this->get(route('public.admission.search', ['application_number' => $application->application_number, 'phone' => '01700000000']));
        $matched->assertOk()->assertSee('Download Admit Card');
        $this->get($this->signedLinkFrom($matched->getContent(), 'pdf'))->assertOk();
        $this->get($this->signedLinkFrom($matched->getContent(), 'admit-card'))->assertOk();

        $this->get(route('public.admission.search', ['application_number' => $application->application_number, 'phone' => '01999999999']))
            ->assertOk()
            ->assertDontSee($application->full_name_en)
            ->assertDontSee('signature=', false);
    }

    public function test_admin_can_still_print_admit_cards_without_a_signed_link(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid']);
        $application->admitCard()->create(['admit_card_number' => 'ADM-TEST-000001']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admissions.admit-cards'))
            ->assertOk()
            ->assertSee(route('admissions.applications.admit-card', $application), false)
            ->assertDontSee(route('public.admission.admit-card', $application), false);

        $this->actingAs($admin)->get(route('admissions.applications.admit-card', $application))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_admit_card_staff_without_application_access_can_still_print(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid']);
        $role = Role::create(['name' => 'Admit Card Desk']);
        $permission = Permission::create(['name' => 'manage_admission_admit_cards', 'display_name' => 'Manage admission admit cards']);
        $role->permissions()->attach($permission);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user)->get(route('admissions.applications.admit-card', $application))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_unpaid_applicant_cannot_be_approved(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'unpaid', 'result_status' => 'passed', 'total_marks' => 60]);

        $this->actingAs($this->admin())
            ->post(route('admissions.applications.review', $application), ['decision' => 'approved', 'notes' => 'Looks fine'])
            ->assertSessionHasErrors(['decision' => 'Cannot approve: payment not received. Collect the form fee first.']);

        $this->assertSame('pending', $application->fresh()->review_status);
        $this->assertDatabaseCount('admission_application_reviews', 0);
    }

    public function test_paid_applicant_who_did_not_pass_needs_a_reason_to_be_approved(): void
    {
        $this->examSetup();
        $admin = $this->admin();
        $failed = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'failed', 'total_marks' => 20]);
        $notMarked = $this->makeApplication(['payment_status' => 'paid']);

        foreach ([$failed, $notMarked] as $application) {
            $this->actingAs($admin)
                ->post(route('admissions.applications.review', $application), ['decision' => 'approved', 'notes' => '  '])
                ->assertSessionHasErrors(['notes' => 'This applicant did not pass the admission exam. Write the reason in Admin notes to approve.']);
            $this->assertSame('pending', $application->fresh()->review_status);
        }

        $this->actingAs($admin)
            ->post(route('admissions.applications.review', $failed), ['decision' => 'approved', 'notes' => 'Sibling of a current student, approved by the Principal'])
            ->assertSessionHasNoErrors();

        $failed->refresh();
        $this->assertSame('approved', $failed->review_status);
        $this->assertSame('Sibling of a current student, approved by the Principal', $failed->admin_notes);
        $this->assertDatabaseHas('admission_application_reviews', [
            'admission_application_id' => $failed->id,
            'decision' => 'approved',
            'notes' => 'Sibling of a current student, approved by the Principal',
        ]);
    }

    public function test_paid_applicant_who_passed_can_be_approved_without_a_note(): void
    {
        $this->examSetup();
        $application = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'passed', 'total_marks' => 60]);

        $this->actingAs($this->admin())
            ->post(route('admissions.applications.review', $application), ['decision' => 'approved'])
            ->assertSessionHasNoErrors();

        $this->assertSame('approved', $application->fresh()->review_status);
    }

    public function test_any_applicant_can_be_rejected_or_set_back_to_pending(): void
    {
        $this->examSetup();
        $admin = $this->admin();
        $application = $this->makeApplication(['payment_status' => 'unpaid']);

        $this->actingAs($admin)->post(route('admissions.applications.review', $application), ['decision' => 'rejected'])->assertSessionHasNoErrors();
        $this->assertSame('rejected', $application->fresh()->review_status);

        $this->actingAs($admin)->post(route('admissions.applications.review', $application), ['decision' => 'pending'])->assertSessionHasNoErrors();
        $this->assertSame('pending', $application->fresh()->review_status);
    }

    public function test_bulk_approval_is_all_or_nothing_when_an_applicant_is_unpaid(): void
    {
        $this->examSetup();
        $paidA = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'passed', 'total_marks' => 70]);
        $unpaid = $this->makeApplication(['payment_status' => 'unpaid', 'result_status' => 'passed', 'total_marks' => 65]);
        $paidB = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'passed', 'total_marks' => 60]);

        $this->actingAs($this->admin())
            ->post(route('admissions.applications.bulk-review'), [
                'application_ids' => [$paidA->id, $unpaid->id, $paidB->id],
                'decision' => 'approved',
            ])
            ->assertSessionHasErrors(['decision' => "Cannot approve: payment not received for {$unpaid->application_number}. Untick them and try again."]);

        foreach ([$paidA, $unpaid, $paidB] as $application) {
            $this->assertSame('pending', $application->fresh()->review_status);
        }
        $this->assertDatabaseCount('admission_application_reviews', 0);
    }

    public function test_bulk_approval_of_applicants_who_did_not_pass_needs_a_reason(): void
    {
        $this->examSetup();
        $admin = $this->admin();
        $passed = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'passed', 'total_marks' => 70]);
        $failed = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'failed', 'total_marks' => 30]);
        $payload = ['application_ids' => [$passed->id, $failed->id], 'decision' => 'approved'];

        $this->actingAs($admin)
            ->post(route('admissions.applications.bulk-review'), $payload)
            ->assertSessionHasErrors(['notes' => "{$failed->application_number} did not pass the admission exam. Write the reason in Admin notes to approve."]);
        $this->assertSame('pending', $passed->fresh()->review_status);

        $this->actingAs($admin)
            ->post(route('admissions.applications.bulk-review'), $payload + ['notes' => 'Approved by the admission committee'])
            ->assertSessionHasNoErrors();
        $this->assertSame('approved', $passed->fresh()->review_status);
        $this->assertSame('approved', $failed->fresh()->review_status);
        $this->assertSame('Approved by the admission committee', $failed->fresh()->admin_notes);
    }

    public function test_review_form_explains_why_approval_is_blocked_or_needs_a_reason(): void
    {
        $this->examSetup();
        $admin = $this->admin();
        $unpaid = $this->makeApplication(['payment_status' => 'unpaid']);
        $failed = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'failed', 'total_marks' => 20]);
        $passed = $this->makeApplication(['payment_status' => 'paid', 'result_status' => 'passed', 'total_marks' => 70]);

        $this->actingAs($admin)->get(route('admissions.applications.show', $unpaid))
            ->assertOk()
            ->assertSee('Approve (payment not received)')
            ->assertSee('Collect the form fee before approving.');

        $this->actingAs($admin)->get(route('admissions.applications.show', $failed))
            ->assertOk()
            ->assertSee('This applicant did not pass. Write the reason in Admin notes to approve.')
            ->assertDontSee('Approve (payment not received)');

        $this->actingAs($admin)->get(route('admissions.applications.show', $passed))
            ->assertOk()
            ->assertDontSee('This applicant did not pass.')
            ->assertDontSee('Approve (payment not received)');

        $this->actingAs($admin)->get(route('admissions.results'))
            ->assertOk()
            ->assertSee('required to approve applicants who did not pass');
    }

    public function test_marks_entry_records_exam_attendance_including_absent_applicants(): void
    {
        [, $class] = $this->examSetup();
        $exam = AdmissionExam::first();
        $admin = $this->admin();
        $present = $this->makeApplication(['payment_status' => 'paid']);
        $absent = $this->makeApplication(['payment_status' => 'paid']);
        $this->assertNull($absent->admitCard);

        // The disabled marks input of an absent row is not submitted by the browser.
        $this->actingAs($admin)->post(route('admissions.marks.batch', $exam), [
            'school_class_id' => $class->id,
            'marks' => [$present->id => 55],
            'absent' => [$absent->id],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $present->refresh()->load('admitCard');
        $this->assertSame('passed', $present->result_status);
        $this->assertSame('present', $present->admitCard->attendance_status);

        $absent->refresh()->load('admitCard');
        $this->assertNull($absent->total_marks);
        $this->assertSame('failed', $absent->result_status);
        $this->assertEquals(40, $absent->pass_mark_snapshot);
        $this->assertNotNull($absent->admitCard, 'An admit card is created so attendance can be stored.');
        $this->assertSame('absent', $absent->admitCard->attendance_status);

        $this->actingAs($admin)->get(route('admissions.marks', [$exam, 'school_class_id' => $class->id]))
            ->assertOk()
            ->assertSee('name="absent[]" value="' . $absent->id . '" checked', false);

        $this->actingAs($admin)->get(route('admissions.exams.details', $exam))
            ->assertOk()
            ->assertViewHas('appearedStudents', 1);

        $this->actingAs($admin)->get(route('admissions.results'))
            ->assertOk()
            ->assertSee($absent->application_number)
            ->assertSee('Absent');

        $pdfHtml = view('pages.admissions.results.pdf', [
            'applications' => AdmissionApplication::with('admitCard')->whereKey($absent->id)->get(),
            'filters' => ['class' => 'All classes', 'session' => 'All sessions', 'status' => 'All results'],
            'resultColumnOptions' => ['applicant' => 'Applicant', 'total_mark' => 'Total mark', 'status' => 'Status'],
            'selectedColumns' => ['applicant', 'total_mark', 'status'],
        ])->render();
        $this->assertStringContainsString('Absent', $pdfHtml);
        $this->assertStringNotContainsString('@else', $pdfHtml);
        $this->actingAs($admin)->get(route('admissions.results.pdf'))->assertOk()->assertHeader('content-type', 'application/pdf');

        // Un-ticking Absent and leaving the mark blank resets the applicant.
        $this->actingAs($admin)->post(route('admissions.marks.batch', $exam), [
            'school_class_id' => $class->id,
            'marks' => [$present->id => 55, $absent->id => null],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $absent->refresh()->load('admitCard');
        $this->assertSame('not_entered', $absent->result_status);
        $this->assertNull($absent->pass_mark_snapshot);
        $this->assertSame('not_marked', $absent->admitCard->attendance_status);
    }

    public function test_absent_ids_must_belong_to_the_exam_class(): void
    {
        [, $class] = $this->examSetup();
        $exam = AdmissionExam::first();
        $unpaid = $this->makeApplication(['payment_status' => 'unpaid']);

        $this->actingAs($this->admin())->post(route('admissions.marks.batch', $exam), [
            'school_class_id' => $class->id,
            'marks' => [],
            'absent' => [$unpaid->id],
        ])->assertStatus(422);

        $this->assertNull($unpaid->fresh()->admitCard);
    }

    public function test_converted_student_fees_are_due_in_the_admission_session_year(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 4));
        [, $class] = $this->examSetup();
        $nextSession = AcademicSession::create(['name_bn' => '2027', 'name_en' => '2027', 'status' => 1]);
        $category = FeeCategory::create(['name' => 'Tuition Fee', 'bn_name' => 'Tuition Fee', 'status' => 1, 'student_type' => 'both']);
        $monthly = FeeSet::create(['name' => 'Monthly', 'academic_session_id' => $nextSession->id, 'school_class_id' => $class->id, 'frequency' => 'monthly']);
        $yearly = FeeSet::create(['name' => 'Yearly', 'academic_session_id' => $nextSession->id, 'school_class_id' => $class->id, 'frequency' => 'yearly']);
        FeeSetItem::create(['fee_set_id' => $monthly->id, 'fee_category_id' => $category->id, 'amount' => 1000]);
        FeeSetItem::create(['fee_set_id' => $yearly->id, 'fee_category_id' => $category->id, 'amount' => 3000]);
        $application = $this->makeApplication(['academic_session_id' => $nextSession->id, 'review_status' => 'approved']);

        $conversion = app(AdmissionConversionService::class)->convert($application);

        $monthlyDueDates = Fee::where('student_id', $conversion->student_id)->where('fee_set_id', $monthly->id)
            ->orderBy('due_date')->pluck('due_date')->map(fn ($date) => Carbon::parse($date)->toDateString());
        $this->assertCount(12, $monthlyDueDates);
        $this->assertSame('2027-01-31', $monthlyDueDates->first());
        $this->assertSame('2027-12-31', $monthlyDueDates->last());

        $yearlyDueDate = Fee::where('student_id', $conversion->student_id)->where('fee_set_id', $yearly->id)->value('due_date');
        $this->assertSame('2027-12-31', Carbon::parse($yearlyDueDate)->toDateString());
    }

    public function test_session_year_is_read_from_the_session_name(): void
    {
        $this->travelTo(now()->setDate(2026, 11, 4));

        $this->assertSame(2027, (new AcademicSession(['name_en' => '2027']))->year());
        $this->assertSame(2026, (new AcademicSession(['name_en' => '2026-27']))->year());
        $this->assertSame(2025, (new AcademicSession(['name_en' => 'Session 2025-2026']))->year());
        $this->assertSame(2026, (new AcademicSession(['name_en' => 'Current']))->year(), 'Falls back to the current year.');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_super_admin' => true]);
    }

    /** Extract a signed public download link (pdf or admit-card) from a rendered status page. */
    private function signedLinkFrom(string $html, string $type): string
    {
        $pattern = '#href="([^"]+/admission/applications/\d+/' . preg_quote($type, '#') . '\?[^"]*signature=[^"]+)"#';
        $this->assertSame(1, preg_match($pattern, $html, $matches), "No signed {$type} link found on the status page.");

        return html_entity_decode($matches[1]);
    }

    private function activeHandCash(): HandCash
    {
        return HandCash::create([
            'label' => 'Main Cash Box',
            'opening_amount' => 0,
            'opening_date' => now()->toDateString(),
            'is_active' => true,
        ]);
    }

    private function makeApplication(array $attributes = []): AdmissionApplication
    {
        static $sequence = 0;
        $sequence++;
        $exam = AdmissionExam::firstOrFail();
        $number = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        $classId = $exam->classSettings()->value('school_class_id');

        return AdmissionApplication::create(array_merge([
            'admission_exam_id' => $exam->id,
            'application_number' => $number,
            'application_no' => $number,
            'academic_session_id' => $exam->academic_session_id,
            'school_class_id' => $classId,
            'class_id' => $classId,
            'full_name_en' => "Applicant {$number}",
            'applicant_data' => ['full_name_en' => "Applicant {$number}", 'gender' => 1, 'father_name' => 'Parent'],
            'father_phone' => '01700000000',
            'submitted_at' => now(),
        ], $attributes));
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
