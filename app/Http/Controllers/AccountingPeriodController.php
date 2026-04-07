<?php

namespace App\Http\Controllers;

use App\Models\AccountingPeriod;
use Illuminate\Http\Request;

class AccountingPeriodController extends Controller
{
    public function index()
    {
        $periods = AccountingPeriod::latest()->get();
        return view('pages.accounting-periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100|unique:accounting_periods,name',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AccountingPeriod::create($request->only('name', 'start_date', 'end_date'));

        return back()->with('success', 'Accounting period created.');
    }

    public function update(Request $request, AccountingPeriod $accountingPeriod)
    {
        if ($accountingPeriod->is_closed) {
            return back()->withErrors(['period' => 'Cannot edit a closed period.']);
        }

        $request->validate([
            'name'       => 'required|string|max:100|unique:accounting_periods,name,' . $accountingPeriod->id,
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $accountingPeriod->update($request->only('name', 'start_date', 'end_date'));

        return back()->with('success', 'Period updated.');
    }

    public function close(AccountingPeriod $accountingPeriod)
    {
        if ($accountingPeriod->is_closed) {
            return back()->withErrors(['period' => 'Period is already closed.']);
        }

        $accountingPeriod->close(auth()->id());

        return back()->with('success', "Period '{$accountingPeriod->name}' has been closed.");
    }

    public function destroy(AccountingPeriod $accountingPeriod)
    {
        if ($accountingPeriod->is_closed) {
            return back()->withErrors(['period' => 'Cannot delete a closed period.']);
        }

        $accountingPeriod->delete();
        return back()->with('success', 'Period deleted.');
    }
}
