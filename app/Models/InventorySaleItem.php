<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySaleItem extends Model
{
    protected $fillable = [
        'inventory_sale_id',
        'inventory_item_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function inventorySale(): BelongsTo
    {
        return $this->belongsTo(InventorySale::class);
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }
}
