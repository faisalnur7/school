<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bank_name', 'account_name', 'account_number',
        'branch_name', 'routing_number',
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
        static::creating(function (self $bankAccount) {
            $bankAccount->balance = (float) ($bankAccount->opening_balance ?? 0);
        });

        static::updating(function (self $bankAccount) {
            if (! $bankAccount->isDirty('opening_balance')) {
                return;
            }

            $originalOpening = (float) ($bankAccount->getOriginal('opening_balance') ?? 0);
            $newOpening = (float) ($bankAccount->opening_balance ?? 0);
            $bankAccount->balance = (float) ($bankAccount->balance ?? 0) + ($newOpening - $originalOpening);
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
