<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventorySale;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function hub()
    {
        abort_if(!auth()->user()?->hasAnyPermission([
            'view_inventory',
            'manage_inventory_categories',
            'manage_inventory_products',
            'manage_inventory_suppliers',
            'manage_inventory_purchases',
            'view_inventory_reports',
        ]), 403);

        $cards = [
            ['icon' => 'fa-tags',          'title' => __('Categories'),        'subtitle' => __('Manage inventory categories'), 'route' => 'inventory.categories.index', 'permission' => 'view_card_inventory_categories', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-boxes',         'title' => __('Products'),          'subtitle' => __('Manage products & stock'),     'route' => 'inventory.products.index', 'permission' => 'view_card_inventory_products', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-truck',         'title' => __('Suppliers'),         'subtitle' => __('Manage suppliers'),            'route' => 'inventory.suppliers.index', 'permission' => 'view_card_inventory_suppliers', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-shopping-cart', 'title' => __('Purchases'),         'subtitle' => __('Record new purchases'),        'route' => 'inventory.purchases.index', 'permission' => 'view_card_inventory_purchases', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-clipboard-list', 'title' => __('Opening Stock'),     'subtitle' => __('Enter opening quantities and average cost'), 'route' => 'inventory.opening-stock.create', 'permission' => 'manage_inventory_products', 'from' => '#2563eb', 'to' => '#1d4ed8'],
            ['icon' => 'fa-receipt',       'title' => __('Sales Hub'),         'subtitle' => __('Track inventory sales by purchase source'), 'route' => 'inventory.sales.hub', 'permission' => 'view_inventory', 'from' => '#0f766e', 'to' => '#115e59'],
            ['icon' => 'fa-chart-bar',     'title' => __('Stock Report'),      'subtitle' => __('View stock summary'),          'route' => 'inventory.reports.stock', 'permission' => 'view_card_inventory_stock_report', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-exclamation',   'title' => __('Low Stock Products'),'subtitle' => __('Items below minimum stock'),   'route' => 'inventory.reports.lowStock', 'permission' => 'view_card_inventory_low_stock', 'from' => '#7c3aed', 'to' => '#6d28d9'],
        ];
        $cards = array_values(array_filter($cards, function ($card) {
            if (($card['permission'] ?? null) === 'view_inventory') {
                return auth()->user()?->hasAnyPermission(['view_inventory', 'manage_inventory_purchases', 'view_inventory_reports']);
            }

            return auth()->user()?->hasPermission($card['permission']);
        }));

        return view('pages.inventory.hub', compact('cards'));
    }

    public function salesHub(Request $request)
    {
        abort_if(!auth()->user()?->hasAnyPermission([
            'view_inventory',
            'manage_inventory_purchases',
            'view_inventory_reports',
        ]), 403);

        $purchases = PurchaseOrder::with(['supplier', 'items.inventoryItem.category'])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->get();

        $purchaseItemIds = collect();
        $selectedPurchase = null;

        if ($request->filled('purchase_id')) {
            $selectedPurchase = $purchases->firstWhere('id', (int) $request->purchase_id);
            if ($selectedPurchase) {
                $purchaseItemIds = $selectedPurchase->items->pluck('inventory_item_id')->filter()->unique()->values();
            }
        }

        $itemIds = collect();
        if ($request->filled('inventory_item_id')) {
            $itemIds = collect([(int) $request->inventory_item_id]);
        }

        $salesQuery = InventorySale::with([
                'student',
                'payment',
                'createdBy',
                'items.inventoryItem.category',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->get('q'));
            $salesQuery->where(function ($sub) use ($q) {
                $sub->whereHas('payment', fn ($p) => $p->where('receipt_no', 'like', "%{$q}%"))
                    ->orWhereHas('student', fn ($s) => $s->where('full_name_en', 'like', "%{$q}%"))
                    ->orWhereHas('items.inventoryItem', fn ($i) => $i->where('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('from')) {
            $salesQuery->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $salesQuery->whereDate('created_at', '<=', $request->to);
        }

        if ($itemIds->isNotEmpty()) {
            $salesQuery->whereHas('items', fn ($q) => $q->whereIn('inventory_item_id', $itemIds));
        }

        if ($purchaseItemIds->isNotEmpty()) {
            $salesQuery->whereHas('items', fn ($q) => $q->whereIn('inventory_item_id', $purchaseItemIds));
        }

        $sales = $salesQuery->paginate(20)->withQueryString();

        $purchaseFilteredItems = InventoryItem::with('category')
            ->whereIn('id', $purchaseItemIds->isNotEmpty() ? $purchaseItemIds : [0])
            ->orderBy('name')
            ->get();

        $allItems = InventoryItem::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.inventory.sales.hub', compact(
            'sales',
            'purchases',
            'selectedPurchase',
            'purchaseFilteredItems',
            'allItems',
        ));
    }
}
