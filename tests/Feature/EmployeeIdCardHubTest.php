<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIdCardHubTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_hub_replaces_add_employee_with_employee_id_cards(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($admin)
            ->get(route('hr.hub'))
            ->assertOk()
            ->assertSee('Employee ID Cards')
            ->assertDontSee('Enrol a new employee');
    }
}
