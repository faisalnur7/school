<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequestItem extends \Illuminate\Database\Eloquent\Model
{
    protected $fillable = [
        'payment_request_id',
        'item_type',
        'fee_id',
        'inventory_item_id',
        'quantity',
        'requested_amount',
        'unit_price',
        'subtotal',
        'display_name',
        'display_reference',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'requested_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function paymentRequest(): BelongsTo
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(Fee::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
