<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionConversion extends Model
{
    protected $fillable = ['admission_application_id', 'student_id', 'academic_session_id', 'school_class_id', 'roll', 'converted_by', 'converted_at'];
    protected $casts = ['converted_at' => 'datetime'];
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
    public function student() { return $this->belongsTo(Student::class); }
}
