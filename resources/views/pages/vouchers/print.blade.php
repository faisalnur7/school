<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucherType }} - {{ $record->reference_no ?? 'N/A' }}</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #111827; margin: 0; padding: 0; background: #f8fafc; }
        .page { width: 100%; max-width: 900px; margin: 24px auto; background: #ffffff; padding: 24px; box-shadow: 0 12px 40px rgba(15, 23, 42, .08); }
        .no-print { display: flex; gap: 12px; justify-content: flex-end; margin-bottom: 24px; }
        .no-print button { background:#111827; color:#fff; border:none; border-radius:6px; padding:10px 18px; cursor:pointer; font-weight:700; }
        .voucher-header { display:flex; align-items:flex-start; gap:16px; margin-bottom:12px; }
        .voucher-header .school-info { line-height:1.4; }
        .school-name { font-size:22px; font-weight:800; margin-bottom:6px; }
        .school-meta { font-size:13px; color:#475569; }
        .voucher-title-row { display:flex; justify-content:center; margin-bottom:24px; }
        .voucher-badge { text-transform:uppercase; background:#ffffff; color:#111827; padding:12px 20px; border-radius:8px; border:1px solid #111827; font-size:14px; font-weight:700; letter-spacing:.05em; }
        .voucher-meta { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
        .meta-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; }
        .meta-card strong { display:block; margin-bottom:6px; font-size:12px; color:#334155; }
        .meta-card span { font-size:14px; color:#0f172a; font-weight:600; }
        .detail-table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        .detail-table th, .detail-table td { border:1px solid #e2e8f0; padding:14px 12px; text-align:left; }
        .detail-table th { background:#f8fafc; color:#334155; font-weight:700; }
        .detail-table td.amount, .detail-table th.amount { text-align:right; }
        .summary-row td { border-top:2px solid #111827; font-weight:700; }
        .amount-words { margin:24px 0; padding:18px; border:1px dashed #cbd5e1; border-radius:12px; background:#f8fafc; font-size:14px; color:#334155; }
        .signatures { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:16px; margin-top:32px; }
        .signature { border-top:1px solid #cbd5e1; text-align:center; padding-top:12px; color:#475569; font-size:13px; }
        .logo { max-width:110px; max-height:110px; object-fit:contain; border-radius:12px; }
        @media print {
            body { background: #fff; }
            .no-print { display:none; }
            .page { box-shadow:none; margin:0; padding:0; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="no-print">
            <button onclick="window.print()">Print Voucher</button>
            <button onclick="window.close()">Close</button>
        </div>

        <div class="voucher-header">
            @if(!empty($setting->logo))
                <div><img src="{{ asset($setting->logo) }}" alt="School Logo" class="logo"></div>
            @endif
            <div class="school-info">
                <div class="school-name">{{ $setting->name ?? 'School Name' }}</div>
                <div class="school-meta">{{ $setting->address ?? 'School address not configured' }}</div>
                <div class="school-meta">{{ $setting->phone ?? 'Phone not configured' }} | {{ $setting->email ?? 'Email not configured' }}</div>
            </div>
        </div>

        <div class="voucher-title-row">
            <div class="voucher-badge">{{ $voucherType }}</div>
        </div>

        <div class="voucher-meta">
            <div class="meta-card">
                <strong>Transaction ID</strong>
                <span>#{{ $record->id ?? 'N/A' }}</span>
            </div>
            <div class="meta-card">
                <strong>Voucher Ref. No.</strong>
                <span>{{ $record->reference_no ?? 'N/A' }}</span>
            </div>
            <div class="meta-card" style="grid-column: span 2;">
                <strong>From Account</strong>
                <span>{{ $fromAccountName }}</span>
            </div>
        </div>

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
            <strong>Amount in words:</strong> {{ $amountInWords }}
        </div>

        <div class="signatures">
            <div class="signature">Receiver</div>
            <div class="signature">Accounts Officer</div>
            <div class="signature">Director</div>
            <div class="signature">Director Signature</div>
        </div>
    </div>
</body>
</html>
