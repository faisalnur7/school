<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function edit($id)
    {
        $fee = Fee::findOrFail($id);
        return view('pages.fees.edit', compact('fee'));
    }

    public function update(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $fee->update($validated);

        return redirect()->route('students.show', $fee->student_id)->with('success', 'Fee updated successfully.');
    }

    public function toggleStatus($id)
    {
        $fee = Fee::findOrFail($id);

        if ($this->isPaidFee($fee)) {
            return back()->with('error', 'Paid fees cannot be toggled.');
        }

        $fee->is_active = !$fee->is_active;

        $fee->save();

        return redirect()->back()->with('success', 'Fee active status updated successfully.');
    }

    public function bulkToggleStatus(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'active_fee_ids' => ['nullable', 'array'],
            'active_fee_ids.*' => ['integer', 'exists:fees,id'],
        ]);

        $activeFeeIds = collect($validated['active_fee_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $fees = Fee::where('student_id', $validated['student_id'])->get();

        foreach ($fees as $fee) {
            if ($this->isPaidFee($fee)) {
                continue;
            }

            $fee->is_active = $activeFeeIds->contains((int) $fee->id);
            $fee->save();
        }

        return back()->with('success', 'Fee activation settings updated successfully.');
    }

    private function isPaidFee(Fee $fee): bool
    {
        return (string) $fee->status === 'paid';
    }

    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->delete();

        return redirect()->back()->with('success', 'Fee deleted successfully.');
    }
}
