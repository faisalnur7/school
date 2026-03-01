<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    /**
     * Display listing + create form
     */
    public function index()
    {
        $feeCategories = FeeCategory::latest()->get();
        return view('pages.fee_categories.index', compact('feeCategories'));
    }

    public function create()
    {
        $feeCategories = FeeCategory::latest()->get();
        return view('pages.fee_categories.create', compact('feeCategories'));
    }

    /**
     * Store new fee category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'bn_name'     => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        FeeCategory::create([
            'name'        => $request->name,
            'bn_name'     => $request->bn_name,
            'description' => $request->description,
            'status'      => 1,
        ]);

        return redirect()->back()->with('success', 'Fee category created successfully.');
    }

    /**
     * Edit page
     */
    public function edit($id)
    {
        $feeCategory   = FeeCategory::findOrFail($id);
        $feeCategories = FeeCategory::latest()->get();

        return view('pages.fee_categories.edit', compact('feeCategory', 'feeCategories'));
    }

    /**
     * Update fee category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'bn_name'     => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $feeCategory = FeeCategory::findOrFail($id);

        $feeCategory->update([
            'name'        => $request->name,
            'bn_name'     => $request->bn_name,
            'description' => $request->description,
        ]);

        return redirect()->route('fee-categories.index')
                         ->with('success', 'Fee category updated successfully.');
    }

    /**
     * Toggle status
     */
    public function toggleStatus($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->status = ! $feeCategory->status;
        $feeCategory->save();

        return redirect()->back();
    }

    /**
     * Delete fee category
     */
    public function destroy($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->delete();

        return redirect()->back()->with('success', 'Fee category deleted successfully.');
    }
}
