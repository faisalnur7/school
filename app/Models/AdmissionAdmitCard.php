<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionAdmitCard extends Model
{
    protected $fillable = ['admission_application_id', 'admit_card_number', 'roll_number', 'candidate_id', 'generated_at', 'generated_by', 'printed_at', 'attendance_status'];
    protected $casts = ['generated_at' => 'datetime', 'printed_at' => 'datetime'];
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
}
