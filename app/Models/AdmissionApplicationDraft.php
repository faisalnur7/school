<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionApplicationDraft extends Model
{
    protected $fillable = ['token_hash', 'admission_exam_id', 'academic_session_id', 'school_class_id', 'applicant_data', 'image_path', 'expires_at', 'confirmed_at'];
    protected $casts = ['applicant_data' => 'array', 'expires_at' => 'datetime', 'confirmed_at' => 'datetime'];

    public function exam() { return $this->belongsTo(AdmissionExam::class, 'admission_exam_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
}
