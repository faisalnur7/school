<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountingPeriod;
use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\HandCash;
use App\Models\IncomeCategory;
use App\Models\MobileBankingAccount;
use App\Models\Shareholder;
use Illuminate\Database\Seeder;

class AccountingSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Account Groups ─────────────────────────────────────────────
        // Top-level groups
        $assets      = $this->group('Assets');
        $liabilities = $this->group('Liabilities');
        $equity      = $this->group('Equity');
        $income      = $this->group('Income');
        $expenses    = $this->group('Expenses');

        // Sub-groups under Assets
        $currentAssets  = $this->group('Current Assets',       $assets->id);
        $fixedAssets    = $this->group('Fixed Assets',         $assets->id);
        $cashAndBank    = $this->group('Cash & Bank',          $currentAssets->id);
        $receivables    = $this->group('Accounts Receivable',  $currentAssets->id);

        // Sub-groups under Liabilities
        $currentLiab    = $this->group('Current Liabilities',  $liabilities->id);
        $payables       = $this->group('Accounts Payable',     $currentLiab->id);

        // Sub-groups under Equity
        $shareholderEq  = $this->group('Shareholder Equity',   $equity->id);

        // Sub-groups under Income
        $feeIncome      = $this->group('Fee Income',           $income->id);
        $otherIncome    = $this->group('Other Income',         $income->id);

        // Sub-groups under Expenses
        $staffExpenses  = $this->group('Staff Expenses',       $expenses->id);
        $adminExpenses  = $this->group('Administrative',       $expenses->id);
        $opExpenses     = $this->group('Operational',          $expenses->id);

        // ── 2. Chart of Accounts ─────────────────────────────────────────
        // ── Cash & Bank accounts (linked to real HandCash/Bank/Mobile) ──
        foreach (HandCash::where('is_active', true)->get() as $hc) {
            $this->account($hc->label, 'asset', $cashAndBank->id, HandCash::class, $hc->id, (float) $hc->opening_amount);
        }

        foreach (BankAccount::where('is_active', true)->get() as $ba) {
            $this->account($ba->bank_name . ' — ' . $ba->account_number, 'asset', $cashAndBank->id, BankAccount::class, $ba->id, (float) $ba->opening_balance);
        }

        foreach (MobileBankingAccount::where('is_active', true)->get() as $mb) {
            $this->account($mb->provider . ' — ' . $mb->account_number, 'asset', $cashAndBank->id, MobileBankingAccount::class, $mb->id, (float) $mb->opening_balance);
        }

        // ── Income category accounts ──
        $incomeCategoryGroupMap = [
            'student-payment' => $feeIncome->id,
            'admission-fee'   => $feeIncome->id,
            'exam-fee'        => $feeIncome->id,
            'transport-fee'   => $feeIncome->id,
            'donation'        => $otherIncome->id,
            'canteen-income'  => $otherIncome->id,
        ];

        foreach (IncomeCategory::all() as $cat) {
            $groupId = $incomeCategoryGroupMap[$cat->slug] ?? $otherIncome->id;
            $this->account($cat->name, 'income', $groupId, IncomeCategory::class, $cat->id);
        }

        // ── Expense category accounts ──
        $expenseCategoryGroupMap = [
            'salary'         => $staffExpenses->id,
            'utility-bill'   => $adminExpenses->id,
            'maintenance'    => $opExpenses->id,
            'transport-cost' => $opExpenses->id,
            'scholarship'    => $adminExpenses->id,
            'office-expense' => $adminExpenses->id,
        ];

        foreach (ExpenseCategory::all() as $cat) {
            $groupId = $expenseCategoryGroupMap[$cat->slug] ?? $adminExpenses->id;
            $this->account($cat->name, 'expense', $groupId, ExpenseCategory::class, $cat->id);
        }

        // ── Shareholder equity accounts ──
        foreach (Shareholder::all() as $sh) {
            $this->account('Capital — ' . $sh->name,    'equity', $shareholderEq->id, Shareholder::class, $sh->id);
            $this->account('Drawings — ' . $sh->name,   'equity', $shareholderEq->id);
        }

        // ── Receivable / Payable control accounts ──
        $this->account('Student Fees Receivable', 'asset',     $receivables->id);
        $this->account('Accounts Payable',        'liability', $payables->id);
        $this->account('Retained Earnings',       'equity',    $equity->id);

        // ── 3. Accounting Period ─────────────────────────────────────────
        $year = now()->year;
        AccountingPeriod::firstOrCreate(
            ['name' => 'FY ' . $year . '-' . ($year + 1)],
            [
                'start_date' => $year . '-07-01',
                'end_date'   => ($year + 1) . '-06-30',
                'is_closed'  => false,
            ]
        );

        // Also create current calendar year period if different
        AccountingPeriod::firstOrCreate(
            ['name' => 'CY ' . $year],
            [
                'start_date' => $year . '-01-01',
                'end_date'   => $year . '-12-31',
                'is_closed'  => false,
            ]
        );

        $this->command->info('✅ Accounting system seeded:');
        $this->command->info('   Account Groups : ' . AccountGroup::count());
        $this->command->info('   Accounts       : ' . Account::count());
        $this->command->info('   Periods        : ' . AccountingPeriod::count());
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function group(string $name, ?int $parentId = null): AccountGroup
    {
        return AccountGroup::firstOrCreate(
            ['name' => $name, 'parent_id' => $parentId],
            ['parent_id' => $parentId]
        );
    }

    private function account(
        string  $name,
        string  $type,
        int     $groupId,
        ?string $referenceType = null,
        ?int    $referenceId   = null,
        float   $openingBalance = 0,
    ): Account {
        $match = ['name' => $name, 'account_group_id' => $groupId];

        if ($referenceType && $referenceId) {
            $match = ['reference_type' => $referenceType, 'reference_id' => $referenceId];
        }

        return Account::updateOrCreate($match, [
            'name'             => $name,
            'type'             => $type,
            'account_group_id' => $groupId,
            'reference_type'   => $referenceType,
            'reference_id'     => $referenceId,
            'opening_balance'  => $openingBalance,
        ]);
    }
}
