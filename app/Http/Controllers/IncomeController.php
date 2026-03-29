<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Income::with('category', 'recorder')->latest('income_date');

        if ($request->filled('category')) {
            $query->where('income_category_id', $request->category);
        }

        if ($request->filled('from')) {
            $query->whereDate('income_date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->from));
        }

        if ($request->filled('to')) {
            $query->whereDate('income_date', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->to));
        }

        $total   = (clone $query)->sum('amount');
        $incomes = $query->paginate(15)->withQueryString();
        $categories = IncomeCategory::where('is_active', true)->get();

        return view('pages.incomes.index', compact('incomes', 'categories', 'total'));
    }

    public function create()
    {
        $categories = IncomeCategory::where('is_active', true)->get();
        $incomes    = Income::with('category')->latest('income_date')->paginate(15);
        $total      = Income::sum('amount');
        return view('pages.incomes.create', compact('categories', 'incomes', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'title'              => 'required|string|max:255',
            'amount'             => 'required|numeric|min:0.01',
            'income_date'        => 'required|date_format:d/m/Y',
            'payment_method'     => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'reference_no'       => 'nullable|string|max:100',
            'description'        => 'nullable|string',
            'attachment'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only([
            'income_category_id', 'title', 'amount',
            'income_date', 'payment_method', 'reference_no', 'description',
        ]);

        $data['income_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->income_date)->format('Y-m-d');
        $data['recorded_by'] = auth()->id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('incomes', 'public');
        }

        Income::create($data);

        return redirect()->route('incomes.index')->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        $categories = IncomeCategory::where('is_active', true)->get();
        $incomes    = Income::with('category')->latest('income_date')->paginate(15);
        $total      = Income::sum('amount');
        return view('pages.incomes.edit', compact('income', 'categories', 'incomes', 'total'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'title'              => 'required|string|max:255',
            'amount'             => 'required|numeric|min:0.01',
            'income_date'        => 'required|date_format:d/m/Y',
            'payment_method'     => 'required|in:Cash,Bank Transfer,Cheque,Mobile Banking,Other',
            'reference_no'       => 'nullable|string|max:100',
            'description'        => 'nullable|string',
            'attachment'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->only([
            'income_category_id', 'title', 'amount',
            'income_date', 'payment_method', 'reference_no', 'description',
        ]);

        $data['income_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->income_date)->format('Y-m-d');

        if ($request->hasFile('attachment')) {
            if ($income->attachment) {
                \Storage::disk('public')->delete($income->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('incomes', 'public');
        }

        $income->update($data);

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        if ($income->attachment) {
            \Storage::disk('public')->delete($income->attachment);
        }

        $income->delete();

        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully.');
    }
}