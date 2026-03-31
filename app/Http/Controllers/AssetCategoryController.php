<?php

namespace App\Http\Controllers;

use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::withCount('assets')->latest()->get();
        return view('pages.assets.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        AssetCategory::create($request->only('name', 'description'));

        return redirect()->route('asset-categories.index')->with('success', 'Category created successfully.');
    }

    public function update(Request $request, AssetCategory $assetCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $assetCategory->update($request->only('name', 'description'));

        return redirect()->route('asset-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(AssetCategory $assetCategory)
    {
        $assetCategory->delete();
        return redirect()->route('asset-categories.index')->with('success', 'Category deleted.');
    }
}
