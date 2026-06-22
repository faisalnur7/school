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
                    'view_dashboard',
                    'view_academics', 'manage_classes', 'manage_sections', 'manage_groups',
                        'manage_sessions', 'manage_subjects', 'manage_classrooms', 'manage_routines',
                    'view_attendance', 'manage_attendance',
                    'view_students', 'create_students', 'edit_students', 'delete_students',
                        'manage_teacher_section_assignments',
                    'view_fees', 'manage_fee_categories', 'manage_fee_sets', 'manage_scholarships',
                        'manage_free_studentships', 'manage_transports', 'manage_student_fees',
                        'collect_payments', 'view_payment_report',
                    'view_financials', 'manage_incomes', 'manage_expenses', 'manage_transactions',
                        'manage_income_categories', 'manage_expense_categories',
                    'view_results', 'manage_exams', 'manage_student_subjects',
                    'view_card_terminal_report', 'view_card_tutorial_exam_report',
                    'view_shareholders', 'manage_shareholders',
                    'view_hr', 'manage_employees', 'manage_designations', 'manage_departments',
                        'manage_salary_structures', 'manage_payroll', 'manage_leave_requests',
                        'manage_payment_information',
                    'view_accounts', 'manage_account_groups', 'manage_accounts_list', 'view_ledger',
                        'manage_accounting_periods', 'manage_journal_entries', 'view_reports',
                        'manage_bank_accounts', 'manage_mobile_banking_accounts', 'manage_hand_cash',
                    'view_assets', 'manage_assets', 'manage_asset_categories',
                        'manage_asset_purchases', 'manage_asset_issues',
                    'view_inventory', 'manage_inventory_categories', 'manage_inventory_products',
                        'manage_inventory_suppliers', 'manage_inventory_purchases', 'view_inventory_reports',
                    'view_budget', 'manage_budget_allocations',
                    'view_institute_settings', 'manage_school_settings',
                        'manage_id_card_templates', 'manage_buildings',
                    'view_users', 'create_users', 'edit_users', 'delete_users',
                    'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
                    'view_permissions', 'edit_permissions',
                    'view_reports_hub', 'view_student_payment_report',
                    'view_location_settings', 'manage_divisions', 'manage_districts',
                        'manage_police_stations', 'manage_post_offices',
                ],
            ],
            [
                'name' => 'Teacher',
                'description' => 'Teacher access',
                'permissions' => [
                    'view_dashboard',
                    'view_academics',
                    'view_attendance', 'manage_attendance',
                    'view_students',
                    'view_results', 'manage_exams',
                    'view_card_terminal_report', 'view_card_tutorial_exam_report',
                ],
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial access',
                'permissions' => [
                    'view_dashboard',
                    'view_fees', 'manage_student_fees', 'collect_payments', 'view_payment_report',
                    'view_financials', 'manage_incomes', 'manage_expenses', 'manage_transactions',
                    'view_accounts', 'view_ledger', 'view_reports',
                    'manage_bank_accounts', 'manage_mobile_banking_accounts', 'manage_hand_cash',
                    'view_reports_hub', 'view_student_payment_report',
                ],
            ],
            [
                'name' => 'User',
                'description' => 'Basic user access',
                'permissions' => [
                    'view_dashboard',
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
