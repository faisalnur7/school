<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'reference_no',
        'type',
        'income_category_id',
        'expense_category_id',
        'shareholder_id',
        'amount',
        'description',
        'transaction_date',
        'payment_method',
        'reference_note',
        'transactionable_type',
        'transactionable_id',
        'recorded_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function shareholder()
    {
        return $this->belongsTo(Shareholder::class);
    }

    // ── Accessors ──────────────────────────────────────────────

    /**
     * Returns whichever category is set regardless of type.
     */
    public function getCategoryAttribute(): IncomeCategory|ExpenseCategory|null
    {
        return $this->incomeCategory ?? $this->expenseCategory;
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? '—';
    }

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === 'income';
    }

    public function getIsCapitalAttribute(): bool
    {
        return $this->type === 'capital';
    }

    public function getIsWithdrawalAttribute(): bool
    {
        return $this->type === 'withdrawal';
    }

    /**
     * Resolve a display name for the cash/bank account linked via the transactionable.
     * Falls back to the payment_method string when no account is stored.
     */
    public function getAccountDisplayNameAttribute(): string
    {
        $source = $this->transactionable;

        $accountType = $source?->account_type ?? null;
        $accountId   = $source?->account_id   ?? null;

        if ($accountType && $accountId) {
            $account = $accountType::find($accountId);

            if ($account) {
                return match ($accountType) {
                    \App\Models\BankAccount::class          => $account->bank_name . ' — ' . $account->account_number,
                    \App\Models\MobileBankingAccount::class => $account->provider  . ' — ' . $account->account_number,
                    \App\Models\HandCash::class             => $account->label,
                    default                                 => $this->payment_method ?? '—',
                };
            }
        }

        return $this->payment_method ?? '—';
    }

    public function getDebitAccountNameAttribute(): string
    {
        return match ($this->type) {
            'income'     => $this->account_display_name,
            'expense'    => $this->expenseCategory?->name ?? '—',
            'capital'    => $this->account_display_name,
            'withdrawal' => 'Drawings — ' . ($this->shareholder?->name ?? '—'),
            default      => '—',
        };
    }

    public function getCreditAccountNameAttribute(): string
    {
        return match ($this->type) {
            'income'     => $this->incomeCategory?->name ?? '—',
            'expense'    => $this->account_display_name,
            'capital'    => 'Capital — ' . ($this->shareholder?->name ?? '—'),
            'withdrawal' => $this->account_display_name,
            default      => '—',
        };
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeCapital($query)
    {
        return $query->where('type', 'capital');
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeForShareholder($query, int $shareholderId)
    {
        return $query->where('shareholder_id', $shareholderId);
    }

    public function scopeForPeriod($query, $from, $to)
    {
        return $query->whereBetween('transaction_date', [$from, $to]);
    }

    // ── Helpers ────────────────────────────────────────────────

    public static function generateReference(): string
    {
        $date     = now()->format('Ymd');
        $sequence = str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        return "TXN-{$date}-{$sequence}";
    }

    /**
     * Ensure only the correct category FK is set based on type.
     */
    protected static function booted(): void
    {
        static::saving(function (Transaction $txn) {
            match ($txn->type) {
                'income' => (function () use ($txn) {
                    $txn->expense_category_id = null;
                    $txn->shareholder_id      = null;
                    if (empty($txn->income_category_id)) {
                        throw new \InvalidArgumentException('income_category_id is required for income transactions.');
                    }
                })(),

                'expense' => (function () use ($txn) {
                    $txn->income_category_id = null;
                    $txn->shareholder_id     = null;
                    if (empty($txn->expense_category_id)) {
                        throw new \InvalidArgumentException('expense_category_id is required for expense transactions.');
                    }
                })(),

                'capital', 'withdrawal' => (function () use ($txn) {
                    $txn->income_category_id  = null;
                    $txn->expense_category_id = null;
                    if (empty($txn->shareholder_id)) {
                        throw new \InvalidArgumentException('shareholder_id is required for capital/withdrawal transactions.');
                    }
                })(),

                default => throw new \InvalidArgumentException("Invalid transaction type: {$txn->type}"),
            };
        });
    }
}
