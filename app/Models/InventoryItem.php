<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    protected $fillable = [
        'category_id',
        'item_type',
        'name',
        'description',
        'purchase_price',
        'selling_price',
        'is_flexible_price',
        'stock_type',
        'current_stock',
        'minimum_stock_alert',
        'unit',
        'is_active',
        'school_class_id',
        'group_id',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_flexible_price' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'inventory_item_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'inventory_item_id');
    }

    public function isMadeToOrder(): bool
    {
        return ($this->stock_type ?? 'stocked') === 'made_to_order';
    }
}
