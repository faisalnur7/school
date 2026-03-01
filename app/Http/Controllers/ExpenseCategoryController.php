<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $data['categories'] = ExpenseCategory::latest()->paginate(10);
        return view('pages.expense-categories.index', $data);
    }

    public function create()
    {
        $data['categories'] = ExpenseCategory::latest()->paginate(10);
        return view('pages.expense-categories.create',$data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:expense_categories,name',
            'slug'        => 'nullable|string|max:255|unique:expense_categories,slug',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        ExpenseCategory::create([
            'name'        => $request->name,
            'slug'        => $request->slug ?? str()->slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Expense category created successfully.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $categories = ExpenseCategory::latest()->paginate(10);
        return view('pages.expense-categories.edit', compact('expenseCategory','categories'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:expense_categories,name,' . $expenseCategory->id,
            'slug'        => 'nullable|string|max:255|unique:expense_categories,slug,' . $expenseCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $expenseCategory->update([
            'name'        => $request->name,
            'slug'        => $request->slug ?? str()->slug($request->name),
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return redirect()->route('expense-categories.index')
                         ->with('success', 'Expense category deleted successfully.');
    }
}