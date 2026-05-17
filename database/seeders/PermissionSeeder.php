<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionCategory;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['name' => 'Dashboard',           'sort' => 1,  'permissions' => [
                ['name' => 'view_dashboard',              'display_name' => 'View Dashboard'],
            ]],
            ['name' => 'Academics',           'sort' => 2,  'permissions' => [
                ['name' => 'view_academics',              'display_name' => 'View Academics'],
                ['name' => 'manage_classes',              'display_name' => 'Manage Classes'],
                ['name' => 'manage_sections',             'display_name' => 'Manage Sections'],
                ['name' => 'manage_groups',               'display_name' => 'Manage Groups'],
                ['name' => 'manage_sessions',             'display_name' => 'Manage Academic Sessions'],
                ['name' => 'manage_subjects',             'display_name' => 'Manage Subjects'],
                ['name' => 'manage_classrooms',           'display_name' => 'Manage Classrooms'],
                ['name' => 'manage_routines',             'display_name' => 'Manage Class Routines'],
            ]],
            ['name' => 'Attendance',          'sort' => 3,  'permissions' => [
                ['name' => 'view_attendance',             'display_name' => 'View Attendance'],
                ['name' => 'manage_attendance',           'display_name' => 'Manage Attendance'],
            ]],
            ['name' => 'Students',            'sort' => 4,  'permissions' => [
                ['name' => 'view_students',               'display_name' => 'View Students'],
                ['name' => 'create_students',             'display_name' => 'Create Students'],
                ['name' => 'edit_students',               'display_name' => 'Edit Students'],
                ['name' => 'delete_students',             'display_name' => 'Delete Students'],
                ['name' => 'manage_teacher_section_assignments', 'display_name' => 'Manage Teacher Section Assignments'],
            ]],
            ['name' => 'Fee Collection',      'sort' => 5,  'permissions' => [
                ['name' => 'view_fees',                   'display_name' => 'View Fee Collection'],
                ['name' => 'manage_fee_categories',       'display_name' => 'Manage Fee Categories'],
                ['name' => 'manage_fee_sets',             'display_name' => 'Manage Fee Sets'],
                ['name' => 'manage_scholarships',         'display_name' => 'Manage Scholarships'],
                ['name' => 'manage_free_studentships',    'display_name' => 'Manage Free Studentships'],
                ['name' => 'manage_transports',           'display_name' => 'Manage Transports'],
                ['name' => 'manage_student_fees',         'display_name' => 'Manage Student Fees'],
                ['name' => 'collect_payments',            'display_name' => 'Collect Payments'],
                ['name' => 'view_payment_report',         'display_name' => 'View Payment Report'],
            ]],
            ['name' => 'Financials',          'sort' => 6,  'permissions' => [
                ['name' => 'view_financials',             'display_name' => 'View Financials'],
                ['name' => 'manage_incomes',              'display_name' => 'Manage Incomes'],
                ['name' => 'manage_expenses',             'display_name' => 'Manage Expenses'],
                ['name' => 'manage_transactions',         'display_name' => 'Manage Transactions'],
                ['name' => 'manage_income_categories',    'display_name' => 'Manage Income Categories'],
                ['name' => 'manage_expense_categories',   'display_name' => 'Manage Expense Categories'],
                ['name' => 'manage_shareholder_transactions', 'display_name' => 'Manage Shareholder Transactions'],
            ]],
            ['name' => 'Result Management',   'sort' => 7,  'permissions' => [
                ['name' => 'view_results',                'display_name' => 'View Result Management'],
                ['name' => 'manage_exams',                'display_name' => 'Manage Exams'],
                ['name' => 'manage_student_subjects',     'display_name' => 'Manage Student Subjects'],
            ]],
            ['name' => 'Shareholders',        'sort' => 8,  'permissions' => [
                ['name' => 'view_shareholders',           'display_name' => 'View Shareholders'],
                ['name' => 'manage_shareholders',         'display_name' => 'Manage Shareholders'],
            ]],
            ['name' => 'HR & Payroll',        'sort' => 9,  'permissions' => [
                ['name' => 'view_hr',                     'display_name' => 'View HR & Payroll'],
                ['name' => 'manage_employees',            'display_name' => 'Manage Employees'],
                ['name' => 'manage_designations',         'display_name' => 'Manage Designations'],
                ['name' => 'manage_departments',          'display_name' => 'Manage Departments'],
                ['name' => 'manage_salary_structures',    'display_name' => 'Manage Salary Structures'],
                ['name' => 'manage_payroll',              'display_name' => 'Manage Payroll'],
                ['name' => 'manage_leave_requests',       'display_name' => 'Manage Leave Requests'],
                ['name' => 'manage_payment_information',  'display_name' => 'Manage Payment Information'],
            ]],
            ['name' => 'Accounts',            'sort' => 10, 'permissions' => [
                ['name' => 'view_accounts',               'display_name' => 'View Accounts'],
                ['name' => 'manage_account_groups',       'display_name' => 'Manage Account Groups'],
                ['name' => 'manage_accounts_list',        'display_name' => 'Manage Accounts List'],
                ['name' => 'view_ledger',                 'display_name' => 'View Ledger'],
                ['name' => 'manage_accounting_periods',   'display_name' => 'Manage Accounting Periods'],
                ['name' => 'manage_journal_entries',      'display_name' => 'Manage Journal Entries'],
                ['name' => 'view_reports',                'display_name' => 'View Reports'],
                ['name' => 'manage_bank_accounts',        'display_name' => 'Manage Bank Accounts'],
                ['name' => 'manage_mobile_banking_accounts', 'display_name' => 'Manage Mobile Banking Accounts'],
                ['name' => 'manage_hand_cash',            'display_name' => 'Manage Hand Cash'],
            ]],
            ['name' => 'Assets',              'sort' => 11, 'permissions' => [
                ['name' => 'view_assets',                 'display_name' => 'View Assets'],
                ['name' => 'manage_assets',               'display_name' => 'Manage Assets'],
                ['name' => 'manage_asset_categories',     'display_name' => 'Manage Asset Categories'],
                ['name' => 'manage_asset_purchases',      'display_name' => 'Manage Asset Purchases'],
                ['name' => 'manage_asset_issues',         'display_name' => 'Manage Asset Issues'],
            ]],
            ['name' => 'Inventory',           'sort' => 12, 'permissions' => [
                ['name' => 'view_inventory',              'display_name' => 'View Inventory'],
                ['name' => 'manage_inventory_categories', 'display_name' => 'Manage Inventory Categories'],
                ['name' => 'manage_inventory_products',   'display_name' => 'Manage Inventory Products'],
                ['name' => 'manage_inventory_suppliers',  'display_name' => 'Manage Inventory Suppliers'],
                ['name' => 'manage_inventory_purchases',  'display_name' => 'Manage Inventory Purchases'],
                ['name' => 'view_inventory_reports',      'display_name' => 'View Inventory Reports'],
            ]],
            ['name' => 'Budget Control',      'sort' => 13, 'permissions' => [
                ['name' => 'view_budget',                 'display_name' => 'View Budget Control'],
                ['name' => 'manage_budget_allocations',   'display_name' => 'Manage Budget Allocations'],
            ]],
            ['name' => 'Institute Settings',  'sort' => 14, 'permissions' => [
                ['name' => 'view_institute_settings',     'display_name' => 'View Institute Settings'],
                ['name' => 'manage_school_settings',      'display_name' => 'Manage School Settings'],
                ['name' => 'manage_id_card_templates',    'display_name' => 'Manage ID Card Templates'],
                ['name' => 'manage_buildings',            'display_name' => 'Manage Buildings'],
            ]],
            ['name' => 'User & Roles',        'sort' => 15, 'permissions' => [
                ['name' => 'view_users',                  'display_name' => 'View Users'],
                ['name' => 'create_users',                'display_name' => 'Create Users'],
                ['name' => 'edit_users',                  'display_name' => 'Edit Users'],
                ['name' => 'delete_users',                'display_name' => 'Delete Users'],
                ['name' => 'view_roles',                  'display_name' => 'View Roles'],
                ['name' => 'create_roles',                'display_name' => 'Create Roles'],
                ['name' => 'edit_roles',                  'display_name' => 'Edit Roles'],
                ['name' => 'delete_roles',                'display_name' => 'Delete Roles'],
                ['name' => 'view_permissions',            'display_name' => 'View Permissions'],
                ['name' => 'edit_permissions',            'display_name' => 'Edit Permissions'],
            ]],
            ['name' => 'Reports',             'sort' => 16, 'permissions' => [
                ['name' => 'view_reports_hub',            'display_name' => 'View Reports Hub'],
                ['name' => 'view_student_payment_report', 'display_name' => 'View Student Payment Report'],
            ]],
            ['name' => 'Location Settings',   'sort' => 17, 'permissions' => [
                ['name' => 'view_location_settings',      'display_name' => 'View Location Settings'],
                ['name' => 'manage_divisions',            'display_name' => 'Manage Divisions'],
                ['name' => 'manage_districts',            'display_name' => 'Manage Districts'],
                ['name' => 'manage_police_stations',      'display_name' => 'Manage Police Stations'],
                ['name' => 'manage_post_offices',         'display_name' => 'Manage Post Offices'],
            ]],
        ];

        foreach ($definitions as $group) {
            $categoryId = PermissionCategory::firstOrCreate(
                ['name' => $group['name']],
                ['sort_order' => $group['sort']]
            )->id;

            foreach ($group['permissions'] as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm['name']],
                    ['display_name' => $perm['display_name'], 'category_id' => $categoryId]
                );
            }
        }
    }
}
