<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\HandCash;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use App\Models\SchoolSetting;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;

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
        $accountGroups = $this->expenseAccountGroups();
        $expenses   = Expense::with('category')->latest('expense_date')->paginate(15);
        $total      = Expense::sum('amount');
        return view('pages.expenses.create', compact('categories', 'accountGroups', 'expenses', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date_format:d/m/Y',
            'account_type'        => 'required|in:App\\Models\\HandCash',
            'account_id'          => 'required|integer',
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'account_type', 'account_id', 'description',
        ]);
        $data['reference_no'] = $request->input('reference_no');

        $data['expense_date'] = Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');
        $data['recorded_by']  = auth()->id();
        $data['reference_no'] = $data['reference_no'] ?: Expense::generateReference($data['expense_date']);
        $data['account_type'] = HandCash::class;
        $data['payment_method'] = 'Cash';

        if (! $this->resolveExpenseAccount($data['account_type'], (int) $data['account_id'])) {
            throw ValidationException::withMessages([
                'account_id' => 'Please choose a valid active account.',
            ]);
        }

        $storedAttachment = null;
        if ($request->hasFile('attachment')) {
            $storedAttachment = $this->storeExpenseAttachment($request->file('attachment'), $data['reference_no']);
            $data['attachment'] = $storedAttachment;
        }

        try {
            DB::transaction(function () use (&$expense, $data) {
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
            });
        } catch (\Throwable $e) {
            if ($storedAttachment) {
                $this->deleteExpenseAttachment($storedAttachment);
            }

            throw $e;
        }

        return redirect()->route('expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $accountGroups = $this->expenseAccountGroups();
        $expenses   = Expense::with('category')->latest('expense_date')->paginate(15);
        $total      = Expense::sum('amount');
        return view('pages.expenses.edit', compact('expense', 'categories', 'accountGroups', 'expenses', 'total'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_date'        => 'required|date_format:d/m/Y',
            'account_type'        => 'required|in:App\\Models\\HandCash',
            'account_id'          => 'required|integer',
            'reference_no'        => 'nullable|string|max:100',
            'description'         => 'nullable|string',
            'attachment'         => 'nullable|mimes:jpg,jpeg,png,pdf|max:100',
        ]);

        $data = $request->only([
            'expense_category_id', 'title', 'amount',
            'expense_date', 'payment_method', 'account_type', 'account_id', 'description',
        ]);
        $data['reference_no'] = $request->input('reference_no');

        $data['expense_date'] = Carbon::createFromFormat('d/m/Y', $request->expense_date)->format('Y-m-d');
        $data['reference_no'] = $data['reference_no'] ?: $expense->reference_no ?: Expense::generateReference($data['expense_date']);
        $data['account_type'] = HandCash::class;
        $data['payment_method'] = 'Cash';

        if (! $this->resolveExpenseAccount($data['account_type'], (int) $data['account_id'])) {
            throw ValidationException::withMessages([
                'account_id' => 'Please choose a valid active account.',
            ]);
        }

        $storedAttachment = null;
        if ($request->hasFile('attachment')) {
            $storedAttachment = $this->storeExpenseAttachment($request->file('attachment'), $data['reference_no'] ?: Expense::generateReference($data['expense_date']));
            $data['attachment'] = $storedAttachment;
        }

        $oldAttachment = $expense->attachment;

        try {
            DB::transaction(function () use ($expense, $data) {
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
            });
        } catch (\Throwable $e) {
            if ($storedAttachment) {
                $this->deleteExpenseAttachment($storedAttachment);
            }

            throw $e;
        }

        if ($storedAttachment && $oldAttachment) {
            $this->deleteExpenseAttachment($oldAttachment);
        }

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $this->deleteExpenseAttachment($expense->attachment);

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

        $fromAccountName = $expense->account_display_name ?: 'Cash / Petty Cash';

        $rows = [[
            'description' => $expense->title ?: 'Expense paid',
            'note' => $expense->description,
            'amount' => $expense->amount,
        ]];

        return view('pages.vouchers.print', [
            'setting' => $setting,
            'voucherType' => 'Debit Voucher',
            'record' => $expense,
            'fromAccountName' => $fromAccountName,
            'rows' => $rows,
            'total' => $expense->amount,
            'showSummary' => false,
        ]);
    }

    private function expenseAccountGroups(): array
    {
        return [
            [
                'label' => 'Cash Accounts',
                'accounts' => HandCash::where('is_active', true)
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

    private function resolveExpenseAccount(string $accountType, int $accountId): mixed
    {
        return match ($accountType) {
            HandCash::class => HandCash::where('is_active', true)->find($accountId),
            default => null,
        };
    }

    private function storeExpenseAttachment($file, string $referenceNo): string
    {
        $directory = public_path('upload/expenses');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $referenceSlug = preg_replace('/[^A-Za-z0-9_-]+/', '_', $referenceNo) ?: 'expense';
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
        $filename = $referenceSlug . '-' . now()->format('His') . '-' . substr(md5(uniqid((string) mt_rand(), true)), 0, 8) . '.' . $extension;

        $file->move($directory, $filename);

        return 'upload/expenses/' . $filename;
    }

    private function deleteExpenseAttachment(?string $attachment): void
    {
        if (! $attachment) {
            return;
        }

        if (str_starts_with($attachment, 'upload/')) {
            $path = public_path($attachment);
            if (is_file($path)) {
                File::delete($path);
            }
            return;
        }

        \Storage::disk('public')->delete($attachment);
    }
}
