<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryItemController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePermission('manage_inventory_products');

        $query = InventoryItem::with('category')->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();
        $categories = InventoryCategory::orderBy('name')->get();

        return view('pages.inventory.products.index', compact('items', 'categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage_inventory_products');

        $categories = InventoryCategory::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name_en')->get();
        $sections = Section::orderBy('name_en')->get();
        $groups = Group::orderBy('name_en')->get();

        return view('pages.inventory.products.create', compact('categories', 'classes', 'sections', 'groups'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_products');

        $validated = $request->validate([
            'category_id' => ['required', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock_alert' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
        ]);

        $category = InventoryCategory::findOrFail($validated['category_id']);
        $isBooks = strcasecmp($category->name, 'Books') === 0;

        $nameUniqueRule = Rule::unique('inventory_items', 'name')->where(function ($query) use ($validated) {
            return $query
                ->where('category_id', $validated['category_id'])
                ->where('school_class_id', $validated['school_class_id'] ?? null)
                ->where('section_id', $validated['section_id'] ?? null)
                ->where('group_id', $validated['group_id'] ?? null);
        });

        if (!$isBooks) {
            $validated['school_class_id'] = null;
            $validated['section_id'] = null;
            $validated['group_id'] = null;
            $nameUniqueRule = Rule::unique('inventory_items', 'name')->where(fn ($q) => $q->where('category_id', $validated['category_id']));
        }

        $skuUniqueRule = Rule::unique('inventory_items', 'sku')->where(fn ($q) => $q->where('category_id', $validated['category_id']));

        $request->validate([
            'name' => ['required', $nameUniqueRule],
            'sku' => ['nullable', $skuUniqueRule],
        ]);

        $validated['purchase_price'] = $validated['purchase_price'] ?? 0;
        $validated['minimum_stock_alert'] = $validated['minimum_stock_alert'] ?? 0;
        $validated['is_active'] = (bool)($validated['is_active'] ?? true);
        $validated['current_stock'] = 0;

        InventoryItem::create($validated);

        return redirect()->route('inventory.products.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $this->authorizePermission('manage_inventory_products');

        $item = InventoryItem::findOrFail($id);
        $categories = InventoryCategory::orderBy('name')->get();
        $classes = SchoolClass::orderBy('name_en')->get();
        $sections = Section::orderBy('name_en')->get();
        $groups = Group::orderBy('name_en')->get();

        return view('pages.inventory.products.edit', compact('item', 'categories', 'classes', 'sections', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizePermission('manage_inventory_products');

        $item = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:inventory_categories,id'],
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock_alert' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
        ]);

        $category = InventoryCategory::findOrFail($validated['category_id']);
        $isBooks = strcasecmp($category->name, 'Books') === 0;

        $nameUniqueRule = Rule::unique('inventory_items', 'name')->ignore($item->id)->where(function ($query) use ($validated) {
            return $query
                ->where('category_id', $validated['category_id'])
                ->where('school_class_id', $validated['school_class_id'] ?? null)
                ->where('section_id', $validated['section_id'] ?? null)
                ->where('group_id', $validated['group_id'] ?? null);
        });

        if (!$isBooks) {
            $validated['school_class_id'] = null;
            $validated['section_id'] = null;
            $validated['group_id'] = null;
            $nameUniqueRule = Rule::unique('inventory_items', 'name')->ignore($item->id)->where(fn ($q) => $q->where('category_id', $validated['category_id']));
        }

        $skuUniqueRule = Rule::unique('inventory_items', 'sku')->ignore($item->id)->where(fn ($q) => $q->where('category_id', $validated['category_id']));

        $request->validate([
            'name' => ['required', $nameUniqueRule],
            'sku' => ['nullable', $skuUniqueRule],
        ]);

        $validated['purchase_price'] = $validated['purchase_price'] ?? $item->purchase_price;
        $validated['minimum_stock_alert'] = $validated['minimum_stock_alert'] ?? $item->minimum_stock_alert;
        $validated['is_active'] = (bool)($validated['is_active'] ?? false);

        $item->update($validated);

        return redirect()->route('inventory.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $this->authorizePermission('manage_inventory_products');

        $item = InventoryItem::withCount('purchaseItems')->findOrFail($id);

        if ((int)$item->current_stock > 0 || $item->purchase_items_count > 0) {
            return redirect()->route('inventory.products.index')->with('error', 'Cannot delete a product with stock or purchase history');
        }

        $item->delete();
        return redirect()->route('inventory.products.index')->with('success', 'Product deleted successfully');
    }
}
