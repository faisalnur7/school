<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('category')->latest()->get();
        $categories = AssetCategory::where('is_active', true)->get();
        return view('pages.assets.assets.index', compact('assets', 'categories'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->get();
        return view('pages.assets.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'required|in:active,inactive,disposed',
        ]);

        Asset::create($request->only('asset_category_id', 'name', 'description', 'status'));

        return redirect()->route('assets.index')->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $asset)
    {
        $categories = AssetCategory::where('is_active', true)->get();
        return view('pages.assets.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_category_id' => 'required|exists:asset_categories,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'status'            => 'required|in:active,inactive,disposed',
        ]);

        $asset->update($request->only('asset_category_id', 'name', 'description', 'status'));

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset deleted.');
    }
}
