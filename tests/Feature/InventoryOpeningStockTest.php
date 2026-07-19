<?php

namespace Tests\Feature;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryOpeningStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_stock_sets_quantity_and_average_cost(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = InventoryCategory::create(['name' => 'Books', 'is_active' => true]);
        $product = InventoryItem::create([
            'category_id' => $category->id,
            'name' => 'Workbook',
            'purchase_price' => 0,
            'average_cost' => 0,
            'current_stock' => 0,
            'minimum_stock_alert' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('inventory.opening-stock.store'), [
            'opening_date' => '2026-07-19',
            'reference_no' => 'OPEN-001',
            'notes' => 'Initial stock',
            'items' => [
                [
                    'inventory_item_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 12.5,
                ],
            ],
        ]);

        $response->assertRedirect(route('inventory.reports.stock'));
        $response->assertSessionHas('success');

        $product->refresh();
        $this->assertSame(10, (int) $product->current_stock);
        $this->assertSame('12.50', (string) $product->average_cost);
        $this->assertSame('12.50', (string) $product->purchase_price);

        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseHas('stock_movements', [
            'inventory_item_id' => $product->id,
            'type' => 'opening_stock',
            'quantity_change' => 10,
        ]);

        $report = $this->actingAs($user)->get(route('inventory.reports.stock'));
        $report->assertOk();
        $report->assertSee('Total Inventory Value');
        $report->assertSee('125.00');
    }

    public function test_later_purchase_updates_weighted_average_cost(): void
    {
        $user = User::factory()->create(['is_super_admin' => true]);

        $category = InventoryCategory::create(['name' => 'Stationery', 'is_active' => true]);
        $product = InventoryItem::create([
            'category_id' => $category->id,
            'name' => 'Pen',
            'purchase_price' => 0,
            'average_cost' => 12,
            'current_stock' => 10,
            'minimum_stock_alert' => 5,
            'unit' => 'pcs',
            'is_active' => true,
        ]);

        $supplier = Supplier::create(['name' => 'Acme Supplier', 'status' => true]);

        $response = $this->actingAs($user)->post(route('inventory.purchases.store'), [
            'supplier_id' => $supplier->id,
            'purchase_date' => '2026-07-19',
            'reference_no' => 'PO-AVG-1',
            'items' => [
                [
                    'inventory_item_id' => $product->id,
                    'quantity' => 10,
                    'unit_price' => 16,
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertSame(20, (int) $product->current_stock);
        $this->assertSame('14.00', (string) $product->average_cost);
        $this->assertSame('16.00', (string) $product->purchase_price);
        $this->assertSame('280.00', number_format($product->stockValue(), 2, '.', ''));

        $purchase = PurchaseOrder::first();
        $this->assertNotNull($purchase);
        $this->assertSame('PO-AVG-1', $purchase->reference_no);
    }
}
