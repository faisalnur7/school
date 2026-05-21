<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceItem extends Model
{
    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'is_absent_email_sent',
        'note',
    ];

    protected $casts = [
        'attendance_id' => 'integer',
        'student_id' => 'integer',
        'is_absent_email_sent' => 'boolean',
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
