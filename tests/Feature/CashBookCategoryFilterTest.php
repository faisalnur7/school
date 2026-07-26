<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashBookCategoryFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_book_can_filter_income_transactions_by_category(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Donation',
            'slug' => 'donation',
            'is_active' => 1,
        ]);

        $otherIncomeCategory = IncomeCategory::create([
            'name' => 'Tuition',
            'slug' => 'tuition',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-INC-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'expense_category_id' => null,
            'amount' => 1500,
            'description' => 'Donation received',
            'transaction_date' => '2026-07-10',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-INC-002',
            'type' => 'income',
            'income_category_id' => $otherIncomeCategory->id,
            'expense_category_id' => null,
            'amount' => 900,
            'description' => 'Tuition income',
            'transaction_date' => '2026-07-11',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.cash-book', [
            'from' => '01/07/2026',
            'to' => '31/07/2026',
            'category_id' => $incomeCategory->id,
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Category: Donation');
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Income - Donation');
        $response->assertDontSee('Income - Tuition');
    }

    public function test_cash_book_can_filter_income_transactions_by_prefixed_category_value(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Admission',
            'slug' => 'admission',
            'is_active' => 1,
        ]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Admission Supplies',
            'slug' => 'admission-supplies',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-INC-101',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'expense_category_id' => null,
            'amount' => 1200,
            'description' => 'Admission fee',
            'transaction_date' => '2026-07-12',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-EXP-101',
            'type' => 'expense',
            'income_category_id' => null,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 400,
            'description' => 'Admission supplies',
            'transaction_date' => '2026-07-12',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.cash-book', [
            'from' => '01/07/2026',
            'to' => '31/07/2026',
            'category_id' => 'income:' . $incomeCategory->id,
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Category: Admission');
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Income - Admission');
        $response->assertDontSee('Expense - Admission Supplies');
    }

    public function test_cash_book_can_filter_expense_transactions_by_category(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $expenseCategory = ExpenseCategory::create([
            'name' => 'Utilities',
            'slug' => 'utilities',
            'is_active' => 1,
        ]);

        $otherExpenseCategory = ExpenseCategory::create([
            'name' => 'Maintenance',
            'slug' => 'maintenance',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-EXP-001',
            'type' => 'expense',
            'income_category_id' => null,
            'expense_category_id' => $expenseCategory->id,
            'amount' => 750,
            'description' => 'Electric bill',
            'transaction_date' => '2026-07-10',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-EXP-002',
            'type' => 'expense',
            'income_category_id' => null,
            'expense_category_id' => $otherExpenseCategory->id,
            'amount' => 320,
            'description' => 'Repair bill',
            'transaction_date' => '2026-07-11',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.cash-book', [
            'from' => '01/07/2026',
            'to' => '31/07/2026',
            'category_id' => $expenseCategory->id,
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Category: Utilities');
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Expense - Utilities');
        $response->assertDontSee('Expense - Maintenance');
    }

    public function test_cash_book_can_render_grouped_view_by_category(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $incomeCategory = IncomeCategory::create([
            'name' => 'Donation',
            'slug' => 'donation',
            'is_active' => 1,
        ]);

        Transaction::create([
            'reference_no' => 'TXN-GRP-001',
            'type' => 'income',
            'income_category_id' => $incomeCategory->id,
            'expense_category_id' => null,
            'amount' => 1000,
            'description' => 'Donation received',
            'transaction_date' => '2026-07-10',
            'payment_method' => 'Cash',
            'reference_note' => null,
            'recorded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.cash-book', [
            'from' => '01/07/2026',
            'to' => '31/07/2026',
            'report_type' => 'summary',
        ]));

        $response->assertOk();
        $response->assertSee('Opening Balance');
        $response->assertSee('Closing Balance');
        $response->assertSee('Income - Donation');
    }
}
