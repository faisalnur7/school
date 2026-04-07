<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id', 'leave_type', 'date_from', 'date_to', 'total_days',
        'reason', 'status', 'approved_by', 'approved_at', 'rejection_reason',
    ];
    protected $casts = ['date_from' => 'date', 'date_to' => 'date', 'approved_at' => 'datetime'];

    public function employee()  { return $this->belongsTo(Employee::class); }
    public function approver()  { return $this->belongsTo(Employee::class, 'approved_by'); }

    public static function calculateDays(string $from, string $to): int
    {
        return (int) Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1;
    }
}
