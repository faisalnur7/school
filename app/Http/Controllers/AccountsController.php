<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\BankAccount;
use App\Models\HandCash;
use App\Models\MobileBankingAccount;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function index()
    {
        $accounts = Account::with('group')->latest()->paginate(20);
        $groups   = AccountGroup::orderBy('name')->get();
        return view('pages.accounts-list.index', compact('accounts', 'groups'));
    }

    public function create()
    {
        $groups      = AccountGroup::orderBy('name')->get();
        $bankAccounts   = BankAccount::where('is_active', true)->get();
        $handCashes     = HandCash::where('is_active', true)->get();
        $mobileAccounts = MobileBankingAccount::where('is_active', true)->get();
        return view('pages.accounts-list.create', compact('groups', 'bankAccounts', 'handCashes', 'mobileAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'reference_type'   => 'nullable|in:App\Models\BankAccount,App\Models\HandCash,App\Models\MobileBankingAccount',
            'reference_id'     => 'nullable|integer',
            'notes'            => 'nullable|string',
        ]);

        Account::create($request->only('name', 'account_group_id', 'reference_type', 'reference_id', 'notes'));

        return redirect()->route('accounts-list.index')->with('success', 'Account created.');
    }

    public function edit(Account $accountsList)
    {
        $groups      = AccountGroup::orderBy('name')->get();
        $bankAccounts   = BankAccount::where('is_active', true)->get();
        $handCashes     = HandCash::where('is_active', true)->get();
        $mobileAccounts = MobileBankingAccount::where('is_active', true)->get();
        return view('pages.accounts-list.edit', compact('accountsList', 'groups', 'bankAccounts', 'handCashes', 'mobileAccounts'));
    }

    public function update(Request $request, Account $accountsList)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'reference_type'   => 'nullable|in:App\Models\BankAccount,App\Models\HandCash,App\Models\MobileBankingAccount',
            'reference_id'     => 'nullable|integer',
            'notes'            => 'nullable|string',
        ]);

        $accountsList->update($request->only('name', 'account_group_id', 'reference_type', 'reference_id', 'notes'));

        return redirect()->route('accounts-list.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $accountsList)
    {
        $accountsList->delete();
        return redirect()->route('accounts-list.index')->with('success', 'Account deleted.');
    }
}
