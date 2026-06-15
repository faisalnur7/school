<?php

namespace App\Services;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Collection;

class StudentPaymentLedgerService
{
    /**
     * Build a chronological ledger for a student within an academic session.
     *
     * Returns:
     *   - student        : Student model with academicInformations eager-loaded
     *   - months         : Collection of month groups, each with:
     *       - label       : e.g. "Jan-2026"
     *       - rows        : Collection of transaction rows
     *       - month_dues  : float
     *       - month_recv  : float
     *   - opening_balance: float (always 0 for session-scoped ledger)
     *   - closing_balance : float
     *   - total_dues     : float
     *   - total_received : float
     */
    public function build(Student $student, int $sessionId): array
    {
        // ── 1. Fee invoices (dues) ────────────────────────────────────────────
        $fees = Fee::with(['feeSet'])
            ->where('student_id', $student->id)
            ->whereHas('feeSet', fn($q) => $q->where('academic_session_id', $sessionId))
            ->where('is_active', 1)
            ->orderBy('due_date')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // ── 2. Payments received (fee payments) ───────────────────────────────
        $payments = Payment::with(['items.fee.feeSet', 'inventorySale.items.inventoryItem.category', 'inventoryDueItems.inventorySaleItem.inventoryItem.category'])
            ->where('student_id', $student->id)
            ->whereHas('items.fee.feeSet', fn($q) => $q->where('academic_session_id', $sessionId))
            ->orderBy('payment_date')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // ── 3. Inventory sale payments ────────────────────────────────────────
        $invPayments = Payment::with(['inventorySale.items.inventoryItem.category', 'inventoryDueItems.inventorySaleItem.inventoryItem.category'])
            ->where('student_id', $student->id)
            ->where(function ($q) {
                $q->whereNotNull('inventory_sale_id')
                  ->orWhereHas('inventoryDueItems');
            })
            ->orderBy('payment_date')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // ── 4. Build flat transaction list ────────────────────────────────────
        $transactions = collect();

        foreach ($fees as $fee) {
            $netAmount = (float) $fee->amount - (float) $fee->scholarship_discount;
            $transactions->push([
                'sort_date'   => $fee->due_date?->format('Y-m-d') ?? '0000-00-00',
                'date'        => $fee->due_date?->format('d M Y') ?? '—',
                'month_key'   => $fee->due_date?->format('Y-m') ?? '0000-00',
                'month_label' => $fee->due_date?->format('M-Y') ?? '—',
                'voucher'     => 'FEE-' . str_pad($fee->id, 5, '0', STR_PAD_LEFT),
                'code'        => 'FEE',
                'description' => $fee->feeSet?->name ?? 'Fee Invoice',
                'dues'        => $netAmount,
                'received'    => 0.0,
                'type'        => 'fee_invoice',
            ]);
        }

        foreach ($payments as $payment) {
            $transactions->push([
                'sort_date'   => $payment->payment_date ?? '0000-00-00',
                'date'        => \Carbon\Carbon::parse($payment->payment_date)->format('d M Y'),
                'month_key'   => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m'),
                'month_label' => \Carbon\Carbon::parse($payment->payment_date)->format('M-Y'),
                'voucher'     => 'RCP-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                'code'        => 'RCP',
                'description' => $payment->description ?: 'Fee Payment',
                'dues'        => 0.0,
                'received'    => (float) $payment->items->sum('amount'),
                'type'        => 'fee_payment',
            ]);

            // Discount/scholarship as separate line if applicable
            if ((float) $payment->discount_amount > 0) {
                $transactions->push([
                    'sort_date'   => $payment->payment_date ?? '0000-00-00',
                    'date'        => \Carbon\Carbon::parse($payment->payment_date)->format('d M Y'),
                    'month_key'   => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m'),
                    'month_label' => \Carbon\Carbon::parse($payment->payment_date)->format('M-Y'),
                    'voucher'     => 'DSC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                    'code'        => 'DSC',
                    'description' => 'Discount / Adjustment',
                    'dues'        => 0.0,
                    'received'    => (float) $payment->discount_amount,
                    'type'        => 'discount',
                ]);
            }
        }

        foreach ($invPayments as $payment) {
            if ($payment->inventorySale) {
                $description = 'Inventory Purchase';
                $items = $payment->inventorySale->items->map(fn($i) => $i->inventoryItem?->name ?? 'Item')->implode(', ');
                if ($items) $description = $items;
                $transactions->push([
                    'sort_date'   => $payment->payment_date ?? '0000-00-00',
                    'date'        => \Carbon\Carbon::parse($payment->payment_date)->format('d M Y'),
                    'month_key'   => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m'),
                    'month_label' => \Carbon\Carbon::parse($payment->payment_date)->format('M-Y'),
                    'voucher'     => 'INV-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                    'code'        => 'INV',
                    'description' => $description,
                    'dues'        => (float) ($payment->inventorySale?->total_amount ?? 0),
                    'received'    => (float) ($payment->inventorySale?->paid_amount ?? 0),
                    'type'        => 'inventory',
                ]);
            }

            if ($payment->inventoryDueItems->isNotEmpty()) {
                foreach ($payment->inventoryDueItems as $dueItem) {
                    $saleItem = $dueItem->inventorySaleItem;
                    $inventoryItem = $saleItem?->inventoryItem;
                    $category = $inventoryItem?->category;
                    $transactions->push([
                        'sort_date'   => $payment->payment_date ?? '0000-00-00',
                        'date'        => \Carbon\Carbon::parse($payment->payment_date)->format('d M Y'),
                        'month_key'   => \Carbon\Carbon::parse($payment->payment_date)->format('Y-m'),
                        'month_label' => \Carbon\Carbon::parse($payment->payment_date)->format('M-Y'),
                        'voucher'     => 'DUE-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                        'code'        => 'DUE',
                        'description' => trim(($category?->name ?? 'Inventory') . ' - ' . ($inventoryItem?->name ?? 'Item')),
                        'dues'        => 0.0,
                        'received'    => (float) $dueItem->amount,
                        'type'        => 'inventory_due',
                    ]);
                }
            }
        }

        // ── 5. Sort chronologically ───────────────────────────────────────────
        $transactions = $transactions->sortBy('sort_date')->values();

        // ── 6. Calculate running balance ──────────────────────────────────────
        $balance = 0.0;
        $rows = $transactions->map(function ($tx) use (&$balance) {
            $balance = $balance + $tx['dues'] - $tx['received'];
            return array_merge($tx, ['balance' => $balance]);
        });

        // ── 7. Group by month ─────────────────────────────────────────────────
        $months = $rows->groupBy('month_key')
            ->map(function (Collection $group) {
                return (object) [
                    'label'      => $group->first()['month_label'],
                    'rows'       => $group,
                    'month_dues' => $group->sum('dues'),
                    'month_recv' => $group->sum('received'),
                ];
            })
            ->sortKeys()
            ->values();

        $totalDues     = $rows->sum('dues');
        $totalReceived = $rows->sum('received');
        $closingBalance = $totalDues - $totalReceived;

        return [
            'student'          => $student,
            'months'           => $months,
            'opening_balance'  => 0.0,
            'closing_balance'  => $closingBalance,
            'total_dues'       => $totalDues,
            'total_received'   => $totalReceived,
        ];
    }
}
