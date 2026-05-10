<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSectionAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'class_id',
        'section_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'session_id' => 'integer',
        'class_id' => 'integer',
        'section_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
}
