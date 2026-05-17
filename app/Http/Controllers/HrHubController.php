<?php

namespace App\Http\Controllers;

class HrHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-tachometer-alt',  'title' => 'HR Dashboard',         'subtitle' => 'HR overview & stats',          'route' => 'hr.dashboard', 'permission' => 'view_card_hr_dashboard', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-users',           'title' => 'All Employees',        'subtitle' => 'View all employees',           'route' => 'hr.employees.index', 'permission' => 'view_card_all_employees', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-user-plus',       'title' => 'Add Employee',         'subtitle' => 'Enrol a new employee',         'route' => 'hr.employees.create', 'permission' => 'view_card_add_employee', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-sitemap',         'title' => 'Departments',          'subtitle' => 'Manage departments',           'route' => 'hr.departments.index', 'permission' => 'view_card_departments', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-briefcase',       'title' => 'Designations',         'subtitle' => 'Manage designations',          'route' => 'hr.designations.index', 'permission' => 'view_card_designations', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-money-check-alt', 'title' => 'Salary Structures',    'subtitle' => 'Manage salary structures',     'route' => 'hr.salary-structures.index', 'permission' => 'view_card_salary_structures', 'from' => '#7c3aed', 'to' => '#6d28d9'],
            ['icon' => 'fa-sliders-h',       'title' => 'Salary Defaults',      'subtitle' => 'Designation salary defaults',  'route' => 'hr.salary.defaults.index', 'permission' => 'view_card_salary_defaults', 'from' => '#0f766e', 'to' => '#0d9488'],
            ['icon' => 'fa-file-invoice-dollar', 'title' => 'Payroll',          'subtitle' => 'Generate & manage payroll',    'route' => 'hr.payroll.index', 'permission' => 'view_card_payroll', 'from' => '#b45309', 'to' => '#92400e'],
            ['icon' => 'fa-calendar-times',  'title' => 'Leave Requests',       'subtitle' => 'Manage leave requests',        'route' => 'hr.leave.index', 'permission' => 'view_card_leave_requests', 'from' => '#be185d', 'to' => '#9d174d'],
            ['icon' => 'fa-balance-scale',   'title' => 'Leave Balances',       'subtitle' => 'View leave balances',          'route' => 'hr.leave.balances', 'permission' => 'view_card_leave_balances', 'from' => '#1d4ed8', 'to' => '#1e40af'],
            ['icon' => 'fa-chart-bar',       'title' => 'Salary Sheet',         'subtitle' => 'View salary sheet report',     'route' => 'hr.reports.salary-sheet', 'permission' => 'view_card_salary_sheet', 'from' => '#0369a1', 'to' => '#075985'],
            ['icon' => 'fa-chart-pie',       'title' => 'Payroll Summary',      'subtitle' => 'View payroll summary',         'route' => 'hr.reports.payroll-summary', 'permission' => 'view_card_payroll_summary', 'from' => '#15803d', 'to' => '#166534'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.hr.hub', compact('cards'));
    }
}
