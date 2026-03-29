<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
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
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'reference_no', 'description',
        ]);

        $data['expense_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');
        $data['recorded_by']  = auth()->id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        Expense::create($data);

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
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'reference_no', 'description',
        ]);

        $data['expense_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');

        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                \Storage::disk('public')->delete($expense->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->attachment) {
            \Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}