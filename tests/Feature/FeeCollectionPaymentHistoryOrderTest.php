<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCollectionPaymentHistoryOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_latest_payment_appears_first_on_collect_payment_page(): void
    {
        $user = User::factory()->create();

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
            'full_name_en' => 'Fee History Student',
            'student_cid'   => 'FEE-001',
            'status'        => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id'          => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id'     => $class->id,
            'section_id'          => $section->id,
            'roll'                => '1',
            'academic_status'     => 'active',
            'is_current'          => true,
        ]);

        Payment::create([
            'student_id'       => $student->id,
            'amount'           => 100,
            'gross_amount'     => 100,
            'scholarship_amount' => 0,
            'discount_type'    => 'flat',
            'discount_amount'  => 0,
            'payment_date'     => '2026-01-01',
            'payment_method'   => 'Cash',
            'receipt_no'       => 'RCPT-OLD',
            'description'      => 'Old payment',
        ]);

        Payment::create([
            'student_id'       => $student->id,
            'amount'           => 150,
            'gross_amount'     => 150,
            'scholarship_amount' => 0,
            'discount_type'    => 'flat',
            'discount_amount'  => 0,
            'payment_date'     => '2026-02-01',
            'payment_method'   => 'Cash',
            'receipt_no'       => 'RCPT-NEW',
            'description'      => 'New payment',
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $student->id]));

        $response->assertOk();
        $response->assertSeeInOrder(['RCPT-NEW', 'RCPT-OLD']);
    }

    public function test_history_lists_only_the_deductions_a_payment_had(): void
    {
        $user = User::factory()->create();
        $student = Student::create([
            'full_name_en' => 'Deduction Student',
            'student_cid'  => 'FEE-002',
            'status'       => 1,
        ]);

        Payment::create([
            'student_id'      => $student->id,
            'amount'          => 900,
            'gross_amount'    => 1000,
            'discount_type'   => 'percent',
            'discount_amount' => 100,
            'payment_date'    => '2026-03-01',
            'payment_method'  => 'Cash',
            'receipt_no'      => 'RCPT-DISC',
        ]);
        Payment::create([
            'student_id'      => $student->id,
            'amount'          => 500,
            'gross_amount'    => 500,
            'discount_amount' => 0,
            'payment_date'    => '2026-02-01',
            'payment_method'  => 'Cash',
            'receipt_no'      => 'RCPT-PLAIN',
        ]);

        $html = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $student->id]))
            ->assertOk()
            ->getContent();

        $rows = [];
        preg_match_all('#<tr>\s*<td class="px-3 py-3 payment-history-receipt">.*?</tr>#s', $html, $rows);
        $this->assertCount(2, $rows[0]);
        [$discounted, $plain] = $rows[0];

        // Discount is stored in BDT, so no "%" next to the amount even for a percentage discount.
        $this->assertStringContainsString('RCPT-DISC', $discounted);
        $this->assertMatchesRegularExpression('#payment-history-deduction-label">Discount</span>\s*<span class="payment-history-deduction-amount is-discount">-100\.00</span>#', $discounted);
        $this->assertStringNotContainsString('Scholarship</span>', $discounted);
        $this->assertStringNotContainsString('%', strip_tags($discounted));

        $this->assertStringContainsString('RCPT-PLAIN', $plain);
        $this->assertStringContainsString('payment-history-deductions is-empty', $plain);
    }
}
