<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Income;
use App\Models\JournalEntry;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\PaymentInventoryItem;
use App\Models\InventorySale;
use App\Models\InventorySaleItem;
use App\Models\StockMovement;
use App\Models\Transaction;
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
            $query->whereDate('payment_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('payment_date', '<=', $request->to);
        }

        $data['payments'] = $query->orderByDesc('payment_date')->orderByDesc('id')->get();
        $data['from']     = $request->from;
        $data['to']       = $request->to;

        return view('pages.payments.index', $data);
    }
    
    public function receipt(Payment $payment){
        $payment->load([
            'items.fee.feeSet.items.category',
            'student.latestAcademicInformation.academicSession',
            'student.latestAcademicInformation.schoolClass',
            'student.latestAcademicInformation.section',
            'collector',
            'inventorySale.items.inventoryItem.category',
            'inventoryDueItems.inventorySaleItem.inventoryItem.category',
        ]);
        $setting = SchoolSetting::current();
        $receiptSummary = $this->buildReceiptSummary($payment);
        $inventorySaleItems = $payment->inventory_sale_id
            ? InventorySale::with('items.inventoryItem.category')
                ->find($payment->inventory_sale_id)
                ?->items ?? collect()
            : collect();

        return view('pages.payments.receipt', compact('payment', 'setting', 'receiptSummary', 'inventorySaleItems'));
    }

    private function buildReceiptSummary(Payment $payment): array
    {
        $feeRecords = $payment->items
            ->map(fn ($item) => $item->fee)
            ->filter()
            ->unique('id')
            ->values();

        $feeSubtotal = (float) $feeRecords->sum(fn ($fee) => (float) ($fee->amount ?? 0));
        $inventorySaleTotal = (float) ($payment->inventorySale?->total_amount ?? 0);
        $inventoryDueTotal = (float) $payment->validInventoryDueItems()
            ->sum(fn ($item) => (float) ($item->inventorySaleItem?->subtotal ?? 0));
        $scholarshipAmt = round((float) $payment->scholarship_received_amount, 2);
        $freeStudentshipAmt = round((float) $payment->free_studentship_received_amount, 2);
        $discountAmt = round((float) ($payment->discount_amount ?? 0), 2);
        $subtotal = round($feeSubtotal + $inventorySaleTotal + $inventoryDueTotal, 2);
        $totalDue = round(max(0, $subtotal - $scholarshipAmt - $freeStudentshipAmt - $discountAmt), 2);

        $paidCutoffDate = $payment->payment_date
            ? Carbon::parse($payment->payment_date)->toDateString()
            : null;

        $paidByFee = collect();
        if ($feeRecords->isNotEmpty() && $paidCutoffDate) {
            $paidByFee = PaymentItem::query()
                ->selectRaw('payment_items.fee_id as fee_id, SUM(payment_items.amount) as total_paid')
                ->join('payments', 'payments.id', '=', 'payment_items.payment_id')
                ->whereIn('payment_items.fee_id', $feeRecords->pluck('id')->all())
                ->where(function ($query) use ($payment, $paidCutoffDate) {
                    $query->whereDate('payments.payment_date', '<', $paidCutoffDate)
                        ->orWhere(function ($subQuery) use ($payment, $paidCutoffDate) {
                            $subQuery->whereDate('payments.payment_date', '=', $paidCutoffDate)
                                ->where('payments.id', '<=', $payment->id);
                        });
                })
                ->groupBy('payment_items.fee_id')
                ->pluck('total_paid', 'fee_id');
        }

        $feePaidTotal = round($feeRecords->sum(fn ($fee) => (float) ($paidByFee[$fee->id] ?? 0)), 2);
        $totalPaid = round((float) $payment->amount, 2);
        $balanceDue = round(max(0, $totalDue - $totalPaid), 2);

        return [
            'feeSubtotal' => $feeSubtotal,
            'subtotal' => $subtotal,
            'scholarshipAmt' => $scholarshipAmt,
            'freeStudentshipAmt' => $freeStudentshipAmt,
            'discountAmt' => $discountAmt,
            'totalDue' => $totalDue,
            'totalPaid' => $totalPaid,
            'balanceDue' => $balanceDue,
        ];
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
        $paymentDate = Carbon::parse($request->payment_date)->toDateString();

        DB::transaction(function () use ($payment, $request, $itemsInput, $saleItemsInput, $hasStructuredItems, $paymentDate) {
            $payment->refresh();
            $payment->loadMissing(['items.fee', 'inventorySale.items.inventoryItem', 'inventoryDueItems.inventorySaleItem.inventoryItem']);

            $payment->update([
                'payment_date' => $paymentDate,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
            ]);
            $this->syncPaymentBusinessDates($payment, $paymentDate);

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
                            if ($inventoryItem && !$inventoryItem->isMadeToOrder()) {
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
                'payment_date' => $paymentDate,
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

    private function syncPaymentBusinessDates(Payment $payment, string $paymentDate): void
    {
        $references = Transaction::withTrashed()
            ->where('transactionable_type', Payment::class)
            ->where('transactionable_id', $payment->id)
            ->pluck('reference_no')
            ->filter()
            ->values();

        Transaction::withTrashed()
            ->where('transactionable_type', Payment::class)
            ->where('transactionable_id', $payment->id)
            ->update(['transaction_date' => $paymentDate]);

        Income::withTrashed()
            ->whereIn('reference_no', $references)
            ->get()
            ->each(function (Income $income) use ($paymentDate): void {
                $income->income_date = $paymentDate;
                $income->save();
            });

        JournalEntry::withTrashed()
            ->where('source_type', Payment::class)
            ->where('source_id', $payment->id)
            ->update(['date' => $paymentDate]);
    }

    public function destroy(Payment $payment)
    {
        $payment->loadMissing([
            'items.fee',
            'inventorySale.items.inventoryItem',
            'inventoryDueItems.inventorySaleItem.inventoryItem',
        ]);

        $studentId = $payment->student_id;
        $receiptNo = $payment->receipt_no;
        $affectedFeeIds = $payment->items->pluck('fee_id')->filter()->unique()->values()->all();
        $inventorySaleItems = $payment->inventorySale?->items ?? collect();
        $inventoryDueItems = $payment->inventoryDueItems ?? collect();

        DB::transaction(function () use ($payment, $receiptNo, $affectedFeeIds, $inventorySaleItems, $inventoryDueItems) {
            foreach ($inventoryDueItems as $paymentDueItem) {
                $saleItem = $paymentDueItem->inventorySaleItem()->lockForUpdate()->first();
                if (!$saleItem) {
                    continue;
                }

                $paidAmount = round((float) ($paymentDueItem->amount ?? 0), 2);
                if ($paidAmount <= 0) {
                    continue;
                }

                $saleItem->paid_amount = max(0, (float) ($saleItem->paid_amount ?? 0) - $paidAmount);
                $saleItem->save();
            }

            foreach ($inventorySaleItems as $saleItem) {
                $inventoryItem = $saleItem->inventoryItem()->lockForUpdate()->first();
                if ($inventoryItem && !$inventoryItem->isMadeToOrder() && (int) $saleItem->quantity > 0) {
                    $inventoryItem->increment('current_stock', (int) $saleItem->quantity);
                }

                if ($inventoryItem && filled($receiptNo)) {
                    StockMovement::where('inventory_item_id', $inventoryItem->id)
                        ->where('note', 'like', '%' . $receiptNo . '%')
                        ->delete();
                }
            }

            $this->deletePaymentIncomeTrail($payment, $receiptNo);

            Transaction::withTrashed()
                ->where('transactionable_type', Payment::class)
                ->where('transactionable_id', $payment->id)
                ->get()
                ->each
                ->forceDelete();

            JournalEntry::withTrashed()
                ->where('source_type', Payment::class)
                ->where('source_id', $payment->id)
                ->get()
                ->each
                ->forceDelete();

            $payment->delete();

            foreach ($affectedFeeIds as $feeId) {
                $this->syncFeePaymentState((int) $feeId);
            }
        });

        return redirect()
            ->route('fees.collect_payment', ['student_id' => $studentId])
            ->with('success', 'Payment deleted successfully.');
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

            if ($inventoryItem && !$inventoryItem->isMadeToOrder() && $saleItem->quantity) {
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
        $payment->loadMissing(['items.fee', 'inventorySale.items', 'inventoryDueItems.inventorySaleItem.inventoryItem.category']);
        $feeReceived = (float) $payment->items->sum('amount');
        $feeGross = (float) $payment->items->sum(fn ($item) => (float) ($item->fee?->amount ?? $item->amount));
        $validDueItems = method_exists($payment, 'validInventoryDueItems')
            ? $payment->validInventoryDueItems()
            : ($payment->inventoryDueItems ?? collect())->filter(fn ($item) => $item->inventorySaleItem?->inventoryItem);
        $inventoryReceived = (float) ($payment->inventorySale?->paid_amount ?? 0) + (float) $validDueItems->sum('amount');
        $inventoryGross = (float) ($payment->inventorySale?->total_amount ?? 0);
        $inventoryGross += (float) $validDueItems->sum(fn ($item) => (float) ($item->inventorySaleItem?->subtotal ?? 0));

        $payment->amount = round($feeReceived + $inventoryReceived, 2);
        $payment->gross_amount = round($feeGross + $inventoryGross, 2);
        $payment->save();
    }

    private function deletePaymentIncomeTrail(Payment $payment, ?string $receiptNo): void
    {
        if (! filled($receiptNo)) {
            return;
        }

        $incomes = Income::withTrashed()
            ->whereIn('title', ['Student Payment', 'Transport Fee', 'Inventory Sale', 'Inventory Sales'])
            ->where(function ($query) use ($receiptNo) {
                $query->where('description', 'like', '%' . $receiptNo . '%')
                    ->orWhere('reference_no', $receiptNo);
            })
            ->get();

        Income::withoutEvents(function () use ($incomes) {
            foreach ($incomes as $income) {
                $accountTransactions = AccountTransaction::where('transactionable_type', Income::class)
                    ->where('transactionable_id', $income->id)
                    ->get();

                foreach ($accountTransactions as $accountTransaction) {
                    $accountType = $accountTransaction->account_type;
                    $accountId = (int) $accountTransaction->account_id;
                    $amount = (float) $accountTransaction->amount;

                    if ($accountType && $accountId && class_exists($accountType)) {
                        if ($accountTransaction->type === 'credit') {
                            $accountType::where('id', $accountId)->decrement('balance', $amount);
                        } else {
                            $accountType::where('id', $accountId)->increment('balance', $amount);
                        }
                    }

                    $accountTransaction->delete();
                }

                Transaction::withTrashed()
                    ->where('transactionable_type', Income::class)
                    ->where('transactionable_id', $income->id)
                    ->get()
                    ->each
                    ->forceDelete();

                JournalEntry::withTrashed()
                    ->where('source_type', Income::class)
                    ->where('source_id', $income->id)
                    ->get()
                    ->each
                    ->forceDelete();

                $income->forceDelete();
            }
        });
    }
}
