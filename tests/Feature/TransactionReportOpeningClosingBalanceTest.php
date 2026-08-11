<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionReportOpeningClosingBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_report_shows_opening_closing_balances_and_summary_view(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Tuition Fee',
            'slug' => 'tuition-fee',
            'is_active' => 1,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Stationery',
            'slug' => 'stationery',
            'is_active' => 1,
        ]);

        $shareholder = Shareholder::create([
            'name' => 'Founding Member',
        ]);

        Transaction::create([
            'reference_no' => 'TXN-OPEN-001',
            'type' => 'capital',
            'shareholder_id' => $shareholder->id,
            'amount' => 50,
            'description' => 'Opening capital',
            'transaction_date' => '2025-12-31',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-INC-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 100,
            'description' => 'January tuition',
            'transaction_date' => '2026-01-02',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-EXP-001',
            'type' => 'expense',
            'expense_category_id' => $expenseCategory->id,
            'amount' => 30,
            'description' => 'Office supplies',
            'transaction_date' => '2026-01-03',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index', [
            'from' => '01/01/2026',
            'to' => '31/01/2026',
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Opening Balance');
        $response->assertSee('Income - Tuition Fee');
        $response->assertSee('Expense - Stationery');
        $response->assertSee('Closing Balance');
    }

    public function test_transaction_report_shows_detailed_rows(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Admission',
            'slug' => 'admission',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-DET-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 250,
            'description' => 'Admission fee',
            'transaction_date' => '2026-07-19',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index', [
            'report_type' => 'detailed',
            'from' => '01/07/2026',
            'to' => '31/07/2026',
        ]));

        $response->assertOk();
        $response->assertSee('Date');
        $response->assertSee('Reference');
        $response->assertSee('TXN-DET-001');
        $response->assertSee('Admission fee');
        $response->assertSee('Income');
    }

    public function test_transaction_grouped_detailed_view_shows_descriptions(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Fees',
            'slug' => 'fees',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-GRP-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'amount' => 400,
            'description' => 'Monthly fees',
            'transaction_date' => '2026-07-19',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index', [
            'view' => 'grouped',
            'report_type' => 'detailed',
            'from' => '01/07/2026',
            'to' => '31/07/2026',
        ]));

        $response->assertOk();
        $response->assertSee('Income - Fees');
        $response->assertSee('TXN-GRP-001');
        $response->assertSee('Monthly fees');
        $response->assertSee('Description');
    }

    public function test_transaction_report_keeps_income_category_selected_when_income_and_expense_ids_overlap(): void
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        DB::table('income_categories')->insert([
            'id' => 12,
            'name' => 'Admission Fee',
            'slug' => 'admission-fee',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('expense_categories')->insert([
            'id' => 12,
            'name' => 'Admission Supplies',
            'slug' => 'admission-supplies',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Transaction::create([
            'reference_no' => 'TXN-INC-012',
            'type' => 'income',
            'income_category_id' => 12,
            'amount' => 300,
            'description' => 'Admission fee',
            'transaction_date' => '2026-07-19',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-EXP-012',
            'type' => 'expense',
            'expense_category_id' => 12,
            'amount' => 150,
            'description' => 'Admission supplies',
            'transaction_date' => '2026-07-19',
            'payment_method' => 'Cash',
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('transactions.index', [
            'type' => 'income',
            'category_id' => 'income:12',
            'report_type' => 'summary',
            'from' => '01/07/2026',
            'to' => '31/07/2026',
        ]));

        $response->assertOk();
        $response->assertSee('value="income:12" selected', false);
        $response->assertDontSee('value="expense:12" selected', false);
        $response->assertSee('Income - Admission Fee');
        $response->assertDontSee('Expense - Admission Supplies');
    }
}
