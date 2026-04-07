<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrTransaction extends Model
{
    protected $table    = 'hr_transactions';
    protected $fillable = ['employee_id', 'payroll_id', 'amount', 'payment_method', 'account_head', 'transaction_date'];
    protected $casts    = ['transaction_date' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function payroll()  { return $this->belongsTo(HrPayroll::class, 'payroll_id'); }
}
