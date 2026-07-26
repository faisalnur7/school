<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpeningStockController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function create(Request $request)
    {
        $this->authorizePermission('manage_inventory_products');

        $categories = InventoryCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = InventoryItem::with('category')
            ->where('is_active', true)
            ->where('stock_type', '!=', 'made_to_order')
            ->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->get('category_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        $items = $query->get();

        return view('pages.inventory.opening-stock.create', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_products');

        $validated = $request->validate([
            'opening_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:0'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $rows = collect($validated['items'])
            ->filter(fn ($row) => (int) ($row['quantity'] ?? 0) > 0)
            ->values();

        if ($rows->isEmpty()) {
            return back()
                ->withInput()
                ->withErrors(['items' => 'Add at least one product with a quantity greater than zero.']);
        }

        $createdBy = auth()->id();
        $reference = $validated['reference_no'] ?: 'OPEN-' . strtoupper(str_replace('-', '', substr((string) $validated['opening_date'], 0, 10))) . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($rows, $validated, $createdBy, $reference) {
            foreach ($rows as $row) {
                $product = InventoryItem::lockForUpdate()->findOrFail((int) $row['inventory_item_id']);
                $quantity = (int) $row['quantity'];
                $unitCost = (float) ($row['unit_cost'] ?? 0);

                $newAverageCost = $product->weightedAverageCostAfterInflow($quantity, $unitCost);

                $product->update([
                    'current_stock' => (int) $product->current_stock + $quantity,
                    'purchase_price' => $unitCost,
                    'average_cost' => $newAverageCost,
                ]);

                StockMovement::create([
                    'inventory_item_id' => $product->id,
                    'type' => 'opening_stock',
                    'quantity_change' => $quantity,
                    'unit_price' => $unitCost,
                    'created_by' => $createdBy,
                    'note' => trim(implode(' ', array_filter([
                        'Opening stock',
                        $reference ? 'Ref: ' . $reference : null,
                        $validated['notes'] ?? null,
                    ]))),
                ]);
            }
        });

        return redirect()
            ->route('inventory.reports.stock')
            ->with('success', 'Opening stock saved successfully.');
    }
}
