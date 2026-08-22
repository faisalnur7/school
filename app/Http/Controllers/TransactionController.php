<?php

namespace App\Http\Controllers;

use App\Models\HandCash;
use App\Models\ExpenseCategory;
use App\Models\IncomeCategory;
use App\Models\MobileBankingAccount;
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
        $pdfStyle = $this->normalizePdfStyle($request->input('pdf_style', 'new'));
        $view = $pdfStyle === 'old'
            ? 'pages.transactions.pdf-old'
            : 'pages.transactions.pdf';

        $html = view($view, $this->reportData($request, true) + ['pdfStyle' => $pdfStyle])->render();

        $mpdf = new Mpdf(['orientation' => 'P', 'margin_top' => 8, 'margin_bottom' => 8, 'margin_left' => 10, 'margin_right' => 10]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('transactions-' . $pdfStyle . '-' . now()->format('Ymd') . '.pdf', 'D');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function reportData(Request $request, bool $forPdf = false): array
    {
        $viewType = $this->normalizeViewType($request->input('view_type', 'detailed'));
        $pdfDescriptionTypes = $this->normalizePdfDescriptionTypes($request);
        $pdfStyle = $this->normalizePdfStyle($request->input('pdf_style', 'new'));
        $query = $this->buildQuery($request);

        [$totalIncome, $totalExpense, $totalCapital, $totalWithdrawal] = $this->totals($query);
        $openingBalance = $this->openingBalance($request);
        $closingBalance = $openingBalance + (($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal));

        $transactions = $forPdf
            ? $this->orderedQuery(clone $query)->get()
            : $this->orderedQuery(clone $query)->paginate(20)->withQueryString();
        $transactionCollection = $forPdf ? $transactions : $transactions->getCollection();
        $transactionGroups = $this->groupTransactions($transactionCollection, true);

        return [
            'viewType'          => $viewType,
            'pdfDescriptionTypes' => $pdfDescriptionTypes,
            'pdfStyle'          => $pdfStyle,
            'transactions'      => $transactions,
            'transactionGroups' => $transactionGroups,
            'totalIncome'       => $totalIncome,
            'totalExpense'      => $totalExpense,
            'totalCapital'      => $totalCapital,
            'totalWithdrawal'   => $totalWithdrawal,
            'openingBalance'    => $openingBalance,
            'closingBalance'    => $closingBalance,
            'reportFromDate'    => $this->parseReportDate($request->input('from')),
            'reportToDate'      => $this->parseReportDate($request->input('to')),
            'accountHeadLabel'  => $this->transactionAccountHeadLabel($request),
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

    private function buildQuery(Request $request, bool $applyDateFilters = true)
    {
        $query = Transaction::with(['shareholder', 'incomeCategory', 'expenseCategory', 'recorder'])
            ->whereIn('type', ['income', 'expense', 'capital', 'withdrawal']);

        if ($request->filled('type'))           { $query->where('type', $request->type); }
        if ($request->filled('shareholder_id')) { $query->where('shareholder_id', $request->shareholder_id); }
        [$categoryType, $categoryId] = $this->parseTransactionCategoryFilter($request->input('category_id'));
        if ($categoryId) {
            if ($categoryType === 'income') {
                $query->where('type', 'income')->where('income_category_id', $categoryId);
            } elseif ($categoryType === 'expense') {
                $query->where('type', 'expense')->where('expense_category_id', $categoryId);
            } else {
                $query->where(fn($q) => $q->where('income_category_id', $categoryId)
                                          ->orWhere('expense_category_id', $categoryId));
            }
        }
        if ($applyDateFilters && $request->filled('from')) {
            $query->whereDate('transaction_date', '>=', Carbon::createFromFormat('d/m/Y', $request->from));
        }
        if ($applyDateFilters && $request->filled('to')) {
            $query->whereDate('transaction_date', '<=', Carbon::createFromFormat('d/m/Y', $request->to));
        }
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('reference_no', 'like', '%' . $request->search . '%')
                                      ->orWhere('description', 'like', '%' . $request->search . '%'));
        }

        return $query;
    }

    private function openingBalance(Request $request): float
    {
        $openingBalance = $this->openingSeedBalance();

        if (! $request->filled('from')) {
            return $openingBalance;
        }

        $query = $this->buildQuery($request, false);
        $from = Carbon::createFromFormat('d/m/Y', $request->from)->toDateString();
        $before = (clone $query)->whereDate('transaction_date', '<', $from)->get();

        $credit = $before->whereIn('type', ['income', 'capital'])->sum('amount');
        $debit  = $before->whereIn('type', ['expense', 'withdrawal'])->sum('amount');

        return (float) ($openingBalance + ($credit - $debit));
    }

    private function openingSeedBalance(): float
    {
        return (float) HandCash::where('is_active', true)->sum('opening_amount')
            + MobileBankingAccount::where('is_active', true)->sum('opening_balance');
    }

    private function normalizePdfDescriptionTypes(Request $request): array
    {
        $allTypes = ['income', 'expense', 'capital', 'withdrawal'];

        if (! $request->boolean('pdf_desc_custom')) {
            return $allTypes;
        }

        $selected = array_values(array_intersect(
            $allTypes,
            (array) $request->input('pdf_description_types', [])
        ));

        return $selected;
    }

    private function normalizePdfStyle(?string $style): string
    {
        return match (strtolower(trim((string) $style))) {
            'old', 'classic', 'legacy' => 'old',
            default => 'new',
        };
    }

    private function parseReportDate(?string $date): ?Carbon
    {
        if (! $date) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $date);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function transactionAccountHeadLabel(Request $request): string
    {
        if ($request->filled('shareholder_id')) {
            $shareholder = Shareholder::find($request->input('shareholder_id'));

            if ($shareholder) {
                return $shareholder->name;
            }
        }

        [$categoryType, $categoryId] = $this->parseTransactionCategoryFilter($request->input('category_id'));

        if ($categoryId) {
            if ($categoryType === 'income') {
                return IncomeCategory::find($categoryId)?->name ?? 'Income';
            }

            if ($categoryType === 'expense') {
                return ExpenseCategory::find($categoryId)?->name ?? 'Expense';
            }
        }

        if ($request->filled('type')) {
            return ucfirst((string) $request->input('type'));
        }

        return 'All Transactions';
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

    private function parseTransactionCategoryFilter(?string $value): array
    {
        if (blank($value)) {
            return [null, null];
        }

        if (str_contains($value, ':')) {
            [$type, $id] = array_pad(explode(':', $value, 2), 2, null);
            $type = in_array($type, ['income', 'expense'], true) ? $type : null;
            $id = is_numeric($id) ? (int) $id : null;

            return [$type, $id];
        }

        return [null, is_numeric($value) ? (int) $value : null];
    }
}
