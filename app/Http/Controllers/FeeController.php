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
        $fee->is_active = !$fee->is_active;

        // keep status as is, but if you also want to set pending/paid when deactivating:
        // $fee->status = $fee->is_active ? 'pending' : 'pending';

        $fee->save();

        return redirect()->back()->with('success', 'Fee active status updated successfully.');
    }

    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);
        $fee->delete();

        return redirect()->back()->with('success', 'Fee deleted successfully.');
    }
}

