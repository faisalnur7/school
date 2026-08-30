<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionExam extends Model
{
    protected $fillable = ['name', 'academic_session_id', 'exam_date', 'form_fee', 'venue', 'reporting_time', 'instructions', 'status', 'created_by', 'application_sequence'];
    protected $casts = ['exam_date' => 'date', 'form_fee' => 'decimal:2', 'status' => 'boolean', 'application_sequence' => 'integer'];

    public function academicSession() { return $this->belongsTo(AcademicSession::class); }
    public function classSettings() { return $this->hasMany(AdmissionExamClassSetting::class); }
    public function applications() { return $this->hasMany(AdmissionApplication::class); }
}
