<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountTransaction;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\HandCash;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\MobileBankingAccount;
use App\Models\InventorySale;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Payroll;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mpdf\Mpdf;

class ReportsController extends Controller
{
    private function supplierDuesQuery(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'payments'])
            ->orderByDesc('purchase_date')
            ->orderByDesc('id');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('purchase_date', '>=', Carbon::createFromFormat('d/m/Y', $request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('purchase_date', '<=', Carbon::createFromFormat('d/m/Y', $request->to));
        }

        return $query;
    }

    private function parseReportDate(?string $value, ?Carbon $default = null): Carbon
    {
        if (blank($value)) {
            return $default ?? now();
        }

        if (str_contains($value, '/')) {
            return Carbon::createFromFormat('d/m/Y', $value);
        }

        return Carbon::parse($value);
    }

    // ── Trial Balance ──────────────────────────────────────────
    public function trialBalance(Request $request)
    {
        $year = $request->get('year', now()->year);

        $totalIncome     = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense    = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $totalCapital    = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $totalWithdrawal = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $yearStart       = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $openingBalance  = $this->openingBalanceBefore($yearStart);
        $closingBalance  = $openingBalance + (($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal));

        $rows = [
            ['account' => 'Income',              'debit' => 0,                'credit' => $totalIncome],
            ['account' => 'Expenses',             'debit' => $totalExpense,    'credit' => 0],
            ['account' => 'Capital Contributions','debit' => 0,                'credit' => $totalCapital],
            ['account' => 'Drawings / Withdrawals','debit' => $totalWithdrawal,'credit' => 0],
        ];

        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');

        return view('pages.reports.trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year', 'openingBalance', 'closingBalance'));
    }

    // ── Balance Sheet ──────────────────────────────────────────
    public function balanceSheet(Request $request)
    {
        $year              = $request->get('year', now()->year);
        $yearEnd           = Carbon::createFromDate($year, 12, 31)->endOfDay();
        $totalIncome       = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense      = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $capital           = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $withdrawals       = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $supplierLiability = $this->supplierLiabilityAsOf($yearEnd);
        $totalLiabilities  = $supplierLiability;
        $netIncome         = $totalIncome - $totalExpense;
        $yearStart         = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $openingBalance    = $this->openingBalanceBefore($yearStart);
        $closingBalance    = $openingBalance + (($capital + $netIncome) - $withdrawals);
        $equity            = $closingBalance;

        return view('pages.reports.balance-sheet', compact(
            'totalIncome',
            'totalExpense',
            'netIncome',
            'capital',
            'withdrawals',
            'equity',
            'year',
            'supplierLiability',
            'totalLiabilities',
            'openingBalance',
            'closingBalance'
        ));
    }

    // ── Cash Book ──────────────────────────────────────────────
    public function cashBook(Request $request)
    {
        [$transactions, $totalIn, $totalOut, $from, $to, $incomeCategories, $expenseCategories, $selectedCategoryId, $selectedCategoryLabel, $summaryRows, $reportType, $openingBalance, $closingBalance] = $this->buildCashBookData($request);

        return view('pages.reports.cash-book', compact(
            'transactions',
            'totalIn',
            'totalOut',
            'from',
            'to',
            'incomeCategories',
            'expenseCategories',
            'selectedCategoryId',
            'selectedCategoryLabel',
            'summaryRows',
            'reportType',
            'openingBalance',
            'closingBalance'
        ));
    }

    // ── Day Book ───────────────────────────────────────────────
    public function dayBook(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::createFromFormat('d/m/Y', $request->date)
            : now();
        $reportType = $request->input('report_type', $request->input('view_mode', 'summary'));
        $reportType = $reportType === 'grouped' ? 'summary' : $reportType;

        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->whereDate('transaction_date', $date)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalDebit  = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $totalCredit = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $openingBalance = $this->openingBalanceBefore($date, false, null, null, false);
        $closingBalance = $openingBalance + ($totalCredit - $totalDebit);
        $summaryRows = $reportType === 'summary' ? $this->groupCashSummaryRows($transactions) : collect();

        return view('pages.reports.day-book', compact('transactions', 'totalDebit', 'totalCredit', 'date', 'reportType', 'summaryRows', 'openingBalance', 'closingBalance'));
    }

    // ── Cash Summary ───────────────────────────────────────────
    public function cashSummary(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay()
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay()
            : now()->endOfDay();

        $accounts = collect();

        foreach (HandCash::where('is_active', true)->get() as $acc) {
            $accounts->push($this->accountSummary($acc, HandCash::class, $from, $to));
        }
        foreach (BankAccount::where('is_active', true)->get() as $acc) {
            $accounts->push($this->accountSummary($acc, BankAccount::class, $from, $to));
        }
        foreach (MobileBankingAccount::where('is_active', true)->get() as $acc) {
            $accounts->push($this->accountSummary($acc, MobileBankingAccount::class, $from, $to));
        }

        return view('pages.reports.cash-summary', compact('accounts', 'from', 'to'));
    }

    private function accountSummary($acc, string $type, Carbon $from, Carbon $to): array
    {
        $base = AccountTransaction::where('account_type', $type)
            ->where('account_id', $acc->id);

        $openingBalance = (clone $base)
            ->where('transaction_date', '<', $from->toDateString())
            ->orderBy('id', 'desc')
            ->value('balance_after') ?? (float) ($acc->balance ?? $acc->opening_balance ?? $acc->opening_amount ?? 0);

        $totalIn  = (clone $base)->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->where('type', 'credit')->sum('amount');
        $totalOut = (clone $base)->whereBetween('transaction_date', [$from->toDateString(), $to->toDateString()])
            ->where('type', 'debit')->sum('amount');

        $closingBalance = $openingBalance + $totalIn - $totalOut;

        $label = match ($type) {
            HandCash::class             => $acc->label,
            BankAccount::class          => $acc->bank_name . ' — ' . $acc->account_number,
            MobileBankingAccount::class => $acc->provider  . ' — ' . $acc->account_number,
            default                     => 'Account',
        };

        return compact('label', 'openingBalance', 'totalIn', 'totalOut', 'closingBalance');
    }

    // ── Receipt & Payment Statement ────────────────────────────
    public function receiptPayment(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : now();

        $receipts = Transaction::with(['incomeCategory', 'shareholder'])
            ->whereIn('type', ['income', 'capital'])
            ->whereBetween('transaction_date', [$from, $to])
            ->get()
            ->groupBy(fn($t) => $t->type === 'capital'
                ? 'Capital — ' . ($t->shareholder?->name ?? 'Shareholder')
                : ($t->incomeCategory?->name ?? 'Uncategorised')
            )
            ->map(fn($rows, $head) => ['head' => $head, 'amount' => $rows->sum('amount')])
            ->values();

        $inventoryReceipts = Payment::with([
                'student',
                'inventorySale.items.inventoryItem.category',
                'inventoryDueItems.inventorySaleItem.inventoryItem.category',
            ])
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->where(function ($q) {
                $q->whereNotNull('inventory_sale_id')
                  ->orWhereHas('inventoryDueItems.inventorySaleItem');
            })
            ->get()
            ->map(function (Payment $payment) {
                $saleItems = $payment->inventorySale?->items ?? collect();
                $dueItems  = method_exists($payment, 'validInventoryDueItems')
                    ? $payment->validInventoryDueItems()
                    : ($payment->inventoryDueItems ?? collect())->filter(fn ($item) => $item->inventorySaleItem?->inventoryItem);

                $labels = $saleItems->map(function ($item) {
                    return ($item->inventoryItem?->name ?? 'Item')
                        . ($item->inventoryItem?->category?->name ? ' • ' . $item->inventoryItem->category->name : '');
                })->merge($dueItems->map(function ($item) {
                    $saleItem = $item->inventorySaleItem;
                    $inventoryItem = $saleItem?->inventoryItem;
                    return ($inventoryItem?->name ?? 'Item')
                        . ($inventoryItem?->category?->name ? ' • ' . $inventoryItem->category->name : '');
                }))->filter()->unique()->values();

                return [
                    'head' => 'Inventory Sale — ' . ($payment->receipt_no ?? '—'),
                    'subhead' => trim(
                        ($payment->student?->full_name_en ?? 'Unknown Student')
                        . ($labels->isNotEmpty() ? ' | ' . $labels->implode(', ') : '')
                    ),
                    'amount' => (float) $payment->inventory_received_amount,
                ];
            })
            ->filter(fn ($row) => $row['amount'] > 0)
            ->values();

        $payments = Transaction::with(['expenseCategory', 'shareholder'])
            ->whereIn('type', ['expense', 'withdrawal'])
            ->whereBetween('transaction_date', [$from, $to])
            ->get()
            ->groupBy(fn($t) => $t->type === 'withdrawal'
                ? 'Withdrawal — ' . ($t->shareholder?->name ?? 'Shareholder')
                : ($t->expenseCategory?->name ?? 'Uncategorised')
            )
            ->map(fn($rows, $head) => ['head' => $head, 'amount' => $rows->sum('amount')])
            ->values();

        $totalReceipts = $receipts->sum('amount');
        $totalInventoryReceipts = $inventoryReceipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $grandTotalReceipts = $totalReceipts + $totalInventoryReceipts;
        $openingBalance = $this->openingBalanceBefore($from, true) + $this->inventoryReceiptTotalBefore($from);
        $closingBalance  = $openingBalance + ($grandTotalReceipts - $totalPayments);

        return view('pages.reports.receipt-payment', compact(
            'receipts',
            'inventoryReceipts',
            'payments',
            'totalReceipts',
            'totalInventoryReceipts',
            'grandTotalReceipts',
            'totalPayments',
            'openingBalance',
            'closingBalance',
            'from',
            'to'
        ));
    }

    // ── Cash Flow Statement ────────────────────────────────────
    public function cashFlow(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : now()->startOfYear();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : now();

        // Operating: income & expenses
        $operatingIn  = Transaction::income()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $operatingOut = Transaction::expense()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $netOperating = $operatingIn - $operatingOut;

        // Financing: capital & withdrawals
        $financingIn  = Transaction::capital()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $financingOut = Transaction::withdrawal()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $netFinancing = $financingIn - $financingOut;

        $openingCash = $this->cashOpeningBalanceAsOf($from);

        $netChange    = $netOperating + $netFinancing;
        $closingCash  = $openingCash + $netChange;

        return view('pages.reports.cash-flow', compact(
            'operatingIn', 'operatingOut', 'netOperating',
            'financingIn', 'financingOut', 'netFinancing',
            'openingCash', 'netChange', 'closingCash',
            'from', 'to'
        ));
    }

    // ── Chart of Accounts with Balances ───────────────────────
    public function chartOfAccounts()
    {
        $groups = AccountGroup::with(['accounts.journalLines'])->orderBy('name')->get();
        $supplierLiability = $this->supplierLiabilityAsOf(now()->endOfDay());
        return view('pages.reports.chart-of-accounts', compact('groups', 'supplierLiability'));
    }

    // ── Supplier Due Report ────────────────────────────────────
    public function supplierDues(Request $request)
    {
        $query = $this->supplierDuesQuery($request);

        $purchases = (clone $query)->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();
        $allPurchases = (clone $query)->get();

        $totals = [
            'amount' => (float) $allPurchases->sum('total_amount'),
            'paid'   => (float) $allPurchases->sum('paid_amount'),
            'due'    => (float) $allPurchases->sum('due_amount'),
        ];

        return view('pages.reports.supplier-dues', compact('purchases', 'suppliers', 'totals'));
    }

    public function supplierDuesPdf(Request $request)
    {
        $query = $this->supplierDuesQuery($request);
        $purchases = (clone $query)->get();

        $totals = [
            'amount' => (float) $purchases->sum('total_amount'),
            'paid'   => (float) $purchases->sum('paid_amount'),
            'due'    => (float) $purchases->sum('due_amount'),
        ];

        $supplierName = 'All Suppliers';
        if ($request->filled('supplier_id')) {
            $supplierName = Supplier::find($request->supplier_id)?->name ?? 'Selected Supplier';
        }

        $statusLabel = $request->filled('status')
            ? ucfirst($request->status)
            : 'All Status';

        $dateParts = [];
        if ($request->filled('from')) {
            $dateParts[] = 'From: ' . Carbon::createFromFormat('d/m/Y', $request->from)->format('d M Y');
        }
        if ($request->filled('to')) {
            $dateParts[] = 'To: ' . Carbon::createFromFormat('d/m/Y', $request->to)->format('d M Y');
        }

        $subtitle = collect([
            $supplierName,
            $statusLabel,
            implode(' | ', $dateParts),
        ])->filter()->implode(' • ');

        $filename = 'supplier-dues';
        if ($request->filled('supplier_id')) {
            $filename .= '-supplier-' . $request->supplier_id;
        }
        if ($request->filled('status')) {
            $filename .= '-' . $request->status;
        }
        if ($request->filled('from')) {
            $filename .= '-from-' . Carbon::createFromFormat('d/m/Y', $request->from)->format('Ymd');
        }
        if ($request->filled('to')) {
            $filename .= '-to-' . Carbon::createFromFormat('d/m/Y', $request->to)->format('Ymd');
        }

        $this->makePdf('pages.reports.pdf.supplier-dues', compact('purchases', 'totals', 'subtitle'), $filename);
    }

    // ── Headwise Transaction List ──────────────────────────────
    public function headwiseTransactions(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : now();

        $incomeHeads = IncomeCategory::withSum(['transactions as total' => fn($q) =>
            $q->whereBetween('transaction_date', [$from, $to])
        ], 'amount')
        ->with(['transactions' => fn($q) =>
            $q->whereBetween('transaction_date', [$from, $to])->latest('transaction_date')
        ])
        ->having('total', '>', 0)
        ->get();

        $expenseHeads = ExpenseCategory::withSum(['transactions as total' => fn($q) =>
            $q->whereBetween('transaction_date', [$from, $to])
        ], 'amount')
        ->with(['transactions' => fn($q) =>
            $q->whereBetween('transaction_date', [$from, $to])->latest('transaction_date')
        ])
        ->having('total', '>', 0)
        ->get();

        $openingBalance = ((float) Income::where('income_date', '<', $from->toDateString())->sum('amount'))
            - ((float) Expense::where('expense_date', '<', $from->toDateString())->sum('amount'));
        $closingBalance = $openingBalance + ($incomeHeads->sum('total') - $expenseHeads->sum('total'));

        return view('pages.reports.headwise-transactions', compact(
            'incomeHeads', 'expenseHeads', 'openingBalance', 'closingBalance', 'from', 'to'
        ));
    }

    // ── Income & Expenditure ───────────────────────────────────
    public function incomeExpenditure(Request $request)
    {
        $from = $this->parseReportDate($request->get('from'), now()->startOfYear())->startOfDay();
        $to   = $this->parseReportDate($request->get('to'), now()->endOfYear())->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $incomeByCategory = Income::with('category')
            ->whereBetween('income_date', [$from, $to])
            ->get()
            ->groupBy('income_category_id')
            ->map(fn($rows) => [
                'name'   => $rows->first()->category?->name ?? 'Uncategorised',
                'amount' => $rows->sum('amount'),
            ])->values();

        $expenseByCategory = Expense::with('category')
            ->whereBetween('expense_date', [$from, $to])
            ->get()
            ->groupBy('expense_category_id')
            ->map(fn($rows) => [
                'name'   => $rows->first()->category?->name ?? 'Uncategorised',
                'amount' => $rows->sum('amount'),
            ])->values();

        $totalIncome  = $incomeByCategory->sum('amount');
        $totalExpense = $expenseByCategory->sum('amount');
        $surplus      = $totalIncome - $totalExpense;
        $openingBalance = ((float) Income::where('income_date', '<', $from->toDateString())->sum('amount'))
            - ((float) Expense::where('expense_date', '<', $from->toDateString())->sum('amount'));
        $closingBalance = $openingBalance + $surplus;

        return view('pages.reports.income-expenditure', compact(
            'incomeByCategory',
            'expenseByCategory',
            'totalIncome',
            'totalExpense',
            'surplus',
            'openingBalance',
            'closingBalance',
            'from',
            'to'
        ));
    }

    // ── PDF helper ────────────────────────────────────────────────────────
    private function makePdf(string $view, array $data, string $filename, string $orientation = 'P'): void
    {
        $html = view($view, $data)->render();
        $mpdf = new Mpdf([
            'orientation'   => $orientation,
            'margin_top'    => 8,
            'margin_bottom' => 8,
            'margin_left'   => 10,
            'margin_right'  => 10,
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename . '.pdf', 'D');
    }

    // ── Detailed Trial Balance ─────────────────────────────────
    public function buildDetailedTrialBalanceRows(string $from, string $to): array
    {
        // Transactions before the period = beginning balances
        $before = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder'])
            ->where('transaction_date', '<', $from)
            ->get();

        // Transactions within the period
        $period = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder'])
            ->whereBetween('transaction_date', [$from, $to])
            ->get();

        $accounts = [];

        $add = function ($key, $label, $bDebit, $bCredit, $pDebit, $pCredit) use (&$accounts) {
            if (!isset($accounts[$key])) {
                $accounts[$key] = ['account' => $label, 'beg_debit' => 0, 'beg_credit' => 0, 'per_debit' => 0, 'per_credit' => 0];
            }
            $accounts[$key]['beg_debit']  += $bDebit;
            $accounts[$key]['beg_credit'] += $bCredit;
            $accounts[$key]['per_debit']  += $pDebit;
            $accounts[$key]['per_credit'] += $pCredit;
        };

        // Income by category: always credit
        $bIncome = $before->where('type', 'income');
        $pIncome = $period->where('type', 'income');
        $incomeKeys = $bIncome->pluck('income_category_id')->merge($pIncome->pluck('income_category_id'))->unique();
        foreach ($incomeKeys as $key) {
            $bGroup = $bIncome->where('income_category_id', $key);
            $pGroup = $pIncome->where('income_category_id', $key);
            $first  = $bGroup->first() ?? $pGroup->first();
            $label  = 'Income — ' . ($first->incomeCategory?->name ?? 'Uncategorised');

            $add(
                'income_' . $key,
                $label,
                0,
                (float) $bGroup->sum('amount'),
                0,
                (float) $pGroup->sum('amount'),
            );
        }

        // Expense by category: always debit
        $bExpense = $before->where('type', 'expense');
        $pExpense = $period->where('type', 'expense');
        $expenseKeys = $bExpense->pluck('expense_category_id')->merge($pExpense->pluck('expense_category_id'))->unique();
        foreach ($expenseKeys as $key) {
            $bGroup = $bExpense->where('expense_category_id', $key);
            $pGroup = $pExpense->where('expense_category_id', $key);
            $first  = $bGroup->first() ?? $pGroup->first();
            $label  = 'Expense — ' . ($first->expenseCategory?->name ?? 'Uncategorised');

            $add(
                'expense_' . $key,
                $label,
                (float) $bGroup->sum('amount'),
                0,
                (float) $pGroup->sum('amount'),
                0,
            );
        }

        // Capital/withdrawal by shareholder (existing behavior)
        foreach (['capital', 'withdrawal'] as $type) {
            $bGroups = $before->where('type', $type);
            $pGroups = $period->where('type', $type);
            $allKeys = $bGroups->pluck('shareholder_id')->merge($pGroups->pluck('shareholder_id'))->unique();

            foreach ($allKeys as $key) {
                $bGroup = $bGroups->where('shareholder_id', $key);
                $pGroup = $pGroups->where('shareholder_id', $key);
                $first  = $bGroup->first() ?? $pGroup->first();

                $label = $type === 'capital'
                    ? 'Capital — ' . ($first->shareholder?->name ?? 'Shareholder')
                    : 'Withdrawal — ' . ($first->shareholder?->name ?? 'Shareholder');

                $isDebit = $type === 'withdrawal';
                $add(
                    $type . '_' . $key,
                    $label,
                    $isDebit ? (float) $bGroup->sum('amount') : 0,
                    $isDebit ? 0 : (float) $bGroup->sum('amount'),
                    $isDebit ? (float) $pGroup->sum('amount') : 0,
                    $isDebit ? 0 : (float) $pGroup->sum('amount'),
                );
            }
        }

        // Inventory Sales
        $bSales = InventorySale::with('items.inventoryItem.category')->where('created_at', '<', $from)->get();
        $pSales = InventorySale::with('items.inventoryItem.category')->whereBetween('created_at', [$from, $to . ' 23:59:59'])->get();

        $buildSaleTotals = fn($sales) => collect($sales)->flatMap->items->groupBy(fn($i) => $i->inventoryItem?->category?->name ?? 'Uncategorised')
            ->map(fn($g) => $g->sum('subtotal'));

        foreach ($buildSaleTotals($bSales)->keys()->merge($buildSaleTotals($pSales)->keys())->unique() as $cat) {
            $add('inv_sale_' . $cat, 'Inventory Sales — ' . $cat, 0, (float)($buildSaleTotals($bSales)[$cat] ?? 0), 0, (float)($buildSaleTotals($pSales)[$cat] ?? 0));
        }

        // Petty Cash & Bank — current balance only (shown as ending)
        foreach (HandCash::where('is_active', true)->orderBy('id')->get() as $hc) {
            $accounts['hc_' . $hc->id] = ['account' => 'Petty Cash — ' . $hc->label, 'beg_debit' => 0, 'beg_credit' => 0, 'per_debit' => 0, 'per_credit' => 0, 'balance_only' => (float)$hc->balance];
        }
        foreach (BankAccount::where('is_active', true)->orderBy('bank_name')->get() as $bank) {
            $accounts['bank_' . $bank->id] = ['account' => 'Bank — ' . $bank->bank_name . ' (' . $bank->account_number . ')', 'beg_debit' => 0, 'beg_credit' => 0, 'per_debit' => 0, 'per_credit' => 0, 'balance_only' => (float)$bank->balance];
        }

        return array_values($accounts);
    }

    public function detailedTrialBalance(Request $request)
    {
        $from = $this->parseReportDate($request->get('from'), now()->startOfMonth());
        $to   = $this->parseReportDate($request->get('to'), now());
        $rows = $this->buildDetailedTrialBalanceRows($from->toDateString(), $to->toDateString());
        return view('pages.reports.details-trial-balance', compact('rows', 'from', 'to'));
    }

    public function detailedTrialBalancePdf(Request $request)
    {
        $from = $this->parseReportDate($request->get('from'), now()->startOfMonth());
        $to   = $this->parseReportDate($request->get('to'), now());
        $rows = $this->buildDetailedTrialBalanceRows($from->toDateString(), $to->toDateString());
        $this->makePdf('pages.reports.pdf.details-trial-balance', compact('rows', 'from', 'to'), 'details-trial-balance-' . $from->format('Y-m-d') . '-' . $to->format('Y-m-d'));
    }

    public function trialBalancePdf(Request $request)
    {
        $year            = $request->get('year', now()->year);
        $totalIncome     = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense    = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $totalCapital    = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $totalWithdrawal = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $yearStart       = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $openingBalance  = $this->openingBalanceBefore($yearStart);
        $closingBalance  = $openingBalance + (($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal));
        $rows = [
            ['account' => 'Income',                'debit' => 0,                'credit' => $totalIncome],
            ['account' => 'Expenses',              'debit' => $totalExpense,    'credit' => 0],
            ['account' => 'Capital Contributions', 'debit' => 0,                'credit' => $totalCapital],
            ['account' => 'Drawings / Withdrawals','debit' => $totalWithdrawal, 'credit' => 0],
        ];
        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');
        $this->makePdf('pages.reports.pdf.trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year', 'openingBalance', 'closingBalance'), 'trial-balance-' . $year);
    }

    public function balanceSheetPdf(Request $request)
    {
        $year              = $request->get('year', now()->year);
        $yearEnd           = Carbon::createFromDate($year, 12, 31)->endOfDay();
        $totalIncome       = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense      = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $capital           = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $withdrawals       = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $supplierLiability = $this->supplierLiabilityAsOf($yearEnd);
        $totalLiabilities  = $supplierLiability;
        $netIncome         = $totalIncome - $totalExpense;
        $yearStart         = Carbon::createFromDate($year, 1, 1)->startOfDay();
        $openingBalance    = $this->openingBalanceBefore($yearStart);
        $closingBalance    = $openingBalance + (($capital + $netIncome) - $withdrawals);
        $equity            = $closingBalance;
        $this->makePdf('pages.reports.pdf.balance-sheet', compact(
            'totalIncome',
            'totalExpense',
            'netIncome',
            'capital',
            'withdrawals',
            'equity',
            'year',
            'supplierLiability',
            'totalLiabilities',
            'openingBalance',
            'closingBalance'
        ), 'balance-sheet-' . $year);
    }

    public function cashBookPdf(Request $request)
    {
        [$transactions, $totalIn, $totalOut, $from, $to, , , , $selectedCategoryLabel, $summaryRows, $reportType, $openingBalance, $closingBalance] = $this->buildCashBookData($request);

        $subtitle = $from->format('d/m/Y') . ' — ' . $to->format('d/m/Y');
        if ($selectedCategoryLabel) {
            $subtitle .= ' | Category: ' . $selectedCategoryLabel;
        }

        $this->makePdf(
            'pages.reports.pdf.cash-book',
            compact('transactions', 'totalIn', 'totalOut', 'from', 'to', 'subtitle', 'summaryRows', 'reportType', 'openingBalance', 'closingBalance'),
            'cash-book-' . $from->format('Ymd') . '-' . $to->format('Ymd'),
            'P'
        );
    }

    private function buildCashBookData(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : now();

        $incomeCategories = IncomeCategory::orderBy('name')->get();
        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        [$selectedCategoryType, $selectedCategoryId] = $this->parseCashBookCategoryFilter($request->input('category_id'));
        $reportType = $request->input('report_type', $request->input('view_mode', 'summary'));
        $reportType = $reportType === 'grouped' ? 'summary' : $reportType;

        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->where('payment_method', 'Cash')
            ->whereBetween('transaction_date', [$from, $to])
            ->when($selectedCategoryId, function ($query) use ($selectedCategoryId, $selectedCategoryType) {
                if ($selectedCategoryType === 'income') {
                    $query->where('type', 'income')->where('income_category_id', $selectedCategoryId);
                    return;
                }

                if ($selectedCategoryType === 'expense') {
                    $query->where('type', 'expense')->where('expense_category_id', $selectedCategoryId);
                    return;
                }

                $query->where(function ($categoryQuery) use ($selectedCategoryId) {
                    $categoryQuery->where(function ($incomeQuery) use ($selectedCategoryId) {
                        $incomeQuery->where('type', 'income')
                            ->where('income_category_id', $selectedCategoryId);
                    })->orWhere(function ($expenseQuery) use ($selectedCategoryId) {
                        $expenseQuery->where('type', 'expense')
                            ->where('expense_category_id', $selectedCategoryId);
                    });
                });
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalIn  = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $totalOut = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $openingBalance = $this->openingBalanceBefore($from, true, $selectedCategoryType, $selectedCategoryId);
        $closingBalance = $openingBalance + ($totalIn - $totalOut);
        $summaryRows = $reportType === 'summary' ? $this->groupCashSummaryRows($transactions) : collect();

        $selectedCategoryLabel = null;
        if ($selectedCategoryId) {
            $selectedCategoryLabel = match ($selectedCategoryType) {
                'income'  => $incomeCategories->firstWhere('id', $selectedCategoryId)?->name,
                'expense' => $expenseCategories->firstWhere('id', $selectedCategoryId)?->name,
                default   => $incomeCategories->firstWhere('id', $selectedCategoryId)?->name
                    ?? $expenseCategories->firstWhere('id', $selectedCategoryId)?->name,
            };
        }

        return [$transactions, $totalIn, $totalOut, $from, $to, $incomeCategories, $expenseCategories, $selectedCategoryId, $selectedCategoryLabel, $summaryRows, $reportType, $openingBalance, $closingBalance];
    }

    private function groupCashSummaryRows(Collection $transactions): Collection
    {
        return $transactions
            ->groupBy(fn (Transaction $txn) => $this->cashBookGroupKey($txn))
            ->map(function ($rows) {
                $first = $rows->first();

                $totalIn = $rows->whereIn('type', ['income', 'capital'])->sum('amount');
                $totalOut = $rows->whereIn('type', ['expense', 'withdrawal'])->sum('amount');

                return [
                    'label'        => $this->cashBookGroupLabel($first),
                    'transactions' => $rows,
                    'totalIn'      => $totalIn,
                    'totalOut'     => $totalOut,
                    'totalDebit'   => $totalOut,
                    'totalCredit'  => $totalIn,
                ];
            })
            ->sortBy(fn (array $group) => $this->cashBookGroupSortKey($group))
            ->values();
    }

    private function openingBalanceBefore(Carbon $date, bool $cashOnly = false, ?string $categoryType = null, ?int $categoryId = null, bool $includeBankOpeningBalance = true): float
    {
        $query = Transaction::query();
        $openingBalance = $cashOnly
            ? $this->cashOpeningSeedBalance()
            : $this->openingSeedBalance($includeBankOpeningBalance);

        if ($cashOnly) {
            $query->where('payment_method', 'Cash');
        }

        if ($categoryId) {
            if ($categoryType === 'income') {
                $query->where('type', 'income')->where('income_category_id', $categoryId);
            } elseif ($categoryType === 'expense') {
                $query->where('type', 'expense')->where('expense_category_id', $categoryId);
            } else {
                $query->where(function ($sub) use ($categoryId) {
                    $sub->where(function ($incomeQuery) use ($categoryId) {
                        $incomeQuery->where('type', 'income')->where('income_category_id', $categoryId);
                    })->orWhere(function ($expenseQuery) use ($categoryId) {
                        $expenseQuery->where('type', 'expense')->where('expense_category_id', $categoryId);
                    });
                });
            }
        }

        $before = (clone $query)
            ->whereDate('transaction_date', '<', $date->toDateString())
            ->get();

        $credit = $before->whereIn('type', ['income', 'capital'])->sum('amount');
        $debit  = $before->whereIn('type', ['expense', 'withdrawal'])->sum('amount');

        return $openingBalance + ($credit - $debit);
    }

    private function openingSeedBalance(bool $includeBankOpeningBalance = true): float
    {
        $balance = (float) HandCash::where('is_active', true)->sum('opening_amount');

        if ($includeBankOpeningBalance) {
            $balance += (float) BankAccount::where('is_active', true)->sum('opening_balance');
        }

        return $balance + (float) MobileBankingAccount::where('is_active', true)->sum('opening_balance');
    }

    private function cashOpeningSeedBalance(): float
    {
        return (float) HandCash::where('is_active', true)->sum('opening_amount');
    }

    private function pettyCashOpeningBalanceAsOf(Carbon $date): float
    {
        $opening = 0.0;

        foreach (HandCash::where('is_active', true)->get() as $acc) {
            $opening += $this->accountBalanceAsOf($acc, HandCash::class, $date);
        }

        return $opening;
    }

    private function cashBookGroupKey(Transaction $txn): string
    {
        return match ($txn->type) {
            'income'     => 'income:' . ($txn->incomeCategory?->id ?? 'uncategorised'),
            'expense'    => 'expense:' . ($txn->expenseCategory?->id ?? 'uncategorised'),
            'capital'    => 'capital:' . ($txn->shareholder?->id ?? 'uncategorised'),
            'withdrawal' => 'withdrawal:' . ($txn->shareholder?->id ?? 'uncategorised'),
            default      => 'other:uncategorised',
        };
    }

    private function cashBookGroupLabel(Transaction $txn): string
    {
        return match ($txn->type) {
            'income'     => 'Income - ' . ($txn->incomeCategory?->name ?? 'Uncategorised'),
            'expense'    => 'Expense - ' . ($txn->expenseCategory?->name ?? 'Uncategorised'),
            'capital'    => 'Capital - ' . ($txn->shareholder?->name ?? 'Uncategorised'),
            'withdrawal' => 'Withdrawal - ' . ($txn->shareholder?->name ?? 'Uncategorised'),
            default      => ucfirst($txn->type),
        };
    }

    private function cashBookGroupSortKey(array $group): string
    {
        $order = match (strtok($group['label'], ' ')) {
            'Income'     => 1,
            'Expense'    => 2,
            'Capital'    => 3,
            'Withdrawal' => 4,
            default      => 9,
        };

        return str_pad((string) $order, 2, '0', STR_PAD_LEFT) . '|' . strtolower($group['label']);
    }

    private function parseCashBookCategoryFilter(?string $value): array
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

    private function cashOpeningBalanceAsOf(Carbon $date): float
    {
        $opening = 0.0;

        foreach (HandCash::where('is_active', true)->get() as $acc) {
            $opening += $this->accountBalanceAsOf($acc, HandCash::class, $date);
        }

        foreach (BankAccount::where('is_active', true)->get() as $acc) {
            $opening += $this->accountBalanceAsOf($acc, BankAccount::class, $date);
        }

        foreach (MobileBankingAccount::where('is_active', true)->get() as $acc) {
            $opening += $this->accountBalanceAsOf($acc, MobileBankingAccount::class, $date);
        }

        return $opening;
    }

    private function accountBalanceAsOf($acc, string $type, Carbon $date): float
    {
        $base = AccountTransaction::where('account_type', $type)
            ->where('account_id', $acc->id);

        $balance = (clone $base)
            ->where('transaction_date', '<', $date->toDateString())
            ->orderBy('id', 'desc')
            ->value('balance_after');

        return (float) ($balance ?? $acc->balance ?? $acc->opening_balance ?? $acc->opening_amount ?? 0);
    }

    private function inventoryReceiptTotalBefore(Carbon $date): float
    {
        return (float) Payment::whereDate('payment_date', '<', $date->toDateString())
            ->where(function ($q) {
                $q->whereNotNull('inventory_sale_id')
                  ->orWhereHas('inventoryDueItems');
            })
            ->sum('inventory_received_amount');
    }

    public function dayBookPdf(Request $request)
    {
        $date = $request->filled('date') ? Carbon::createFromFormat('d/m/Y', $request->date) : now();
        $reportType = $request->input('report_type', $request->input('view_mode', 'summary'));
        $reportType = $reportType === 'grouped' ? 'summary' : $reportType;
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->whereDate('transaction_date', $date)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
        $totalDebit  = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $totalCredit = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $openingBalance = $this->openingBalanceBefore($date, false, null, null, false);
        $closingBalance = $openingBalance + ($totalCredit - $totalDebit);
        $summaryRows = $reportType === 'summary' ? $this->groupCashSummaryRows($transactions) : collect();
        $this->makePdf('pages.reports.pdf.day-book', compact('transactions', 'totalDebit', 'totalCredit', 'date', 'reportType', 'summaryRows', 'openingBalance', 'closingBalance'), 'day-book-' . $date->format('Ymd'), 'P');
    }

    public function incomeExpenditurePdf(Request $request)
    {
        $from = $this->parseReportDate($request->get('from'), now()->startOfYear())->startOfDay();
        $to   = $this->parseReportDate($request->get('to'), now()->endOfYear())->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        $incomeByCategory = Income::with('category')->whereBetween('income_date', [$from, $to])->get()
            ->groupBy('income_category_id')
            ->map(fn($rows) => ['name' => $rows->first()->category?->name ?? 'Uncategorised', 'amount' => $rows->sum('amount')])->values();
        $expenseByCategory = Expense::with('category')->whereBetween('expense_date', [$from, $to])->get()
            ->groupBy('expense_category_id')
            ->map(fn($rows) => ['name' => $rows->first()->category?->name ?? 'Uncategorised', 'amount' => $rows->sum('amount')])->values();
        $totalIncome  = $incomeByCategory->sum('amount');
        $totalExpense = $expenseByCategory->sum('amount');
        $surplus      = $totalIncome - $totalExpense;
        $openingBalance = ((float) Income::where('income_date', '<', $from->toDateString())->sum('amount'))
            - ((float) Expense::where('expense_date', '<', $from->toDateString())->sum('amount'));
        $closingBalance = $openingBalance + $surplus;
        $this->makePdf(
            'pages.reports.pdf.income-expenditure',
            compact('incomeByCategory', 'expenseByCategory', 'totalIncome', 'totalExpense', 'surplus', 'openingBalance', 'closingBalance', 'from', 'to'),
            'income-expenditure-' . $from->format('Ymd') . '-' . $to->format('Ymd')
        );
    }

    public function cashSummaryPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)->endOfDay()     : now()->endOfDay();
        $accounts = collect();
        foreach (HandCash::where('is_active', true)->get() as $acc)            { $accounts->push($this->accountSummary($acc, HandCash::class, $from, $to)); }
        foreach (BankAccount::where('is_active', true)->get() as $acc)         { $accounts->push($this->accountSummary($acc, BankAccount::class, $from, $to)); }
        foreach (MobileBankingAccount::where('is_active', true)->get() as $acc) { $accounts->push($this->accountSummary($acc, MobileBankingAccount::class, $from, $to)); }
        $this->makePdf('pages.reports.pdf.cash-summary', compact('accounts', 'from', 'to'), 'cash-summary-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
    }

    public function receiptPaymentPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from) : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)   : now();
        $receipts = Transaction::with(['incomeCategory', 'shareholder'])->whereIn('type', ['income', 'capital'])
            ->whereBetween('transaction_date', [$from, $to])->get()
            ->groupBy(fn($t) => $t->type === 'capital' ? 'Capital — ' . ($t->shareholder?->name ?? 'Shareholder') : ($t->incomeCategory?->name ?? 'Uncategorised'))
            ->map(fn($rows, $head) => ['head' => $head, 'amount' => $rows->sum('amount')])->values();
        $inventoryReceipts = Payment::with([
                'student',
                'inventorySale.items.inventoryItem.category',
                'inventoryDueItems.inventorySaleItem.inventoryItem.category',
            ])
            ->whereBetween('payment_date', [$from->toDateString(), $to->toDateString()])
            ->where(function ($q) {
                $q->whereNotNull('inventory_sale_id')
                  ->orWhereHas('inventoryDueItems.inventorySaleItem');
            })
            ->get()
            ->map(function (Payment $payment) {
                $saleItems = $payment->inventorySale?->items ?? collect();
                $dueItems  = method_exists($payment, 'validInventoryDueItems')
                    ? $payment->validInventoryDueItems()
                    : ($payment->inventoryDueItems ?? collect())->filter(fn ($item) => $item->inventorySaleItem?->inventoryItem);
                $labels = $saleItems->map(function ($item) {
                    return ($item->inventoryItem?->name ?? 'Item')
                        . ($item->inventoryItem?->category?->name ? ' • ' . $item->inventoryItem->category->name : '');
                })->merge($dueItems->map(function ($item) {
                    $saleItem = $item->inventorySaleItem;
                    $inventoryItem = $saleItem?->inventoryItem;
                    return ($inventoryItem?->name ?? 'Item')
                        . ($inventoryItem?->category?->name ? ' • ' . $inventoryItem->category->name : '');
                }))->filter()->unique()->values();

                return [
                    'head' => 'Inventory Sale — ' . ($payment->receipt_no ?? '—'),
                    'subhead' => trim(
                        ($payment->student?->full_name_en ?? 'Unknown Student')
                        . ($labels->isNotEmpty() ? ' | ' . $labels->implode(', ') : '')
                    ),
                    'amount' => (float) $payment->inventory_received_amount,
                ];
            })
            ->filter(fn ($row) => $row['amount'] > 0)
            ->values();
        $payments = Transaction::with(['expenseCategory', 'shareholder'])->whereIn('type', ['expense', 'withdrawal'])
            ->whereBetween('transaction_date', [$from, $to])->get()
            ->groupBy(fn($t) => $t->type === 'withdrawal' ? 'Withdrawal — ' . ($t->shareholder?->name ?? 'Shareholder') : ($t->expenseCategory?->name ?? 'Uncategorised'))
            ->map(fn($rows, $head) => ['head' => $head, 'amount' => $rows->sum('amount')])->values();
        $totalReceipts = $receipts->sum('amount');
        $totalInventoryReceipts = $inventoryReceipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $grandTotalReceipts = $totalReceipts + $totalInventoryReceipts;
        $this->makePdf('pages.reports.pdf.receipt-payment', compact('receipts', 'inventoryReceipts', 'payments', 'totalReceipts', 'totalInventoryReceipts', 'grandTotalReceipts', 'totalPayments', 'from', 'to'), 'receipt-payment-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
    }

    public function cashFlowPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from) : now()->startOfYear();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)   : now();
        $operatingIn  = Transaction::income()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $operatingOut = Transaction::expense()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $netOperating = $operatingIn - $operatingOut;
        $financingIn  = Transaction::capital()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $financingOut = Transaction::withdrawal()->whereBetween('transaction_date', [$from, $to])->sum('amount');
        $netFinancing = $financingIn - $financingOut;
        $openingCash  = HandCash::where('is_active', true)->sum('opening_amount')
                      + BankAccount::where('is_active', true)->sum('opening_balance')
                      + MobileBankingAccount::where('is_active', true)->sum('opening_balance');
        $netChange   = $netOperating + $netFinancing;
        $closingCash = $openingCash + $netChange;
        $this->makePdf('pages.reports.pdf.cash-flow', compact('operatingIn', 'operatingOut', 'netOperating', 'financingIn', 'financingOut', 'netFinancing', 'openingCash', 'netChange', 'closingCash', 'from', 'to'), 'cash-flow-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
    }

    public function chartOfAccountsPdf()
    {
        $groups = AccountGroup::with(['accounts.journalLines'])->orderBy('name')->get();
        $supplierLiability = $this->supplierLiabilityAsOf(now()->endOfDay());
        $this->makePdf('pages.reports.pdf.chart-of-accounts', compact('groups', 'supplierLiability'), 'chart-of-accounts', 'L');
    }

    private function supplierLiabilityAsOf(Carbon $asOf): float
    {
        return (float) PurchaseOrder::whereDate('purchase_date', '<=', $asOf->toDateString())
            ->sum('due_amount');
    }

    public function headwiseTransactionsPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from) : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)   : now();
        $incomeHeads = IncomeCategory::withSum(['transactions as total' => fn($q) => $q->whereBetween('transaction_date', [$from, $to])], 'amount')
            ->with(['transactions' => fn($q) => $q->whereBetween('transaction_date', [$from, $to])->latest('transaction_date')])
            ->having('total', '>', 0)->get();
        $expenseHeads = ExpenseCategory::withSum(['transactions as total' => fn($q) => $q->whereBetween('transaction_date', [$from, $to])], 'amount')
            ->with(['transactions' => fn($q) => $q->whereBetween('transaction_date', [$from, $to])->latest('transaction_date')])
            ->having('total', '>', 0)->get();
        $openingBalance = ((float) Income::where('income_date', '<', $from->toDateString())->sum('amount'))
            - ((float) Expense::where('expense_date', '<', $from->toDateString())->sum('amount'));
        $closingBalance = $openingBalance + ($incomeHeads->sum('total') - $expenseHeads->sum('total'));
        $this->makePdf('pages.reports.pdf.headwise-transactions', compact('incomeHeads', 'expenseHeads', 'openingBalance', 'closingBalance', 'from', 'to'), 'headwise-transactions-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
    }
}
