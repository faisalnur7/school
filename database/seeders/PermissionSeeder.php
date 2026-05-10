<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionCategory;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'User Management', 'description' => 'Manage system users', 'sort_order' => 1],
            ['name' => 'Role Management', 'description' => 'Manage user roles', 'sort_order' => 2],
            ['name' => 'Permission Management', 'description' => 'Manage permissions', 'sort_order' => 3],
            ['name' => 'System Settings', 'description' => 'System configuration', 'sort_order' => 4],
            ['name' => 'Academic Management', 'description' => 'Academic operations', 'sort_order' => 5],
            ['name' => 'Financial Management', 'description' => 'Financial operations', 'sort_order' => 6],
            ['name' => 'Inventory Management', 'description' => 'Inventory operations', 'sort_order' => 7],
        ];

        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[$cat['name']] = PermissionCategory::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description'], 'sort_order' => $cat['sort_order']]
            )->id;
        }

        $permissions = [
            'User Management' => [
                ['name' => 'view_users', 'display_name' => 'View Users'],
                ['name' => 'create_users', 'display_name' => 'Create Users'],
                ['name' => 'edit_users', 'display_name' => 'Edit Users'],
                ['name' => 'delete_users', 'display_name' => 'Delete Users'],
            ],
            'Role Management' => [
                ['name' => 'view_roles', 'display_name' => 'View Roles'],
                ['name' => 'create_roles', 'display_name' => 'Create Roles'],
                ['name' => 'edit_roles', 'display_name' => 'Edit Roles'],
                ['name' => 'delete_roles', 'display_name' => 'Delete Roles'],
            ],
            'Permission Management' => [
                ['name' => 'view_permissions', 'display_name' => 'View Permissions'],
                ['name' => 'create_permissions', 'display_name' => 'Create Permissions'],
                ['name' => 'edit_permissions', 'display_name' => 'Edit Permissions'],
                ['name' => 'delete_permissions', 'display_name' => 'Delete Permissions'],
            ],
            'System Settings' => [
                ['name' => 'view_settings', 'display_name' => 'View Settings'],
                ['name' => 'edit_settings', 'display_name' => 'Edit Settings'],
            ],
            'Academic Management' => [
                ['name' => 'manage_classes', 'display_name' => 'Manage Classes'],
                ['name' => 'manage_students', 'display_name' => 'Manage Students'],
                ['name' => 'manage_attendance', 'display_name' => 'Manage Attendance'],
            ],
            'Financial Management' => [
                ['name' => 'manage_fees', 'display_name' => 'Manage Fees'],
                ['name' => 'view_reports', 'display_name' => 'View Reports'],
                ['name' => 'manage_accounts', 'display_name' => 'Manage Accounts'],
            ],
            'Inventory Management' => [
                ['name' => 'view_inventory', 'display_name' => 'View Inventory Hub'],
                ['name' => 'manage_inventory_categories', 'display_name' => 'Manage Inventory Categories'],
                ['name' => 'manage_inventory_products', 'display_name' => 'Manage Inventory Products'],
                ['name' => 'manage_inventory_suppliers', 'display_name' => 'Manage Inventory Suppliers'],
                ['name' => 'manage_inventory_purchases', 'display_name' => 'Manage Inventory Purchases'],
                ['name' => 'view_inventory_reports', 'display_name' => 'View Inventory Reports'],
            ],
        ];

        foreach ($permissions as $category => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm['name']],
                    [
                        'display_name' => $perm['display_name'],
                        'category_id' => $categoryMap[$category],
                    ]
                );
            }
        }
    }
}
