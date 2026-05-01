<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\HandCash;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\JournalEntry;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Database\Seeder;

class DemoAccountingSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'test@example.com')->value('id');

        $handCash = HandCash::firstOrCreate(
            ['label' => 'Main Cash'],
            [
                'opening_amount' => 50000,
                'opening_date'   => now()->toDateString(),
                'is_active'      => true,
                'balance'        => 50000,
                'recorded_by'    => $userId,
            ]
        );

        $bank = BankAccount::firstOrCreate(
            ['account_number' => '0123456789'],
            [
                'bank_name'       => 'Example Bank',
                'account_name'    => 'School Main Account',
                'opening_balance' => 250000,
                'balance'         => 250000,
                'opening_date'    => now()->toDateString(),
                'is_active'       => true,
            ]
        );

        $incomeCategory = IncomeCategory::firstOrCreate(
            ['slug' => 'student-payment'],
            ['name' => 'Student Payment', 'is_active' => true]
        );

        $expenseCategory = ExpenseCategory::firstOrCreate(
            ['slug' => 'office-expense'],
            ['name' => 'Office Expense', 'is_active' => true]
        );

        $shareholder = Shareholder::firstOrCreate(['name' => 'Mohammed Abdul Alam']);

        $groupAssets = AccountGroup::firstOrCreate(['name' => 'Assets', 'parent_id' => null]);
        $groupIncome = AccountGroup::firstOrCreate(['name' => 'Income', 'parent_id' => null]);
        $groupExpense = AccountGroup::firstOrCreate(['name' => 'Expenses', 'parent_id' => null]);
        $groupEquity = AccountGroup::firstOrCreate(['name' => 'Equity', 'parent_id' => null]);

        $cashAccount = Account::firstOrCreate(
            ['reference_type' => HandCash::class, 'reference_id' => $handCash->id],
            [
                'name'             => 'Cash - Main Cash',
                'account_group_id' => $groupAssets->id,
                'type'             => 'asset',
                'opening_balance'  => 0,
                'notes'            => 'Demo mapped cash account',
            ]
        );

        $bankAccount = Account::firstOrCreate(
            ['reference_type' => BankAccount::class, 'reference_id' => $bank->id],
            [
                'name'             => 'Bank - School Main Account',
                'account_group_id' => $groupAssets->id,
                'type'             => 'asset',
                'opening_balance'  => 0,
                'notes'            => 'Demo mapped bank account',
            ]
        );

        $incomeHeadAccount = Account::firstOrCreate(
            ['reference_type' => IncomeCategory::class, 'reference_id' => $incomeCategory->id],
            [
                'name'             => 'Income - Student Payment',
                'account_group_id' => $groupIncome->id,
                'type'             => 'income',
                'opening_balance'  => 0,
            ]
        );

        $expenseHeadAccount = Account::firstOrCreate(
            ['reference_type' => ExpenseCategory::class, 'reference_id' => $expenseCategory->id],
            [
                'name'             => 'Expense - Office Expense',
                'account_group_id' => $groupExpense->id,
                'type'             => 'expense',
                'opening_balance'  => 0,
            ]
        );

        $equityAccount = Account::firstOrCreate(
            ['reference_type' => Shareholder::class, 'reference_id' => $shareholder->id],
            [
                'name'             => 'Capital - ' . $shareholder->name,
                'account_group_id' => $groupEquity->id,
                'type'             => 'equity',
                'opening_balance'  => 0,
            ]
        );

        $income = Income::updateOrCreate(
            ['reference_no' => 'DEMO-INC-001'],
            [
                'income_category_id' => $incomeCategory->id,
                'title'              => 'April Tuition Collection',
                'amount'             => 120000,
                'income_date'        => now()->subDays(8)->toDateString(),
                'payment_method'     => 'Cash',
                'account_type'       => HandCash::class,
                'account_id'         => $handCash->id,
                'description'        => 'Demo tuition collection for report preview',
                'recorded_by'        => $userId,
            ]
        );

        Transaction::updateOrCreate(
            ['transactionable_type' => Income::class, 'transactionable_id' => $income->id],
            [
                'reference_no'         => 'DEMO-TXN-INC-001',
                'type'                 => 'income',
                'income_category_id'   => $incomeCategory->id,
                'expense_category_id'  => null,
                'shareholder_id'       => null,
                'amount'               => $income->amount,
                'description'          => $income->description,
                'transaction_date'     => $income->income_date,
                'payment_method'       => $income->payment_method,
                'recorded_by'          => $userId,
            ]
        );

        $this->postJournalIfMissing(
            Income::class,
            $income->id,
            $income->income_date->toDateString(),
            'Demo Income Entry',
            [
                ['account_id' => $cashAccount->id, 'debit' => (float) $income->amount, 'credit' => 0],
                ['account_id' => $incomeHeadAccount->id, 'debit' => 0, 'credit' => (float) $income->amount],
            ],
            $userId
        );

        $expense = Expense::updateOrCreate(
            ['reference_no' => 'DEMO-EXP-001'],
            [
                'expense_category_id' => $expenseCategory->id,
                'title'               => 'Office Utility & Supplies',
                'amount'              => 35000,
                'expense_date'        => now()->subDays(5)->toDateString(),
                'payment_method'      => 'Bank Transfer',
                'account_type'        => BankAccount::class,
                'account_id'          => $bank->id,
                'description'         => 'Demo office expense for reporting preview',
                'recorded_by'         => $userId,
            ]
        );

        Transaction::updateOrCreate(
            ['transactionable_type' => Expense::class, 'transactionable_id' => $expense->id],
            [
                'reference_no'         => 'DEMO-TXN-EXP-001',
                'type'                 => 'expense',
                'income_category_id'   => null,
                'expense_category_id'  => $expenseCategory->id,
                'shareholder_id'       => null,
                'amount'               => $expense->amount,
                'description'          => $expense->description,
                'transaction_date'     => $expense->expense_date,
                'payment_method'       => $expense->payment_method,
                'recorded_by'          => $userId,
            ]
        );

        $this->postJournalIfMissing(
            Expense::class,
            $expense->id,
            $expense->expense_date->toDateString(),
            'Demo Expense Entry',
            [
                ['account_id' => $expenseHeadAccount->id, 'debit' => (float) $expense->amount, 'credit' => 0],
                ['account_id' => $bankAccount->id, 'debit' => 0, 'credit' => (float) $expense->amount],
            ],
            $userId
        );

        $capitalTxn = Transaction::updateOrCreate(
            ['reference_no' => 'DEMO-TXN-CAP-001'],
            [
                'type'                => 'capital',
                'income_category_id'  => null,
                'expense_category_id' => null,
                'shareholder_id'      => $shareholder->id,
                'amount'              => 200000,
                'description'         => 'Demo capital injection',
                'transaction_date'    => now()->subDays(10)->toDateString(),
                'payment_method'      => 'Bank Transfer',
                'transactionable_type'=> Shareholder::class,
                'transactionable_id'  => $shareholder->id,
                'recorded_by'         => $userId,
            ]
        );

        $this->upsertAccountTransactionBySource(
            Transaction::class,
            $capitalTxn->id,
            BankAccount::class,
            $bank->id,
            'credit',
            (float) $capitalTxn->amount,
            'capital',
            $capitalTxn->reference_no,
            $capitalTxn->description,
            $capitalTxn->transaction_date,
            $userId
        );

        $this->postJournalIfMissing(
            Transaction::class,
            $capitalTxn->id,
            $capitalTxn->transaction_date->toDateString(),
            'Demo Capital Entry',
            [
                ['account_id' => $bankAccount->id, 'debit' => (float) $capitalTxn->amount, 'credit' => 0],
                ['account_id' => $equityAccount->id, 'debit' => 0, 'credit' => (float) $capitalTxn->amount],
            ],
            $userId
        );

        $withdrawTxn = Transaction::updateOrCreate(
            ['reference_no' => 'DEMO-TXN-WDR-001'],
            [
                'type'                => 'withdrawal',
                'income_category_id'  => null,
                'expense_category_id' => null,
                'shareholder_id'      => $shareholder->id,
                'amount'              => 25000,
                'description'         => 'Demo shareholder withdrawal',
                'transaction_date'    => now()->subDays(2)->toDateString(),
                'payment_method'      => 'Cash',
                'transactionable_type'=> Shareholder::class,
                'transactionable_id'  => $shareholder->id,
                'recorded_by'         => $userId,
            ]
        );

        $this->upsertAccountTransactionBySource(
            Transaction::class,
            $withdrawTxn->id,
            HandCash::class,
            $handCash->id,
            'debit',
            (float) $withdrawTxn->amount,
            'withdrawal',
            $withdrawTxn->reference_no,
            $withdrawTxn->description,
            $withdrawTxn->transaction_date,
            $userId
        );

        $this->postJournalIfMissing(
            Transaction::class,
            $withdrawTxn->id,
            $withdrawTxn->transaction_date->toDateString(),
            'Demo Withdrawal Entry',
            [
                ['account_id' => $equityAccount->id, 'debit' => (float) $withdrawTxn->amount, 'credit' => 0],
                ['account_id' => $cashAccount->id, 'debit' => 0, 'credit' => (float) $withdrawTxn->amount],
            ],
            $userId
        );
    }

    private function postJournalIfMissing(
        string $sourceType,
        int $sourceId,
        string $date,
        string $description,
        array $lines,
        ?int $userId
    ): void {
        $exists = JournalEntry::where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->exists();

        if ($exists) {
            return;
        }

        JournalService::post($date, $description, $lines, $sourceType, $sourceId, $userId);
    }

    private function upsertAccountTransactionBySource(
        string $transactionableType,
        int $transactionableId,
        string $accountType,
        int $accountId,
        string $direction,
        float $amount,
        string $purpose,
        ?string $referenceNo,
        ?string $description,
        string $date,
        ?int $recordedBy
    ): void {
        AccountTransaction::removeSource($transactionableType, $transactionableId);

        AccountTransaction::record(
            $accountType,
            $accountId,
            $direction,
            $amount,
            $purpose,
            $referenceNo,
            $description,
            \Carbon\Carbon::parse($date),
            $transactionableType,
            $transactionableId,
            $recordedBy
        );
    }
}
