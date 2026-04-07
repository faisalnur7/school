<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrPayroll;
use App\Models\LeaveRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees  = Employee::active()->count();
        $totalTeachers   = Employee::active()->where('employee_type', 'teacher')->count();
        $totalStaff      = Employee::active()->where('employee_type', 'staff')->count();
        $pendingLeaves   = LeaveRequest::where('status', 'pending')->count();
        $missingSalary   = Employee::active()->doesntHave('salaryStructure')->count();

        $currentMonth    = now()->month;
        $currentYear     = now()->year;
        $monthPayrolls   = HrPayroll::forMonth($currentMonth, $currentYear)->get();
        $currentPayroll  = [
            'count'      => $monthPayrolls->count(),
            'total_net'  => $monthPayrolls->sum('net_salary'),
            'total_gross'=> $monthPayrolls->sum('gross_salary'),
            'paid'       => $monthPayrolls->where('status', 'paid')->count(),
        ];

        $recentEmployees = Employee::active()->with('designation')
            ->latest()->take(5)->get();

        $payrollHistory  = HrPayroll::selectRaw('payroll_month, payroll_year, SUM(net_salary) as total_net, SUM(gross_salary) as total_gross, COUNT(*) as count')
            ->groupBy('payroll_year', 'payroll_month')
            ->orderByDesc('payroll_year')->orderByDesc('payroll_month')
            ->take(3)->get();

        $leaveToday = LeaveRequest::whereDate('created_at', today())->count();

        return view('hr.dashboard.index', compact(
            'totalEmployees', 'totalTeachers', 'totalStaff',
            'pendingLeaves', 'missingSalary', 'currentPayroll',
            'recentEmployees', 'payrollHistory', 'leaveToday'
        ));
    }
}
