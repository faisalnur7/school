<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeStudentship extends Model
{
    protected $fillable = [
        'student_id',
        'student_academic_information_id',
        'name',
        'type',
        'amount',
        'percentage',
        'academic_session_id',
        'fee_category_id',
        'permitted_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentAcademicInformation()
    {
        return $this->belongsTo(StudentAcademicInformation::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function calculateDiscount($feeAmount)
    {
        if ($this->type === self::TYPE_FIXED) {
            return min($this->amount, $feeAmount);
        }
        
        return ($feeAmount * $this->percentage) / 100;
    }
}
