<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HandCash extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'label', 'opening_amount', 'balance', 'opening_date',
        'is_active', 'notes', 'recorded_by',
    ];

    protected $casts = [
        'opening_date'   => 'date',
        'opening_amount' => 'decimal:2',
        'balance'        => 'decimal:2',
        'is_active'      => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $handCash) {
            $handCash->balance = (float) ($handCash->opening_amount ?? 0);
        });

        static::updating(function (self $handCash) {
            if (! $handCash->isDirty('opening_amount')) {
                return;
            }

            $originalOpening = (float) ($handCash->getOriginal('opening_amount') ?? 0);
            $newOpening = (float) ($handCash->opening_amount ?? 0);
            $handCash->balance = (float) ($handCash->balance ?? 0) + ($newOpening - $originalOpening);
        });
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function accountTransactions()
    {
        return $this->morphMany(AccountTransaction::class, 'account', 'account_type', 'account_id');
    }

    public function getCurrentBalanceAttribute()
    {
        return $this->accountTransactions()->latest('id')->value('balance_after')
            ?? (float) ($this->balance ?? $this->opening_amount ?? 0);
    }
}
