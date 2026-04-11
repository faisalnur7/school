<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\BudgetAllocation;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class BudgetAllocationController extends Controller
{
    public function index(Request $request)
    {
        $year        = $request->get('year', now()->year);
        $allocations = BudgetAllocation::with(['account.group', 'expenseCategory'])
            ->where('fiscal_year', $year)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalBudget = BudgetAllocation::where('fiscal_year', $year)->sum('amount');

        return view('pages.budget-allocations.index', array_merge(
            compact('allocations', 'year', 'totalBudget'),
            $this->formData()
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_id'          => 'required|exists:accounts,id',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'period'              => 'required|in:monthly,yearly',
            'fiscal_year'         => 'required|integer|min:2000|max:2100',
            'fiscal_month'        => 'nullable|integer|min:1|max:12',
            'notes'               => 'nullable|string',
        ]);

        BudgetAllocation::create(array_merge(
            $request->only('account_id', 'expense_category_id', 'amount', 'period', 'fiscal_year', 'fiscal_month', 'notes'),
            ['recorded_by' => auth()->id()]
        ));

        return back()->with('success', 'Budget allocation saved.');
    }

    public function update(Request $request, BudgetAllocation $budgetAllocation)
    {
        $request->validate([
            'account_id'          => 'required|exists:accounts,id',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'amount'              => 'required|numeric|min:0.01',
            'period'              => 'required|in:monthly,yearly',
            'fiscal_year'         => 'required|integer|min:2000|max:2100',
            'fiscal_month'        => 'nullable|integer|min:1|max:12',
            'notes'               => 'nullable|string',
        ]);

        $budgetAllocation->update(
            $request->only('account_id', 'expense_category_id', 'amount', 'period', 'fiscal_year', 'fiscal_month', 'notes')
        );

        return back()->with('success', 'Allocation updated.');
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

        $allocations = BudgetAllocation::with(['account.group', 'expenseCategory'])
            ->where('fiscal_year', $year)
            ->get()
            ->map(fn($a) => [
                'account'     => $a->account?->name ?? '—',
                'group'       => $a->account?->group?->name ?? '—',
                'category'    => $a->expenseCategory?->name ?? ($a->account?->reference_type === ExpenseCategory::class ? ExpenseCategory::find($a->account->reference_id)?->name : 'All'),
                'period'      => $a->period,
                'month'       => $a->fiscal_month ? date('M', mktime(0, 0, 0, $a->fiscal_month, 1)) : null,
                'budget'      => $a->amount,
                'actual'      => $a->actual_spent,
                'remaining'   => $a->remaining,
                'utilization' => $a->utilization,
            ]);

        $totalBudget = $allocations->sum('budget');
        $totalActual = $allocations->sum('actual');
        $overCount   = $allocations->filter(fn($a) => $a['actual'] > $a['budget'])->count();

        return view('pages.budget-allocations.report', compact(
            'allocations', 'totalBudget', 'totalActual', 'overCount', 'year'
        ));
    }

    // ── PDF Export ─────────────────────────────────────────────
    public function reportPdf(Request $request)
    {
        $year = $request->get('year', now()->year);

        $allocations = BudgetAllocation::with(['account.group', 'expenseCategory'])
            ->where('fiscal_year', $year)
            ->get()
            ->map(fn($a) => [
                'account'     => $a->account?->name ?? '—',
                'group'       => $a->account?->group?->name ?? '—',
                'category'    => $a->expenseCategory?->name ?? ($a->account?->reference_type === ExpenseCategory::class ? ExpenseCategory::find($a->account->reference_id)?->name : 'All'),
                'period'      => $a->period,
                'month'       => $a->fiscal_month ? date('M', mktime(0, 0, 0, $a->fiscal_month, 1)) : null,
                'budget'      => $a->amount,
                'actual'      => $a->actual_spent,
                'remaining'   => $a->remaining,
                'utilization' => $a->utilization,
            ]);

        $totalBudget = $allocations->sum('budget');
        $totalActual = $allocations->sum('actual');

        $html = view('pages.budget-allocations.pdf', compact('allocations', 'totalBudget', 'totalActual', 'year'))->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'margin_top' => 8, 'margin_bottom' => 8, 'margin_left' => 10, 'margin_right' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('budget-vs-actual-' . $year . '.pdf', 'D');
    }

    // ── Helpers ────────────────────────────────────────────────
    private function formData(): array
    {
        return [
            'accountGroups' => AccountGroup::with(['accounts' => fn($q) => $q->orderBy('name')])
                ->orderBy('name')->get(),
            'categories'    => ExpenseCategory::where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
