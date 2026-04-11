<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    protected $fillable = [
        'account_id', 'expense_category_id',
        'amount', 'period', 'fiscal_year', 'fiscal_month',
        'notes', 'recorded_by',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Actual expenses for this allocation's linked expense category in the fiscal period.
     * If no expense_category_id, falls back to the category linked to the account via reference.
     */
    public function getActualSpentAttribute(): float
    {
        $categoryId = $this->expense_category_id;

        // If no direct category, try resolving from the account's reference
        if (!$categoryId && $this->account?->reference_type === ExpenseCategory::class) {
            $categoryId = $this->account->reference_id;
        }

        $query = Expense::query();

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $query->whereYear('expense_date', $this->fiscal_year);

        if ($this->period === 'monthly' && $this->fiscal_month) {
            $query->whereMonth('expense_date', $this->fiscal_month);
        }

        return (float) $query->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->actual_spent;
    }

    public function getUtilizationAttribute(): float
    {
        return $this->amount > 0
            ? round(($this->actual_spent / $this->amount) * 100, 1)
            : 0;
    }
}
