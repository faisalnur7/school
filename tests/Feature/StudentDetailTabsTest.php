<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use App\Models\Fee;
use App\Models\FeeSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDetailTabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_detail_renders_all_profile_sections_as_tabs(): void
    {
        $this->actingAs(User::factory()->create(['is_super_admin' => true]));
        $student = Student::create(['full_name_en' => 'Tabbed Student']);
        $feeSet = FeeSet::create(['name' => 'Tuition Fee']);
        Fee::create([
            'student_id' => $student->id,
            'fee_set_id' => $feeSet->id,
            'amount' => 2080,
            'due_date' => '2026-01-31',
            'status' => 'pending',
        ]);

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('role="tablist"', false)
            ->assertSee('data-student-tab="basic"', false)
            ->assertSee('data-student-tab="parents"', false)
            ->assertSee('data-student-tab="contact"', false)
            ->assertSee('data-student-tab="academic"', false)
            ->assertSee('data-student-tab="fees"', false)
            ->assertSee('data-student-tab="transport"', false)
            ->assertSee('data-student-tab="inventory"', false)
            ->assertSee('student-profile-tab-content', false)
            ->assertSee('student-profile-fee-tabs', false)
            ->assertSee('grid-template-columns: repeat(5, minmax(0, 1fr));', false)
            ->assertSee("html[data-theme='dark'] .student-profile-panel", false)
            ->assertSee("html.dark .student-profile-panel", false);
    }
}
