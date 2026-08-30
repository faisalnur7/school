<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionExamClassSetting extends Model
{
    protected $fillable = ['admission_exam_id', 'school_class_id', 'total_mark', 'pass_mark'];
    protected $casts = ['total_mark' => 'decimal:2', 'pass_mark' => 'decimal:2'];
    public function exam() { return $this->belongsTo(AdmissionExam::class, 'admission_exam_id'); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class); }
}
