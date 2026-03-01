<?php

namespace App\Traits;
use App\Models\Transaction;

trait HasTransactions
{
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function recordIncome(int $categoryId, array $data): Transaction
    {
        return $this->transactions()->create(array_merge([
            'reference_no'       => Transaction::generateReference(),
            'type'               => 'income',
            'income_category_id' => $categoryId,
            'transaction_date'   => now()->toDateString(),
            'recorded_by'        => auth()->id(),
        ], $data));
    }

    public function recordExpense(int $categoryId, array $data): Transaction
    {
        return $this->transactions()->create(array_merge([
            'reference_no'        => Transaction::generateReference(),
            'type'                => 'expense',
            'expense_category_id' => $categoryId,
            'transaction_date'    => now()->toDateString(),
            'recorded_by'         => auth()->id(),
        ], $data));
    }
}