<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: dejavusans; color: #0f172a; font-size: 9pt; }
        .copy { border: 1px solid #94a3b8; padding: 16px; margin-bottom: 18px; }
        .label { text-align: center; background: #f8fafc; padding: 5px; font-weight: bold; letter-spacing: 1px; font-size: 8pt; }
        .header td { vertical-align: top; }
        .school { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .muted { color: #475569; font-size: 8pt; }
        .line { border-top: 1px solid #94a3b8; margin: 10px 0; }
        .dash { border-top: 1px dashed #94a3b8; margin: 8px 0; }
        table { width: 100%; border-collapse: collapse; }
        .info td { width: 50%; padding: 3px 6px 5px 0; }
        .caption { color: #64748b; font-size: 7pt; text-transform: uppercase; font-weight: bold; }
        .value { font-weight: bold; }
        .items th { text-align: left; border-top: 1px solid #94a3b8; border-bottom: 1px solid #94a3b8; padding: 5px 0; font-size: 8pt; }
        .items td { border-bottom: 1px dashed #dbe3ef; padding: 5px 0; }
        .section-title { margin-top: 10px; border-bottom: 1px solid #0f172a; padding: 5px 0; font-size: 8pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .right { text-align: right; }
        .total { border-top: 1px solid #94a3b8; margin-top: 8px; padding-top: 7px; }
        .paid { color: #059669; font-weight: bold; }
        .stamp { color: #0f766e; border: 1px solid #0f766e; padding: 3px 10px; font-weight: bold; }
        .signature { text-align: right; color: #64748b; font-size: 8pt; }
    </style>
</head>
<body>
    <div class="copy">
        <div class="label">Student Copy</div>
        <table class="header">
            <tr>
                <td>
                    @if($setting?->logo && is_file(public_path($setting->logo)))
                        <img src="{{ public_path($setting->logo) }}" style="width:48px;height:48px;object-fit:contain;float:left;margin-right:10px">
                    @endif
                    <div class="school">{{ $setting?->name ?: 'School Name' }}</div>
                    @if($setting?->address)<div class="muted">{{ $setting->address }}</div>@endif
                    <div class="muted">FEE PAYMENT RECEIPT</div>
                </td>
                <td class="right">
                    <div class="caption">Receipt No.</div>
                    <div style="font-size:12pt;font-weight:bold;color:#0f766e">{{ $payment->receipt_no ?: '—' }}</div>
                    <div class="muted">Payment: {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '—' }}</div>
                </td>
            </tr>
        </table>
        <div class="line"></div>
        @php $academic = $payment->student?->latestAcademicInformation; @endphp
        <table class="info">
            <tr><td><div class="caption">Student Name</div><div class="value">{{ $payment->student?->full_name_en ?: '—' }}</div></td><td><div class="caption">Student ID</div><div class="value">{{ $payment->student?->student_cid ?: '—' }}</div></td></tr>
            <tr><td><div class="caption">Academic Information</div><div class="value">{{ collect([$academic?->academicSession?->name_en, $academic?->schoolClass?->name_en, $academic?->section?->name_en, $academic?->roll])->filter()->join(' - ') ?: '—' }}</div></td><td><div class="caption">Collected By</div><div class="value">{{ $payment->collector?->name ?: '—' }}</div></td></tr>
        </table>
        <div class="dash"></div>
        <table class="items">
            <tr><th>Description</th><th class="right">Amount (BDT)</th></tr>
            @foreach($payment->items as $item)
                <tr><td>{{ $item->fee?->feeSet?->name ?: 'Fee payment' }}</td><td class="right">{{ number_format((float) $item->amount, 2) }}</td></tr>
            @endforeach
        </table>
        @php
            $saleItems = $inventorySaleItems ?? collect();
            $dueItems = method_exists($payment, 'validInventoryDueItems')
                ? $payment->validInventoryDueItems()
                : collect();
        @endphp
        @if($saleItems->isNotEmpty())
            <div class="section-title">Items Sold</div>
            <table class="items">
                <tr><th>Category</th><th>Item</th><th>Qty</th><th class="right">Amount (BDT)</th></tr>
                @foreach($saleItems as $saleItem)
                    @php $inventoryItem = $saleItem->inventoryItem; @endphp
                    <tr>
                        <td>{{ $inventoryItem?->category?->name ?: 'Inventory' }}</td>
                        <td>{{ $inventoryItem?->name ?: 'Item' }}</td>
                        <td>{{ number_format((float) $saleItem->quantity, 0) }}</td>
                        <td class="right">BDT {{ number_format((float) $saleItem->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
        @if($dueItems->isNotEmpty())
            <div class="section-title">Inventory Due Settlements</div>
            <table class="items">
                @foreach($dueItems as $dueItem)
                    @php $saleItem = $dueItem->inventorySaleItem; $inventoryItem = $saleItem?->inventoryItem; @endphp
                    <tr>
                        <td>{{ $inventoryItem?->category?->name ?: 'Inventory' }} — {{ $inventoryItem?->name ?: 'Item' }}</td>
                        <td class="right">BDT {{ number_format((float) $dueItem->amount, 2) }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
        <table class="total">
            <tr><td>Subtotal</td><td class="right">BDT {{ number_format((float) ($receiptSummary['subtotal'] ?? 0), 2) }}</td></tr>
            @if(($receiptSummary['discountAmt'] ?? 0) > 0)<tr><td>Discount</td><td class="right">- BDT {{ number_format((float) $receiptSummary['discountAmt'], 2) }}</td></tr>@endif
            <tr class="paid"><td>Total Paid</td><td class="right">BDT {{ number_format((float) ($receiptSummary['totalPaid'] ?? $payment->amount), 2) }}</td></tr>
            @if(($receiptSummary['balanceDue'] ?? 0) > 0)<tr><td>Balance Due</td><td class="right">BDT {{ number_format((float) $receiptSummary['balanceDue'], 2) }}</td></tr>@endif
        </table>
        <table style="margin-top:16px"><tr><td><span class="stamp">{{ ($receiptSummary['balanceDue'] ?? 0) > 0 ? 'DUE' : 'PAID' }}</span></td><td class="signature">____________________<br>Authorised Signature</td></tr></table>
        @if(($receiptSummary['balanceDue'] ?? 0) > 0)
            <div style="text-align:right;color:#b45309;font-weight:bold;font-size:8pt;margin-top:8px">Outstanding balance after this payment: BDT {{ number_format((float) $receiptSummary['balanceDue'], 2) }}</div>
        @endif
        <div class="muted" style="text-align:center;margin-top:14px">Thank you — Please keep this receipt for your records.</div>
    </div>
</body>
</html>
