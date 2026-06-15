<?php

namespace Tests\Feature;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_creates_rows_and_updates_stock(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = InventoryCategory::create(['name' => 'Stationery', 'is_active' => true]);
        $product = InventoryItem::create([
            'category_id' => $category->id,
            'name' => 'Pen',
            'purchase_price' => 10,
            'current_stock' => 0,
            'minimum_stock_alert' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $supplier = Supplier::create(['name' => 'Acme Supplier', 'status' => true]);

        $response = $this
            ->actingAs($user)
            ->post(route('inventory.purchases.store'), [
                'supplier_id' => $supplier->id,
                'purchase_date' => '2026-05-11',
                'reference_no' => 'PO-1',
                'items' => [
                    [
                        'inventory_item_id' => $product->id,
                        'quantity' => 3,
                        'unit_price' => 12.5,
                    ],
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertSame(3, (int)$product->current_stock);
        $this->assertSame('12.50', (string)$product->purchase_price);

        $this->assertDatabaseCount('purchase_orders', 1);
        $this->assertDatabaseCount('purchase_order_items', 1);
        $this->assertDatabaseCount('stock_movements', 1);

        $this->assertDatabaseHas('purchase_order_items', [
            'inventory_item_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_partial_purchase_payment_updates_due_status_and_ledger(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = InventoryCategory::create(['name' => 'Stationery', 'is_active' => true]);
        $product = InventoryItem::create([
            'category_id' => $category->id,
            'name' => 'Notebook',
            'purchase_price' => 10,
            'current_stock' => 0,
            'minimum_stock_alert' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $supplier = Supplier::create(['name' => 'Local Supplier', 'status' => true]);

        $response = $this->actingAs($user)->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-05-11',
            'reference_no' => 'PO-100',
            'amount' => 10,
            'items' => [
                [
                    'inventory_item_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 10,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $purchase = PurchaseOrder::first();
        $this->assertSame('partial', $purchase->status);
        $this->assertSame('10.00', (string) $purchase->paid_amount);
        $this->assertSame('20.00', (string) $purchase->due_amount);

        $this->assertDatabaseCount('purchase_order_payments', 1);
        $this->assertDatabaseCount('expenses', 1);
        $this->assertDatabaseCount('transactions', 1);

        $balanceSheet = $this->actingAs($user)->get(route('reports.balance-sheet', ['year' => 2026]));
        $balanceSheet->assertOk();
        $balanceSheet->assertSee('Accounts Payable - Suppliers');
        $balanceSheet->assertSee('20.00');

        $voucher = $this->actingAs($user)->get(route('inventory.purchases.voucher', $purchase->id));
        $voucher->assertOk();
        $voucher->assertSee('Credit Purchase Voucher');
        $voucher->assertSee('Subtotal');
        $voucher->assertSee('Paid');
        $voucher->assertSee('Due');
        $voucher->assertSee('20.00');

        $payResponse = $this->actingAs($user)->post(route('inventory.purchases.payments.store', $purchase->id), [
            'amount' => 15,
            'payment_date' => '2026-05-15',
            'reference_no' => 'PAY-1',
            'notes' => 'Second installment',
        ]);

        $payResponse->assertSessionHasNoErrors();

        $purchase->refresh();
        $this->assertSame('partial', $purchase->status);
        $this->assertSame('25.00', (string) $purchase->paid_amount);
        $this->assertSame('5.00', (string) $purchase->due_amount);

        $this->assertDatabaseCount('purchase_order_payments', 2);
        $this->assertDatabaseCount('expenses', 2);
        $this->assertDatabaseCount('transactions', 2);

        $this->actingAs($user)->post(route('inventory.purchases.payments.store', $purchase->id), [
            'amount' => 5,
            'payment_date' => '2026-05-16',
            'reference_no' => 'PAY-2',
            'notes' => 'Final installment',
        ])->assertSessionHasNoErrors();

        $purchase->refresh();
        $this->assertSame('paid', $purchase->status);
        $this->assertSame('30.00', (string) $purchase->paid_amount);
        $this->assertSame('0.00', (string) $purchase->due_amount);

        $this->assertDatabaseCount('purchase_order_payments', 3);
        $this->assertDatabaseCount('expenses', 3);
        $this->assertDatabaseCount('transactions', 3);

        $report = $this->actingAs($user)->get(route('reports.supplier-dues'));
        $report->assertOk();
        $report->assertSee('Supplier Due Report');
        $report->assertSee('PO-100');
        $report->assertSee('paid');
    }
}
