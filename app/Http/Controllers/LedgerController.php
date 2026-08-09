<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with([
            'incomeCategory',
            'expenseCategory',
            'shareholder',
            'transactionable',
        ])->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('shareholder_id')) {
            $query->where('shareholder_id', $request->shareholder_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=',
                \Carbon\Carbon::createFromFormat('d/m/Y', $request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=',
                \Carbon\Carbon::createFromFormat('d/m/Y', $request->to));
        }

        $totalDebit  = (clone $query)->whereIn('type', ['expense', 'withdrawal'])->sum('amount');
        $totalCredit = (clone $query)->whereIn('type', ['income', 'capital'])->sum('amount');
        $openingBalance = 0.0;

        if ($request->filled('from')) {
            $from = Carbon::createFromFormat('d/m/Y', $request->from)->toDateString();
            $openingQuery = Transaction::query()
                ->whereIn('type', ['income', 'expense', 'capital', 'withdrawal']);

            if ($request->filled('type')) {
                $openingQuery->where('type', $request->type);
            }

            if ($request->filled('payment_method')) {
                $openingQuery->where('payment_method', $request->payment_method);
            }

            if ($request->filled('shareholder_id')) {
                $openingQuery->where('shareholder_id', $request->shareholder_id);
            }

            $openingBalance = (clone $openingQuery)
                ->whereDate('transaction_date', '<', $from)
                ->get()
                ->sum(fn (Transaction $txn) => in_array($txn->type, ['income', 'capital']) ? $txn->amount : -$txn->amount);
        }

        $closingBalance = $openingBalance + ($totalCredit - $totalDebit);

        $transactions = $query->paginate(20)->withQueryString();
        $shareholders = Shareholder::orderBy('name')->get();

        return view('pages.ledger.index', compact(
            'transactions', 'shareholders', 'totalDebit', 'totalCredit', 'openingBalance', 'closingBalance'
        ));
    }
}
