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
        'average_cost',
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
        'average_cost' => 'decimal:2',
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

    public function stockValue(): float
    {
        $cost = (float) ($this->average_cost ?? $this->purchase_price ?? 0);

        return round(((int) $this->current_stock) * $cost, 2);
    }

    public function weightedAverageCostAfterInflow(int $quantityAdded, float $unitCost): float
    {
        $currentQuantity = max(0, (int) $this->current_stock);
        $currentAverage = max(0, (float) ($this->average_cost ?? 0));
        $newQuantity = $currentQuantity + max(0, $quantityAdded);

        if ($newQuantity <= 0) {
            return 0;
        }

        $newAverage = (($currentQuantity * $currentAverage) + ($quantityAdded * $unitCost)) / $newQuantity;

        return round($newAverage, 2);
    }
}
