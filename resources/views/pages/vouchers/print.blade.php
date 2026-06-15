<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucherType }} - {{ $record->reference_no ?? 'N/A' }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #111827; margin: 0; padding: 0; background: #f1f5f9; font-size: 12px; }
        .page { width: 100%; max-width: 780px; margin: 16px auto; background: #fff; padding: 18px 20px; box-shadow: 0 4px 20px rgba(15,23,42,.1); }

        /* Toolbar */
        .no-print { display: flex; gap: 8px; justify-content: flex-end; margin-bottom: 14px; }
        .no-print button { background: #111827; color: #fff; border: none; border-radius: 5px; padding: 6px 14px; cursor: pointer; font-weight: 700; font-size: 11px; }

        /* Header */
        .voucher-header { display: flex; align-items: center; gap: 10px; padding-bottom: 10px; border-bottom: 1.5px solid #111827; margin-bottom: 8px; }
        .logo { width: 52px; height: 52px; object-fit: contain; border-radius: 6px; flex-shrink: 0; }
        .school-name { font-size: 16px; font-weight: 800; line-height: 1.2; margin-bottom: 2px; }
        .school-meta { font-size: 10.5px; color: #475569; line-height: 1.5; }

        /* Voucher badge */
        .voucher-title-row { text-align: center; margin: 8px 0; }
        .voucher-badge { display: inline-block; text-transform: uppercase; border: 1px solid #111827; padding: 4px 16px; border-radius: 4px; font-size: 11px; font-weight: 700; letter-spacing: .08em; }

        /* Meta grid */
        .voucher-meta { display: grid; grid-template-columns: auto auto 1fr; gap: 6px; margin-bottom: 10px; }
        .meta-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 7px 10px; }
        .meta-card strong { display: block; font-size: 9.5px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
        .meta-card span { font-size: 12px; font-weight: 600; color: #0f172a; white-space: nowrap; }

        /* Table */
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .detail-table th, .detail-table td { border: 1px solid #e2e8f0; padding: 7px 9px; text-align: left; }
        .detail-table th { background: #f8fafc; color: #334155; font-weight: 700; font-size: 11px; }
        .detail-table td { font-size: 11.5px; }
        .detail-table td.amount, .detail-table th.amount { text-align: right; }
        .summary-row td { border-top: 1.5px solid #111827; font-weight: 700; background: #f8fafc; }

        /* Amount words */
    .amount-words { padding: 8px 12px; border: 1px dashed #cbd5e1; border-radius: 6px; background: #f8fafc; font-size: 11px; color: #334155; margin-bottom: 16px; }
    .amount-words strong { color: #111827; }

    .due-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 12px 0 14px; }
    .due-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; }
    .due-box .label { display: block; font-size: 9.5px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 2px; }
    .due-box .value { font-size: 12.5px; font-weight: 700; color: #0f172a; }
    .due-box.due .value { color: #dc2626; }
    .due-box.paid .value { color: #16a34a; }
    .due-box.status .value { color: #7c3aed; text-transform: uppercase; }

        /* Signatures */
        .signatures { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 80px; }
        .signature { border-top: 1px solid #94a3b8; text-align: center; padding-top: 6px; color: #475569; font-size: 10.5px; }

        @media print {
            body { background: #fff; font-size: 11px; }
            .no-print { display: none; }
            .page { box-shadow: none; margin: 0; padding: 14px 16px; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="page">

    <div class="no-print">
        <button onclick="window.print()">Print Voucher</button>
        <button onclick="window.close()">Close</button>
    </div>

    <!-- Header -->
    <div class="voucher-header">
        @if(!empty($setting->logo))
            <img src="{{ asset($setting->logo) }}" alt="School Logo" class="logo">
        @endif
        <div>
            <div class="school-name">{{ $setting->name ?? 'School Name' }}</div>
            <div class="school-meta">{{ $setting->address ?? 'School address not configured' }}</div>
            <div class="school-meta">{{ $setting->phone ?? 'Phone not configured' }} &nbsp;|&nbsp; {{ $setting->email ?? 'Email not configured' }}</div>
        </div>
    </div>

    <!-- Badge -->
    <div class="voucher-title-row">
        <span class="voucher-badge">{{ $voucherType }}</span>
    </div>

    <!-- Meta -->
    <div class="voucher-meta">
        <div class="meta-card">
            <strong>Transaction ID</strong>
            <span>#{{ $record->id ?? 'N/A' }}</span>
        </div>
        <div class="meta-card">
            <strong>Voucher Ref. No.</strong>
            <span>{{ $record->reference_no ?? 'N/A' }}</span>
        </div>
        <div class="meta-card">
            <strong>Supplier</strong>
            <span>{{ $fromAccountName }}</span>
        </div>
    </div>

    <!-- Table -->
    <table class="detail-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th class="amount">Unit Price</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['quantity'] ?? '—' }}</td>
                    <td class="amount">{{ isset($row['unit_price']) ? number_format($row['unit_price'], 2) : '—' }}</td>
                    <td class="amount">{{ number_format($row['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr class="summary-row">
                <td colspan="3">Total</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="due-summary">
        <div class="due-box">
            <span class="label">Subtotal</span>
            <span class="value">{{ number_format((float) $record->total_amount, 2) }}</span>
        </div>
        <div class="due-box paid">
            <span class="label">Paid</span>
            <span class="value">{{ number_format((float) ($record->paid_amount ?? 0), 2) }}</span>
        </div>
        <div class="due-box due">
            <span class="label">Due</span>
            <span class="value">{{ number_format((float) ($record->due_amount ?? 0), 2) }}</span>
        </div>
        <div class="due-box status">
            <span class="label">Status</span>
            <span class="value">{{ ucfirst($record->status ?? 'unpaid') }}</span>
        </div>
    </div>

    @php
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $amountString = number_format((float) $total, 2, '.', '');
        [$whole, $fraction] = explode('.', $amountString);
        $words = ucfirst(trim($formatter->format((int) $whole) ?: 'Zero')) . ' Taka';
        if ((int) $fraction > 0) {
            $words .= ' and ' . trim($formatter->format((int) $fraction)) . ' Paisa';
        }
        $amountInWords = $words . ' Only';
    @endphp

    <div class="amount-words">
        <strong>Invoice amount in words:</strong> {{ $amountInWords }}
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="signature">Receiver</div>
        <div class="signature">Accounts Officer</div>
        <div class="signature">Director</div>
        <div class="signature">Director Signature</div>
    </div>

</div>
</body>
</html>
