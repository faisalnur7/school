<?php

namespace Tests\Feature;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
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
}

