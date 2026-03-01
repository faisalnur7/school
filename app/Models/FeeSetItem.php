<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_set_id',
        'fee_category_id',
        'amount',
    ];

    // Relations
    public function feeSet()
    {
        return $this->belongsTo(FeeSet::class);
    }

    public function category()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }
}
