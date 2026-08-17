<?php

namespace Tests\Feature;

use App\Models\HandCash;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_income_create_page_shows_cash_account_dropdown(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = IncomeCategory::create([
            'name' => 'Donation',
            'slug' => 'donation',
            'is_active' => 1,
        ]);

        $cashAccount = HandCash::create([
            'label' => 'Main Cash Box',
            'opening_amount' => 0,
            'opening_date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Income account',
        ]);

        $response = $this->actingAs($user)->get(route('incomes.create'));

        $response->assertOk();
        $response->assertSee('Cash Account');
        $response->assertSee('Main Cash Box');
        $response->assertDontSee('Payment Method');
    }

    public function test_income_store_uses_selected_cash_account_and_renders_credit_voucher_with_description(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = IncomeCategory::create([
            'name' => 'Donation',
            'slug' => 'donation',
            'is_active' => 1,
        ]);

        $cashAccount = HandCash::create([
            'label' => 'Main Cash Box',
            'opening_amount' => 0,
            'opening_date' => now()->toDateString(),
            'is_active' => true,
            'notes' => 'Income account',
        ]);

        $response = $this->actingAs($user)->post(route('incomes.store'), [
            'income_category_id' => $category->id,
            'title' => 'Donation Received',
            'amount' => 2500,
            'income_date' => '17/08/2026',
            'account_type' => HandCash::class,
            'account_id' => $cashAccount->id,
            'description' => 'Annual fundraising donation',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('incomes.index'));

        $income = Income::first();
        $this->assertNotNull($income);
        $this->assertSame('Cash', $income->payment_method);
        $this->assertSame(HandCash::class, $income->account_type);
        $this->assertSame($cashAccount->id, $income->account_id);
        $this->assertSame('Main Cash Box', $income->account_display_name);

        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'title' => 'Donation Received',
            'payment_method' => 'Cash',
        ]);

        $voucher = $this->actingAs($user)->get(route('incomes.voucher', $income->id));
        $voucher->assertOk();
        $voucher->assertSee('Credit Voucher');
        $voucher->assertSee('Main Cash Box');
        $voucher->assertSee('Donation Received');
        $voucher->assertSee('Annual fundraising donation');
        $voucher->assertDontSee('Subtotal');
        $voucher->assertDontSee('Paid');
        $voucher->assertDontSee('Due');
    }
}
