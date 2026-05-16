<?php
namespace App\Services;

use App\Models\Account;
use App\Models\Employee;
use App\Models\ExpenseCategory;
use App\Models\HrPayroll;
use App\Models\HrTransaction;
use App\Models\Expense;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\HandCash;
use App\Models\MobileBankingAccount;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    private function toExpensePaymentMethod(string $method): string
    {
        return match ($method) {
            'bank'          => 'Bank Transfer',
            'mobile_wallet' => 'Mobile Banking',
            'cash'          => 'Cash',
            default         => 'Other',
        };
    }

    private function resolvePayoutSource(string $method): array
    {
        return match ($method) {
            'bank' => [BankAccount::class, (int) BankAccount::where('is_active', true)->value('id')],
            'mobile_wallet' => [MobileBankingAccount::class, (int) MobileBankingAccount::where('is_active', true)->value('id')],
            default => [HandCash::class, (int) HandCash::where('is_active', true)->value('id')],
        };
    }

    public function preview(int $month, int $year): array
    {
        $employees = Employee::active()->with(['salaryStructure', 'designation', 'paymentInformation'])->get();
        $rows = [];
        foreach ($employees as $emp) {
            $salary = $emp->salaryStructure;
            $existing = HrPayroll::where('employee_id', $emp->id)
                ->where('payroll_month', $month)->where('payroll_year', $year)->exists();
            $rows[] = [
                'employee'        => $emp,
                'salary'          => $salary,
                'gross'           => $salary?->gross_salary ?? 0,
                'deductions'      => $salary?->other_deductions ?? 0,
                'net'             => $salary?->net_salary ?? 0,
                'missing_salary'  => !$salary,
                'already_exists'  => $existing,
            ];
        }
        return $rows;
    }

    public function generate(int $month, int $year): array
    {
        $employees = Employee::active()->with(['salaryStructure', 'paymentInformation'])->get();
        $created = 0; $skipped = 0;

        DB::transaction(function () use ($employees, $month, $year, &$created, &$skipped) {
            $lastDay = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

            foreach ($employees as $emp) {
                $salary = $emp->salaryStructure;
                if (!$salary) { $skipped++; continue; }

                $exists = HrPayroll::where('employee_id', $emp->id)
                    ->where('payroll_month', $month)->where('payroll_year', $year)->exists();
                if ($exists) { $skipped++; continue; }

                $method = $emp->paymentInformation?->payment_method ?? 'cash';
                $expenseMethod = $this->toExpensePaymentMethod($method);
                [$payoutAccountType, $payoutAccountId] = $this->resolvePayoutSource($method);

                $payroll = HrPayroll::create([
                    'employee_id'     => $emp->id,
                    'payroll_month'   => $month,
                    'payroll_year'    => $year,
                    'gross_salary'    => $salary->gross_salary,
                    'other_deductions'=> $salary->other_deductions,
                    'net_salary'      => $salary->net_salary,
                    'payment_method'  => $method,
                    'status'          => 'pending',
                    'is_locked'       => false,
                ]);

                HrTransaction::create([
                    'employee_id'      => $emp->id,
                    'payroll_id'       => $payroll->id,
                    'amount'           => $salary->net_salary,
                    'payment_method'   => $method,
                    'account_head'     => 'Salary Expense',
                    'transaction_date' => $lastDay,
                ]);

                // Also post to the main transactions ledger
                $salaryCategoryId = ExpenseCategory::where('name', 'Salary')->value('id') ?? 1;
                $reference = Transaction::generateReference();

                Expense::create([
                    'expense_category_id' => $salaryCategoryId,
                    'title'               => 'Salary — ' . $emp->name,
                    'reference_no'        => $reference,
                    'amount'              => $salary->net_salary,
                    'payment_method'      => $expenseMethod,
                    'account_type'        => null,
                    'account_id'          => null,
                    'description'         => 'Payroll ' . date('F', mktime(0,0,0,$month,1)) . ' ' . $year . ' | ' . $emp->employee_id,
                    'expense_date'        => $lastDay,
                    'recorded_by'         => auth()->id() ?? 1,
                ]);

                Transaction::create([
                    'reference_no'        => $reference,
                    'type'                => 'expense',
                    'expense_category_id' => $salaryCategoryId,
                    'amount'              => $salary->net_salary,
                    'payment_method'      => $expenseMethod,
                    'description'         => 'Salary — ' . $emp->name . ' (' . $emp->employee_id . ') ' . date('F', mktime(0,0,0,$month,1)) . ' ' . $year,
                    'transaction_date'    => $lastDay,
                    'transactionable_type'=> HrPayroll::class,
                    'transactionable_id'  => $payroll->id,
                    'recorded_by'         => auth()->id() ?? 1,
                ]);

                JournalService::postSafe(
                    $lastDay,
                    'Salary — ' . $emp->name,
                    [
                        ['account_id' => Account::resolveForSource(ExpenseCategory::class, $salaryCategoryId), 'debit' => (float) $salary->net_salary, 'credit' => 0],
                        ['account_id' => Account::resolveForSource($payoutAccountType, $payoutAccountId),      'debit' => 0, 'credit' => (float) $salary->net_salary],
                    ],
                    HrPayroll::class,
                    $payroll->id,
                    auth()->id() ?? 1
                );

                $created++;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }

    public function markPaid(int $payrollId): void
    {
        $payroll = HrPayroll::findOrFail($payrollId);
        if ($payroll->isLocked()) throw new \Exception('Payroll is locked.');

        $method = $payroll->payment_method ?? 'cash';
        [$payoutAccountType, $payoutAccountId] = $this->resolvePayoutSource($method);

        // Debit the payout account (HandCash for cash payments) at actual payment time
        if ($payoutAccountId) {
            $tx = Transaction::where('transactionable_type', HrPayroll::class)
                ->where('transactionable_id', $payrollId)
                ->first();

            if ($tx) {
                $expense = Expense::where('reference_no', $tx->reference_no)->first();
                if ($expense && !$expense->account_id) {
                    $expense->account_type = $payoutAccountType;
                    $expense->account_id   = $payoutAccountId;
                    $expense->save(); // triggers AccountTransaction debit on HandCash
                }
            }
        }

        $payroll->update(['status' => 'paid', 'processed_at' => now()]);
    }

    public function lock(int $month, int $year): int
    {
        return HrPayroll::where('payroll_month', $month)->where('payroll_year', $year)
            ->update(['is_locked' => true]);
    }

    public function getSummary(int $month, int $year): array
    {
        $payrolls = HrPayroll::forMonth($month, $year)->get();
        return [
            'count'        => $payrolls->count(),
            'total_gross'  => $payrolls->sum('gross_salary'),
            'total_net'    => $payrolls->sum('net_salary'),
            'paid_count'   => $payrolls->where('status', 'paid')->count(),
            'pending_count'=> $payrolls->where('status', 'pending')->count(),
        ];
    }
}
