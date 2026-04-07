<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id', 'employee_id', 'name', 'employee_type', 'designation_id',
        'department_id', 'school_id', 'branch_id', 'reporting_manager_id',
        'dob', 'gender', 'phone', 'address', 'photo', 'joining_date', 'status',
    ];

    protected $casts = ['dob' => 'date', 'joining_date' => 'date'];

    public function scopeActive($q) { return $q->where('status', 'active'); }

    public function user()             { return $this->belongsTo(User::class); }
    public function designation()      { return $this->belongsTo(Designation::class); }
    public function department()       { return $this->belongsTo(Department::class); }
    public function manager()          { return $this->belongsTo(Employee::class, 'reporting_manager_id'); }
    public function subordinates()     { return $this->hasMany(Employee::class, 'reporting_manager_id'); }
    public function salaryStructure()  { return $this->hasOne(SalaryStructure::class)->latestOfMany('effective_from'); }
    public function salaryStructures() { return $this->hasMany(SalaryStructure::class); }
    public function payrolls()         { return $this->hasMany(HrPayroll::class); }
    public function leaveBalances()    { return $this->hasMany(LeaveBalance::class); }
    public function leaveRequests()    { return $this->hasMany(LeaveRequest::class); }
    public function paymentInformation(){ return $this->hasOne(PaymentInformation::class); }
    public function documents()        { return $this->hasMany(EmployeeDocument::class); }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo && file_exists(public_path($this->photo))
            ? asset($this->photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=2563eb&color=fff&size=80';
    }
}
