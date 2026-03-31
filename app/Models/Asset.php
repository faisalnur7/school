<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['asset_category_id', 'name', 'description', 'quantity', 'status'];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(AssetPurchaseItem::class);
    }
}
