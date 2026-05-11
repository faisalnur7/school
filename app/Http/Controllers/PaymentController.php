<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $data['payments'] = $query->latest()->get();
        $data['from']     = $request->from;
        $data['to']       = $request->to;

        return view('pages.payments.index', $data);
    }
    
    public function receipt(Payment $payment){
        $payment->load(['items.fee.feeSet', 'student', 'collector']);
        $setting = \App\Models\SchoolSetting::first();
        return view('pages.payments.receipt', compact('payment', 'setting'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['items.fee.feeSet.items.category', 'student', 'collector']);
        
        return view('pages.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $newAmount = (float) $request->amount;

        DB::transaction(function () use ($payment, $request, $newAmount) {
            $originalAmount = (float) $payment->amount;
            $payment->update([
                'amount' => $newAmount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
            ]);

            $items = $payment->items()->with('fee')->get();
            $itemTotal = (float) $items->sum('amount');

            if ($items->isNotEmpty() && $itemTotal > 0 && $newAmount !== $itemTotal) {
                $scale = $newAmount / $itemTotal;
                $lastItem = $items->last();
                $runningTotal = 0.0;

                foreach ($items as $item) {
                    $oldItemAmount = (float) $item->amount;
                    $updatedAmount = round($oldItemAmount * $scale, 2);
                    $runningTotal += $updatedAmount;

                    if ($item->is($lastItem)) {
                        $updatedAmount += round($newAmount - $runningTotal, 2);
                    }

                    $item->amount = $updatedAmount;
                    $item->save();

                    $fee = $item->fee;
                    $fee->paid_amount += round($updatedAmount - $oldItemAmount, 2);
                    $fee->paid_amount = max(0, min($fee->paid_amount, $fee->amount));
                    $fee->status = $fee->paid_amount <= 0 ? 'pending' : ($fee->paid_amount >= $fee->amount ? 'paid' : 'partial');
                    $fee->save();
                }
            }

            $payment->gross_amount = $newAmount;
            $payment->save();
        });

        return redirect()->back()->with('success', 'Payment updated successfully');
    }

    public function removeItem(\App\Models\PaymentItem $paymentItem)
    {
        $payment = $paymentItem->payment;
        $fee = $paymentItem->fee;
        $itemAmount = $paymentItem->amount;

        // Delete the payment item
        $paymentItem->delete();

        // Update fee's paid_amount
        $fee->paid_amount = max(0, $fee->paid_amount - $itemAmount);
        
        // Recalculate fee status
        if ($fee->paid_amount <= 0) {
            $fee->status = 'pending';
        } else {
            $fee->status = $fee->paid_amount >= $fee->amount ? 'paid' : 'partial';
        }
        $fee->save();

        // Recalculate payment amount
        $totalPaymentAmount = $payment->items->sum('amount');
        $payment->amount = $totalPaymentAmount;
        $payment->gross_amount = $totalPaymentAmount;
        $payment->save();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment item removed successfully']);
        }

        return redirect()->back()->with('success', 'Payment item removed successfully');
    }
}
