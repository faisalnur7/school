<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\BankAccount;
use App\Models\HandCash;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionAndDayBookSummaryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_summary_report_shows_category_totals_and_balances(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Donation',
            'slug' => 'donation',
            'is_active' => 1,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Utilities',
            'slug' => 'utilities',
            'is_active' => 1,
        ]);

        $shareholder = Shareholder::create(['name' => 'Founder']);

        Transaction::create([
            'reference_no' => 'TXN-BAL-001',
            'type' => 'capital',
            'shareholder_id' => $shareholder->id,
            'amount' => 500,
            'description' => 'Opening capital',
            'transaction_date' => '2026-06-30',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-TXN-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 1200,
            'description' => 'Donation received',
            'transaction_date' => '2026-07-10',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-TXN-002',
            'type' => 'expense',
            'expense_category_id' => $expenseCategory->id,
            'amount' => 300,
            'description' => 'Electric bill',
            'transaction_date' => '2026-07-11',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index', [
            'from' => '01/07/2026',
            'to' => '31/07/2026',
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Income - Donation');
        $response->assertSee('Expense - Utilities');
    }

    public function test_day_book_summary_report_shows_category_totals_and_balances(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        HandCash::create([
            'label' => 'Petty Cash',
            'opening_amount' => 241167,
            'opening_date' => '2026-08-08',
            'is_active' => true,
            'recorded_by' => $user->id,
        ]);

        BankAccount::create([
            'bank_name' => 'City Bank',
            'account_name' => 'Main Account',
            'account_number' => '1234567890',
            'opening_balance' => 97500,
            'opening_date' => '2026-08-08',
            'is_active' => true,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Tuition',
            'slug' => 'tuition',
            'is_active' => 1,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Maintenance',
            'slug' => 'maintenance',
            'is_active' => 1,
        ]);

        $shareholder = Shareholder::create(['name' => 'Founder']);

        Transaction::create([
            'reference_no' => 'TXN-DAY-000',
            'type' => 'capital',
            'shareholder_id' => $shareholder->id,
            'amount' => 6900,
            'description' => 'Opening carry forward',
            'transaction_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-DAY-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 1050,
            'description' => 'Tuition collection',
            'transaction_date' => '2026-08-09',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-DAY-002',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 22650,
            'description' => 'Student payment',
            'transaction_date' => '2026-08-09',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-DAY-003',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 960,
            'description' => 'Transport fee',
            'transaction_date' => '2026-08-09',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.day-book', [
            'date' => '09/08/2026',
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Income - Tuition');
        $response->assertSee('248,067.00');
        $response->assertSee('272,727.00');
    }

    public function test_day_book_opening_balance_ignores_bank_opening_balance(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        HandCash::create([
            'label' => 'Petty Cash',
            'opening_amount' => 241167,
            'opening_date' => '2026-08-08',
            'is_active' => true,
            'recorded_by' => $user->id,
        ]);

        BankAccount::create([
            'bank_name' => 'City Bank',
            'account_name' => 'Main Account',
            'account_number' => '1234567890',
            'opening_balance' => 97500,
            'opening_date' => '2026-08-08',
            'is_active' => true,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Tuition',
            'slug' => 'tuition',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-DAY-010',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 1500,
            'description' => 'Fee receipt',
            'transaction_date' => '2026-08-08',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.day-book', [
            'date' => '08/08/2026',
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('242,667.00');
        $response->assertDontSee('340,167.00');
    }
}
