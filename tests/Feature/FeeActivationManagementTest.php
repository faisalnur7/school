<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeAmountHistory;
use App\Models\FeeSet;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeActivationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_collect_payment_excludes_inactive_fees_from_dues_and_keeps_assigned_fees(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-001');

        $activeFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 500,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
            'due_date' => now()->addMonth(),
        ]);

        $inactiveFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 700,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => false,
            'due_date' => now()->addMonths(2),
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('fees.collect_payment', ['student_id' => $context['student']->id]));

        $response->assertOk();
        $response->assertSee('Assigned Fees', false);

        $this->assertCount(1, $response->viewData('pendingFees'));
        $this->assertTrue($response->viewData('pendingFees')->contains('id', $activeFee->id));
        $this->assertFalse($response->viewData('pendingFees')->contains('id', $inactiveFee->id));
        $this->assertCount(2, $response->viewData('assignedFees'));
    }

    public function test_paid_fee_cannot_be_toggled_from_student_profile(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-002');

        $paidFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 800,
            'paid_amount' => 800,
            'status' => 'paid',
            'is_active' => true,
            'due_date' => now()->addMonth(),
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('fees.toggle-status', $paidFee->id));

        $response->assertSessionHas('error', 'Paid fees cannot be toggled.');
        $this->assertDatabaseHas('fees', [
            'id' => $paidFee->id,
            'is_active' => 1,
            'status' => 'paid',
        ]);
    }

    public function test_pending_fee_amount_can_be_updated_from_collection_flow(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-EDIT-001');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 500,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);

        $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('fees.update', $fee->id), [
                'amount' => 650,
                'return_to_collect' => 1,
            ])
            ->assertRedirect(route('fees.collect_payment', ['student_id' => $context['student']->id]));

        $this->assertDatabaseHas('fees', [
            'id' => $fee->id,
            'amount' => 650,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('fee_amount_histories', [
            'fee_id' => $fee->id,
            'edited_by' => $user->id,
            'old_amount' => 500,
            'new_amount' => 650,
        ]);
        $this->assertSame(1, FeeAmountHistory::where('fee_id', $fee->id)->count());
    }

    public function test_partial_fee_amount_cannot_be_lower_than_paid_amount(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-EDIT-002');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 800,
            'paid_amount' => 300,
            'status' => 'partial',
            'is_active' => true,
        ]);

        $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('fees.update', $fee->id), ['amount' => 250])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseHas('fees', ['id' => $fee->id, 'amount' => 800, 'status' => 'partial']);
    }

    public function test_paid_fee_cannot_be_updated(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-EDIT-003');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 800,
            'paid_amount' => 800,
            'status' => 'paid',
            'is_active' => true,
        ]);

        $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('fees.update', $fee->id), ['amount' => 900])
            ->assertSessionHas('error', 'Paid fees cannot be edited.');

        $this->assertDatabaseHas('fees', ['id' => $fee->id, 'amount' => 800, 'status' => 'paid']);
    }

    public function test_bulk_toggle_updates_unpaid_fees_and_skips_paid_fees(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-003');

        $unpaidActiveFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 300,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
            'due_date' => now()->addMonth(),
        ]);

        $unpaidInactiveFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 400,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => false,
            'due_date' => now()->addMonths(2),
        ]);

        $paidFee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 900,
            'paid_amount' => 900,
            'status' => 'paid',
            'is_active' => false,
            'due_date' => now()->addMonths(3),
        ]);

        $this->withoutMiddleware()
            ->actingAs($user)
            ->post(route('fees.bulk-toggle-status'), [
                'student_id' => $context['student']->id,
                'active_fee_ids' => [$unpaidInactiveFee->id, $paidFee->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('fees', [
            'id' => $unpaidActiveFee->id,
            'is_active' => 0,
        ]);
        $this->assertDatabaseHas('fees', [
            'id' => $unpaidInactiveFee->id,
            'is_active' => 1,
        ]);
        $this->assertDatabaseHas('fees', [
            'id' => $paidFee->id,
            'is_active' => 0,
            'status' => 'paid',
        ]);
    }

    public function test_student_show_page_renders_regular_fee_active_toggle(): void
    {
        $user = User::factory()->create();
        $context = $this->createStudentContext('FEE-ACT-004');

        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 650,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
            'due_date' => now()->addMonth(),
        ]);

        $response = $this->withoutMiddleware()
            ->actingAs($user)
            ->get(route('students.show', $context['student']->id));

        $response->assertOk();
        $response->assertSee('Active', false);
        $response->assertSee('regularFeeSwitch' . $fee->id, false);
    }

    private function createStudentContext(string $studentCid): array
    {
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
            'full_name_en' => 'Fee Student ' . $studentCid,
            'student_cid' => $studentCid,
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

        $feeSet = FeeSet::create([
            'name' => 'Monthly Fee',
            'academic_session_id' => $session->id,
            'frequency' => 'monthly',
        ]);

        return compact('session', 'class', 'section', 'student', 'feeSet');
    }
}
