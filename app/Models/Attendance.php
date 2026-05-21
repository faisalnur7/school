<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'session_id',
        'class_id',
        'section_id',
        'date',
        'taken_by',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'class_id' => 'integer',
        'section_id' => 'integer',
        'taken_by' => 'integer',
        'date' => 'date:Y-m-d',
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function takenBy()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    public function items()
    {
        return $this->hasMany(AttendanceItem::class);
    }
}
