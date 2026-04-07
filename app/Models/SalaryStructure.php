<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Model
{
    protected $fillable = [
        'employee_id', 'designation_id', 'basic_salary', 'house_rent',
        'medical_allowance', 'transport_allowance', 'special_allowance',
        'bonus', 'other_deductions', 'effective_from',
    ];
    protected $casts = ['effective_from' => 'date'];

    public function employee()    { return $this->belongsTo(Employee::class); }
    public function designation() { return $this->belongsTo(Designation::class); }

    public function getGrossSalaryAttribute(): float
    {
        return (float)($this->basic_salary + $this->house_rent + $this->medical_allowance
            + $this->transport_allowance + $this->special_allowance + $this->bonus);
    }

    public function getNetSalaryAttribute(): float
    {
        return max(0, $this->gross_salary - (float)$this->other_deductions);
    }
}
