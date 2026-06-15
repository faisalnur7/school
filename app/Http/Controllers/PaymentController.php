<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PaymentInventoryItem;
use App\Models\InventorySaleItem;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;
use App\Models\SchoolSetting;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with([
            'student.academicInformations.schoolClass',
            'student.academicInformations.section',
            'student.academicInformations.group',
            'items.fee.feeSet',
            'inventorySale.items.inventoryItem',
            'inventoryDueItems.inventorySaleItem.inventoryItem.category',
            'collector',
        ]);

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
        $payment->load(['items.fee.feeSet.items.category', 'student', 'collector', 'inventorySale.items.inventoryItem.category', 'inventoryDueItems.inventorySaleItem.inventoryItem.category']);
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
            'inventoryDueItems.inventorySaleItem.inventoryItem.category',
        ]);
        
        return view('pages.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.amount' => 'nullable|numeric|min:0',
            'sale_items' => 'nullable|array',
            'sale_items.*.quantity' => 'nullable|integer|min:1',
            'sale_items.*.unit_price' => 'nullable|numeric|min:0',
            'sale_items.*.paid_amount' => 'nullable|numeric|min:0',
        ]);

        $itemsInput = $request->input('items', []);
        $saleItemsInput = $request->input('sale_items', []);
        $hasStructuredItems = (is_array($itemsInput) && count($itemsInput)) || (is_array($saleItemsInput) && count($saleItemsInput));

        DB::transaction(function () use ($payment, $request, $itemsInput, $saleItemsInput, $hasStructuredItems) {
            $payment->refresh();
            $payment->loadMissing(['items.fee', 'inventorySale.items.inventoryItem', 'inventoryDueItems.inventorySaleItem.inventoryItem']);

            $payment->update([
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
            ]);

            if ($hasStructuredItems) {
                $affectedFeeIds = [];

                foreach ((array) $itemsInput as $id => $vals) {
                    $item = PaymentItem::where('id', $id)
                        ->where('payment_id', $payment->id)
                        ->with('fee')
                        ->first();
                    if (!$item) {
                        continue;
                    }

                    $fee = $item->fee;
                    $newItemAmount = round((float) ($vals['amount'] ?? $item->amount), 2);
                    $newItemAmount = min(max(0, $newItemAmount), max(0, (float) ($fee?->net_amount ?? $newItemAmount)));
                    $item->amount = $newItemAmount;
                    $item->save();

                    if ($fee) {
                        $affectedFeeIds[$fee->id] = true;
                    }
                }

                if (is_array($saleItemsInput) && count($saleItemsInput) && $payment->inventorySale) {
                    foreach ($saleItemsInput as $id => $vals) {
                        $si = InventorySaleItem::where('id', $id)
                            ->where('inventory_sale_id', $payment->inventory_sale_id)
                            ->lockForUpdate()
                            ->first();
                        if (!$si) {
                            continue;
                        }

                        $oldQty = (int) $si->quantity;
                        $oldSubtotal = (float) $si->subtotal;
                        $oldPaid = (float) ($si->paid_amount ?? $oldSubtotal);

                        $qty = (int) ($vals['quantity'] ?? $oldQty);
                        $unit = round((float) ($vals['unit_price'] ?? $si->unit_price), 2);
                        $subtotal = round($qty * $unit, 2);
                        $paidAmount = round((float) ($vals['paid_amount'] ?? $oldPaid), 2);
                        $paidAmount = min(max(0, $paidAmount), $subtotal);

                        $stockDelta = $qty - $oldQty;
                        if ($stockDelta !== 0) {
                            $inventoryItem = $si->inventoryItem()->lockForUpdate()->first();
                            if ($inventoryItem) {
                                if ($stockDelta > 0) {
                                    $inventoryItem->decrement('current_stock', $stockDelta);
                                } else {
                                    $inventoryItem->increment('current_stock', abs($stockDelta));
                                }

                                \App\Models\StockMovement::create([
                                    'inventory_item_id' => $inventoryItem->id,
                                    'type' => $stockDelta > 0 ? 'sale_adjustment' : 'sale_return',
                                    'quantity_change' => -$stockDelta,
                                    'unit_price' => $unit,
                                    'created_by' => auth()->id(),
                                    'note' => 'Inventory sale edit for receipt ' . $payment->receipt_no,
                                ]);
                            }
                        }

                        $si->quantity = $qty;
                        $si->unit_price = $unit;
                        $si->subtotal = $subtotal;
                        $si->paid_amount = $paidAmount;
                        $si->save();
                    }

                    $payment->inventorySale->total_amount = (float) $payment->inventorySale->items()->sum('subtotal');
                    $payment->inventorySale->save();
                }

                foreach (array_keys($affectedFeeIds) as $feeId) {
                    $this->syncFeePaymentState($feeId);
                }

                $payment->amount = $payment->fee_received_amount + $payment->inventory_received_amount;
                $payment->gross_amount = $payment->calculated_gross_amount;
                $payment->save();

                return;
            }

            $newAmount = (float) $request->amount;
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
                    $netAmount = max(0, (float) $fee->net_amount);
                    $fee->paid_amount = max(0, min($fee->paid_amount, $netAmount));
                    $fee->status = $fee->paid_amount <= 0 ? 'pending' : ($fee->paid_amount >= $netAmount ? 'paid' : 'partial');
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
        DB::transaction(function () use ($paymentItem) {
            $payment = $paymentItem->payment;
            $feeId = $paymentItem->fee_id;
            $paymentItem->delete();

            if ($feeId) {
                $this->syncFeePaymentState($feeId);
            }

            $this->syncPaymentTotals($payment->fresh(['items.fee', 'inventorySale.items', 'inventoryDueItems.inventorySaleItem']));
        });

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment item removed successfully']);
        }

        return redirect()->back()->with('success', 'Payment item removed successfully');
    }

    public function removeInventoryItem(InventorySaleItem $saleItem)
    {
        DB::transaction(function () use ($saleItem) {
            $saleItem->loadMissing('inventoryItem', 'inventorySale.payment');
            $sale = $saleItem->inventorySale;
            $payment = $sale?->payment;
            $inventoryItem = $saleItem->inventoryItem;

            if ($inventoryItem && $saleItem->quantity) {
                $inventoryItem->increment('current_stock', (int) $saleItem->quantity);

                \App\Models\StockMovement::create([
                    'inventory_item_id' => $inventoryItem->id,
                    'type' => 'sale_return',
                    'quantity_change' => (int) $saleItem->quantity,
                    'unit_price' => (float) $saleItem->unit_price,
                    'created_by' => auth()->id(),
                    'note' => 'Inventory sale item removed from receipt ' . ($payment?->receipt_no ?? 'N/A'),
                ]);
            }

            $saleItem->delete();

            if ($sale) {
                $remainingItems = $sale->items()->get();
                if ($remainingItems->isNotEmpty()) {
                    $sale->total_amount = (float) $remainingItems->sum('subtotal');
                    $sale->save();
                } else {
                    $payment?->forceFill(['inventory_sale_id' => null])->save();
                    $sale->delete();
                }
            }

            if ($payment) {
            $this->syncPaymentTotals($payment->fresh(['items.fee', 'inventorySale.items', 'inventoryDueItems.inventorySaleItem']));
            }
        });

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Inventory item removed successfully']);
        }

        return redirect()->back()->with('success', 'Inventory item removed successfully');
    }

    private function syncFeePaymentState(int $feeId): void
    {
        $fee = \App\Models\Fee::find($feeId);
        if (!$fee) {
            return;
        }

        $paid = (float) PaymentItem::where('fee_id', $feeId)->sum('amount');
        $netAmount = max(0, (float) $fee->net_amount);
        $fee->paid_amount = max(0, min($paid, $netAmount));
        $fee->status = $fee->paid_amount <= 0 ? 'pending' : ($fee->paid_amount >= $netAmount ? 'paid' : 'partial');
        $fee->save();
    }

    private function syncPaymentTotals(Payment $payment): void
    {
        $payment->loadMissing(['items.fee', 'inventorySale.items', 'inventoryDueItems.inventorySaleItem']);
        $feeReceived = (float) $payment->items->sum('amount');
        $feeGross = (float) $payment->items->sum(fn ($item) => (float) ($item->fee?->amount ?? $item->amount));
        $inventoryReceived = (float) ($payment->inventorySale?->paid_amount ?? 0) + (float) $payment->inventoryDueItems->sum('amount');
        $inventoryGross = (float) ($payment->inventorySale?->total_amount ?? 0);
        $inventoryGross += (float) $payment->inventoryDueItems->sum(fn ($item) => (float) ($item->inventorySaleItem?->subtotal ?? 0));

        $payment->amount = round($feeReceived + $inventoryReceived, 2);
        $payment->gross_amount = round($feeGross + $inventoryGross, 2);
        $payment->save();
    }
}
