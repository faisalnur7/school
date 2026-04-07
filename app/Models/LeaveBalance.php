<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = ['employee_id', 'leave_type', 'total_leave', 'used_leave', 'remaining_leave'];

    public function employee() { return $this->belongsTo(Employee::class); }

    public function canTake(int $days): bool { return $this->remaining_leave >= $days; }
}
