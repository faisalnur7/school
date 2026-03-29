<?php

namespace App\Traits;
use App\Models\Transaction;
use App\Models\Income;
use App\Models\Expense;

trait HasTransactions
{
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function recordIncome(int $categoryId, string $title, array $data): Transaction
    {
        $reference = Transaction::generateReference();

        Income::create(array_merge([
            'income_category_id' => $categoryId,
            'title'              => $title,
            'reference_no'       => $reference,
            'income_date'        => now()->toDateString(),
            'recorded_by'        => auth()->id(),
        ], $data));

        return $this->transactions()->create(array_merge([
            'reference_no'       => $reference,
            'type'               => 'income',
            'income_category_id' => $categoryId,
            'title'              => $title,
            'transaction_date'   => now()->toDateString(),
            'recorded_by'        => auth()->id(),
        ], $data));
    }

    public function recordExpense(int $categoryId, string $title, array $data): Transaction
    {
        $reference = Transaction::generateReference();

        Expense::create(array_merge([
            'expense_category_id' => $categoryId,
            'title'               => $title,
            'reference_no'        => $reference,
            'expense_date'        => now()->toDateString(),
            'recorded_by'         => auth()->id(),
        ], $data));

        return $this->transactions()->create(array_merge([
            'reference_no'        => $reference,
            'type'                => 'expense',
            'expense_category_id' => $categoryId,
            'title'               => $title,
            'transaction_date'    => now()->toDateString(),
            'recorded_by'         => auth()->id(),
        ], $data));
    }
}