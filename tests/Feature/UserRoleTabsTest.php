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

    private function platformAdmin(): User
    {
        $role = Role::create(['name' => 'Platform Admin', 'system_key' => 'platform_admin', 'is_reserved' => true]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    private function superAdminRole(): Role
    {
        return Role::create(['name' => 'Super Admin', 'system_key' => 'super_admin', 'is_reserved' => true]);
    }

    public function test_platform_admin_can_withdraw_super_admin_flag_and_role(): void
    {
        $platform = $this->platformAdmin();
        $superRole = $this->superAdminRole();
        $user = User::factory()->create(['is_super_admin' => true, 'role_id' => $superRole->id]);

        $this->actingAs($platform)->get(route('users.index'))
            ->assertOk()
            ->assertSee('Withdraw Super Admin')
            ->assertSee(route('users.revoke-super-admin', $user->id));

        $this->actingAs(User::factory()->create(['is_super_admin' => true]))->get(route('users.index'))
            ->assertOk()
            ->assertDontSee('Withdraw Super Admin');

        $this->actingAs($platform)
            ->from(route('users.index'))
            ->post(route('users.revoke-super-admin', $user->id))
            ->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertFalse($user->is_super_admin);
        $this->assertNull($user->role_id);
    }

    public function test_super_admin_cannot_withdraw_super_admin(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $user = User::factory()->create(['is_super_admin' => true]);

        $this->actingAs($admin)
            ->post(route('users.revoke-super-admin', $user->id))
            ->assertForbidden();

        $this->assertTrue($user->fresh()->is_super_admin);
    }

    public function test_platform_admin_can_change_role_of_super_admin_role_user(): void
    {
        $platform = $this->platformAdmin();
        $superRole = $this->superAdminRole();
        $teacher = Role::create(['name' => 'Teacher']);
        $user = User::factory()->create(['role_id' => $superRole->id]);

        $this->actingAs($platform)->get(route('users.edit', $user->id))
            ->assertOk()
            ->assertSee('Super Admin');

        $this->actingAs($platform)->post(route('users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $teacher->id,
            'is_active' => 1,
        ])->assertRedirect(route('users.index'));

        $this->assertSame($teacher->id, $user->fresh()->role_id);

        $this->actingAs($platform)->post(route('users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $superRole->id,
            'is_active' => 1,
        ])->assertRedirect(route('users.index'));

        $this->assertSame($superRole->id, $user->fresh()->role_id);
    }

    public function test_non_platform_admin_cannot_manage_super_admin_role_user_or_assign_that_role(): void
    {
        $admin = User::factory()->create(['is_super_admin' => true]);
        $superRole = $this->superAdminRole();
        $target = User::factory()->create(['role_id' => $superRole->id]);
        $other = User::factory()->create();

        $this->actingAs($admin)->get(route('users.edit', $target->id))->assertNotFound();
        $this->actingAs($admin)->post(route('users.toggle-status', $target->id))->assertNotFound();

        $this->actingAs($admin)->post(route('users.update', $other->id), [
            'name' => $other->name,
            'email' => $other->email,
            'role_id' => $superRole->id,
        ])->assertSessionHasErrors('role_id');
    }

    public function test_platform_admin_accounts_remain_unmanageable_from_users_page(): void
    {
        $platform = $this->platformAdmin();
        $otherPlatform = User::factory()->create(['role_id' => $platform->role_id]);

        $this->actingAs($platform)->get(route('users.edit', $otherPlatform->id))->assertNotFound();
        $this->actingAs($platform)->post(route('users.revoke-super-admin', $otherPlatform->id))->assertNotFound();
    }
}
