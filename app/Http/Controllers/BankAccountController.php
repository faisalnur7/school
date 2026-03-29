<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::latest()->paginate(15);
        return view('pages.bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::latest()->paginate(15);
        return view('pages.bank-accounts.create', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name'       => 'required|string|max:255',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:100|unique:bank_accounts,account_number',
            'branch_name'     => 'nullable|string|max:255',
            'routing_number'  => 'nullable|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'opening_date'    => 'required|date_format:d/m/Y',
            'notes'           => 'nullable|string',
        ]);

        $data = $request->only([
            'bank_name', 'account_name', 'account_number',
            'branch_name', 'routing_number', 'opening_balance', 'notes',
        ]);
        $data['opening_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d');
        $data['is_active']    = true;

        BankAccount::create($data);

        return redirect()->route('bank-accounts.index')->with('success', 'Bank account added successfully.');
    }

    public function edit(BankAccount $bankAccount)
    {
        $bankAccounts = BankAccount::latest()->paginate(15);
        return view('pages.bank-accounts.edit', compact('bankAccount', 'bankAccounts'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'bank_name'       => 'required|string|max:255',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:100|unique:bank_accounts,account_number,' . $bankAccount->id,
            'branch_name'     => 'nullable|string|max:255',
            'routing_number'  => 'nullable|string|max:100',
            'opening_balance' => 'required|numeric|min:0',
            'opening_date'    => 'required|date_format:d/m/Y',
            'notes'           => 'nullable|string',
        ]);

        $data = $request->only([
            'bank_name', 'account_name', 'account_number',
            'branch_name', 'routing_number', 'opening_balance', 'notes',
        ]);
        $data['opening_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d');

        $bankAccount->update($data);

        return redirect()->route('bank-accounts.index')->with('success', 'Bank account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'Bank account deleted successfully.');
    }
}