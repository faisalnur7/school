<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetAllocation extends Model
{
    protected $fillable = [
        'budget_head_id', 'expense_category_id',
        'amount', 'period', 'fiscal_year', 'fiscal_month',
        'notes', 'recorded_by',
    ];

    public function budgetHead()
    {
        return $this->belongsTo(BudgetHead::class);
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /** Actual expenses for this allocation's category in the fiscal period */
    public function getActualSpentAttribute(): float
    {
        $query = Expense::query();

        if ($this->expense_category_id) {
            $query->where('expense_category_id', $this->expense_category_id);
        }

        $query->whereYear('expense_date', $this->fiscal_year);

        if ($this->period === 'monthly' && $this->fiscal_month) {
            $query->whereMonth('expense_date', $this->fiscal_month);
        }

        return (float) $query->sum('amount');
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->amount - $this->actual_spent);
    }

    public function getUtilizationAttribute(): float
    {
        return $this->amount > 0
            ? round(($this->actual_spent / $this->amount) * 100, 1)
            : 0;
    }
}
