<?php

namespace App\Http\Controllers;

use App\Models\BudgetHead;
use Illuminate\Http\Request;

class BudgetHeadController extends Controller
{
    public function index()
    {
        $heads   = BudgetHead::with('parent')->latest()->paginate(20);
        $parents = BudgetHead::whereNull('parent_id')->get();
        return view('pages.budget-heads.index', compact('heads', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:budget_heads,id',
            'description' => 'nullable|string',
        ]);

        BudgetHead::create($request->only('name', 'parent_id', 'description'));

        return back()->with('success', 'Budget head created.');
    }

    public function update(Request $request, BudgetHead $budgetHead)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:budget_heads,id',
            'description' => 'nullable|string',
        ]);

        $budgetHead->update($request->only('name', 'parent_id', 'description'));

        return back()->with('success', 'Budget head updated.');
    }

    public function destroy(BudgetHead $budgetHead)
    {
        $budgetHead->delete();
        return back()->with('success', 'Budget head deleted.');
    }
}
