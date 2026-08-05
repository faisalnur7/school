<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $dashboardPermissions = [
        [
            'name' => 'view_card_dashboard',
            'display_name' => 'View Dashboard Card',
        ],
        [
            'name' => 'view_divwise_dashboard',
            'display_name' => 'View Division-wise Dashboard',
        ],
    ];

    private array $prunedPermissions = [
        [
            'name' => 'manage_fee_categories',
            'display_name' => 'Manage Fee Categories',
            'category' => 'Fee Collection',
        ],
        [
            'name' => 'manage_fee_sets',
            'display_name' => 'Manage Fee Sets',
            'category' => 'Fee Collection',
        ],
        [
            'name' => 'manage_student_fees',
            'display_name' => 'Manage Student Fees',
            'category' => 'Fee Collection',
        ],
        [
            'name' => 'collect_payments',
            'display_name' => 'Collect Payments',
            'category' => 'Fee Collection',
        ],
        [
            'name' => 'view_payment_report',
            'display_name' => 'View Payment Report',
            'category' => 'Fee Collection',
        ],
        [
            'name' => 'manage_payment_information',
            'display_name' => 'Manage Payment Information',
            'category' => 'HR & Payroll',
        ],
        [
            'name' => 'view_card_hr_dashboard',
            'display_name' => 'View HR Dashboard Card',
            'category' => 'HR & Payroll',
        ],
        [
            'name' => 'view_ledger',
            'display_name' => 'View Ledger',
            'category' => 'Accounts',
        ],
        [
            'name' => 'view_card_create_exam',
            'display_name' => 'View Create Exam Card',
            'category' => 'Result Management',
        ],
        [
            'name' => 'manage_website_content',
            'display_name' => 'Manage Website Content',
            'category' => 'Website Management',
        ],
        [
            'name' => 'manage_academic_calendar',
            'display_name' => 'Manage Academic Calendar',
            'category' => 'Website Management',
        ],
        [
            'name' => 'view_contact_messages',
            'display_name' => 'View Contact Messages',
            'category' => 'Website Management',
        ],
    ];

    public function up(): void
    {
        $this->ensureDashboardPermissions();
        $this->removePrunedPermissions();
        $this->syncRolePermissions();
    }

    public function down(): void
    {
        $this->restorePrunedPermissions();
        $this->removeDashboardPermissions();
        $this->syncRolePermissions();
    }

    private function ensureDashboardPermissions(): void
    {
        $categoryId = $this->getCategoryId('Dashboard');
        $now = now();

        foreach ($this->dashboardPermissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'category_id' => $categoryId,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function removeDashboardPermissions(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($this->dashboardPermissions, 'name'))
            ->pluck('id');

        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    private function removePrunedPermissions(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('name', array_column($this->prunedPermissions, 'name'))
            ->pluck('id');

        DB::table('role_permissions')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    private function restorePrunedPermissions(): void
    {
        $now = now();

        foreach ($this->prunedPermissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'display_name' => $permission['display_name'],
                    'category_id' => $this->getCategoryId($permission['category']),
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    private function syncRolePermissions(): void
    {
        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $roleIds = DB::table('roles')->pluck('id', 'name');

        if (isset($roleIds['Super Admin'])) {
            DB::table('role_permissions')->where('role_id', $roleIds['Super Admin'])->delete();

            $allPermissions = DB::table('permissions')->pluck('id')->all();
            $rows = array_map(
                fn ($permissionId) => ['role_id' => $roleIds['Super Admin'], 'permission_id' => $permissionId],
                $allPermissions
            );

            if (!empty($rows)) {
                DB::table('role_permissions')->insert($rows);
            }
        }

        foreach (['Admin', 'Teacher', 'Accountant', 'User'] as $roleName) {
            if (!isset($roleIds[$roleName])) {
                continue;
            }

            $permissionNames = ['view_card_dashboard'];

            if ($roleName === 'Admin') {
                $permissionNames[] = 'view_divwise_dashboard';
            }

            $rows = [];
            foreach ($permissionNames as $permissionName) {
                if (!isset($permissionIds[$permissionName])) {
                    continue;
                }

                $rows[] = [
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                ];
            }

            foreach ($rows as $row) {
                DB::table('role_permissions')->updateOrInsert($row, []);
            }
        }
    }

    private function getCategoryId(string $categoryName): int
    {
        $categoryId = DB::table('permission_categories')->where('name', $categoryName)->value('id');

        if ($categoryId) {
            return (int) $categoryId;
        }

        return (int) DB::table('permission_categories')->insertGetId([
            'name' => $categoryName,
            'sort_order' => 99,
        ]);
    }
};
