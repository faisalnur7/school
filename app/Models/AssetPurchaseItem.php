<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPurchaseItem extends Model
{
    protected $fillable = ['asset_purchase_id', 'asset_id', 'quantity', 'unit_price', 'total_price'];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(AssetPurchase::class, 'asset_purchase_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
