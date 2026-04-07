<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_set_id',
        'scholarship_id',
        'amount',
        'scholarship_discount',
        'paid_amount',
        'due_date',
        'status',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'scholarship_discount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function feeSet()
    {
        return $this->belongsTo(FeeSet::class, 'fee_set_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function paymentItems()
    {
        return $this->hasMany(\App\Models\PaymentItem::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function getNetAmountAttribute()
    {
        return $this->amount - $this->scholarship_discount;
    }

    public function getDueAmountAttribute()
    {
        return $this->getNetAmountAttribute() - $this->paid_amount;
    }

    public function applyScholarship()
    {
        $scholarship = Scholarship::where('student_id', $this->student_id)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->first();

        if ($scholarship) {
            $discount = $scholarship->calculateDiscount($this->amount);
            $this->update([
                'scholarship_id' => $scholarship->id,
                'scholarship_discount' => $discount,
            ]);
            return true;
        }

        return false;
    }

}
