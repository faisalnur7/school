<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeIndexDesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_actions_use_consistent_compact_buttons(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($user)
            ->get(route('hr.employees.index'))
            ->assertOk()
            ->assertSee('employee-action-btn', false)
            ->assertSee('employee-action-view', false)
            ->assertSee('employee-action-edit', false)
            ->assertSee('employee-action-delete', false);
    }
}
