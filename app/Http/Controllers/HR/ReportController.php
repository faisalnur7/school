<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\HrPayroll;
use App\Models\LeaveRequest;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salarySheet(Request $request)
    {
        $query = Employee::active()->with(['designation', 'salaryStructure'])
            ->when($request->department,      fn($q) => $q->where('department', $request->department))
            ->when($request->designation_id,  fn($q) => $q->where('designation_id', $request->designation_id))
            ->when($request->employee_type,   fn($q) => $q->where('employee_type', $request->employee_type))
            ->orderBy('employee_type')->orderBy('name');

        $employees    = $query->get();
        $designations = Designation::active()->orderBy('name')->get();
        $departments  = Employee::distinct()->pluck('department')->filter()->sort()->values();

        return view('hr.reports.salary-sheet', compact('employees', 'designations', 'departments'));
    }

    public function payrollSummary(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $payrolls = HrPayroll::forMonth($month, $year)
            ->with('employee.designation')->get();

        $summary = [
            'count'       => $payrolls->count(),
            'total_gross' => $payrolls->sum('gross_salary'),
            'total_net'   => $payrolls->sum('net_salary'),
            'paid_count'  => $payrolls->where('status', 'paid')->count(),
            'pending'     => $payrolls->where('status', 'pending')->count(),
        ];

        return view('hr.reports.payroll-summary', compact('payrolls', 'summary', 'month', 'year'));
    }

    public function leaveReport(Request $request)
    {
        $requests = LeaveRequest::with(['employee.designation', 'approver'])
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->leave_type,  fn($q) => $q->where('leave_type', $request->leave_type))
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->date_from,   fn($q) => $q->whereDate('date_from', '>=', $request->date_from))
            ->when($request->date_to,     fn($q) => $q->whereDate('date_to', '<=', $request->date_to))
            ->latest()->get();

        $employees = Employee::active()->orderBy('name')->get();
        return view('hr.reports.leave-report', compact('requests', 'employees'));
    }

    public function hierarchyReport()
    {
        $designations = Designation::active()
            ->withCount(['employees' => fn($q) => $q->where('status', 'active')])
            ->with(['employees' => fn($q) => $q->active()->select('id', 'name', 'designation_id')])
            ->orderBy('employee_type')->orderBy('hierarchy_level')
            ->get();

        return view('hr.reports.hierarchy', compact('designations'));
    }
}
