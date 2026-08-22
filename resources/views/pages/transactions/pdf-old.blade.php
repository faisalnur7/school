<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 10mm 9mm 12mm 9mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 10px;
        color: #000;
        font-weight: 700;
    }
    body * { color: #000 !important; }
    .center { text-align: center; }
    .school-header-wrap {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 10px;
        margin-bottom: 14px;
    }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box {
        width: 52px;
        height: 52px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        text-align: center;
        vertical-align: middle;
        line-height: 50px;
        overflow: hidden;
        background: #fff;
    }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #000; }
    .school-title { font-size: 18px; font-weight: 700; margin-top: 4px; }
    .school-line { font-size: 11px; margin-top: 2px; }
    .report-title { font-size: 16px; font-weight: 700; text-decoration: none; margin-top: 12px; }
    .report-subtitle { font-size: 12px; font-weight: 700; margin-top: 4px; }
    .meta-line { font-size: 11px; font-weight: 700; margin-top: 10px; text-align: left; }
    .summary { width: 100%; border-collapse: collapse; margin-top: 12px; margin-bottom: 10px; }
    .summary td {
        border: 1px solid #d1d5db;
        padding: 5px 8px;
        text-align: center;
        vertical-align: middle;
    }
    .summary .label { font-size: 9px; font-weight: 700; }
    .summary .value { font-size: 13px; font-weight: 700; }
    .ledger {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin-top: 8px;
    }
    .ledger th,
    .ledger td {
        border: 1px dotted #777;
        padding: 4px 5px;
        vertical-align: top;
        font-size: 10px;
        font-weight: 700;
    }
    .ledger th {
        text-align: center;
        font-size: 10.5px;
    }
    .ledger .num { text-align: right; }
    .ledger .nowrap { white-space: nowrap; }
    .ledger .section td {
        border-top: 0;
        border-bottom: 0;
        padding-top: 6px;
        padding-bottom: 2px;
        font-size: 11px;
    }
    .ledger .section .section-label {
        font-size: 12px;
        font-weight: 700;
    }
    .ledger .opening td,
    .ledger .closing td {
        font-size: 11px;
    }
    .ledger .opening .label,
    .ledger .closing .label {
        text-align: right;
        padding-right: 8px;
    }
    .ledger .group-total td {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        font-size: 11px;
    }
    .ledger .group-total .label {
        text-align: right;
        padding-right: 8px;
    }
    .ledger .account-head {
        padding-left: 10px;
    }
    .ledger .indent {
        padding-left: 16px;
        font-weight: 700;
    }
    .ledger .muted {
        font-weight: 700;
    }
    .footer {
        margin-top: 8px;
        font-size: 10px;
        font-style: italic;
    }
    .right { text-align: right; }
    .center-text { text-align: center; }
</style>
</head>
<body>
@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $address = $school->address ?: '';
    $contacts = array_filter([$school->contact_number_1, $school->contact_number_2]);
    $logoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $hasLogo = $logoPath && file_exists($logoPath);
    $fromLabel = $reportFromDate ? $reportFromDate->format('d/m/Y') : (request('from') ?: 'All');
    $toLabel = $reportToDate ? $reportToDate->format('d/m/Y') : (request('to') ?: 'All');
    $reportKind = $viewType === 'summary' ? 'Summary' : 'Details';
    $showDescription = $viewType === 'detailed';
    $showDescriptionForType = function (string $type) use ($showDescription, $pdfDescriptionTypes) {
        return $showDescription && in_array($type, $pdfDescriptionTypes ?? [], true);
    };
    $lineNo = 0;
    $openingBalanceDisplay = number_format($openingBalance, 2);
    $closingBalanceDisplay = number_format($closingBalance, 2);
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

<div class="center">
    <div class="report-title">Transaction Report</div>
    <div class="report-subtitle">Generated: {{ now()->format('d M Y, h:i A') }}</div>
</div>

<table class="summary">
    <tr>
        <td><div class="label">Opening Balance</div><div class="value">{{ number_format($openingBalance, 2) }}</div></td>
        <td><div class="label">Income</div><div class="value">{{ number_format($totalIncome, 2) }}</div></td>
        <td><div class="label">Expense</div><div class="value">{{ number_format($totalExpense, 2) }}</div></td>
        <td><div class="label">Capital</div><div class="value">{{ number_format($totalCapital, 2) }}</div></td>
        <td><div class="label">Withdrawal</div><div class="value">{{ number_format($totalWithdrawal, 2) }}</div></td>
        @php $net = ($totalIncome + $totalCapital) - ($totalExpense + $totalWithdrawal); @endphp
        <td><div class="label">Net</div><div class="value">{{ number_format($net, 2) }}</div></td>
        <td><div class="label">Closing Balance</div><div class="value">{{ number_format($closingBalance, 2) }}</div></td>
    </tr>
</table>

<div class="meta-line">Tr. Date : {{ $fromLabel }} &nbsp; To : {{ $toLabel }}</div>

<table class="ledger">
    <colgroup>
        @if($showDescription)
            <col style="width: 12%;">
            <col style="width: 10%;">
            <col style="width: 13%;">
            <col style="width: 35%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
        @else
            <col style="width: 22%;">
            <col style="width: 44%;">
            <col style="width: 17%;">
            <col style="width: 17%;">
        @endif
    </colgroup>
    <thead>
        <tr>
            @if($showDescription)
                <th>A/c Code/Tr #</th>
                <th>Tr. Date</th>
                <th>Vr. #</th>
                <th>Accounts Head</th>
                <th>Dr/Receipts</th>
                <th>Cr/Payments</th>
            @else
                <th>Accounts Head</th>
                <th>Dr/Receipts</th>
                <th>Cr/Payments</th>
                <th>&nbsp;</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if($viewType === 'summary')
            <tr class="opening">
                @if($showDescription)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="label account-head">Opening Balance :</td>
                    <td class="num">{{ $openingBalanceDisplay }}</td>
                    <td class="num">--</td>
                @else
                    <td class="label account-head">Opening Balance :</td>
                    <td class="num">{{ $openingBalanceDisplay }}</td>
                    <td class="num">--</td>
                    <td></td>
                @endif
            </tr>
            @forelse($transactionGroups as $group)
                <tr class="section">
                    @if($showDescription)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="section-label account-head">{{ $group['label'] }}</td>
                        <td></td>
                        <td></td>
                    @else
                        <td class="section-label account-head">{{ $group['label'] }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
                <tr>
                    @if($showDescription)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="indent">{{ $group['label'] }}</td>
                        <td class="num">{{ number_format($group['totalDebit'], 2) }}</td>
                        <td class="num">{{ number_format($group['totalCredit'], 2) }}</td>
                    @else
                        <td class="indent">{{ $group['label'] }}</td>
                        <td class="num">{{ number_format($group['totalDebit'], 2) }}</td>
                        <td class="num">{{ number_format($group['totalCredit'], 2) }}</td>
                        <td></td>
                    @endif
                </tr>
            @empty
                <tr>
                    @if($showDescription)
                        <td colspan="6" class="center-text">No transactions found</td>
                    @else
                        <td colspan="4" class="center-text">No transactions found</td>
                    @endif
                </tr>
            @endforelse
            <tr class="closing">
                @if($showDescription)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="label account-head">Closing Balance :</td>
                    <td class="num">{{ $closingBalanceDisplay }}</td>
                    <td class="num">--</td>
                @else
                    <td class="label account-head">Closing Balance :</td>
                    <td class="num">{{ $closingBalanceDisplay }}</td>
                    <td class="num">--</td>
                    <td></td>
                @endif
            </tr>
        @else
            <tr class="opening">
                @if($showDescription)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="label account-head">Opening Balance :</td>
                    <td class="num">{{ $openingBalanceDisplay }}</td>
                    <td class="num">--</td>
                @else
                    <td class="label account-head">Opening Balance :</td>
                    <td class="num">{{ $openingBalanceDisplay }}</td>
                    <td class="num">--</td>
                    <td></td>
                @endif
            </tr>
            @forelse($transactionGroups as $group)
                <tr class="section">
                    @if($showDescription)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="section-label account-head">{{ $group['label'] }}</td>
                        <td></td>
                        <td></td>
                    @else
                        <td class="section-label account-head">{{ $group['label'] }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
                @foreach($group['rows'] as $txn)
                    @php
                        $lineNo++;
                        $isCredit = in_array($txn->type, ['income', 'capital']);
                        $accountHead = $txn->description
                            ?: ($txn->type === 'income'
                                ? ($txn->incomeCategory?->name ?? '—')
                                : ($txn->type === 'expense'
                                    ? ($txn->expenseCategory?->name ?? '—')
                                    : ($txn->shareholder?->name ?? '—')));
                        $voucherNo = $txn->reference_note ?: ($txn->reference_no ?? '—');
                    @endphp
                    <tr>
                        @if($showDescription)
                            <td class="nowrap">{{ $txn->reference_no ?? '—' }}</td>
                            <td class="nowrap">{{ $txn->transaction_date?->format('d/m/Y') }}</td>
                            <td class="nowrap">{{ $voucherNo }}</td>
                            <td class="account-head">{{ $showDescriptionForType($txn->type) ? $accountHead : '—' }}</td>
                            <td class="num">{{ $isCredit ? '--' : number_format($txn->amount, 2) }}</td>
                            <td class="num">{{ $isCredit ? number_format($txn->amount, 2) : '--' }}</td>
                        @else
                            <td class="account-head">{{ $showDescriptionForType($txn->type) ? $accountHead : '—' }}</td>
                            <td class="num">{{ $isCredit ? '--' : number_format($txn->amount, 2) }}</td>
                            <td class="num">{{ $isCredit ? number_format($txn->amount, 2) : '--' }}</td>
                            <td></td>
                        @endif
                    </tr>
                @endforeach
                <tr class="group-total">
                    @if($showDescription)
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="label account-head">{{ $group['label'] }} Total :</td>
                        <td class="num">{{ number_format($group['totalDebit'], 2) }}</td>
                        <td class="num">{{ number_format($group['totalCredit'], 2) }}</td>
                    @else
                        <td class="label account-head">{{ $group['label'] }} Total :</td>
                        <td class="num">{{ number_format($group['totalDebit'], 2) }}</td>
                        <td class="num">{{ number_format($group['totalCredit'], 2) }}</td>
                        <td></td>
                    @endif
                </tr>
            @empty
                <tr>
                    @if($showDescription)
                        <td colspan="6" class="center-text">No transactions found</td>
                    @else
                        <td colspan="4" class="center-text">No transactions found</td>
                    @endif
                </tr>
            @endforelse
            <tr class="closing">
                @if($showDescription)
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="label account-head">Closing Balance :</td>
                    <td class="num">{{ $closingBalanceDisplay }}</td>
                    <td class="num">--</td>
                @else
                    <td class="label account-head">Closing Balance :</td>
                    <td class="num">{{ $closingBalanceDisplay }}</td>
                    <td class="num">--</td>
                    <td></td>
                @endif
            </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    Print Date : {{ now()->format('d-M-y') }}
</div>

</body>
</html>
