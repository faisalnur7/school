<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\EducationalQualification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeFormDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_create_form_has_clear_sections_and_preserves_existing_fields(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        EducationalQualification::create([
            'name' => 'Bachelor of Education',
            'abbreviation' => 'B.Ed.',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('hr.employees.create'))
            ->assertOk()
            ->assertSee('employee-form-page', false)
            ->assertSee('Create a new staff profile', false)
            ->assertSee('employee-form-page__header', false)
            ->assertSee('employee-form-section__step', false)
            ->assertSee('.employee-form-page .select2-container--default .select2-selection--single', false)
            ->assertSee('.employee-form-page .select2-container--default.select2-sm .select2-selection--single', false)
            ->assertSee('.employee-qualification-row > .row', false)
            ->assertSee('background: #fbfcff', false)
            ->assertSee('rgba(99,102,241,.14)', false)
            ->assertSee('select2-dropdown', false)
            ->assertSee('Review all sections before saving.', false)
            ->assertSee('Personal Information')
            ->assertSee('Employment Information')
            ->assertSee('HR &amp; Contact Information', false)
            ->assertSee('employee-form-section[aria-labelledby="employee-hr-heading"]', false)
            ->assertSee('3 / 4', false)
            ->assertSee('Photo')
            ->assertSee('Payment Method')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="employee_type"', false)
            ->assertSee('name="designation_id"', false)
            ->assertSee('name="academic_session_ids[]"', false)
            ->assertSee('name="payment_method"', false)
            ->assertSee('Payment Method <span class="text-danger">*</span>', false)
            ->assertSee('name="password_confirmation"', false)
            ->assertSee('Select gender')
            ->assertSee('B.Ed. - Bachelor of Education')
            ->assertSee('name="qualifications[0][qualification_id]"', false)
            ->assertSee('name="qualifications[0][institution_name]"', false)
            ->assertSee("template.querySelectorAll('.select2-container').forEach(element => element.remove())", false)
            ->assertSee("document.createElement('input')", false)
            ->assertSee("employee-file-dropzone__filename", false)
            ->assertSee("preview.innerHTML = ''", false)
            ->assertSee('name="national_id"', false)
            ->assertSee('name="passport_number"', false)
            ->assertSee('name="emergency_contact_name"', false)
            ->assertSee('name="emergency_contact_phone"', false)
            ->assertSee('accept="image/jpeg,image/png,image/webp"', false)
            ->assertSee('Maximum 2 MB. JPEG, PNG, or WebP only.', false)
            ->assertSee('accept=".pdf,.jpg,.jpeg,.png,.webp"', false)
            ->assertSee('Maximum 2 MB. PDF, JPEG, PNG, or WebP only.', false)
            ->assertSee('employee-file-dropzone', false)
            ->assertSee('qualification-document-dropzone', false)
            ->assertSee('data-file-preview', false)
            ->assertSee('Remove selected file', false)
            ->assertSee('remove-qualification-row', false)
            ->assertSee('Swal.fire', false)
            ->assertSee('Save Employee')
            ->assertSee('Cancel');

        $this->assertStringNotContainsString('employee-payment-heading', $this->get(route('hr.employees.create'))->getContent());
    }

    public function test_date_of_birth_is_inside_personal_information_section(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $html = $this->actingAs($user)->get(route('hr.employees.create'))->getContent();

        $personalStart = strpos($html, 'id="employee-personal-heading"');
        $employmentStart = strpos($html, 'id="employee-employment-heading"');
        $dateOfBirth = strpos($html, 'id="employeeDob"');

        $this->assertNotFalse($personalStart);
        $this->assertNotFalse($employmentStart);
        $this->assertNotFalse($dateOfBirth);
        $this->assertGreaterThan($personalStart, $dateOfBirth);
        $this->assertLessThan($employmentStart, $dateOfBirth);
    }

    public function test_address_is_last_and_hr_notes_are_not_in_the_form(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);
        $html = $this->actingAs($user)->get(route('hr.employees.create'))->getContent();

        $this->assertStringNotContainsString('name="notes"', $html);
        $this->assertGreaterThan(strpos($html, 'name="emergency_contact_relation"'), strpos($html, 'name="address"'));
    }
}
