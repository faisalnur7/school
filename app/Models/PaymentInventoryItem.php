<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInventoryItem extends Model
{
    protected $fillable = [
        'payment_id',
        'inventory_sale_item_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function inventorySaleItem(): BelongsTo
    {
        return $this->belongsTo(InventorySaleItem::class);
    }
}
