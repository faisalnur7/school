<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access',
                'permissions' => [], // All permissions
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access',
                'permissions' => [
                    'view_users', 'create_users', 'edit_users',
                    'view_roles', 'create_roles', 'edit_roles',
                    'view_permissions', 'create_permissions', 'edit_permissions',
                    'view_settings', 'edit_settings',
                    'manage_classes', 'manage_students', 'manage_attendance',
                    'manage_fees', 'view_reports', 'manage_accounts',
                    'view_inventory',
                    'manage_inventory_categories',
                    'manage_inventory_products',
                    'manage_inventory_suppliers',
                    'manage_inventory_purchases',
                    'view_inventory_reports',
                ],
            ],
            [
                'name' => 'Teacher',
                'description' => 'Teacher access',
                'permissions' => [
                    'view_users',
                    'manage_classes', 'manage_students', 'manage_attendance',
                ],
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial access',
                'permissions' => [
                    'view_users',
                    'manage_fees', 'view_reports', 'manage_accounts',
                ],
            ],
            [
                'name' => 'User',
                'description' => 'Basic user access',
                'permissions' => [
                    'view_users',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            if ($roleData['name'] === 'Super Admin') {
                // Super Admin gets all permissions
                $allPermissions = Permission::pluck('id')->toArray();
                $role->permissions()->sync($allPermissions);
            } else {
                // Other roles get specific permissions
                $permissions = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->permissions()->sync($permissions);
            }
        }
    }
}
