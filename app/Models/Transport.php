<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transport extends Model
{
    protected $fillable = [
        'student_id',
        'student_academic_information_id',
        'academic_session_id',
        'fee_category_id',
        'amount',
        'status',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

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
}
