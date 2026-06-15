<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventorySale extends Model
{
    protected $fillable = [
        'payment_id',
        'student_id',
        'total_amount',
        'created_by',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventorySaleItem::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getPaidAmountAttribute(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return (float) $items->sum(fn ($item) => (float) ($item->paid_amount ?? $item->subtotal));
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->paid_amount);
    }
}
