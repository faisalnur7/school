<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignationSalaryDefault extends Model
{
    protected $fillable = [
        'designation_id', 'basic_salary', 'house_rent', 'medical_allowance',
        'transport_allowance', 'special_allowance', 'bonus', 'other_deductions',
    ];

    public function designation() { return $this->belongsTo(Designation::class); }

    public function getGrossSalaryAttribute(): float
    {
        return (float)($this->basic_salary + $this->house_rent + $this->medical_allowance
            + $this->transport_allowance + $this->special_allowance + $this->bonus);
    }
}
