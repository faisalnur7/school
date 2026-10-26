<?php

namespace Tests\Feature;

use App\Models\Designation;
use App\Models\EducationalQualification;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeQualificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_create_persists_catalog_qualification_and_cleans_irrelevant_payment_values(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $designation = Designation::create([
            'name' => 'Office Assistant',
            'employee_type' => 'staff',
            'hierarchy_level' => 1,
            'status' => 'active',
        ]);
        $qualification = EducationalQualification::create([
            'name' => 'Bachelor of Education',
            'abbreviation' => 'B.Ed.',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->post(route('hr.employees.store'), [
                'name' => 'Ayesha Rahman',
                'email' => 'ayesha@example.test',
                'password' => 'secret123',
                'password_confirmation' => 'secret123',
                'employee_type' => 'staff',
                'designation_id' => $designation->id,
                'gender' => 'female',
                'joining_date' => '2026-10-22',
                'national_id' => '1987654321',
                'passport_number' => 'P1234567',
                'emergency_contact_name' => 'Rahman Family',
                'emergency_contact_phone' => '01800000000',
                'emergency_contact_relation' => 'Brother',
                'notes' => 'Part-time evening staff member.',
                'payment_method' => 'cash',
                'bank_name' => 'Should be cleared',
                'account_number' => '12345',
                'mobile_wallet_provider' => 'Should be cleared',
                'mobile_wallet_number' => '01700000000',
                'qualifications' => [[
                    'qualification_id' => $qualification->id,
                    'institution_name' => 'National University',
                    'passing_year' => 2020,
                    'result' => 'First Class',
                ]],
            ])
            ->assertRedirect(route('hr.employees.index'));

        $employee = Employee::with(['qualifications', 'paymentInformation'])->firstOrFail();

        $this->assertSame($qualification->id, $employee->qualifications->first()->qualification_id);
        $this->assertSame('National University', $employee->qualifications->first()->institution_name);
        $this->assertSame('1987654321', $employee->national_id);
        $this->assertSame('P1234567', $employee->passport_number);
        $this->assertSame('01800000000', $employee->emergency_contact_phone);
        $this->assertSame('Part-time evening staff member.', $employee->notes);
        $this->assertSame('cash', $employee->paymentInformation->payment_method);
        $this->assertNull($employee->paymentInformation->bank_name);
        $this->assertNull($employee->paymentInformation->mobile_wallet_provider);
    }

    public function test_joining_date_and_password_confirmation_are_required(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $designation = Designation::create([
            'name' => 'Office Assistant',
            'employee_type' => 'staff',
            'hierarchy_level' => 1,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->from(route('hr.employees.create'))
            ->post(route('hr.employees.store'), [
                'name' => 'Ayesha Rahman',
                'email' => 'ayesha@example.test',
                'password' => 'secret123',
                'employee_type' => 'staff',
                'designation_id' => $designation->id,
                'gender' => 'female',
            ])
            ->assertSessionHasErrors(['joining_date', 'password_confirmation']);
    }
}
