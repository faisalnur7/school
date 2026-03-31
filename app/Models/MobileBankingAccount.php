<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MobileBankingAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'provider', 'account_name', 'account_number', 'account_type',
        'opening_balance', 'balance', 'opening_date', 'is_active', 'notes',
    ];

    protected $casts = [
        'opening_date'    => 'date',
        'opening_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function accountTransactions()
    {
        return $this->morphMany(AccountTransaction::class, 'account', 'account_type', 'account_id');
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->accountTransactions()->latest('id')->value('balance_after') ?? 0;
    }
}