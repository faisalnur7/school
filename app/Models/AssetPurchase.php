<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTransactions;

class AssetPurchase extends Model
{
    use HasTransactions;

    protected $fillable = [
        'reference_no', 'purchase_date', 'total_amount',
        'payment_type', 'account_type', 'account_id',
        'expense_id', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount'  => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(AssetPurchaseItem::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function account()
    {
        return $this->morphTo('account', 'account_type', 'account_id');
    }
}
