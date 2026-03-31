<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AccountTransaction;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'income_category_id',
        'title',
        'amount',
        'income_date',
        'payment_method',
        'reference_no',
        'description',
        'attachment',
        'recorded_by',
        'account_type',
        'account_id', 
    ];

    protected $casts = [
        'income_date' => 'date',
        'amount'      => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function account()
    {
        return $this->morphTo('account', 'account_type', 'account_id');
    }

    public function getAccountModelAttribute()
    {
        return match($this->account_type) {
            'App\Models\BankAccount'          => \App\Models\BankAccount::find($this->account_id),
            'App\Models\MobileBankingAccount' => \App\Models\MobileBankingAccount::find($this->account_id),
            'App\Models\HandCash'             => \App\Models\HandCash::find($this->account_id),
            default                           => null,
        };
    }

    protected static function booted(): void
    {
        static::saved(function (Income $income) {
            if ($income->account_type && $income->account_id) {
                AccountTransaction::upsertForSource(
                    $income->account_type,
                    $income->account_id,
                    'credit',
                    $income->amount,
                    'income',
                    $income->reference_no,
                    $income->description,
                    $income->income_date,
                    self::class,
                    $income->id,
                    $income->recorded_by
                );
            } else {
                AccountTransaction::removeSource(self::class, $income->id);
            }
        });

        static::deleted(function (Income $income) {
            AccountTransaction::removeSource(self::class, $income->id);
        });
    }
}
