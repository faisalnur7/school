<?php
namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\DB;

class LeaveService
{
    public function getApprover(Employee $emp): ?Employee
    {
        $level = $emp->designation->hierarchy_level;
        $type  = $emp->employee_type;

        // Try exact level - 1
        $approver = Employee::active()
            ->whereHas('designation', fn($q) =>
                $q->where('employee_type', $type)->where('hierarchy_level', $level - 1)
            )
            ->where('id', '!=', $emp->id)
            ->first();

        if ($approver) return $approver;

        // Escalate to lowest hierarchy_level (highest authority) in same type
        return Employee::active()
            ->whereHas('designation', fn($q) =>
                $q->where('employee_type', $type)->orderBy('hierarchy_level')
            )
            ->where('id', '!=', $emp->id)
            ->first();
    }

    public function submit(array $data): LeaveRequest
    {
        $days    = LeaveRequest::calculateDays($data['date_from'], $data['date_to']);
        $balance = LeaveBalance::where('employee_id', $data['employee_id'])
            ->where('leave_type', $data['leave_type'])->first();

        if (!$balance || !$balance->canTake($days)) {
            throw new \Exception('Insufficient leave balance.');
        }

        return LeaveRequest::create(array_merge($data, ['total_days' => $days, 'status' => 'pending']));
    }

    public function approve(int $requestId, int $approverId): void
    {
        DB::transaction(function () use ($requestId, $approverId) {
            $request  = LeaveRequest::findOrFail($requestId);
            $approver = Employee::findOrFail($approverId);

            if ($request->employee_id === $approverId) {
                throw new \Exception('Cannot approve own leave.');
            }
            if ($approver->designation->hierarchy_level >= $request->employee->designation->hierarchy_level) {
                throw new \Exception('Insufficient authority to approve.');
            }

            $request->update(['status' => 'approved', 'approved_by' => $approverId, 'approved_at' => now()]);

            $balance = LeaveBalance::where('employee_id', $request->employee_id)
                ->where('leave_type', $request->leave_type)->first();
            if ($balance) {
                $balance->increment('used_leave', $request->total_days);
                $balance->decrement('remaining_leave', $request->total_days);
            }
        });
    }

    public function reject(int $requestId, int $approverId, string $reason): void
    {
        $request = LeaveRequest::findOrFail($requestId);
        if ($request->employee_id === $approverId) throw new \Exception('Cannot reject own leave.');
        $request->update(['status' => 'rejected', 'approved_by' => $approverId, 'rejection_reason' => $reason]);
    }
}
