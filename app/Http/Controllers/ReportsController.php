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
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ReportsController extends Controller
{
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
        $year = $request->get('year', now()->year);

        $netIncome   = Transaction::income()->whereYear('transaction_date', $year)->sum('amount')
                     - Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $capital     = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $withdrawals = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $equity      = $capital - $withdrawals + $netIncome;

        return view('pages.reports.balance-sheet', compact('netIncome', 'capital', 'withdrawals', 'equity', 'year'));
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
            ->orderBy('transaction_date')
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
            ->orderBy('created_at')
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
        $totalPayments = $payments->sum('amount');

        return view('pages.reports.receipt-payment', compact(
            'receipts', 'payments', 'totalReceipts', 'totalPayments', 'from', 'to'
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
        return view('pages.reports.chart-of-accounts', compact('groups'));
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
            'incomeByCategory', 'expenseByCategory',
            'totalIncome', 'totalExpense', 'surplus', 'year'
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

    // ── PDF exports ───────────────────────────────────────────────────────
    // ── Detailed Trial Balance ─────────────────────────────────
    private function buildDetailedTrialBalanceRows(int $year): array
    {
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder'])
            ->whereYear('transaction_date', $year)
            ->get();

        $rows = [];

        foreach ($transactions->where('type', 'income')->groupBy('income_category_id') as $catId => $group) {
            $rows[] = ['account' => 'Income — ' . ($group->first()->incomeCategory?->name ?? 'Uncategorised'), 'debit' => 0, 'credit' => $group->sum('amount')];
        }
        foreach ($transactions->where('type', 'expense')->groupBy('expense_category_id') as $catId => $group) {
            $rows[] = ['account' => 'Expense — ' . ($group->first()->expenseCategory?->name ?? 'Uncategorised'), 'debit' => $group->sum('amount'), 'credit' => 0];
        }
        foreach ($transactions->where('type', 'capital')->groupBy('shareholder_id') as $shId => $group) {
            $rows[] = ['account' => 'Capital — ' . ($group->first()->shareholder?->name ?? 'Shareholder'), 'debit' => 0, 'credit' => $group->sum('amount')];
        }
        foreach ($transactions->where('type', 'withdrawal')->groupBy('shareholder_id') as $shId => $group) {
            $rows[] = ['account' => 'Withdrawal — ' . ($group->first()->shareholder?->name ?? 'Shareholder'), 'debit' => $group->sum('amount'), 'credit' => 0];
        }

        return $rows;
    }

    public function detailedTrialBalance(Request $request)
    {
        $year = $request->get('year', now()->year);
        $rows = $this->buildDetailedTrialBalanceRows($year);
        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');
        return view('pages.reports.details-trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year'));
    }

    public function detailedTrialBalancePdf(Request $request)
    {
        $year = $request->get('year', now()->year);
        $rows = $this->buildDetailedTrialBalanceRows($year);
        $totalDebit  = collect($rows)->sum('debit');
        $totalCredit = collect($rows)->sum('credit');
        $this->makePdf('pages.reports.pdf.details-trial-balance', compact('rows', 'totalDebit', 'totalCredit', 'year'), 'details-trial-balance-' . $year);
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
        $year        = $request->get('year', now()->year);
        $netIncome   = Transaction::income()->whereYear('transaction_date', $year)->sum('amount')
                     - Transaction::expense()->whereYear('transaction_date', $year)->sum('amount');
        $capital     = Transaction::capital()->whereYear('transaction_date', $year)->sum('amount');
        $withdrawals = Transaction::withdrawal()->whereYear('transaction_date', $year)->sum('amount');
        $equity      = $capital - $withdrawals + $netIncome;
        $this->makePdf('pages.reports.pdf.balance-sheet', compact('netIncome', 'capital', 'withdrawals', 'equity', 'year'), 'balance-sheet-' . $year);
    }

    public function cashBookPdf(Request $request)
    {
        $from = $request->filled('from') ? Carbon::createFromFormat('d/m/Y', $request->from) : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::createFromFormat('d/m/Y', $request->to)   : now();
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->where('payment_method', 'Cash')->whereBetween('transaction_date', [$from, $to])->orderBy('transaction_date')->get();
        $totalIn  = $transactions->whereIn('type', ['income', 'capital'])->sum('amount');
        $totalOut = $transactions->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $this->makePdf('pages.reports.pdf.cash-book', compact('transactions', 'totalIn', 'totalOut', 'from', 'to'), 'cash-book-' . $from->format('Ymd') . '-' . $to->format('Ymd'), 'L');
    }

    public function dayBookPdf(Request $request)
    {
        $date = $request->filled('date') ? Carbon::createFromFormat('d/m/Y', $request->date) : now();
        $transactions = Transaction::with(['incomeCategory', 'expenseCategory', 'shareholder', 'transactionable'])
            ->whereDate('transaction_date', $date)->orderBy('created_at')->get();
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
        $payments = Transaction::with(['expenseCategory', 'shareholder'])->whereIn('type', ['expense', 'withdrawal'])
            ->whereBetween('transaction_date', [$from, $to])->get()
            ->groupBy(fn($t) => $t->type === 'withdrawal' ? 'Withdrawal — ' . ($t->shareholder?->name ?? 'Shareholder') : ($t->expenseCategory?->name ?? 'Uncategorised'))
            ->map(fn($rows, $head) => ['head' => $head, 'amount' => $rows->sum('amount')])->values();
        $totalReceipts = $receipts->sum('amount');
        $totalPayments = $payments->sum('amount');
        $this->makePdf('pages.reports.pdf.receipt-payment', compact('receipts', 'payments', 'totalReceipts', 'totalPayments', 'from', 'to'), 'receipt-payment-' . $from->format('Ymd') . '-' . $to->format('Ymd'));
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
        $this->makePdf('pages.reports.pdf.chart-of-accounts', compact('groups'), 'chart-of-accounts', 'L');
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
