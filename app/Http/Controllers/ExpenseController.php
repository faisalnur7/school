<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use App\Models\SchoolSetting;
use App\Services\JournalService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('category', 'recorder', 'approver')->latest('expense_date');

        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category);
        }

        if ($request->filled('from')) {
            $query->whereDate('expense_date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('expense_date', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->to));
        }

        $total      = (clone $query)->sum('amount');
        $expenses   = $query->paginate(15)->withQueryString();
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('pages.expenses.index', compact('expenses', 'categories', 'total'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $expenses   = Expense::with('category')->latest('expense_date')->paginate(15);
        $total      = Expense::sum('amount');
        return view('pages.expenses.create', compact('categories', 'expenses', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date_format:d/m/Y',
            'payment_method'      => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'account_id'          => 'nullable|integer',
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'account_type', 'account_id', 'reference_no', 'description',
        ]);

        $data['expense_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');
        $data['recorded_by']  = auth()->id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense = Expense::create($data);

        Transaction::create([
            'reference_no'         => Transaction::generateReference(),
            'type'                 => 'expense',
            'expense_category_id'  => $expense->expense_category_id,
            'amount'               => $expense->amount,
            'description'          => $expense->description,
            'transaction_date'     => $expense->expense_date,
            'payment_method'       => $expense->payment_method,
            'transactionable_type' => Expense::class,
            'transactionable_id'   => $expense->id,
            'recorded_by'          => auth()->id(),
        ]);

        JournalService::postSafe(
            $expense->expense_date->toDateString(),
            $expense->title,
            [
                ['account_id' => Account::resolveForSource(ExpenseCategory::class, $expense->expense_category_id), 'debit' => (float) $expense->amount, 'credit' => 0],
                ['account_id' => Account::resolveForSource($expense->account_type ?? '', $expense->account_id ?? 0), 'debit' => 0, 'credit' => (float) $expense->amount],
            ],
            Expense::class,
            $expense->id,
            auth()->id()
        );

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $expenses   = Expense::with('category')->latest('expense_date')->paginate(15);
        $total      = Expense::sum('amount');
        return view('pages.expenses.edit', compact('expense', 'categories', 'expenses', 'total'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date_format:d/m/Y',
            'payment_method'      => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'account_id'          => 'nullable|integer',
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'account_type', 'account_id', 'reference_no', 'description',
        ]);

        $data['expense_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');

        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                \Storage::disk('public')->delete($expense->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        Transaction::updateOrCreate(
            ['transactionable_type' => Expense::class, 'transactionable_id' => $expense->id],
            [
                'reference_no'         => $expense->reference_no ?: Transaction::generateReference(),
                'type'                 => 'expense',
                'expense_category_id'  => $expense->expense_category_id,
                'amount'               => $expense->amount,
                'description'          => $expense->description,
                'transaction_date'     => $expense->expense_date,
                'payment_method'       => $expense->payment_method,
                'recorded_by'          => auth()->id(),
            ]
        );

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->attachment) {
            \Storage::disk('public')->delete($expense->attachment);
        }

        AccountTransaction::removeSource(Expense::class, $expense->id);

        Transaction::where('transactionable_type', Expense::class)
            ->where('transactionable_id', $expense->id)
            ->delete();

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    public function voucher(Expense $expense)
    {
        $setting = SchoolSetting::current();

        $fromAccountName = $expense->account_type
            ? class_basename($expense->account_type)
            : 'Cash / Petty Cash';

        $rows = [[
            'description' => $expense->title ?: ($expense->description ?? 'Expense paid'),
            'amount' => $expense->amount,
        ]];

        return view('pages.vouchers.print', [
            'setting' => $setting,
            'voucherType' => 'Credit Voucher',
            'record' => $expense,
            'fromAccountName' => $fromAccountName,
            'rows' => $rows,
            'total' => $expense->amount,
        ]);
    }
}
