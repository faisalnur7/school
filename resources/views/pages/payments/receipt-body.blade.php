{{-- Header --}}
<div class="school-logo-row">
    <div class="school-logo-and-name">
        @if($setting?->logo)
            <img src="{{ asset($setting->logo) }}" style="width:50px;height:50px;object-fit:contain;flex-shrink:0">
        @else
            <div class="school-logo-box">{{ strtoupper(substr($setting?->name ?? 'S', 0, 1)) }}</div>
        @endif
        <div class="school-info">
            <div class="school-name">{{ $setting?->name ?? 'School Name' }}</div>
            @if($setting?->address)<div class="school-sub">{{ $setting->address }}</div>@endif
            @if($setting?->email || $setting?->contact_number_1)
                <div class="school-sub">
                    @if($setting?->email){{ $setting->email }}@endif
                    @if($setting?->email && $setting?->contact_number_1) &nbsp;|&nbsp; @endif
                    @if($setting?->contact_number_1){{ $setting->contact_number_1 }}@endif
                    @if($setting?->contact_number_2) / {{ $setting->contact_number_2 }}@endif
                </div>
            @endif
            <div class="school-sub" style="margin-top:3px;font-weight:700;letter-spacing:.1em">FEE PAYMENT RECEIPT</div>
        </div>
    </div>
    <div class="receipt-tag">
        <div class="receipt-tag-label">Receipt No.</div>
        <div class="receipt-tag-no">{{ $payment->receipt_no }}</div>
    </div>
</div>

<hr class="divider-solid">

{{-- Student & Payment Info --}}
<div class="info-grid">
    <div class="info-cell">
        <div class="lbl">Student Name</div>
        <div class="val">{{ $payment->student->full_name_en }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Student ID</div>
        <div class="val">{{ $payment->student->student_cid }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Payment Date</div>
        <div class="val">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Payment Method</div>
        <div class="val">{{ $payment->payment_method }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Collected By</div>
        <div class="val">{{ $payment->collector->name ?? '—' }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Time</div>
        <div class="val">{{ \Carbon\Carbon::parse($payment->payment_date)->format('h:i A') }}</div>
    </div>
</div>

<hr class="divider-dash">

{{-- Fee Items --}}
<table class="items-table">
    <thead>
        <tr>
            <th>Description</th>
            <th style="text-align:right">Amount (BDT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payment->items as $item)
            @php
                $monthName   = '';
                $feeSetItems = $item->fee?->feeSet?->items ?? collect();
                $itemPaid    = (float) $item->amount;
                $feeSetTotal = (float) $feeSetItems->sum('amount');
                if ($item->fee?->feeSet?->frequency === 'monthly') {
                    $monthName = ' - ' . \Carbon\Carbon::parse($item->fee->due_date)->format('F');
                }
            @endphp
            @if($feeSetItems->count())
                @foreach ($feeSetItems as $fsi)
                    @php
                        // Distribute paid amount proportionally across fee set categories
                        $proportion = $feeSetTotal > 0 ? ((float)$fsi->amount / $feeSetTotal) : 0;
                        $lineAmount = round($itemPaid * $proportion, 2);
                    @endphp
                    <tr>
                        <td>{{ $fsi->category->name ?? '—' }}{{ $monthName }}</td>
                        <td style="text-align:right">{{ number_format($lineAmount, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $item->fee?->feeSet?->name ?? '—' }}{{ $monthName }}</td>
                    <td style="text-align:right">{{ number_format($itemPaid, 2) }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>

{{-- Subtotal / Scholarship / Discount / Total --}}
@php
    $hasScholarship = $payment->scholarship_amount > 0;
    $hasDiscount    = $payment->discount_amount > 0;
    $saleItems      = $payment->inventorySale?->items ?? collect();
    $inventoryTotal = (float)($payment->inventorySale?->total_amount ?? 0);
    $feeTotal       = (float)$payment->items->sum('amount');
@endphp

{{-- Items Sold (Inventory) --}}
@if($saleItems->isNotEmpty())
<div style="margin-top:10px">
    <div style="font-size:8.5px;letter-spacing:.12em;font-weight:900;text-transform:uppercase;color:#333;border-bottom:1.5px solid #111;padding-bottom:4px;margin-bottom:4px">Items Sold</div>
    @php
        // Group inventory items by category
        $groupedItems = $saleItems->groupBy(function($si) {
            return $si->inventoryItem->category_id ?? 0;
        });
    @endphp
    @foreach($groupedItems as $categoryId => $items)
        @php
            $category = $items->first()->inventoryItem->category ?? null;
            $categoryName = $category ? $category->name : 'Unknown Category';
            $categoryTotal = $items->sum('subtotal');
        @endphp
        <div style="margin-bottom:8px; display:flex; justify-content: space-between;">
            <div style="font-size:9px;letter-spacing:.08em;font-weight:700;text-transform:uppercase;color:#333">
                {{ $categoryName }}
            </div>
            <div style="font-size:11px;font-weight:700;text-align:right;color:#111">
                BDT {{ number_format($categoryTotal, 2) }}
            </div>
        </div>
    @endforeach
</div>
@endif
<div style="margin-top:10px; border-top:1.5px dashed #bbb; padding-top:8px">

    @if($hasScholarship || $hasDiscount || $inventoryTotal > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#555;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Fee Subtotal</span>
            <span>BDT {{ number_format($feeTotal + $payment->scholarship_amount + $payment->discount_amount, 2) }}</span>
        </div>
    @endif

    @if($hasScholarship)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#059669;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">🎓 Scholarship</span>
            <span>- BDT {{ number_format($payment->scholarship_amount, 2) }}</span>
        </div>
    @endif

    @if($hasDiscount)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#b45309;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Discount</span>
            <span>- BDT {{ number_format($payment->discount_amount, 2) }}</span>
        </div>
    @endif

    @if($inventoryTotal > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#555;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Items Sold</span>
            <span>BDT {{ number_format($inventoryTotal, 2) }}</span>
        </div>
    @endif

    <div class="total-row">
        <span class="total-lbl">Total Paid</span>
        <div>
            <span class="total-currency">BDT</span>
            <span class="total-val">{{ number_format($payment->amount, 2) }}</span>
        </div>
    </div>
</div>

{{-- Stamp + Signature --}}
<div class="stamp-row">
    <div class="stamp">✓ PAID</div>
    <div class="signature-line">
        <div class="sig-line"></div>
        <div class="sig-label">Authorised Signature</div>
    </div>
</div>

{{-- Footer --}}
<div class="slip-footer">
    Thank you — Please keep this receipt for your records.
</div>
