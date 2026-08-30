<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionApplicationReview extends Model
{
    protected $fillable = ['admission_application_id', 'decision', 'notes', 'reviewed_by', 'reviewed_at'];
    protected $casts = ['reviewed_at' => 'datetime'];
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
}
