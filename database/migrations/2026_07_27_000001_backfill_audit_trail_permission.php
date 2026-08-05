<?php

use App\Models\Permission;
use App\Models\PermissionCategory;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $categoryId = PermissionCategory::query()
            ->where('name', 'User & Roles')
            ->value('id');

        $permission = Permission::query()->firstOrCreate(
            ['name' => 'view_audit_trail'],
            [
                'display_name' => 'View Audit Trail',
                'category_id' => $categoryId,
            ]
        );

        Role::query()
            ->where('name', 'Admin')
            ->get()
            ->each(function (Role $role) use ($permission) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            });
    }

    public function down(): void
    {
        $permission = Permission::query()
            ->where('name', 'view_audit_trail')
            ->first();

        if (! $permission) {
            return;
        }

        Role::query()
            ->where('name', 'Admin')
            ->get()
            ->each(function (Role $role) use ($permission) {
                $role->permissions()->detach($permission->id);
            });

        $permission->delete();
    }
};
