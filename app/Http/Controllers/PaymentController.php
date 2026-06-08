<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\SchoolSetting;

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
        $payment->load(['items.fee.feeSet.items.category', 'student', 'collector', 'inventorySale.items.inventoryItem']);
        $setting = SchoolSetting::current();
        return view('pages.payments.receipt', compact('payment', 'setting'));
    }

    public function edit(Payment $payment)
    {
        $payment->load([
            'items.fee.feeSet.items.category',
            'student',
            'collector',
            'inventorySale.items.inventoryItem.category',
        ]);
        
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

        // Prefer per-item updates when provided; otherwise fall back to previous scaling logic.
        $itemsInput = $request->input('items', []);

        DB::transaction(function () use ($payment, $request, $itemsInput) {
            // First: handle inventory sale item updates if provided
            $saleItemsInput = $request->input('sale_items', []);
            $inventoryTotal = 0.0;
            if (is_array($saleItemsInput) && count($saleItemsInput) && $payment->inventorySale) {
                foreach ($saleItemsInput as $id => $vals) {
                    $si = \App\Models\InventorySaleItem::where('id', $id)->where('inventory_sale_id', $payment->inventory_sale_id)->first();
                    if (!$si) continue;

                    $qty = (int) ($vals['quantity'] ?? $si->quantity);
                    $unit = round((float) ($vals['unit_price'] ?? $si->unit_price), 2);

                    $si->quantity = $qty;
                    $si->unit_price = $unit;
                    $si->subtotal = round($qty * $unit, 2);
                    $si->save();
                }

                // Recalculate inventory sale total
                $inventoryTotal = (float) $payment->inventorySale->items()->sum('subtotal');
                $payment->inventorySale->total_amount = $inventoryTotal;
                $payment->inventorySale->save();
            }

            // Then: handle fee payment items if provided
            if (is_array($itemsInput) && count($itemsInput)) {
                $providedTotal = 0.0;
                foreach ($itemsInput as $id => $vals) {
                    $providedTotal += (float) ($vals['amount'] ?? 0);
                }
                $feesTotal = (float) $providedTotal;

                // Update payment header fields (payment_date/method/description apply regardless)
                $payment->update([
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'description' => $request->description,
                ]);

                // Apply per-item amounts and adjust linked fees
                foreach ($itemsInput as $id => $vals) {
                    $item = \App\Models\PaymentItem::where('id', $id)->where('payment_id', $payment->id)->with('fee')->first();
                    if (!$item) continue;

                    $oldItemAmount = (float) $item->amount;
                    $newItemAmount = round((float) ($vals['amount'] ?? 0), 2);
                    $diff = round($newItemAmount - $oldItemAmount, 2);

                    $item->amount = $newItemAmount;
                    $item->save();

                    $fee = $item->fee;
                    if ($fee) {
                        $fee->paid_amount = max(0, min($fee->paid_amount + $diff, $fee->amount));
                        $fee->status = $fee->paid_amount <= 0 ? 'pending' : ($fee->paid_amount >= $fee->amount ? 'paid' : 'partial');
                        $fee->save();
                    }
                }

                // recompute fees total from DB to be safe
                $feesTotal = (float) $payment->items()->sum('amount');

                // Compute final payment totals combining fees + inventory
                $totalPayment = $feesTotal + $inventoryTotal;
                $payment->amount = $totalPayment;
                $payment->gross_amount = $totalPayment;
                $payment->save();

                return;
            }

            // ── Legacy behaviour: scale existing items when top-level amount changed ──
            $newAmount = (float) $request->amount;
            $originalAmount = (float) $payment->amount;
            $payment->update([
                'amount' => $newAmount,
                'gross_amount' => $newAmount,
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
