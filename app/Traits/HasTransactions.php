<?php

namespace App\Traits;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Income;
use App\Models\Expense;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Services\JournalService;

trait HasTransactions
{
    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function recordIncome(int $categoryId, string $title, array $data): Transaction
    {
        $reference = Transaction::generateReference();
        $incomeDate = $data['income_date'] ?? now()->toDateString();
        $transactionDate = $data['transaction_date'] ?? $incomeDate;

        $income = Income::create(array_merge([
            'income_category_id' => $categoryId,
            'title'              => $title,
            'reference_no'       => $reference,
            'income_date'        => $incomeDate,
            'recorded_by'        => auth()->id(),
        ], $data));

        $txn = $this->transactions()->create(array_merge([
            'reference_no'       => $reference,
            'type'               => 'income',
            'income_category_id' => $categoryId,
            'title'              => $title,
            'transaction_date'   => $transactionDate,
            'recorded_by'        => auth()->id(),
        ], $data));

        JournalService::postSafe(
            $transactionDate,
            $title,
            [
                ['account_id' => Account::resolveForSource($data['account_type'] ?? '', (int) ($data['account_id'] ?? 0)), 'debit' => (float) $data['amount'], 'credit' => 0],
                ['account_id' => Account::resolveForSource(IncomeCategory::class, $categoryId), 'debit' => 0, 'credit' => (float) $data['amount']],
            ],
            get_class($this),
            $this->id,
            auth()->id()
        );

        return $txn;
    }

    public function recordExpense(int $categoryId, string $title, array $data): Transaction
    {
        $expenseDate = $data['expense_date'] ?? now()->toDateString();
        $expenseReference = Expense::generateReference($expenseDate);
        $reference = Transaction::generateReference();
        $transactionDate = $data['transaction_date'] ?? $expenseDate;

        $expense = Expense::create(array_merge([
            'expense_category_id' => $categoryId,
            'title'               => $title,
            'reference_no'        => $expenseReference,
            'expense_date'        => $expenseDate,
            'recorded_by'         => auth()->id(),
        ], $data));

        $txn = $this->transactions()->create(array_merge([
            'reference_no'        => $reference,
            'type'                => 'expense',
            'expense_category_id' => $categoryId,
            'title'               => $title,
            'transaction_date'    => $transactionDate,
            'recorded_by'         => auth()->id(),
        ], $data));

        JournalService::postSafe(
            $transactionDate,
            $title,
            [
                ['account_id' => Account::resolveForSource(ExpenseCategory::class, $categoryId), 'debit' => (float) $data['amount'], 'credit' => 0],
                ['account_id' => Account::resolveForSource($data['account_type'] ?? '', (int) ($data['account_id'] ?? 0)), 'debit' => 0, 'credit' => (float) $data['amount']],
            ],
            get_class($this),
            $this->id,
            auth()->id()
        );

        return $txn;
    }
}
