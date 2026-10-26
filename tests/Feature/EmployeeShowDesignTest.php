<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeShowDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_show_uses_the_create_form_data_without_salary_tabs(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $employeeUser = User::factory()->create([
            'name' => 'Md. Raqib Hossain',
            'email' => 'raqib@example.test',
        ]);
        $designation = Designation::create([
            'name' => 'Principal',
            'employee_type' => 'staff',
            'hierarchy_level' => 1,
            'status' => 'active',
        ]);
        $employee = Employee::create([
            'user_id' => $employeeUser->id,
            'employee_id' => 'EMP-001',
            'name' => 'Md. Raqib Hossain',
            'employee_type' => 'staff',
            'designation_id' => $designation->id,
            'gender' => 'male',
            'joining_date' => '2024-12-01',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('hr.employees.show', $employee));

        $response->assertOk()
            ->assertSee('Personal Information')
            ->assertSee('Employment Information')
            ->assertSee('HR &amp; Contact Information', false)
            ->assertSee('Educational Qualifications')
            ->assertSee('flex: 0 0 100%', false)
            ->assertSee('max-width: none', false)
            ->assertSee('Payment Method')
            ->assertSee('Edit')
            ->assertSee('Back')
            ->assertDontSee('id="empTabs"', false)
            ->assertDontSee('id="tabSalary"', false)
            ->assertDontSee('id="tabLeave"', false)
            ->assertDontSee('id="tabDocs"', false)
            ->assertDontSee('id="tabPayment"', false);
    }
}
