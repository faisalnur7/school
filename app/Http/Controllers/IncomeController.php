<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\HandCash;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use App\Models\SchoolSetting;
use App\Services\JournalService;
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
        $excludedCategories = [
            'Student Payment',
            'Admission Fee',
            'Exam Fee',
            'Transport Fee',
            'Stationery',
            'Books',
            'School Bag',
            'Student Uniform',
            'Sports Dress',
            'Inventory Sale',
            'Inventory Sales',
        ];

        $categories = IncomeCategory::where('is_active', true)
            ->whereNotIn('name', $excludedCategories)
            ->get();
        $accountGroups = $this->incomeAccountGroups();

        $incomes    = Income::with('category')->latest('income_date')->paginate(15);
        $total      = Income::sum('amount');
        return view('pages.incomes.create', compact('categories', 'accountGroups', 'incomes', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'title'              => 'required|string|max:255',
            'amount'             => 'required|numeric|min:0.01',
            'income_date'        => 'required|date_format:d/m/Y',
            'account_type'       => 'required|in:App\\Models\\HandCash',
            'account_id'         => 'required|integer',
            'reference_no'       => 'nullable|string|max:100',
            'description'        => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'income_category_id', 'title', 'amount',
            'income_date', 'account_type', 'account_id', 'reference_no', 'description',
        ]);

        $data['income_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->income_date)->format('Y-m-d');
        $data['recorded_by'] = auth()->id();
        $data['account_type'] = HandCash::class;
        $data['payment_method'] = 'Cash';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('incomes', 'public');
        }

        $income = Income::create($data);

        Transaction::create([
            'reference_no'         => Transaction::generateReference(),
            'type'                 => 'income',
            'income_category_id'   => $income->income_category_id,
            'amount'               => $income->amount,
            'description'          => $income->description,
            'transaction_date'     => $income->income_date,
            'payment_method'       => $income->payment_method,
            'transactionable_type' => Income::class,
            'transactionable_id'   => $income->id,
            'recorded_by'          => auth()->id(),
        ]);

        JournalService::postSafe(
            $income->income_date->toDateString(),
            $income->title,
            [
                ['account_id' => Account::resolveForSource($income->account_type ?? '', $income->account_id ?? 0), 'debit' => (float) $income->amount, 'credit' => 0],
                ['account_id' => Account::resolveForSource(IncomeCategory::class, $income->income_category_id), 'debit' => 0, 'credit' => (float) $income->amount],
            ],
            Income::class,
            $income->id,
            auth()->id()
        );

        return redirect()->route('incomes.index')->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        $categories = IncomeCategory::where('is_active', true)->get();
        $accountGroups = $this->incomeAccountGroups();
        $incomes    = Income::with('category')->latest('income_date')->paginate(15);
        $total      = Income::sum('amount');
        return view('pages.incomes.edit', compact('income', 'categories', 'accountGroups', 'incomes', 'total'));
    }

    public function update(Request $request, Income $income)
    {
        $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'title'              => 'required|string|max:255',
            'amount'             => 'required|numeric|min:0.01',
            'income_date'        => 'required|date_format:d/m/Y',
            'account_type'       => 'required|in:App\\Models\\HandCash',
            'account_id'         => 'required|integer',
            'reference_no'       => 'nullable|string|max:100',
            'description'        => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'income_category_id', 'title', 'amount',
            'income_date', 'account_type', 'account_id', 'reference_no', 'description',
        ]);

        $data['income_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->income_date)->format('Y-m-d');
        $data['account_type'] = HandCash::class;
        $data['payment_method'] = 'Cash';

        if ($request->hasFile('attachment')) {
            if ($income->attachment) {
                \Storage::disk('public')->delete($income->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('incomes', 'public');
        }

        $income->update($data);

        $existingRef = Transaction::where('transactionable_type', Income::class)
            ->where('transactionable_id', $income->id)
            ->value('reference_no');

        Transaction::updateOrCreate(
            ['transactionable_type' => Income::class, 'transactionable_id' => $income->id],
            [
                'reference_no'         => $existingRef ?: Transaction::generateReference(),
                'type'                 => 'income',
                'income_category_id'   => $income->income_category_id,
                'amount'               => $income->amount,
                'description'          => $income->description,
                'transaction_date'     => $income->income_date,
                'payment_method'       => $income->payment_method,
                'recorded_by'          => auth()->id(),
            ]
        );

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        if ($income->attachment) {
            \Storage::disk('public')->delete($income->attachment);
        }

        $income->delete();

        AccountTransaction::removeSource(Income::class, $income->id);

        // delete matching transaction record if exists
        Transaction::where('transactionable_type', Income::class)
            ->where('transactionable_id', $income->id)
            ->delete();

        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully.');
    }

    public function voucher(Income $income)
    {
        $setting = SchoolSetting::current();

        $fromAccountName = $income->account_display_name ?: 'Cash / Petty Cash';

        $rows = [[
            'description' => $income->title ?: 'Income received',
            'note' => $income->description,
            'amount' => $income->amount,
        ]];

        return view('pages.vouchers.print', [
            'setting' => $setting,
            'voucherType' => 'Credit Voucher',
            'record' => $income,
            'fromAccountName' => $fromAccountName,
            'rows' => $rows,
            'total' => $income->amount,
            'showSummary' => false,
        ]);
    }

    private function incomeAccountGroups(): array
    {
        return [
            [
                'label' => 'Cash Accounts',
                'accounts' => \App\Models\HandCash::where('is_active', true)
                    ->orderBy('label')
                    ->get()
                    ->map(fn (HandCash $account) => [
                        'id' => $account->id,
                        'label' => $account->label,
                        'type' => HandCash::class,
                    ])
                    ->all(),
            ],
        ];
    }
}
