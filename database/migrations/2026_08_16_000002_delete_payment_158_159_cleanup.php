<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $paymentIds = [158, 159];

            $payments = DB::table('payments')
                ->whereIn('id', $paymentIds)
                ->get(['id', 'receipt_no', 'student_id']);

            if ($payments->isEmpty()) {
                return;
            }

            $receiptNos = $payments->pluck('receipt_no')->filter()->values()->all();

            $affectedFeeIds = DB::table('payment_items')
                ->whereIn('payment_id', $paymentIds)
                ->pluck('fee_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $inventorySaleIds = DB::table('inventory_sales')
                ->whereIn('payment_id', $paymentIds)
                ->pluck('id')
                ->all();

            $inventorySaleItems = collect();
            if (! empty($inventorySaleIds)) {
                $inventorySaleItems = DB::table('inventory_sale_items')
                    ->whereIn('inventory_sale_id', $inventorySaleIds)
                    ->get(['id', 'inventory_sale_id', 'inventory_item_id', 'quantity', 'unit_price', 'subtotal', 'paid_amount']);
            }

            foreach ($inventorySaleItems as $saleItem) {
                $inventoryItem = DB::table('inventory_items')
                    ->where('id', $saleItem->inventory_item_id)
                    ->lockForUpdate()
                    ->first(['id', 'current_stock', 'stock_type']);

                if ($inventoryItem && ($inventoryItem->stock_type ?? 'stocked') !== 'made_to_order' && (int) $saleItem->quantity > 0) {
                    DB::table('inventory_items')
                        ->where('id', $inventoryItem->id)
                        ->increment('current_stock', (int) $saleItem->quantity);
                }
            }

            foreach ($receiptNos as $receiptNo) {
                DB::table('stock_movements')
                    ->where('note', 'like', '%' . $receiptNo . '%')
                    ->delete();
            }

            DB::table('transactions')
                ->where('transactionable_type', 'App\\Models\\Payment')
                ->whereIn('transactionable_id', $paymentIds)
                ->delete();

            if (! empty($receiptNos)) {
                $incomeIds = DB::table('incomes')
                    ->where(function ($query) use ($receiptNos) {
                        foreach ($receiptNos as $receiptNo) {
                            $query->orWhere('reference_no', $receiptNo)
                                ->orWhere('description', 'like', '%' . $receiptNo . '%');
                        }
                    })
                    ->pluck('id')
                    ->all();

                if (! empty($incomeIds)) {
                    DB::table('transactions')
                        ->where('transactionable_type', 'App\\Models\\Income')
                        ->whereIn('transactionable_id', $incomeIds)
                        ->delete();

                    DB::table('journal_entries')
                        ->where('source_type', 'App\\Models\\Income')
                        ->whereIn('source_id', $incomeIds)
                        ->delete();

                    DB::table('account_transactions')
                        ->where('transactionable_type', 'App\\Models\\Income')
                        ->whereIn('transactionable_id', $incomeIds)
                        ->delete();

                    DB::table('incomes')
                        ->whereIn('id', $incomeIds)
                        ->delete();
                }

                DB::table('journal_entries')
                    ->where('source_type', 'App\\Models\\Payment')
                    ->whereIn('source_id', $paymentIds)
                    ->delete();
            }

            DB::table('payment_inventory_items')
                ->whereIn('payment_id', $paymentIds)
                ->delete();

            if (! empty($inventorySaleIds)) {
                DB::table('inventory_sale_items')
                    ->whereIn('inventory_sale_id', $inventorySaleIds)
                    ->delete();

                DB::table('inventory_sales')
                    ->whereIn('id', $inventorySaleIds)
                    ->delete();
            }

            DB::table('payment_items')
                ->whereIn('payment_id', $paymentIds)
                ->delete();

            DB::table('payments')
                ->whereIn('id', $paymentIds)
                ->delete();

            foreach ($affectedFeeIds as $feeId) {
                $fee = DB::table('fees')
                    ->where('id', $feeId)
                    ->first(['id', 'amount', 'scholarship_discount']);

                if (! $fee) {
                    continue;
                }

                $paidAmount = (float) DB::table('payment_items')
                    ->where('fee_id', $feeId)
                    ->sum('amount');
                $netAmount = max(0, (float) $fee->amount - (float) ($fee->scholarship_discount ?? 0));

                DB::table('fees')
                    ->where('id', $feeId)
                    ->update([
                        'paid_amount' => round(min($paidAmount, $netAmount), 2),
                        'status' => $paidAmount <= 0 ? 'pending' : ($paidAmount >= $netAmount ? 'paid' : 'partial'),
                    ]);
            }
        });
    }

    public function down(): void
    {
        // Irreversible cleanup migration for the two specific payment records.
    }
};
