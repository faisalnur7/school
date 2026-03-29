<?php
namespace App\Http\Controllers;

use App\Models\MobileBankingAccount;
use Illuminate\Http\Request;

class MobileBankingAccountController extends Controller
{
    public static array $providers = ['bKash', 'Nagad', 'Rocket', 'Upay', 'SureCash', 'Other'];

    public function index()
    {
        $accounts = MobileBankingAccount::latest()->paginate(15);
        return view('pages.mobile-banking-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $accounts  = MobileBankingAccount::latest()->paginate(15);
        $providers = self::$providers;
        return view('pages.mobile-banking-accounts.create', compact('accounts', 'providers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider'        => 'required|string|max:100',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:100|unique:mobile_banking_accounts,account_number',
            'account_type'    => 'required|in:Personal,Agent,Merchant',
            'opening_balance' => 'required|numeric|min:0',
            'opening_date'    => 'required|date_format:d/m/Y',
            'notes'           => 'nullable|string',
        ]);

        $data = $request->only([
            'provider', 'account_name', 'account_number',
            'account_type', 'opening_balance', 'notes',
        ]);
        $data['opening_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d');
        $data['is_active']    = true;

        MobileBankingAccount::create($data);

        return redirect()->route('mobile-banking-accounts.index')->with('success', 'Mobile banking account added successfully.');
    }

    public function edit(MobileBankingAccount $mobileBankingAccount)
    {
        $accounts  = MobileBankingAccount::latest()->paginate(15);
        $providers = self::$providers;
        return view('pages.mobile-banking-accounts.edit', compact('mobileBankingAccount', 'accounts', 'providers'));
    }

    public function update(Request $request, MobileBankingAccount $mobileBankingAccount)
    {
        $request->validate([
            'provider'        => 'required|string|max:100',
            'account_name'    => 'required|string|max:255',
            'account_number'  => 'required|string|max:100|unique:mobile_banking_accounts,account_number,' . $mobileBankingAccount->id,
            'account_type'    => 'required|in:Personal,Agent,Merchant',
            'opening_balance' => 'required|numeric|min:0',
            'opening_date'    => 'required|date_format:d/m/Y',
            'notes'           => 'nullable|string',
        ]);

        $data = $request->only([
            'provider', 'account_name', 'account_number',
            'account_type', 'opening_balance', 'notes',
        ]);
        $data['opening_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d');

        $mobileBankingAccount->update($data);

        return redirect()->route('mobile-banking-accounts.index')->with('success', 'Mobile banking account updated successfully.');
    }

    public function destroy(MobileBankingAccount $mobileBankingAccount)
    {
        $mobileBankingAccount->delete();
        return redirect()->route('mobile-banking-accounts.index')->with('success', 'Mobile banking account deleted successfully.');
    }
}