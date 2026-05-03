<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmitCard extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'seat_plan_id',
        'roll_number',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function seatPlan(): BelongsTo
    {
        return $this->belongsTo(SeatPlan::class, 'seat_plan_id');
    }
}
