<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Transaction;
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

        $transactions = $query->paginate(20)->withQueryString();
        $shareholders = Shareholder::orderBy('name')->get();

        return view('pages.ledger.index', compact(
            'transactions', 'shareholders', 'totalDebit', 'totalCredit'
        ));
    }
}
