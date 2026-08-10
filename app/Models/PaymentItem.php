<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentItem extends Model
{
    protected $fillable = [
        'payment_id',
        'fee_id',
        'amount',
        'scholarship_amount',
        'free_studentship_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scholarship_amount' => 'decimal:2',
        'free_studentship_amount' => 'decimal:2',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }
}
