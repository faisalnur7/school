<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\Shareholder;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mpdf\Mpdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.transactions.index', array_merge(
            $this->reportData($request),
            $this->filterData()
        ));
    }

    public function pdf(Request $request)
    {
        $html = view('pages.transactions.pdf', $this->reportData($request, true))->render();

        $mpdf = new Mpdf(['orientation' => 'P', 'margin_top' => 8, 'margin_bottom' => 8, 'margin_left' => 10, 'margin_right' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('transactions-' . now()->format('Ymd') . '.pdf', 'D');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function reportData(Request $request, bool $forPdf = false): array
    {
        $viewType = $this->normalizeViewType($request->input('view_type', 'detailed'));
        $query = $this->buildQuery($request);

        [$totalIncome, $totalExpense, $totalCapital, $totalWithdrawal] = $this->totals($query);

        $transactions = $forPdf
            ? $this->orderedQuery(clone $query)->get()
            : $this->orderedQuery(clone $query)->paginate(20)->withQueryString();
        $transactionCollection = $forPdf ? $transactions : $transactions->getCollection();
        $transactionGroups = $this->groupTransactions($transactionCollection, true);

        return [
            'viewType'          => $viewType,
            'transactions'      => $transactions,
            'transactionGroups' => $transactionGroups,
            'totalIncome'       => $totalIncome,
            'totalExpense'      => $totalExpense,
            'totalCapital'      => $totalCapital,
            'totalWithdrawal'   => $totalWithdrawal,
        ];
    }

    private function normalizeViewType(?string $viewType): string
    {
        return match ($viewType) {
            'summary', 'categorized_detailed_no_description', 'categorized_summary' => 'summary',
            'detailed', 'categorized_detailed' => 'detailed',
            default => 'detailed',
        };
    }

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

    private function orderedQuery($query)
    {
        return $query
            ->orderByRaw("CASE type WHEN 'income' THEN 1 WHEN 'expense' THEN 2 WHEN 'capital' THEN 3 WHEN 'withdrawal' THEN 4 ELSE 9 END")
            ->orderByRaw("CASE
                WHEN type = 'income' THEN COALESCE(income_category_id, 0)
                WHEN type = 'expense' THEN COALESCE(expense_category_id, 0)
                WHEN type IN ('capital', 'withdrawal') THEN COALESCE(shareholder_id, 0)
                ELSE 0
            END")
            ->orderBy('transaction_date')
            ->orderBy('id');
    }

    private function groupTransactions(Collection $transactions, bool $withRows = true): Collection
    {
        return $transactions
            ->groupBy(fn (Transaction $txn) => $this->groupKey($txn))
            ->map(function (Collection $rows) use ($withRows) {
                $first = $rows->first();

                $group = [
                    'label'       => $this->groupLabel($first),
                    'type'        => $first->type,
                    'count'       => $rows->count(),
                    'totalDebit'  => $rows->whereIn('type', ['expense', 'withdrawal'])->sum('amount'),
                    'totalCredit' => $rows->whereIn('type', ['income', 'capital'])->sum('amount'),
                ];

                if ($withRows) {
                    $group['rows'] = $rows->values();
                }

                return $group;
            })
            ->sortBy(fn (array $group) => $this->groupSortKey($group))
            ->values();
    }

    private function groupKey(Transaction $txn): string
    {
        return match ($txn->type) {
            'income'     => 'income:' . ($txn->incomeCategory?->id ?? 'uncategorised'),
            'expense'    => 'expense:' . ($txn->expenseCategory?->id ?? 'uncategorised'),
            'capital'    => 'capital:' . ($txn->shareholder?->id ?? 'uncategorised'),
            'withdrawal' => 'withdrawal:' . ($txn->shareholder?->id ?? 'uncategorised'),
            default      => 'other:uncategorised',
        };
    }

    private function groupLabel(Transaction $txn): string
    {
        return match ($txn->type) {
            'income'     => 'Income - ' . ($txn->incomeCategory?->name ?? 'Uncategorised'),
            'expense'    => 'Expense - ' . ($txn->expenseCategory?->name ?? 'Uncategorised'),
            'capital'    => 'Capital - ' . ($txn->shareholder?->name ?? 'Uncategorised'),
            'withdrawal' => 'Withdrawal - ' . ($txn->shareholder?->name ?? 'Uncategorised'),
            default      => ucfirst($txn->type),
        };
    }

    private function groupSortKey(array $group): string
    {
        $order = match ($group['type']) {
            'income'     => 1,
            'expense'    => 2,
            'capital'    => 3,
            'withdrawal' => 4,
            default      => 9,
        };

        return str_pad((string) $order, 2, '0', STR_PAD_LEFT) . '|' . strtolower($group['label']);
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
        ];
    }
}
