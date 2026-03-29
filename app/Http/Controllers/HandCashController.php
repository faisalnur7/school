<?php

namespace App\Http\Controllers;

use App\Models\HandCash;
use Illuminate\Http\Request;

class HandCashController extends Controller
{
    public function index()
    {
        $handCashes = HandCash::with('recorder')->latest()->paginate(15);
        return view('pages.hand-cash.index', compact('handCashes'));
    }

    public function create()
    {
        $handCashes = HandCash::with('recorder')->latest()->paginate(15);
        return view('pages.hand-cash.create', compact('handCashes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label'          => 'required|string|max:255',
            'opening_amount' => 'required|numeric|min:0',
            'opening_date'   => 'required|date_format:d/m/Y',
            'notes'          => 'nullable|string',
        ]);

        HandCash::create([
            'label'          => $request->label,
            'opening_amount' => $request->opening_amount,
            'opening_date'   => \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d'),
            'notes'          => $request->notes,
            'is_active'      => true,
            'recorded_by'    => auth()->id(),
        ]);

        return redirect()->route('hand-cash.index')->with('success', 'Hand cash entry added successfully.');
    }

    public function edit(HandCash $handCash)
    {
        $handCashes = HandCash::with('recorder')->latest()->paginate(15);
        return view('pages.hand-cash.edit', compact('handCash', 'handCashes'));
    }

    public function update(Request $request, HandCash $handCash)
    {
        $request->validate([
            'label'          => 'required|string|max:255',
            'opening_amount' => 'required|numeric|min:0',
            'opening_date'   => 'required|date_format:d/m/Y',
            'notes'          => 'nullable|string',
        ]);

        $handCash->update([
            'label'          => $request->label,
            'opening_amount' => $request->opening_amount,
            'opening_date'   => \Carbon\Carbon::createFromFormat('d/m/Y', $request->opening_date)->format('Y-m-d'),
            'notes'          => $request->notes,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        return redirect()->route('hand-cash.index')->with('success', 'Hand cash entry updated successfully.');
    }

    public function destroy(HandCash $handCash)
    {
        $handCash->delete();
        return redirect()->route('hand-cash.index')->with('success', 'Hand cash entry deleted successfully.');
    }
}