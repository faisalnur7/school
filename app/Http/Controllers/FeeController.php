<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\FeeAmountHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        if ($this->isPaidFee($fee)) {
            return back()->with('error', 'Paid fees cannot be edited.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ]);

        $minimumAmount = (float) ($fee->paid_amount ?? 0) + (float) ($fee->scholarship_discount ?? 0);
        if ((float) $validated['amount'] < $minimumAmount) {
            return back()
                ->withErrors(['amount' => 'The fee amount cannot be less than the amount already paid.'])
                ->withInput();
        }

        $oldAmount = (float) $fee->amount;

        DB::transaction(function () use ($fee, $validated, $oldAmount) {
            $fee->update($validated);

            $netAmount = (float) $fee->amount - (float) ($fee->scholarship_discount ?? 0);
            $fee->status = $fee->paid_amount <= 0
                ? 'pending'
                : ($fee->paid_amount >= $netAmount ? 'paid' : 'partial');
            $fee->save();

            if ($oldAmount !== (float) $fee->amount) {
                FeeAmountHistory::create([
                    'fee_id' => $fee->id,
                    'edited_by' => auth()->id(),
                    'old_amount' => $oldAmount,
                    'new_amount' => (float) $fee->amount,
                ]);
            }
        });

        if ($request->boolean('return_to_collect')) {
            return redirect()->route('fees.collect_payment', ['student_id' => $fee->student_id])
                ->with('success', 'Fee amount updated successfully.');
        }

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
