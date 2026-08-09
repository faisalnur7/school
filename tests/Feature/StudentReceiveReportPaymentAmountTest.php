<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentReceiveReportPaymentAmountTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_receive_report_uses_raw_payment_amounts(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);

        $student = Student::create([
            'student_cid' => 'S-001',
            'full_name_bn' => 'Test Student',
            'full_name_en' => 'Test Student',
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

        $feeSet = FeeSet::create([
            'name' => 'General Fees',
            'academic_session_id' => $session->id,
        ]);
        $fee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1,
            'scholarship_discount' => 0,
            'paid_amount' => 0,
            'due_date' => '2026-08-09',
            'status' => 'active',
            'is_active' => 1,
        ]);

        $paymentOne = Payment::create([
            'student_id' => $student->id,
            'amount' => 12000,
            'receipt_no' => 'R-001',
            'payment_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $paymentOne->id,
            'fee_id' => $fee->id,
            'amount' => 1,
        ]);

        $paymentTwo = Payment::create([
            'student_id' => $student->id,
            'amount' => 14080,
            'receipt_no' => 'R-002',
            'payment_date' => '2026-08-09',
            'payment_method' => 'Cash',
            'collected_by' => $user->id,
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $paymentTwo->id,
            'fee_id' => $fee->id,
            'amount' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('fees.student-receive-report', [
            'student_id' => '',
            'session_id' => $session->id,
            'class_id' => '',
            'section_id' => '',
            'from_date' => '2026-08-08',
            'to_date' => '2026-08-09',
        ]));

        $response->assertOk();
        $response->assertSee('Grand Total Received');
        $response->assertSee('26,080.00');
        $response->assertSee('Receipt R-001');
        $response->assertSee('Receipt R-002');
    }
}
