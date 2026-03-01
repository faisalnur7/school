<?php

namespace App\Http\Controllers;

use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    public function index()
    {
        $data['categories'] = IncomeCategory::latest()->paginate(10);
        return view('pages.income-categories.index', $data);
    }

    public function create()
    {
        $data['categories'] = IncomeCategory::latest()->paginate(10);
        return view('pages.income-categories.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:income_categories,name',
            'slug'        => 'nullable|string|max:255|unique:income_categories,slug',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        IncomeCategory::create([
            'name'        => $request->name,
            'slug'        => $request->slug ?? str()->slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('income-categories.index')
                         ->with('success', 'Income category created successfully.');
    }

    public function edit(IncomeCategory $incomeCategory)
    {
        $categories = IncomeCategory::latest()->paginate(10);
        return view('pages.income-categories.edit', compact('incomeCategory','categories'));
    }

    public function update(Request $request, IncomeCategory $incomeCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:income_categories,name,' . $incomeCategory->id,
            'slug'        => 'nullable|string|max:255|unique:income_categories,slug,' . $incomeCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $incomeCategory->update([
            'name'        => $request->name,
            'slug'        => $request->slug ?? str()->slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('income-categories.index')
                         ->with('success', 'Income category updated successfully.');
    }

    public function destroy(IncomeCategory $incomeCategory)
    {
        $incomeCategory->delete();

        return redirect()->route('income-categories.index')
                         ->with('success', 'Income category deleted successfully.');
    }
}