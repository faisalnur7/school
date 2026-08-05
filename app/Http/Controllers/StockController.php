<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\SchoolClass;
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

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->filled('school_class_id')) {
            $query->where('school_class_id', $request->get('school_class_id'));
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->get('group_id'));
        }

        $totalInventoryValue = (float) (clone $query)
            ->selectRaw('COALESCE(SUM(current_stock * COALESCE(average_cost, purchase_price)), 0) as total_value')
            ->value('total_value');

        $items = $query->paginate(25)->withQueryString();
        $categories = InventoryCategory::orderBy('name')->get();
        $classes = SchoolClass::get();
        $groups = Group::orderBy('name_en')->get();

        return view('pages.inventory.reports.stock', compact(
            'items',
            'totalInventoryValue',
            'categories',
            'classes',
            'groups'
        ));
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
