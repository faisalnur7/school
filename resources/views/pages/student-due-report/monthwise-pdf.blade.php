<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 10mm 9mm 12mm 9mm; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: "Times New Roman", Times, serif; font-size: 10px; color: #000; font-weight: 700; }
    body * { color: #000 !important; }
    .center { text-align: center; }
    .school-header-wrap { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; margin-bottom: 14px; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; vertical-align: middle; line-height: 50px; overflow: hidden; background: #fff; }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #000; }
    .school-title { font-size: 18px; font-weight: 700; margin-top: 4px; }
    .school-line { font-size: 11px; margin-top: 2px; }
    .report-title { font-size: 16px; font-weight: 700; margin-top: 12px; }
    .report-subtitle { font-size: 12px; font-weight: 700; margin-top: 4px; }
    .student-summary { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .student-summary td { border: 1px solid #d1d5db; padding: 5px 7px; vertical-align: top; }
    .student-summary .label { font-size: 9px; }
    .student-summary .value { font-size: 11px; }
    .summary { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 10px; }
    .summary td { border: 1px solid #d1d5db; padding: 5px 8px; text-align: center; vertical-align: middle; }
    .summary .label { font-size: 9px; }
    .summary .value { font-size: 13px; }
    .ledger { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8px; }
    .ledger th, .ledger td { border: 1px dotted #777; padding: 4px 6px; vertical-align: top; font-size: 10px; font-weight: 700; }
    .ledger th { text-align: center; font-size: 10.5px; }
    .ledger tr { page-break-inside: avoid; }
    .ledger .month-row td { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 6px; font-size: 12px; }
    .ledger .month-total td { border-top: 1px solid #000; padding-top: 6px; font-size: 10.5px; }
    .ledger .running-total td { border-bottom: 1px solid #000; padding-bottom: 6px; font-size: 10.5px; font-style: italic; }
    .ledger .grand-total td { border-top: 2px solid #000; border-bottom: 2px solid #000; padding-top: 6px; padding-bottom: 6px; font-size: 11px; }
    .ledger .description { text-align: left; }
    .ledger .number { text-align: right; white-space: nowrap; }
    .ledger .due { font-weight: 700; }
    .footer { margin-top: 8px; font-size: 10px; font-style: italic; text-align: right; }
</style>
</head>
<body>
@include('partials.report-pdf-header')

<div class="center">
    <div class="report-title">Monthwise Fee Payment &amp; Inventory Sales Report</div>
    <div class="report-subtitle">Academic Session: {{ $session->name_en }}</div>
</div>

<table class="student-summary">
    <tr>
        <td><div class="label">Student ID</div><div class="value">{{ $student->student_cid ?? '—' }}</div></td>
        <td><div class="label">Student Name</div><div class="value">{{ $student->full_name_en ?? '—' }}</div></td>
        <td><div class="label">Class</div><div class="value">{{ $academicInfo->schoolClass?->name_en ?? '—' }}</div></td>
        <td><div class="label">Section</div><div class="value">{{ $academicInfo->section?->name_en ?? '—' }}</div></td>
    </tr>
</table>

<table class="summary">
    <tr>
        <td><div class="label">Total Amount</div><div class="value">{{ number_format($totals['amount'], 2) }}</div></td>
        <td><div class="label">Total Paid</div><div class="value">{{ number_format($totals['paid'], 2) }}</div></td>
        <td><div class="label">Total Due</div><div class="value">{{ number_format($totals['due'], 2) }}</div></td>
    </tr>
</table>

<table class="ledger">
    <colgroup>
        <col style="width: 19%;"><col style="width: 45%;"><col style="width: 12%;"><col style="width: 12%;"><col style="width: 12%;">
    </colgroup>
    <thead>
        <tr><th>Month</th><th>Description</th><th>Amount</th><th>Paid</th><th>Due</th></tr>
    </thead>
    <tbody>
        @forelse($months as $month)
            <tr class="month-row"><td colspan="5">{{ $month->label }}</td></tr>
            @foreach($month->rows as $row)
                <tr>
                    <td></td>
                    <td class="description">{{ $row->description }}</td>
                    <td class="number">{{ $row->amount > 0 ? number_format($row->amount, 2) : '--' }}</td>
                    <td class="number">{{ $row->paid > 0 ? number_format($row->paid, 2) : '--' }}</td>
                    <td class="number due">{{ number_format($row->due, 2) }}</td>
                </tr>
            @endforeach
            <tr class="month-total">
                <td colspan="2" class="number">Month Total:</td>
                <td class="number">{{ number_format($month->amount, 2) }}</td>
                <td class="number">{{ number_format($month->paid, 2) }}</td>
                <td class="number">{{ number_format($month->due, 2) }}</td>
            </tr>
            <tr class="running-total">
                <td colspan="4" class="number">Running Total:</td>
                <td class="number">{{ number_format($month->running_due, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;padding:12px">No payment or inventory records found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="grand-total">
            <td colspan="2" class="number">Grand Total:</td>
            <td class="number">{{ number_format($totals['amount'], 2) }}</td>
            <td class="number">{{ number_format($totals['paid'], 2) }}</td>
            <td class="number">{{ number_format($totals['due'], 2) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</body>
</html>
