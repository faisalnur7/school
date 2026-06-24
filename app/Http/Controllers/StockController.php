<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function stockReport(Request $request)
    {
        $this->authorizePermission('view_inventory_reports');

        $query = InventoryItem::with('category')
            ->where('stock_type', '!=', 'made_to_order')
            ->orderBy('name');

        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(25)->withQueryString();
        return view('pages.inventory.reports.stock', compact('items'));
    }

    public function lowStock(Request $request)
    {
        $this->authorizePermission('view_inventory_reports');

        $query = InventoryItem::with('category')
            ->where('is_active', true)
            ->where('stock_type', '!=', 'made_to_order')
            ->whereColumn('current_stock', '<', 'minimum_stock_alert')
            ->orderBy('current_stock');

        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(25)->withQueryString();
        return view('pages.inventory.reports.low-stock', compact('items'));
    }
}
