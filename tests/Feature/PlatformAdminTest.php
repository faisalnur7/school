<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    private function permission(string $name, bool $exposed = true): Permission
    {
        $category = PermissionCategory::firstOrCreate(['name' => 'Test'], ['sort_order' => 1]);

        return Permission::firstOrCreate(['name' => $name], [
            'name' => $name,
            'display_name' => str($name)->headline(),
            'category_id' => $category->id,
            'exposed_to_roles' => $exposed,
        ]);
    }

    private function roleUser(string $roleName, array $permissionNames = [], array $roleAttributes = []): User
    {
        $role = Role::create(['name' => $roleName] + $roleAttributes);
        $permissions = collect($permissionNames)->map(fn ($name) => $this->permission($name));
        $role->permissions()->sync($permissions->pluck('id'));

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_platform_admin_has_full_application_access_without_assigned_permissions(): void
    {
        $platformRole = Role::create([
            'name' => 'Platform Admin',
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $admin = User::factory()->create(['role_id' => $platformRole->id]);

        $this->actingAs($admin)->get(route('roles.index'))->assertOk();
        $this->assertTrue($admin->fresh()->hasPermission('permission_that_does_not_exist'));
    }

    public function test_non_platform_admin_cannot_access_platform_admin_area(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('platform-admins.index'))->assertForbidden();
    }

    public function test_reserved_roles_are_hidden_from_normal_roles_and_user_role_selectors(): void
    {
        $this->permission('view_roles');
        $admin = $this->roleUser('Institute User', ['view_roles', 'view_users', 'create_users']);
        Role::create(['name' => 'Platform Admin', 'system_key' => 'platform_admin', 'is_reserved' => true]);
        Role::create(['name' => 'Super Admin', 'system_key' => 'super_admin', 'is_reserved' => true]);

        $this->actingAs($admin)->get(route('roles.index'))
            ->assertOk()
            ->assertDontSee('Platform Admin')
            ->assertDontSee('Super Admin');

        $this->actingAs($admin)->get(route('users.create'))
            ->assertOk()
            ->assertDontSee('Platform Admin')
            ->assertDontSee('Super Admin');
    }

    public function test_hidden_permissions_are_not_shown_or_assignable_to_normal_roles(): void
    {
        $this->permission('view_roles');
        $hidden = $this->permission('manage_everything', false);
        $admin = $this->roleUser('Institute User', ['view_roles', 'create_roles']);

        $this->actingAs($admin)->get(route('roles.create'))
            ->assertOk()
            ->assertDontSee('Manage Everything');

        $this->actingAs($admin)->post(route('roles.store'), [
            'name' => 'Restricted Role',
            'permissions' => [$hidden->id],
        ])->assertSessionHasErrors('permissions.0');
    }

    public function test_platform_admin_users_are_excluded_from_normal_user_counts(): void
    {
        $this->permission('view_users');
        $admin = $this->roleUser('Institute User', ['view_users']);
        $platformRole = Role::create([
            'name' => 'Platform Admin',
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $platformAdmin = User::factory()->create(['role_id' => $platformRole->id]);

        $this->actingAs($admin)->get(route('users.index'))
            ->assertOk()
            ->assertSee($admin->name)
            ->assertDontSee($platformAdmin->name)
            ->assertDontSee('Platform Admin');
    }

    public function test_platform_admin_can_create_and_delete_platform_admin_users(): void
    {
        $platformRole = Role::create([
            'name' => 'Platform Admin',
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $admin = User::factory()->create(['role_id' => $platformRole->id]);

        $response = $this->actingAs($admin)->post(route('platform-admins.store'), [
            'name' => 'Second Platform Admin',
            'email' => 'second-platform@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('platform-admins.index'));
        $created = User::where('email', 'second-platform@example.com')->firstOrFail();
        $this->assertSame($platformRole->id, $created->role_id);
        $this->assertTrue(Hash::check('password', $created->password));

        $this->actingAs($admin)->delete(route('platform-admins.destroy', $created))
            ->assertRedirect(route('platform-admins.index'));
        $this->assertDatabaseMissing('users', ['id' => $created->id]);
    }

    public function test_platform_admin_permission_visibility_uses_the_role_permission_card_layout(): void
    {
        $platformRole = Role::create([
            'name' => 'Platform Admin',
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $admin = User::factory()->create(['role_id' => $platformRole->id]);
        $this->permission('view_dashboard');

        $this->actingAs($admin)->get(route('platform-admins.index'))
            ->assertOk()
            ->assertSee('permission-card-grid', false)
            ->assertSee('permission-card-tile', false)
            ->assertSee('permissionModal_test', false)
            ->assertSee('Permissions visible in institute Roles CRUD');
    }
}
