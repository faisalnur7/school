<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Shareholder;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $transactions = (clone $query)->latest('transaction_date')->paginate(20)->withQueryString();

        [$totalIncome, $totalExpense, $totalCapital, $totalWithdrawal] = $this->totals($query);

        return view('pages.transactions.index', array_merge(
            compact('transactions', 'totalIncome', 'totalExpense', 'totalCapital', 'totalWithdrawal'),
            $this->filterData()
        ));
    }

    public function pdf(Request $request)
    {
        $query        = $this->buildQuery($request);
        $transactions = (clone $query)->latest('transaction_date')->get();

        [$totalIncome, $totalExpense, $totalCapital, $totalWithdrawal] = $this->totals($query);

        $html = view('pages.transactions.pdf', compact(
            'transactions', 'totalIncome', 'totalExpense', 'totalCapital', 'totalWithdrawal'
        ))->render();

        $mpdf = new Mpdf(['orientation' => 'L', 'margin_top' => 8, 'margin_bottom' => 8, 'margin_left' => 10, 'margin_right' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('transactions-' . now()->format('Ymd') . '.pdf', 'D');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function buildQuery(Request $request)
    {
        $query = Transaction::with(['shareholder', 'incomeCategory', 'expenseCategory', 'recorder'])
            ->whereIn('type', ['income', 'expense', 'capital', 'withdrawal']);

        if ($request->filled('type'))           { $query->where('type', $request->type); }
        if ($request->filled('payment_method')) { $query->where('payment_method', $request->payment_method); }
        if ($request->filled('shareholder_id')) { $query->where('shareholder_id', $request->shareholder_id); }
        if ($request->filled('category_id')) {
            $query->where(fn($q) => $q->where('income_category_id', $request->category_id)
                                      ->orWhere('expense_category_id', $request->category_id));
        }
        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', Carbon::createFromFormat('d/m/Y', $request->from));
        }
        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', Carbon::createFromFormat('d/m/Y', $request->to));
        }
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('reference_no', 'like', '%' . $request->search . '%')
                                      ->orWhere('description', 'like', '%' . $request->search . '%'));
        }

        return $query;
    }

    private function totals($query): array
    {
        return [
            (clone $query)->where('type', 'income')->sum('amount'),
            (clone $query)->where('type', 'expense')->sum('amount'),
            (clone $query)->where('type', 'capital')->sum('amount'),
            (clone $query)->where('type', 'withdrawal')->sum('amount'),
        ];
    }

    private function filterData(): array
    {
        return [
            'shareholders'      => Shareholder::orderBy('name')->get(),
            'incomeCategories'  => IncomeCategory::orderBy('name')->get(),
            'expenseCategories' => ExpenseCategory::orderBy('name')->get(),
            'paymentMethods'    => ['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'],
        ];
    }
}
