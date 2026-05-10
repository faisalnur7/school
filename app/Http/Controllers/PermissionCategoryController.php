<?php

namespace App\Http\Controllers;

use App\Models\PermissionCategory;
use Illuminate\Http\Request;

class PermissionCategoryController extends Controller
{
    public function index()
    {
        $categories = PermissionCategory::withCount('permissions')->orderBy('sort_order')->paginate(15);
        return view('pages.permission-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.permission-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:permission_categories',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        PermissionCategory::create($validated);

        return redirect()->route('permission-categories.index')->with('success', 'Category created successfully');
    }

    public function edit(PermissionCategory $permissionCategory)
    {
        return view('pages.permission-categories.edit', compact('permissionCategory'));
    }

    public function update(Request $request, PermissionCategory $permissionCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:permission_categories,name,' . $permissionCategory->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $permissionCategory->update($validated);

        return redirect()->route('permission-categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(PermissionCategory $permissionCategory)
    {
        if (!auth()->user()->is_super_admin) {
            return redirect()->route('permission-categories.index')->with('error', 'Only Super Admin can delete categories');
        }

        $permissionCategory->delete();
        return redirect()->route('permission-categories.index')->with('success', 'Category deleted successfully');
    }
}
