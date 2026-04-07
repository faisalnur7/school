<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['asset_category_id', 'name', 'description', 'quantity', 'purchase_price', 'current_value', 'status'];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'current_value'  => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(AssetPurchaseItem::class);
    }

    public function issues()
    {
        return $this->hasMany(AssetIssue::class);
    }

    public function getIssuedQuantityAttribute(): int
    {
        return (int) $this->issues()->where('status', 'issued')->sum('quantity');
    }

    public function getReturnedQuantityAttribute(): int
    {
        return (int) $this->issues()->where('status', 'returned')->sum('quantity');
    }

    public function getAvailableStockAttribute(): int
    {
        return $this->quantity - $this->issued_quantity;
    }
}
