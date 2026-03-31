<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\MobileBankingAccount;
use App\Models\HandCash;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function getAccounts(Request $request)
    {
        $accounts = match($request->type) {
            'bank'      => BankAccount::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->bank_name . ' - ' . $a->account_number]),
            'mobile'    => MobileBankingAccount::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->provider . ' - ' . $a->account_number]),
            'hand_cash' => HandCash::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->label]),
            default     => collect(),
        };

        return response()->json($accounts);
    }
}
