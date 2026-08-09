<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
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

class FeeSetYearlyDueDateTest extends TestCase
{
    use RefreshDatabase;

    public function test_yearly_fee_set_persists_due_date_and_assigns_fees_with_it(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 1',
            'name_bn' => 'Class 1',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'A',
            'name_bn' => 'A',
            'status' => 1,
        ]);

        $student = Student::create([
            'full_name_en' => 'Yearly Fee Student',
            'student_cid' => 'STU-2026-001',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '1',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $category = FeeCategory::create([
            'name' => 'Admission Fee',
            'bn_name' => 'ভর্তি ফি',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $response = $this->actingAs($user)->post(route('fee-sets.store'), [
            'name' => 'Yearly Set',
            'bn_name' => 'Yearly Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'group_id' => null,
            'frequency' => 'yearly',
            'due_date' => '2026-11-15',
            'description' => 'Yearly fee set',
            'fee_category_id' => [$category->id],
            'amount' => [2500],
        ]);

        $response->assertRedirect();

        $feeSet = FeeSet::with('items')->firstOrFail();
        $this->assertSame('2026-11-15', $feeSet->due_date?->toDateString());

        $generatedFee = Fee::where('student_id', $student->id)
            ->where('fee_set_id', $feeSet->id)
            ->firstOrFail();

        $this->assertSame('2026-11-15', $generatedFee->due_date?->toDateString());
        $this->assertSame(2500.0, (float) $generatedFee->amount);
        $this->assertSame('pending', $generatedFee->status);
    }

    public function test_yearly_fee_set_update_replaces_due_date(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 2',
            'name_bn' => 'Class 2',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'B',
            'name_bn' => 'B',
            'status' => 1,
        ]);

        $student = Student::create([
            'full_name_en' => 'Updated Yearly Fee Student',
            'student_cid' => 'STU-2026-002',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '2',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $category = FeeCategory::create([
            'name' => 'Lab Fee',
            'bn_name' => 'ল্যাব ফি',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Initial Yearly Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'yearly',
            'due_date' => '2026-10-10',
        ]);

        $response = $this->actingAs($user)->put(route('fee-sets.update', $feeSet->id), [
            'name' => 'Initial Yearly Set',
            'bn_name' => null,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'group_id' => null,
            'frequency' => 'yearly',
            'due_date' => '2026-12-05',
            'description' => null,
            'fee_category_id' => [$category->id],
            'amount' => [3000],
        ]);

        $response->assertRedirect();

        $updatedFeeSet = FeeSet::findOrFail($feeSet->id);
        $this->assertSame('2026-12-05', $updatedFeeSet->due_date?->toDateString());

        $updatedFee = Fee::where('student_id', $student->id)
            ->where('fee_set_id', $feeSet->id)
            ->firstOrFail();

        $this->assertSame('2026-12-05', $updatedFee->due_date?->toDateString());
        $this->assertSame(3000.0, (float) $updatedFee->amount);
        $this->assertSame('pending', $updatedFee->status);

        $this->assertFalse(
            Fee::where('student_id', $student->id)
                ->where('fee_set_id', $feeSet->id)
                ->whereDate('due_date', '2026-10-10')
                ->exists()
        );
    }

    public function test_fee_set_update_skips_existing_month_fees_and_keeps_payments(): void
    {
        $user = User::factory()->create();

        $session = AcademicSession::create([
            'name_en' => '2026',
            'name_bn' => '২০২৬',
            'status' => 1,
        ]);

        $class = SchoolClass::create([
            'name_en' => 'Class 3',
            'name_bn' => 'Class 3',
            'status' => 1,
        ]);

        $section = Section::create([
            'school_class_id' => $class->id,
            'name_en' => 'C',
            'name_bn' => 'C',
            'status' => 1,
        ]);

        $student = Student::create([
            'full_name_en' => 'Protected Month Student',
            'student_cid' => 'STU-2026-003',
            'status' => 1,
        ]);

        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'roll' => '3',
            'academic_status' => 'active',
            'is_current' => true,
        ]);

        $category = FeeCategory::create([
            'name' => 'Tuition Fee',
            'bn_name' => 'টিউশন ফি',
            'status' => 1,
            'student_type' => 'both',
        ]);

        $feeSet = FeeSet::create([
            'name' => 'Monthly Tuition Set',
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'frequency' => 'monthly',
        ]);

        $existingFee = Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 1000,
            'paid_amount' => 300,
            'due_date' => '2026-01-31',
            'status' => 'partial',
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'amount' => 300,
            'payment_date' => '2026-01-15',
            'payment_method' => 'Cash',
            'receipt_no' => 'RCPT-2026-001',
            'status' => 'completed',
        ]);

        PaymentItem::create([
            'payment_id' => $payment->id,
            'fee_id' => $existingFee->id,
            'amount' => 300,
        ]);

        $response = $this->actingAs($user)->put(route('fee-sets.update', $feeSet->id), [
            'name' => 'Monthly Tuition Set',
            'bn_name' => null,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'group_id' => null,
            'frequency' => 'monthly',
            'description' => null,
            'fee_category_id' => [$category->id],
            'amount' => [1200],
        ]);

        $response->assertRedirect();

        $feeSet->refresh();

        $preservedFee = Fee::where('student_id', $student->id)
            ->where('fee_set_id', $feeSet->id)
            ->whereDate('due_date', '2026-01-31')
            ->firstOrFail();

        $this->assertSame($existingFee->id, $preservedFee->id);
        $this->assertSame(1000.0, (float) $preservedFee->amount);
        $this->assertSame(300.0, (float) $preservedFee->paid_amount);
        $this->assertSame('partial', $preservedFee->status);

        $this->assertTrue(
            PaymentItem::where('fee_id', $existingFee->id)->exists()
        );

        $febFee = Fee::where('student_id', $student->id)
            ->where('fee_set_id', $feeSet->id)
            ->whereDate('due_date', '2026-02-28')
            ->firstOrFail();

        $this->assertSame(1200.0, (float) $febFee->amount);
        $this->assertSame('pending', $febFee->status);
    }
}
