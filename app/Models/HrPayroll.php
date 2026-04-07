<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPayroll extends Model
{
    protected $table    = 'hr_payrolls';
    protected $fillable = [
        'employee_id', 'payroll_month', 'payroll_year', 'gross_salary',
        'other_deductions', 'net_salary', 'payment_method', 'status',
        'is_locked', 'processed_at',
    ];
    protected $casts = ['is_locked' => 'boolean', 'processed_at' => 'datetime'];

    public function scopeForMonth($q, int $m, int $y) { return $q->where('payroll_month', $m)->where('payroll_year', $y); }
    public function scopeLocked($q)  { return $q->where('is_locked', true); }
    public function scopePending($q) { return $q->where('status', 'pending'); }

    public function employee()    { return $this->belongsTo(Employee::class); }
    public function transaction() { return $this->hasOne(HrTransaction::class, 'payroll_id'); }

    public function isLocked(): bool { return (bool)$this->is_locked; }
}
