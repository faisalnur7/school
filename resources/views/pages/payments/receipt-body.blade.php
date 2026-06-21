{{-- Header --}}
@php
    $schoolName = $setting?->name ?: 'School Name';
    $schoolAddress = $setting?->address;
    $contactParts = array_filter([
        $setting?->contact_number_1,
        $setting?->contact_number_2,
        $setting?->whatsapp_number ? 'WhatsApp: '.$setting->whatsapp_number : null,
        $setting?->email,
        $setting?->website,
    ]);
@endphp
<div class="school-logo-row">
    <div class="school-header-main">
        <div class="school-logo-and-name">
            @if($setting?->logo)
                <img src="{{ asset($setting->logo) }}" style="width:50px;height:50px;object-fit:contain;flex-shrink:0">
            @else
                <div class="school-logo-box">{{ strtoupper(substr($schoolName, 0, 1)) }}</div>
            @endif
            <div class="school-info">
                <div class="school-name">{{ $schoolName }}</div>
            </div>
        </div>
    </div>
    <div class="school-header-meta">
        @if($schoolAddress)<div class="school-sub">{{ $schoolAddress }}</div>@endif
        @if(count($contactParts))
            <div class="school-sub">{{ implode(' | ', $contactParts) }}</div>
        @endif
        <div class="receipt-tag" style="margin-top:5px;">
            <div class="receipt-tag-label">Receipt No.</div>
            <div class="receipt-tag-no">{{ $payment->receipt_no }}</div>
        </div>
        <div class="school-sub" style="margin-top:3px;font-weight:700;letter-spacing:.1em">FEE PAYMENT RECEIPT</div>
    </div>
</div>

<hr class="divider-solid">

{{-- Student & Payment Info --}}
<div class="info-grid">
    <div class="info-cell">
        <div class="lbl">Student Name</div>
        <div class="val">{{ $payment->student?->full_name_en ?? '—' }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Student ID</div>
        <div class="val">{{ $payment->student?->student_cid ?? '—' }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Payment Date</div>
        <div class="val">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y \a\t h:iA') }}</div>
    </div>
    <div class="info-cell">
        <div class="lbl">Collected By</div>
        <div class="val">{{ $payment->collector->name ?? '—' }}</div>
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
    $feeItems       = $payment->items;
    $feeRecords     = $feeItems->map(fn ($item) => $item->fee)->filter()->unique('id');
    $saleItems      = $payment->inventorySale?->items ?? collect();
    $dueItems       = $payment->inventoryDueItems ?? collect();
    $inventoryTotal = (float)($payment->inventorySale?->total_amount ?? 0);
    $feeSubtotal    = (float) ($receiptSummary['feeSubtotal'] ?? $feeRecords->sum(fn ($fee) => (float) ($fee->amount ?? 0)));
    $subtotal       = round($feeSubtotal + $inventoryTotal, 2);
    $scholarshipAmt = round((float) ($receiptSummary['scholarshipAmt'] ?? $payment->scholarship_amount), 2);
    $freeStudentshipAmt = round((float) ($receiptSummary['freeStudentshipAmt'] ?? $payment->discount_amount), 2);
    $totalDue       = round($receiptSummary['totalDue'] ?? max(0, $subtotal - $scholarshipAmt - $freeStudentshipAmt), 2);
    $totalPaid      = round($receiptSummary['totalPaid'] ?? $payment->amount, 2);
    $balanceDue     = round($receiptSummary['balanceDue'] ?? max(0, $totalDue - $totalPaid), 2);
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
            $categoryKey = strtolower($categoryName);
            $showProductNames = !str_contains($categoryKey, 'book');
            $productNames = $showProductNames
                ? $items->map(fn($si) => $si->inventoryItem?->name)
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ')
                : '';
            $categoryLabel = $productNames
                ? $categoryName . ' — ' . $productNames
                : $categoryName;
            $categoryTotal = $items->sum('subtotal');
        @endphp
        <div style="margin-bottom:8px; display:flex; justify-content: space-between;">
            <div style="font-size:9px;letter-spacing:.08em;font-weight:700;text-transform:uppercase;color:#333">
                {{ $categoryLabel }}
            </div>
            <div style="font-size:11px;font-weight:700;text-align:right;color:#111">
                BDT {{ number_format($categoryTotal, 2) }}
            </div>
        </div>
    @endforeach
</div>
@endif

    @if($dueItems->isNotEmpty())
<div style="margin-top:10px">
    <div style="font-size:8.5px;letter-spacing:.12em;font-weight:900;text-transform:uppercase;color:#333;border-bottom:1.5px solid #111;padding-bottom:4px;margin-bottom:4px">Inventory Due Settlements</div>
    @foreach($dueItems as $dueItem)
        @php
            $saleItem = $dueItem->inventorySaleItem;
            $inventoryItem = $saleItem?->inventoryItem;
            $categoryName = $inventoryItem?->category?->name ?? 'Inventory';
            $label = $categoryName . ' — ' . ($inventoryItem?->name ?? 'Item');
        @endphp
        <div style="margin-bottom:8px; display:flex; justify-content: space-between;">
            <div style="font-size:9px;letter-spacing:.08em;font-weight:700;text-transform:uppercase;color:#333">
                {{ $label }}
            </div>
            <div style="font-size:11px;font-weight:700;text-align:right;color:#111">
                BDT {{ number_format((float) $dueItem->amount, 2) }}
            </div>
        </div>
    @endforeach
</div>
@endif
<div style="margin-top:10px; border-top:1.5px dashed #bbb; padding-top:8px">

    @if($subtotal > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#555;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Subtotal</span>
            <span>BDT {{ number_format($subtotal, 2) }}</span>
        </div>
    @endif

    @if($scholarshipAmt > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#b45309;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Scholarship</span>
            <span>- BDT {{ number_format($scholarshipAmt, 2) }}</span>
        </div>
    @endif

    @if($freeStudentshipAmt > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#555;margin-bottom:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;font-weight:700">Free Studentship</span>
            <span>- BDT {{ number_format($freeStudentshipAmt, 2) }}</span>
        </div>
    @endif

    @if($totalDue > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#7c2d12;font-weight:800;margin-top:4px">
            <span style="letter-spacing:.06em;text-transform:uppercase;">Total Due</span>
            <span>BDT {{ number_format($totalDue, 2) }}</span>
        </div>
    @endif

    @if($totalPaid > 0)
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#059669;font-weight:800;margin-top:6px">
            <span style="letter-spacing:.06em;text-transform:uppercase;">Total Paid</span>
            <span>BDT {{ number_format($totalPaid, 2) }}</span>
        </div>
    @endif
</div>

{{-- Stamp + Signature --}}
<div class="stamp-row">
    <div class="stamp">{{ $balanceDue > 0 ? '⚠ DUE' : '✓ PAID' }}</div>
    <div class="signature-line">
        <div class="sig-line"></div>
        <div class="sig-label">Authorised Signature</div>
    </div>
</div>

@if($balanceDue > 0)
    <div style="margin-top:8px;font-size:9px;color:#b45309;text-align:right;letter-spacing:.06em;font-weight:700">
        Outstanding balance after this payment: BDT {{ number_format($balanceDue, 2) }}
    </div>
@endif

{{-- Footer --}}
<div class="slip-footer">
    Thank you — Please keep this receipt for your records.
</div>
