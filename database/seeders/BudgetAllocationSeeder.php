<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BudgetAllocation;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class BudgetAllocationSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        // Resolve account IDs by name (safe — uses DB, not hardcoded IDs)
        $acc = fn(string $name) => Account::where('name', 'like', "%{$name}%")->value('id');
        $cat = fn(string $slug) => ExpenseCategory::where('slug', $slug)->value('id');

        // ── Yearly Allocations ────────────────────────────────────────────
        $yearly = [
            // Staff Expenses
            ['account' => 'Salary',         'category' => 'salary',         'amount' => 2400000, 'notes' => 'Annual salary budget for all staff'],
            ['account' => 'Scholarship',     'category' => 'scholarship',    'amount' => 150000,  'notes' => 'Annual scholarship disbursements'],

            // Administrative
            ['account' => 'Utility Bill',    'category' => 'utility-bill',   'amount' => 120000,  'notes' => 'Electricity, water, internet for the year'],
            ['account' => 'Office Expense',  'category' => 'office-expense', 'amount' => 80000,   'notes' => 'Stationery, printing, office supplies'],

            // Operational
            ['account' => 'Maintenance',     'category' => 'maintenance',    'amount' => 200000,  'notes' => 'Building and equipment maintenance'],
            ['account' => 'Transport Cost',  'category' => 'transport-cost', 'amount' => 180000,  'notes' => 'School transport operational cost'],
        ];

        foreach ($yearly as $row) {
            $accountId  = $acc($row['account']);
            $categoryId = $cat($row['category']);
            if (!$accountId) continue;

            BudgetAllocation::firstOrCreate(
                ['account_id' => $accountId, 'period' => 'yearly', 'fiscal_year' => $year, 'fiscal_month' => null],
                [
                    'expense_category_id' => $categoryId,
                    'amount'              => $row['amount'],
                    'notes'               => $row['notes'],
                    'recorded_by'         => 1,
                ]
            );
        }

        // ── Monthly Allocations (Salary — broken down per month) ──────────
        $salaryAccountId  = $acc('Salary');
        $salaryCategoryId = $cat('salary');
        $monthlyBudget    = 200000; // 200k/month × 12 = 2.4M matches yearly

        if ($salaryAccountId) {
            foreach (range(1, 12) as $month) {
                BudgetAllocation::firstOrCreate(
                    ['account_id' => $salaryAccountId, 'period' => 'monthly', 'fiscal_year' => $year, 'fiscal_month' => $month],
                    [
                        'expense_category_id' => $salaryCategoryId,
                        'amount'              => $monthlyBudget,
                        'notes'               => 'Monthly salary — ' . date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year,
                        'recorded_by'         => 1,
                    ]
                );
            }
        }

        // ── Monthly Allocations (Utility Bill — quarterly peaks) ──────────
        $utilityAccountId  = $acc('Utility Bill');
        $utilityCategoryId = $cat('utility-bill');

        $utilityMonthly = [
            1 => 8000, 2 => 8000, 3 => 9000,  4 => 10000, 5 => 12000, 6 => 14000,
            7 => 14000, 8 => 13000, 9 => 11000, 10 => 9000, 11 => 8000, 12 => 8000,
        ];

        if ($utilityAccountId) {
            foreach ($utilityMonthly as $month => $amount) {
                BudgetAllocation::firstOrCreate(
                    ['account_id' => $utilityAccountId, 'period' => 'monthly', 'fiscal_year' => $year, 'fiscal_month' => $month],
                    [
                        'expense_category_id' => $utilityCategoryId,
                        'amount'              => $amount,
                        'notes'               => 'Utility budget — ' . date('F', mktime(0, 0, 0, $month, 1)),
                        'recorded_by'         => 1,
                    ]
                );
            }
        }

        // ── Previous Year Yearly (for comparison in reports) ──────────────
        $prevYear = $year - 1;
        $prevYearly = [
            ['account' => 'Salary',        'category' => 'salary',         'amount' => 2200000],
            ['account' => 'Utility Bill',  'category' => 'utility-bill',   'amount' => 110000],
            ['account' => 'Maintenance',   'category' => 'maintenance',    'amount' => 180000],
            ['account' => 'Transport Cost','category' => 'transport-cost', 'amount' => 160000],
            ['account' => 'Office Expense','category' => 'office-expense', 'amount' => 70000],
            ['account' => 'Scholarship',   'category' => 'scholarship',    'amount' => 130000],
        ];

        foreach ($prevYearly as $row) {
            $accountId  = $acc($row['account']);
            $categoryId = $cat($row['category']);
            if (!$accountId) continue;

            BudgetAllocation::firstOrCreate(
                ['account_id' => $accountId, 'period' => 'yearly', 'fiscal_year' => $prevYear, 'fiscal_month' => null],
                [
                    'expense_category_id' => $categoryId,
                    'amount'              => $row['amount'],
                    'notes'               => 'FY ' . $prevYear . ' annual budget',
                    'recorded_by'         => 1,
                ]
            );
        }

        $this->command->info('✅ Budget Allocations seeded:');
        $this->command->info('   Current year (' . $year . ')  : ' . BudgetAllocation::where('fiscal_year', $year)->count() . ' allocations');
        $this->command->info('   Previous year (' . $prevYear . '): ' . BudgetAllocation::where('fiscal_year', $prevYear)->count() . ' allocations');
        $this->command->info('   Total                  : ' . BudgetAllocation::count());
    }
}
