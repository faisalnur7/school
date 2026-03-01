<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{


    protected $fillable = [
        'reference_no',
        'type',
        'income_category_id',
        'expense_category_id',
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

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
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
            if ($txn->type === 'income') {
                $txn->expense_category_id = null;

                if (empty($txn->income_category_id)) {
                    throw new InvalidArgumentException('income_category_id is required for income transactions.');
                }
            }

            if ($txn->type === 'expense') {
                $txn->income_category_id = null;

                if (empty($txn->expense_category_id)) {
                    throw new InvalidArgumentException('expense_category_id is required for expense transactions.');
                }
            }
        });
    }
}