<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Shareholder;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Services\JournalService;
use App\Services\PettyCashService;
use Illuminate\Http\Request;

class ShareholderTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['shareholder', 'incomeCategory', 'expenseCategory', 'recorder'])
            ->whereIn('type', ['income', 'expense', 'capital', 'withdrawal']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('shareholder_id')) {
            $query->where('shareholder_id', $request->shareholder_id);
        }

        if ($request->filled('category_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('income_category_id', $request->category_id)
                  ->orWhere('expense_category_id', $request->category_id);
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->to));
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();
        
        $shareholders     = Shareholder::orderBy('name')->get();
        $incomeCategories = IncomeCategory::orderBy('name')->get();
        $expenseCategories = ExpenseCategory::orderBy('name')->get();

        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');
        $totalCapital = (clone $query)->where('type', 'capital')->sum('amount');
        $totalWithdrawal = (clone $query)->where('type', 'withdrawal')->sum('amount');

        return view('pages.shareholder-transactions.index', compact(
            'transactions', 'shareholders', 'incomeCategories', 'expenseCategories',
            'totalIncome', 'totalExpense', 'totalCapital', 'totalWithdrawal'
        ));
    }

    public function create()
    {
        $shareholders = Shareholder::orderBy('name')->get();
        return view('pages.shareholder-transactions.create', compact('shareholders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shareholder_id'   => 'required|exists:shareholders,id',
            'type'             => 'required|in:capital,withdrawal',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date_format:d/m/Y',
            'payment_method'   => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'account_id'       => 'required|integer',
            'account_type'     => 'nullable|in:App\\Models\\HandCash,App\\Models\\BankAccount,App\\Models\\MobileBankingAccount',
            'description'      => 'nullable|string|max:500',
        ]);

        $txn = Transaction::create([
            'reference_no'         => Transaction::generateReference(),
            'type'                 => $request->type,
            'shareholder_id'       => $request->shareholder_id,
            'transactionable_type' => Shareholder::class,
            'transactionable_id'   => $request->shareholder_id,
            'amount'               => $request->amount,
            'transaction_date'     => \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date)->format('Y-m-d'),
            'payment_method'       => $request->payment_method,
            'description'          => $request->filled('description')
                                        ? $request->description
                                        : ($request->type === 'capital' ? 'Capital investment' : 'Withdrawal / Drawing'),
            'recorded_by'          => auth()->id(),
        ]);

        if ($request->filled('account_type') && $request->filled('account_id')) {
            AccountTransaction::record(
                $request->account_type,
                $request->account_id,
                $request->type === 'capital' ? 'credit' : 'debit',
                $request->amount,
                $request->type,
                $txn->reference_no,
                $txn->description,
                \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date),
                Transaction::class,
                $txn->id,
                auth()->id()
            );
        } else {
            // No explicit account — route through petty cash
            if ($request->type === 'capital') {
                PettyCashService::credit(
                    (float) $request->amount, 'capital', $txn->reference_no, $txn->description,
                    \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date),
                    Transaction::class, $txn->id
                );
            } else {
                PettyCashService::debit(
                    (float) $request->amount, 'withdrawal', $txn->reference_no, $txn->description,
                    \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date),
                    Transaction::class, $txn->id
                );
            }
        }

        $cashAccountId       = Account::resolveForSource($request->account_type ?? '', (int) $request->account_id);
        $shareholderAccountId = Account::resolveForSource(Shareholder::class, (int) $request->shareholder_id);
        $date                = \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date)->toDateString();

        if ($request->type === 'capital') {
            // Dr Cash/Bank, Cr Capital (Shareholder)
            JournalService::postSafe($date, 'Capital — ' . optional($txn->shareholder)->name, [
                ['account_id' => $cashAccountId,        'debit' => (float) $request->amount, 'credit' => 0],
                ['account_id' => $shareholderAccountId, 'debit' => 0, 'credit' => (float) $request->amount],
            ], Transaction::class, $txn->id, auth()->id());
        } else {
            // Dr Drawings (Shareholder), Cr Cash/Bank
            JournalService::postSafe($date, 'Withdrawal — ' . optional($txn->shareholder)->name, [
                ['account_id' => $shareholderAccountId, 'debit' => (float) $request->amount, 'credit' => 0],
                ['account_id' => $cashAccountId,        'debit' => 0, 'credit' => (float) $request->amount],
            ], Transaction::class, $txn->id, auth()->id());
        }

        return redirect()->route('shareholder-transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function edit(Transaction $shareholderTransaction)
    {
        $shareholders = Shareholder::orderBy('name')->get();
        return view('pages.shareholder-transactions.edit', [
            'transaction'  => $shareholderTransaction,
            'shareholders' => $shareholders,
        ]);
    }

    public function update(Request $request, Transaction $shareholderTransaction)
    {
        $request->validate([
            'shareholder_id'   => 'required|exists:shareholders,id',
            'type'             => 'required|in:capital,withdrawal',
            'amount'           => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date_format:d/m/Y',
            'payment_method'   => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'account_id'       => 'nullable|integer',
            'account_type'     => 'nullable|in:App\\Models\\HandCash,App\\Models\\BankAccount,App\\Models\\MobileBankingAccount',
            'description'      => 'nullable|string|max:500',
        ]);

        $shareholderTransaction->update([
            'type'                 => $request->type,
            'shareholder_id'       => $request->shareholder_id,
            'transactionable_type' => Shareholder::class,
            'transactionable_id'   => $request->shareholder_id,
            'amount'               => $request->amount,
            'transaction_date'     => \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date)->format('Y-m-d'),
            'payment_method'       => $request->payment_method,
            'description'          => $request->filled('description')
                                        ? $request->description
                                        : ($request->type === 'capital' ? 'Capital investment' : 'Withdrawal / Drawing'),
        ]);

        if ($request->filled('account_type') && $request->filled('account_id')) {
            AccountTransaction::upsertForSource(
                $request->account_type,
                $request->account_id,
                $request->type === 'capital' ? 'credit' : 'debit',
                $request->amount,
                $request->type,
                $shareholderTransaction->reference_no,
                $shareholderTransaction->description,
                \Carbon\Carbon::createFromFormat('d/m/Y', $request->transaction_date),
                Transaction::class,
                $shareholderTransaction->id,
                auth()->id()
            );
        } else {
            AccountTransaction::removeSource(Transaction::class, $shareholderTransaction->id);
        }

        return redirect()->route('shareholder-transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $shareholderTransaction)
    {
        AccountTransaction::removeSource(Transaction::class, $shareholderTransaction->id);
        $shareholderTransaction->delete();

        return redirect()->route('shareholder-transactions.index')->with('success', 'Transaction deleted.');
    }
}
