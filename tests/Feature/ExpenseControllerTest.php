<?php

namespace Tests\Feature;

use App\Models\HandCash;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_create_page_shows_cash_account_dropdown(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        ExpenseCategory::create([
            'name' => 'Utilities',
            'slug' => 'utilities',
            'is_active' => 1,
        ]);

        $cashAccount = HandCash::create([
            'label' => 'Main Cash Box',
            'opening_amount' => 0,
            'opening_date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Expense account',
        ]);

        $response = $this->actingAs($user)->get(route('expenses.create'));

        $response->assertOk();
        $response->assertSee('Cash Account');
        $response->assertSee('Main Cash Box');
        $response->assertDontSee('Payment Method');
    }

    public function test_expense_store_uses_selected_account_and_renders_debit_voucher_with_description(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = ExpenseCategory::create([
            'name' => 'Utilities',
            'slug' => 'utilities',
            'is_active' => 1,
        ]);

        $cashAccount = HandCash::create([
            'label' => 'Main Cash Box',
            'opening_amount' => 0,
            'opening_date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Expense account',
        ]);

        $response = $this->actingAs($user)->post(route('expenses.store'), [
            'expense_category_id' => $category->id,
            'title' => 'Electricity bill',
            'amount' => 1500,
            'expense_date' => '17/08/2026',
            'account_type' => HandCash::class,
            'account_id' => $cashAccount->id,
            'description' => 'Maintenance work done',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('expenses.index'));

        $expense = Expense::first();
        $this->assertNotNull($expense);
        $this->assertSame('Cash', $expense->payment_method);
        $this->assertSame(HandCash::class, $expense->account_type);
        $this->assertSame($cashAccount->id, $expense->account_id);
        $this->assertSame('Main Cash Box', $expense->account_display_name);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'title' => 'Electricity bill',
            'payment_method' => 'Cash',
        ]);

        $voucher = $this->actingAs($user)->get(route('expenses.voucher', $expense->id));
        $voucher->assertOk();
        $voucher->assertSee('Debit Voucher');
        $voucher->assertSee('Main Cash Box');
        $voucher->assertSee('Electricity bill');
        $voucher->assertSee('Maintenance work done');
        $voucher->assertDontSee('Subtotal');
        $voucher->assertDontSee('Paid');
        $voucher->assertDontSee('Due');
    }
}
