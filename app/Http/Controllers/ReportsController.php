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

    // ── Trial Balance ──────────────────────────────────────────
    public function trialBalance(Request $request)
    {
        $year = $request->get('year', now()->year);

        $totalIncome     = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense    = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $totalCapital    = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $totalWithdrawal = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');

        $rows = [
            ['account' => 'Income',              'debit' => 0,                'credit' => $totalIncome],
            ['account' => 'Expenses',             'debit' => $totalExpense,    'credit' => 0],
            ['account' => 'Capital Contributions','debit' => 0,                'credit' => $totalCapital],
            ['account' => 'Drawings / Withdrawals','debit' => $totalWithdrawal,'credit' => 0],
        ];

        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');

        return view('pages.reports.trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year'));
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
        $equity            = $capital + $netIncome - $withdrawals;

        return view('pages.reports.balance-sheet', compact(
            'totalIncome',
            'totalExpense',
            'netIncome',
            'capital',
            'withdrawals',
            'equity',
            'year',
            'supplierLiability',
            'totalLiabilities'
        ));
    }

    // ── Cash Book ──────────────────────────────────────────────
    public function cashBook(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::createFromFormat('d/m/Y', $request->from)
            : now()->startOfMonth();
        $to = $request->filled('to')
            ? Carbon::createFromFormat('d/m/Y', $request->to)
            : now();

        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->where('payment_method', 'Cash')
            ->whereBetween('transaction_date', [$from, $to])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalIn  = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $totalOut = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');

        return view('pages.reports.cash-book', compact('transactions', 'totalIn', 'totalOut', 'from', 'to'));
    }

    // ── Day Book ───────────────────────────────────────────────
    public function dayBook(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::createFromFormat('d/m/Y', $request->date)
            : now();

        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->whereDate('transaction_date', $date)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalDebit  = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $totalCredit = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');

        return view('pages.reports.day-book', compact('transactions', 'totalDebit', 'totalCredit', 'date'));
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
            ->value('balance_after') ?? (float) ($acc->opening_balance ?? $acc->opening_amount ?? 0);

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
                  ->orWhereHas('inventoryDueItems');
            })
            ->get()
            ->map(function (Payment $payment) {
                $saleItems = $payment->inventorySale?->items ?? collect();
                $dueItems  = $payment->inventoryDueItems ?? collect();

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

        return view('pages.reports.receipt-payment', compact(
            'receipts',
            'inventoryReceipts',
            'payments',
            'totalReceipts',
            'totalInventoryReceipts',
            'grandTotalReceipts',
            'totalPayments',
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

        // Opening cash = sum of all account opening balances
        $openingCash = HandCash::where('is_active', true)->sum('opening_amount')
                     + BankAccount::where('is_active', true)->sum('opening_balance')
                     + MobileBankingAccount::where('is_active', true)->sum('opening_balance');

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

        return view('pages.reports.headwise-transactions', compact(
            'incomeHeads', 'expenseHeads', 'from', 'to'
        ));
    }

    // ── Income & Expenditure ───────────────────────────────────
    public function incomeExpenditure(Request $request)
    {
        $year = $request->get('year', now()->year);

        $incomeByCategory = Income::with('category')
            ->whereYear('income_date', $year)
            ->get()
            ->groupBy('income_category_id')
            ->map(fn($rows) => [
                'name'   => $rows->first()->category?->name ?? 'Uncategorised',
                'amount' => $rows->sum('amount'),
            ])->values();

        $expenseByCategory = Expense::with('category')
            ->whereYear('expense_date', $year)
            ->get()
            ->groupBy('expense_category_id')
            ->map(fn($rows) => [
                'name'   => $rows->first()->category?->name ?? 'Uncategorised',
                'amount' => $rows->sum('amount'),
            ])->values();

        $totalIncome  = $incomeByCategory->sum('amount');
        $totalExpense = $expenseByCategory->sum('amount');
        $surplus      = $totalIncome - $totalExpense;

        return view('pages.reports.income-expenditure', compact(
            'incomeByCategory',
            'expenseByCategory',
            'totalIncome',
            'totalExpense',
            'surplus',
            'year'
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
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $rows = $this->buildDetailedTrialBalanceRows($from, $to);
        return view('pages.reports.details-trial-balance', compact('rows', 'from', 'to'));
    }

    public function detailedTrialBalancePdf(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->toDateString());
        $to   = $request->get('to',   now()->toDateString());
        $rows = $this->buildDetailedTrialBalanceRows($from, $to);
        $this->makePdf('pages.reports.pdf.details-trial-balance', compact('rows', 'from', 'to'), 'details-trial-balance-' . $from . '-' . $to);
    }

    public function trialBalancePdf(Request $request)
    {
        $year            = $request->get('year', now()->year);
        $totalIncome     = Transaction::income()->whereYear('transaction_date', $year)->sum('amount');
        $totalExpense    = Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $totalCapital    = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $totalWithdrawal = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $rows = [
            ['account' => 'Income',                'debit' => 0,                'credit' => $totalIncome],
            ['account' => 'Expenses',              'debit' => $totalExpense,    'credit' => 0],
            ['account' => 'Capital Contributions', 'debit' => 0,                'credit' => $totalCapital],
            ['account' => 'Drawings / Withdrawals','debit' => $totalWithdrawal, 'credit' => 0],
        ];
        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');
        $this->makePdf('pages.reports.pdf.trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year'), 'trial-balance-' . $year);
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
        $equity            = $capital + $netIncome - $withdrawals;
        $this->makePdf('pages.reports.pdf.balance-sheet', compact(
            'totalIncome',
            'totalExpense',
            'netIncome',
            'capital',
            'withdrawals',
            'equity',
            'year',
            'supplierLiability',
            'totalLiabilities'
        ), 'balance-sheet-' . $year);
    }

    public function cashBookPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from) : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)   : now();
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->where('payment_method', 'Cash')
            ->whereBetween('transaction_date', [$from, $to])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
        $totalIn  = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $totalOut = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $this->makePdf('pages.reports.pdf.cash-book', compact('transactions', 'totalIn', 'totalOut', 'from', 'to'), 'cash-book-' . $from->format('Ymd') . '-' . $to->format('Ymd'), 'L');
    }

    public function dayBookPdf(Request $request)
    {
        $date = $request->filled('date') ? Carbon::createFromFormat('d/m/Y', $request->date) : now();
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->whereDate('transaction_date', $date)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
        $totalDebit  = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $totalCredit = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $this->makePdf('pages.reports.pdf.day-book', compact('transactions', 'totalDebit', 'totalCredit', 'date'), 'day-book-' . $date->format('Ymd'), 'L');
    }

    public function incomeExpenditurePdf(Request $request)
    {
        $year = $request->get('year', now()->year);
        $incomeByCategory = Income::with('category')->whereYear('income_date', $year)->get()
            ->groupBy('income_category_id')
            ->map(fn($rows) => ['name' => $rows->first()->category?->name ?? 'Uncategorised', 'amount' => $rows->sum('amount')])->values();
        $expenseByCategory = Expense::with('category')->whereYear('expense_date', $year)->get()
            ->groupBy('expense_category_id')
            ->map(fn($rows) => ['name' => $rows->first()->category?->name ?? 'Uncategorised', 'amount' => $rows->sum('amount')])->values();
        $totalIncome  = $incomeByCategory->sum('amount');
        $totalExpense = $expenseByCategory->sum('amount');
        $surplus      = $totalIncome - $totalExpense;
        $this->makePdf('pages.reports.pdf.income-expenditure', compact('incomeByCategory', 'expenseByCategory', 'totalIncome', 'totalExpense', 'surplus', 'year'), 'income-expenditure-' . $year);
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
                  ->orWhereHas('inventoryDueItems');
            })
            ->get()
            ->map(function (Payment $payment) {
                $saleItems = $payment->inventorySale?->items ?? collect();
                $dueItems  = $payment->inventoryDueItems ?? collect();
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
        $this->makePdf('pages.reports.pdf.headwise-transactions', compact('incomeHeads', 'expenseHeads', 'from', 'to'), 'headwise-transactions-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
    }
}
