<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionPayment extends Model
{
    protected $fillable = ['admission_application_id', 'amount', 'gross_amount', 'discount_amount', 'total_amount', 'payment_method', 'payment_reference', 'status', 'paid_at', 'verified_by', 'verified_at', 'remarks'];
    protected $casts = ['amount' => 'decimal:2', 'gross_amount' => 'decimal:2', 'discount_amount' => 'decimal:2', 'total_amount' => 'decimal:2', 'paid_at' => 'datetime', 'verified_at' => 'datetime'];
    public function application() { return $this->belongsTo(AdmissionApplication::class, 'admission_application_id'); }
}
