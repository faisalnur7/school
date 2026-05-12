<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\SchoolClass;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $query = PurchaseOrder::with('supplier')->orderByDesc('purchase_date')->orderByDesc('id');
        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where('reference_no', 'like', "%{$q}%")
                ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$q}%"));
        }

        $purchases = $query->paginate(20)->withQueryString();
        return view('pages.inventory.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $this->authorizePermission('manage_inventory_purchases');

        $suppliers = Supplier::where('status', true)->orderBy('name')->get();
        $products = InventoryItem::with(['category', 'schoolClass', 'group'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = $products->pluck('category')->filter()->unique('id')->sortBy('name')->values();
        $classes    = SchoolClass::where('status', true)->get();
        $groups     = Group::get();

        return view('pages.inventory.purchases.create', compact('suppliers', 'products', 'categories', 'classes', 'groups'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $createdBy = auth()->id();

        $items = collect($validated['items'])
            ->groupBy('inventory_item_id')
            ->map(function ($rows) {
                $qty = (int)$rows->sum(fn ($r) => (int)$r['quantity']);
                $unitPrice = (float)($rows->last()['unit_price'] ?? 0);
                return [
                    'inventory_item_id' => (int)$rows->first()['inventory_item_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $qty * $unitPrice,
                ];
            })
            ->values();

        $totalAmount = (float)$items->sum('line_total');

        $purchase = DB::transaction(function () use ($validated, $items, $totalAmount, $createdBy) {
            $purchase = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'reference_no' => $validated['reference_no'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'created_by' => $createdBy,
            ]);

            foreach ($items as $row) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'inventory_item_id' => $row['inventory_item_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => $row['line_total'],
                ]);

                $product = InventoryItem::lockForUpdate()->findOrFail($row['inventory_item_id']);
                $product->update([
                    'current_stock' => (int)$product->current_stock + (int)$row['quantity'],
                    'purchase_price' => $row['unit_price'],
                ]);

                StockMovement::create([
                    'inventory_item_id' => $product->id,
                    'type' => 'purchase',
                    'quantity_change' => (int)$row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'purchase_order_id' => $purchase->id,
                    'created_by' => $createdBy,
                    'note' => $validated['reference_no'] ? 'Ref: ' . $validated['reference_no'] : null,
                ]);
            }

            return $purchase;
        });

        return redirect()->route('inventory.purchases.show', $purchase->id)->with('success', 'Purchase saved successfully');
    }

    public function show($id)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $purchase = PurchaseOrder::with(['supplier', 'items.inventoryItem.category', 'createdBy'])
            ->findOrFail($id);

        return view('pages.inventory.purchases.show', compact('purchase'));
    }
}
