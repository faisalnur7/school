<?php

namespace App\Http\Controllers;

use App\Models\Shareholder;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ShareholderController extends Controller
{
    public function index()
    {
        $shareholders = Shareholder::withCount('transactions')
            ->withSum(['transactions as capital_sum' => fn($q) => $q->where('type', 'capital')], 'amount')
            ->withSum(['transactions as withdrawal_sum' => fn($q) => $q->where('type', 'withdrawal')], 'amount')
            ->latest()
            ->paginate(15);

        return view('pages.shareholders.index', compact('shareholders'));
    }

    public function create()
    {
        return view('pages.shareholders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Shareholder::create($request->only('name', 'phone', 'email', 'address'));

        return redirect()->route('shareholders.index')->with('success', 'Shareholder created successfully.');
    }

    public function edit(Shareholder $shareholder)
    {
        return view('pages.shareholders.edit', compact('shareholder'));
    }

    public function update(Request $request, Shareholder $shareholder)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $shareholder->update($request->only('name', 'phone', 'email', 'address'));

        return redirect()->route('shareholders.index')->with('success', 'Shareholder updated successfully.');
    }

    public function destroy(Shareholder $shareholder)
    {
        $shareholder->delete();

        return redirect()->route('shareholders.index')->with('success', 'Shareholder deleted.');
    }
}
