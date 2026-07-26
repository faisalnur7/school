<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; font-size: 10px; color: #000; font-weight: 700; }
    body * { color: #000 !important; font-weight: 700 !important; }
    b, strong { font-weight: 700 !important; }
    .school-header-wrap { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; margin-bottom: 10px; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; vertical-align: middle; line-height: 50px; overflow: hidden; background: #fff; }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #000; }
    .school-title { font-size: 16px; font-weight: 700; color: #000; margin-top: 1px; }
    .school-line { font-size: 10px; color: #000; margin-top: 2px; }
    .pdf-header { background: #fff; color: #000; padding: 8px 12px; margin-bottom: 10px; text-align: center; }
    .pdf-header h1 { font-size: 14px; font-weight: 700; color: #000; }
    .pdf-header .meta { font-size: 9px; color: #000; margin-top: 2px; font-weight: 700; }
    .summary { display: table; width: 100%; margin-bottom: 10px; border-collapse: collapse; }
    .summary td { display: table-cell; padding: 5px 10px; border: 1px solid #e2e8f0; text-align: center; }
    .summary .label { font-size: 9px; color: #000; font-weight: 700; }
    .summary .value { font-size: 13px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th { background: #f1f5f9; color: #000; padding: 5px 6px; text-align: left; border-bottom: 2px solid #e2e8f0; font-weight: 700; }
    td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; font-weight: 700; }
    tfoot td { background: #f8fafc; font-weight: 700; border-top: 2px solid #cbd5e1; }
    .detail-row td { font-weight: 400 !important; }
    .text-right { text-align: right; }
    .green { color: #000 !important; }
    .red   { color: #000 !important; }
    .blue  { color: #000 !important; }
    .amber { color: #000 !important; }
    .muted { color: #000 !important; }
    .mono  { font-family: monospace; font-size: 9px; }
    .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; font-weight: 700; color: #000 !important; }
</style>
</head>
<body>

@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $address = $school->address;
    $contacts = array_filter([$school->contact_number_1, $school->contact_number_2]);
    $logoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $hasLogo = $logoPath && file_exists($logoPath);
    $showDescription = $viewType === 'detailed';
    $showDescriptionForType = function (string $type) use ($showDescription, $pdfDescriptionTypes) {
        return $showDescription && in_array($type, $pdfDescriptionTypes ?? [], true);
    };
@endphp

<div class="school-header-wrap">
    <table class="school-header-table">
        <tr>
            <td class="school-header-logo-cell">
                <div class="school-logo-box">
                    @if($hasLogo)
                        <img src="{{ $logoPath }}" alt="{{ $schoolName }} logo" class="school-logo-img">
                    @else
                        <span class="school-logo-fallback">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>
                    @endif
                </div>
            </td>
            <td class="school-header-info-cell">
                <div class="school-title">{{ $schoolName }}</div>
                @if($address)
                    <div class="school-line">{{ $address }}</div>
                @endif
                @if(count($contacts))
                    <div class="school-line">{{ implode(' | ', $contacts) }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

<div class="pdf-header">
    <h1>Transaction Report</h1>
    <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table class="summary" style="margin-bottom:10px">
    <tr>
        <td><div class="label"><strong>Income</strong></div><div class="value"><strong>{{ number_format($totalIncome, 2) }}</strong></div></td>
        <td><div class="label"><strong>Expense</strong></div><div class="value"><strong>{{ number_format($totalExpense, 2) }}</strong></div></td>
        <td><div class="label"><strong>Capital</strong></div><div class="value"><strong>{{ number_format($totalCapital, 2) }}</strong></div></td>
        <td><div class="label"><strong>Withdrawal</strong></div><div class="value"><strong>{{ number_format($totalWithdrawal, 2) }}</strong></div></td>
        @php $net = ($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal); @endphp
        <td><div class="label"><strong>Net</strong></div><div class="value"><strong>{{ number_format($net, 2) }}</strong></div></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Reference</th>
            @if($showDescription)
                <th style="width:220px; max-width:220px;">Description</th>
            @endif
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th>Recorded By</th>
        </tr>
    </thead>
    <tbody>
        @php $rowNumber = 0; @endphp
        @forelse($transactionGroups as $group)
            @php
                $badgeColor = match($group['type']) {
                    'income'     => 'background:#f0fdf4;border:1px solid #bbf7d0',
                    'expense'    => 'background:#fff1f2;border:1px solid #fecdd3',
                    'capital'    => 'background:#eff6ff;border:1px solid #bfdbfe',
                    'withdrawal' => 'background:#fefce8;border:1px solid #fde68a',
                    default      => 'background:#f1f5f9',
                };
            @endphp
                <tr>
                    <td colspan="{{ $showDescription ? 6 : 5 }}" style="background:#eef2ff;color:#000;font-weight:700;padding:6px 8px">
                    <span class="badge" style="{{ $badgeColor }};font-size:12px;padding:4px 8px;margin-right:8px">
                        <strong>{{ $group['label'] }}</strong>
                    </span>
                </td>
            </tr>
            @foreach($group['rows'] as $txn)
                @php
                    $rowNumber++;
                    $isCredit = in_array($txn->type, ['income', 'capital']);
                @endphp
                <tr class="detail-row">
                    <td class="muted">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                    <td class="mono">{{ $txn->reference_no ?? '—' }}</td>
                    @if($showDescription)
                        <td style="width:220px; max-width:220px; word-break:break-word;">
                            <div class="muted">
                                {{ $showDescriptionForType($txn->type) ? ($txn->description ?? '—') : '—' }}
                            </div>
                        </td>
                    @endif
                    <td class="text-right">{{ !$isCredit ? number_format($txn->amount, 2) : '—' }}</td>
                    <td class="text-right">{{ $isCredit ? number_format($txn->amount, 2) : '—' }}</td>
                    <td class="muted">{{ $txn->recorder?->name ?? '—' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ $showDescription ? 3 : 2 }}" style="background:#f8fafc;font-weight:700;padding:6px 8px"><strong>Total</strong></td>
                <td class="text-right" style="background:#f8fafc;font-weight:700"><strong>{{ number_format($group['totalDebit'], 2) }}</strong></td>
                <td class="text-right" style="background:#f8fafc;font-weight:700"><strong>{{ number_format($group['totalCredit'], 2) }}</strong></td>
                <td style="background:#f8fafc"></td>
            </tr>
        @empty
            <tr><td colspan="{{ $showDescription ? 6 : 5 }}" style="text-align:center;color:#000;padding:12px"><strong>No transactions found</strong></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $showDescription ? 3 : 2 }}" style="font-weight:700 !important;"><strong>Total ({{ $transactions->count() }} records)</strong></td>
            <td class="text-right" style="font-weight:700 !important;"><strong>{{ number_format($totalExpense + $totalWithdrawal, 2) }}</strong></td>
            <td class="text-right" style="font-weight:700 !important;"><strong>{{ number_format($totalIncome + $totalCapital, 2) }}</strong></td>
            <td style="font-weight:700 !important;"></td>
        </tr>
    </tfoot>
</table>

</body>
</html>
