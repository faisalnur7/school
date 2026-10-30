<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeSet;
use App\Models\FeeSetItem;
use App\Models\Payment;
use App\Models\PaymentRequest;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAcademicInformation;
use App\Models\User;
use App\Services\PaymentRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentRequestCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_payment_loads_the_request_into_the_existing_collection_page(): void
    {
        $context = $this->createStudentContext('COLLECT-PR-001');
        $fee = $this->createFee($context['student'], $context['feeSet'], 300);
        $request = app(PaymentRequestService::class)->createForStudent($context['student'], ['fees' => [['fee_id' => $fee->id]]]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->permissionedUser())
            ->get(\Illuminate\Support\Facades\URL::temporarySignedRoute('fees.collect_payment', now()->addMinutes(15), ['student_id' => $context['student']->id, 'payment_request' => $request->id]));

        $response->assertOk()->assertSee('Immutable mobile payment request');
        $this->assertSame($request->id, $response->viewData('paymentRequest')->id);
    }

    public function test_exact_request_collection_uses_normal_payment_flow_and_completes_request(): void
    {
        $context = $this->createStudentContext('COLLECT-PR-002');
        $fee = $this->createFee($context['student'], $context['feeSet'], 300);
        $request = app(PaymentRequestService::class)->createForStudent($context['student'], ['fees' => [['fee_id' => $fee->id]]]);

        $response = $this->withoutMiddleware()
            ->actingAs($this->permissionedUser())
            ->postJson(route('fees.pay'), [
                'payment_request_id' => $request->id,
                'student_id' => $context['student']->id,
                'fees' => [['fee_id' => $fee->id, 'amount' => 300]],
                'items' => [],
                'inventory_dues' => [],
                'discount_amount' => 0,
                'discount_type' => 'flat',
                'payment_date' => '2026-10-30',
                'payment_method' => 'Cash',
            ]);

        $response->assertOk()->assertJsonPath('message', 'Payment collected successfully');
        $this->assertDatabaseHas('payment_requests', [
            'id' => $request->id,
            'status' => PaymentRequest::STATUS_COMPLETED,
        ]);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('fees', ['id' => $fee->id, 'status' => 'paid', 'paid_amount' => 300]);
    }

    public function test_request_collection_rejects_edited_amounts_without_creating_a_payment(): void
    {
        $context = $this->createStudentContext('COLLECT-PR-003');
        $fee = $this->createFee($context['student'], $context['feeSet'], 300);
        $request = app(PaymentRequestService::class)->createForStudent($context['student'], ['fees' => [['fee_id' => $fee->id]]]);

        $this->withoutMiddleware()
            ->actingAs($this->permissionedUser())
            ->postJson(route('fees.pay'), [
                'payment_request_id' => $request->id,
                'student_id' => $context['student']->id,
                'fees' => [['fee_id' => $fee->id, 'amount' => 250]],
                'payment_date' => '2026-10-30',
                'payment_method' => 'Cash',
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('payment_requests', ['id' => $request->id, 'status' => PaymentRequest::STATUS_PENDING]);
        $this->assertDatabaseCount('payments', 0);
    }

    private function createFee(Student $student, FeeSet $feeSet, int $amount): Fee
    {
        return Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => $amount,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
    }

    private function permissionedUser(): User
    {
        $category = PermissionCategory::firstOrCreate(['name' => 'Fee Collection'], ['sort_order' => 5]);
        $permission = Permission::firstOrCreate(['name' => 'collect_payment_requests'], [
            'display_name' => 'Collect Payment Requests',
            'category_id' => $category->id,
            'exposed_to_roles' => true,
        ]);
        $role = Role::create(['name' => 'Payment Request Collector']);
        $role->permissions()->sync([$permission->id]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function createStudentContext(string $cid): array
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create(['student_cid' => $cid, 'full_name_en' => 'Collection Student', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
        ]);
        $category = FeeCategory::create(['name_en' => 'Tuition']);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id]);
        FeeSetItem::create(['fee_set_id' => $feeSet->id, 'fee_category_id' => $category->id, 'amount' => 300]);

        return compact('session', 'class', 'section', 'student', 'feeSet');
    }
}
