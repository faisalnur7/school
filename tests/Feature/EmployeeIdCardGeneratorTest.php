<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIdCardGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_id_card_page_filters_and_renders_employee_data(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $designation = Designation::create([
            'name' => 'Principal',
            'employee_type' => 'staff',
            'hierarchy_level' => 1,
            'status' => 'active',
        ]);
        $employee = Employee::create([
            'user_id' => User::factory()->create()->id,
            'employee_id' => 'EMP-001',
            'name' => 'Md. Raqib Hossain',
            'employee_type' => 'staff',
            'designation_id' => $designation->id,
            'gender' => 'male',
            'joining_date' => '2024-12-01',
            'phone' => '01630100775',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('hr.employee-id-cards', ['employee_type' => 'staff', 'employee_search' => 'EMP-001']))
            ->assertOk()
            ->assertSee('Generate Employee ID Cards')
            ->assertSee('Md. Raqib Hossain')
            ->assertSee('EMP-001')
            ->assertSee('Employee Name')
            ->assertSee('Role')
            ->assertSee('Department')
            ->assertSee('id-card__label-badge">EMPLOYEE ID', false)
            ->assertDontSee('id-card__label-badge">STUDENT ID', false)
            ->assertDontSee('Student Name')
            ->assertSee('Employee ID Card Settings')
            ->assertSee('Layout &amp; Grid', false)
            ->assertSee('Photo &amp; Logo', false)
            ->assertSee('Typography &amp; Colors', false)
            ->assertSee('Background')
            ->assertSee('Visibility');

        $this->assertStringContainsString(
            'btn-danger',
            $this->actingAs($admin)
                ->get(route('hr.employee-id-cards', ['employee_type' => 'staff', 'employee_search' => 'EMP-001']))
                ->getContent()
        );
    }

    public function test_empty_employee_id_card_pdf_redirects_without_generating_a_file(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($admin)
            ->get(route('hr.employee-id-cards.pdf', ['employee_search' => 'missing']))
            ->assertRedirect(route('hr.employee-id-cards'))
            ->assertSessionHas('error');
    }

    public function test_employee_id_card_pdf_is_generated_for_selected_employees(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $designation = Designation::create([
            'name' => 'Teacher',
            'employee_type' => 'teacher',
            'hierarchy_level' => 1,
            'status' => 'active',
        ]);
        $employee = Employee::create([
            'user_id' => User::factory()->create()->id,
            'employee_id' => 'EMP-002',
            'name' => 'Amina Akter',
            'employee_type' => 'teacher',
            'designation_id' => $designation->id,
            'gender' => 'female',
            'joining_date' => '2025-01-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('hr.employee-id-cards.pdf', [
            'employee_type' => 'teacher',
            'employee_search' => $employee->employee_id,
        ]));

        $response->assertOk()->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
