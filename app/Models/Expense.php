<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AccountTransaction;
use App\Services\PettyCashService;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'expense_category_id',
        'title',
        'amount',
        'expense_date',
        'payment_method',
        'reference_no',
        'description',
        'attachment',
        'approved_by',
        'recorded_by',
        'account_type',
        'account_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
        static::saved(function (Expense $expense) {
            $accountType = $expense->account_type;
            $accountId   = $expense->account_id;

            if (!$accountType || !$accountId) {
                $petty = PettyCashService::account();
                if ($petty) {
                    $accountType = HandCash::class;
                    $accountId   = $petty->id;
                }
            }

            if ($accountType && $accountId) {
                AccountTransaction::upsertForSource(
                    $accountType, $accountId, 'debit',
                    $expense->amount, 'expense',
                    $expense->reference_no, $expense->description,
                    $expense->expense_date, self::class, $expense->id, $expense->recorded_by
                );
            }
        });

        static::deleted(function (Expense $expense) {
            AccountTransaction::removeSource(self::class, $expense->id);
        });
    }

}