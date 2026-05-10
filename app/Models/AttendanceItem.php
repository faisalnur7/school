<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceItem extends Model
{
    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'note',
    ];

    protected $casts = [
        'attendance_id' => 'integer',
        'student_id' => 'integer',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

