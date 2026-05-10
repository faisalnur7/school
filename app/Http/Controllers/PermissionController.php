<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionCategory;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::with('category')->paginate(20);
        return view('pages.permissions.index', compact('permissions'));
    }

    public function create()
    {
        $categories = PermissionCategory::all();
        return view('pages.permissions.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:permission_categories,id',
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit(Permission $permission)
    {
        $categories = PermissionCategory::all();
        return view('pages.permissions.edit', compact('permission', 'categories'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name,' . $permission->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:permission_categories,id',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy(Permission $permission)
    {
        if (!auth()->user()->is_super_admin) {
            return redirect()->route('permissions.index')->with('error', 'Only Super Admin can delete permissions');
        }

        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
}
