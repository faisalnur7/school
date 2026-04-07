<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Services\LeaveService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function __construct(private LeaveService $service) {}

    public function index(Request $request)
    {
        $requests = LeaveRequest::with(['employee.designation', 'approver'])
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();

        $employees = Employee::active()->orderBy('name')->get();
        return view('hr.leave.index', compact('requests', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->with('leaveBalances')->orderBy('name')->get();
        return view('hr.leave.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:casual,sick,annual,maternity,other',
            'date_from'   => 'required|date',
            'date_to'     => 'required|date|after_or_equal:date_from',
            'reason'      => 'required|string',
        ]);

        try {
            $this->service->submit($data);
            return redirect()->route('hr.leave.index')->with('success', 'Leave request submitted.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function approve(int $id, Request $request)
    {
        $approverId = $request->validate(['approver_id' => 'required|exists:employees,id'])['approver_id'];
        try {
            $this->service->approve($id, $approverId);
            return back()->with('success', 'Leave approved.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(int $id, Request $request)
    {
        $data = $request->validate([
            'approver_id'      => 'required|exists:employees,id',
            'rejection_reason' => 'required|string',
        ]);
        try {
            $this->service->reject($id, $data['approver_id'], $data['rejection_reason']);
            return back()->with('success', 'Leave rejected.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function balances()
    {
        $employees = Employee::active()->with('leaveBalances')->orderBy('name')->get();
        return view('hr.leave.balances', compact('employees'));
    }

    public function setBalance(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:casual,sick,annual,maternity,other',
            'total_leave' => 'required|integer|min:0',
        ]);

        $balance = LeaveBalance::updateOrCreate(
            ['employee_id' => $data['employee_id'], 'leave_type' => $data['leave_type']],
            ['total_leave' => $data['total_leave'], 'remaining_leave' => $data['total_leave']]
        );

        return back()->with('success', 'Leave balance updated.');
    }
}
