<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\Fee;
use App\Models\FeeSet;
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

class PaymentRequestAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_employee_can_filter_view_and_cancel_a_request(): void
    {
        $context = $this->createStudentContext('ADMIN-PR-001');
        $fee = Fee::create([
            'student_id' => $context['student']->id,
            'fee_set_id' => $context['feeSet']->id,
            'amount' => 400,
            'paid_amount' => 0,
            'status' => 'pending',
            'is_active' => true,
        ]);
        $request = app(PaymentRequestService::class)->createForStudent($context['student'], [
            'fees' => [['fee_id' => $fee->id]],
        ]);
        $employee = $this->permissionedUser(['view_fees', 'view_card_payment_requests', 'view_payment_requests', 'cancel_payment_requests']);

        $this->actingAs($employee)
            ->get(route('fees.hub'))
            ->assertOk()
            ->assertSee('Payment Requests');

        $this->actingAs($employee)
            ->get(route('payment-requests.index', ['student_id' => $context['student']->student_cid, 'academic_session_id' => $context['session']->id, 'school_class_id' => $context['class']->id, 'section_id' => $context['section']->id]))
            ->assertOk()
            ->assertSee($context['student']->student_cid)
            ->assertSee('View');

        $this->actingAs($employee)
            ->get(route('payment-requests.show', $request))
            ->assertOk()
            ->assertSee('Monthly Fee')
            ->assertSee('Immutable request');

        $this->actingAs($employee)
            ->post(route('payment-requests.cancel', $request))
            ->assertRedirect(route('fees.collect_payment', ['student_id' => $context['student']->id]));

        $this->assertDatabaseHas('payment_requests', ['id' => $request->id, 'status' => 'cancelled']);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_employee_without_payment_request_permission_cannot_open_the_page(): void
    {
        $employee = $this->permissionedUser(['view_fees']);

        $this->actingAs($employee)->get(route('payment-requests.index'))->assertForbidden();
    }

    private function permissionedUser(array $permissions): User
    {
        $category = PermissionCategory::firstOrCreate(['name' => 'Fee Collection'], ['sort_order' => 5]);
        $role = Role::create(['name' => 'Payment Request Employee']);
        $permissionIds = collect($permissions)->map(function (string $name) use ($category) {
            return Permission::firstOrCreate(['name' => $name], [
                'display_name' => str($name)->headline(),
                'category_id' => $category->id,
                'exposed_to_roles' => true,
            ])->id;
        });
        $role->permissions()->sync($permissionIds);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function createStudentContext(string $cid): array
    {
        $session = AcademicSession::create(['name_bn' => '2026', 'name_en' => '2026', 'status' => 1]);
        $class = SchoolClass::create(['name_bn' => 'Class 1', 'name_en' => 'Class 1', 'status' => 1]);
        $section = Section::create(['school_class_id' => $class->id, 'name_bn' => 'A', 'name_en' => 'A']);
        $student = Student::create(['student_cid' => $cid, 'full_name_en' => 'Admin Payment Student', 'status' => 1]);
        StudentAcademicInformation::create([
            'student_id' => $student->id,
            'academic_session_id' => $session->id,
            'school_class_id' => $class->id,
            'section_id' => $section->id,
            'is_current' => true,
            'academic_status' => 'active',
        ]);
        $feeSet = FeeSet::create(['name' => 'Monthly Fee', 'academic_session_id' => $session->id]);

        return compact('session', 'class', 'section', 'student', 'feeSet');
    }
}
