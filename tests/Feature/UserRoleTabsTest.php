<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTabsTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_page_filters_users_by_role_and_includes_make_super_admin_tab(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $teacher = Role::create(['name' => 'Teacher']);
        $student = Role::create(['name' => 'Student']);
        $teacherUser = User::factory()->create(['role_id' => $teacher->id]);
        User::factory()->create(['role_id' => $student->id]);

        $response = $this->actingAs($admin)->get(route('users.index', ['role' => $teacher->id]));

        $response->assertOk()
            ->assertSee('Teacher')
            ->assertSee($teacherUser->name)
            ->assertSee('Make Super Admin');

        $response = $this->actingAs($admin)->get(route('users.index', ['role' => 'make-super-admin']));

        $response->assertOk()
            ->assertSee('Make Super Admin')
            ->assertSee($teacherUser->name);
    }

    public function test_super_admin_can_promote_a_user_from_the_last_tab(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create(['is_super_admin' => false]);

        $response = $this->actingAs($admin)->post(route('users.make-super-admin', $user->id));

        $response->assertRedirect(route('users.index', ['role' => 'make-super-admin']));
        $this->assertTrue($user->fresh()->is_super_admin);
    }

    public function test_non_super_admin_cannot_promote_a_user(): void
    {
        $admin = User::factory()->create(['is_super_admin' => false]);
        $user = User::factory()->create(['is_super_admin' => false]);

        $this->actingAs($admin)
            ->post(route('users.make-super-admin', $user->id))
            ->assertForbidden();

        $this->assertFalse($user->fresh()->is_super_admin);
    }
}
