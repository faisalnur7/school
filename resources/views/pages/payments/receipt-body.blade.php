{{-- Header --}}
<div class="school-logo-row">
    <div class="school-logo-and-name">
        <div class="school-logo-box">
            GCSC
        </div>
        <div class="school-info">
            <div class="school-name">Green Chartered School & College</div>
            <div class="school-sub">FEE PAYMENT RECEIPT</div>
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
            <th>Amount (BDT)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payment->items as $i => $item)
            @foreach ($item?->fee?->feeSet?->items as $feeSetItem)
                @php 
                    if($item?->fee?->feeSet->frequency == 'monthly'){
                        $monthName = Carbon\Carbon::parse($item?->fee->due_date)->format('F');
                    }else{
                        $monthName = '';
                    }
                @endphp
                <tr>
                    <td>{{ $feeSetItem->category->name ?? '—' }} @if($monthName){{' - '.$monthName}}@endif</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

{{-- Total --}}
<div class="total-row">
    <span class="total-lbl">Total Paid</span>
    <div>
        <span class="total-currency">BDT</span>
        <span class="total-val">{{ number_format($payment->amount, 2) }}</span>
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
