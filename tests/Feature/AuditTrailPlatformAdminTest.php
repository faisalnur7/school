<?php

namespace Tests\Feature;

use App\Models\AuditTrail;
use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailPlatformAdminTest extends TestCase
{
    use RefreshDatabase;

    private function auditPermission(): Permission
    {
        $category = PermissionCategory::firstOrCreate(['name' => 'Security'], ['sort_order' => 1]);

        return Permission::firstOrCreate(['name' => 'view_audit_trail'], [
            'display_name' => 'View Audit Trail',
            'category_id' => $category->id,
            'exposed_to_roles' => true,
        ]);
    }

    private function userWithRole(string $roleName, array $roleAttributes = [], array $permissionNames = []): User
    {
        $role = Role::create(['name' => $roleName] + $roleAttributes);

        if ($permissionNames !== []) {
            $permissions = collect($permissionNames)->map(function (string $name) {
                if ($name === 'view_audit_trail') {
                    return $this->auditPermission();
                }

                return Permission::create([
                    'name' => $name,
                    'display_name' => str($name)->headline(),
                    'category_id' => PermissionCategory::firstOrCreate(['name' => 'Test'], ['sort_order' => 2])->id,
                    'exposed_to_roles' => true,
                ]);
            });
            $role->permissions()->sync($permissions->pluck('id'));
        }

        return User::factory()->create([
            'name' => $roleName,
            'role_id' => $role->id,
        ]);
    }

    public function test_institute_roles_cannot_open_the_platform_admin_audit_log(): void
    {
        $instituteAdmin = $this->userWithRole('Institute Admin', [], ['view_audit_trail']);
        $instituteSuperAdmin = $this->userWithRole('Super Admin', [
            'system_key' => 'super_admin',
            'is_reserved' => true,
        ]);

        $this->actingAs($instituteAdmin)->get(route('audit-trails.index'))->assertForbidden();
        $this->actingAs($instituteSuperAdmin)->get(route('audit-trails.index'))->assertForbidden();
    }

    public function test_platform_admin_sees_all_audit_entries_including_their_own(): void
    {
        $platformAdmin = $this->userWithRole('Platform Admin', [
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $otherPlatformAdmin = User::factory()->create([
            'name' => 'Second Platform Admin',
            'role_id' => $platformAdmin->role_id,
        ]);
        $instituteAdmin = $this->userWithRole('Institute Admin');
        $instituteSuperAdmin = $this->userWithRole('Super Admin', [
            'system_key' => 'super_admin',
            'is_reserved' => true,
        ]);

        AuditTrail::record('Own platform action', 'Own platform admin action', $platformAdmin);
        AuditTrail::record('Platform action', 'Platform admin action', $otherPlatformAdmin);
        AuditTrail::record('Institute action', 'Institute admin action', $instituteAdmin);
        AuditTrail::record('Super admin action', 'Institute super admin action', $instituteSuperAdmin);

        $this->actingAs($platformAdmin)->get(route('audit-trails.index'))
            ->assertOk()
            ->assertSee('Platform Admin')
            ->assertSee('Second Platform Admin')
            ->assertSee('Institute Admin')
            ->assertSee('Super Admin');
    }

    public function test_audit_trail_navigation_is_only_rendered_for_platform_admins(): void
    {
        $platformAdmin = $this->userWithRole('Platform Admin', [
            'system_key' => 'platform_admin',
            'is_reserved' => true,
        ]);
        $instituteAdmin = $this->userWithRole('Institute Admin', [], ['view_users', 'view_audit_trail']);

        $this->actingAs($platformAdmin)->get(route('users.hub'))
            ->assertOk()
            ->assertSee('Audit Trail');

        $this->actingAs($instituteAdmin)->get(route('users.hub'))
            ->assertOk()
            ->assertDontSee('href="'.route('audit-trails.index').'"', false);
    }
}
