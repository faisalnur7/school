<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAcademicInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'school_class_id',
        'section_id',
        'group_id',
        'roll',
    ];

    protected $casts = [
        'student_id'          => 'integer',
        'academic_session_id' => 'integer',
        'school_class_id'     => 'integer',
        'section_id'          => 'integer',
        'group_id'            => 'integer',
    ];

    /* =========================
     | Relationships
     ========================= */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
