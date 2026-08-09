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
        'balance'         => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $account) {
            $account->balance = (float) ($account->opening_balance ?? 0);
        });

        static::updating(function (self $account) {
            if (! $account->isDirty('opening_balance')) {
                return;
            }

            $originalOpening = (float) ($account->getOriginal('opening_balance') ?? 0);
            $newOpening = (float) ($account->opening_balance ?? 0);
            $account->balance = (float) ($account->balance ?? 0) + ($newOpening - $originalOpening);
        });
    }

    public function accountTransactions()
    {
        return $this->morphMany(AccountTransaction::class, 'account', 'account_type', 'account_id');
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->accountTransactions()->latest('id')->value('balance_after')
            ?? (float) ($this->balance ?? $this->opening_balance ?? 0);
    }
}
