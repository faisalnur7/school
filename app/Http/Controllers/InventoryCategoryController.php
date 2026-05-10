<?php

namespace App\Http\Controllers;

use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoryCategoryController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePermission('manage_inventory_categories');

        $query = InventoryCategory::query()->orderBy('name');
        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        $categories = $query->paginate(20)->withQueryString();
        return view('pages.inventory.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage_inventory_categories');
        return view('pages.inventory.categories.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_categories');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:inventory_categories,name'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool)($validated['is_active'] ?? true);
        InventoryCategory::create($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Category created successfully');
    }

    public function edit($id)
    {
        $this->authorizePermission('manage_inventory_categories');
        $category = InventoryCategory::findOrFail($id);
        return view('pages.inventory.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizePermission('manage_inventory_categories');
        $category = InventoryCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('inventory_categories', 'name')->ignore($category->id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool)($validated['is_active'] ?? false);
        $category->update($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $this->authorizePermission('manage_inventory_categories');
        $category = InventoryCategory::withCount('items')->findOrFail($id);

        if ($category->items_count > 0) {
            return redirect()->route('inventory.categories.index')->with('error', 'Cannot delete a category with products');
        }

        $category->delete();
        return redirect()->route('inventory.categories.index')->with('success', 'Category deleted successfully');
    }
}

