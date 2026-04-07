<?php

namespace App\Http\Controllers;

use App\Models\BudgetAllocation;
use App\Models\BudgetHead;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class BudgetAllocationController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $query = BudgetAllocation::with(['budgetHead', 'expenseCategory'])
            ->where('fiscal_year', $year)
            ->latest();

        $allocations  = $query->paginate(20)->withQueryString();
        $heads        = BudgetHead::orderBy('name')->get();
        $categories   = ExpenseCategory::where('is_active', true)->get();
        $totalBudget  = BudgetAllocation::where('fiscal_year', $year)->sum('amount');

        return view('pages.budget-allocations.index', compact(
            'allocations', 'heads', 'categories', 'year', 'totalBudget'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_head_id'      => 'required|exists:budget_heads,id',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'period'              => 'required|in:monthly,yearly',
            'fiscal_year'         => 'required|integer|min:2000|max:2100',
            'fiscal_month'        => 'nullable|integer|min:1|max:12',
            'notes'               => 'nullable|string',
        ]);

        BudgetAllocation::create(array_merge(
            $request->only('budget_head_id','expense_category_id','amount','period','fiscal_year','fiscal_month','notes'),
            ['recorded_by' => auth()->id()]
        ));

        return back()->with('success', 'Budget allocation saved.');
    }

    public function destroy(BudgetAllocation $budgetAllocation)
    {
        $budgetAllocation->delete();
        return back()->with('success', 'Allocation deleted.');
    }

    // ── Budget vs Actual Report ────────────────────────────────
    public function report(Request $request)
    {
        $year = $request->get('year', now()->year);

        $allocations = BudgetAllocation::with(['budgetHead', 'expenseCategory'])
            ->where('fiscal_year', $year)
            ->get()
            ->map(function ($a) {
                return [
                    'head'       => $a->budgetHead->name,
                    'category'   => $a->expenseCategory?->name ?? 'All',
                    'period'     => $a->period,
                    'budget'     => $a->amount,
                    'actual'     => $a->actual_spent,
                    'remaining'  => $a->remaining,
                    'utilization'=> $a->utilization,
                ];
            });

        $totalBudget = $allocations->sum('budget');
        $totalActual = $allocations->sum('actual');

        return view('pages.budget-allocations.report', compact(
            'allocations', 'totalBudget', 'totalActual', 'year'
        ));
    }
}
