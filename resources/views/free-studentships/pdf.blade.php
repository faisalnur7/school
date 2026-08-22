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
    .meta-line { font-size: 11px; font-weight: 700; margin-top: 10px; }
    .summary { width: 100%; border-collapse: collapse; margin-top: 12px; margin-bottom: 10px; }
    .summary td { border: 1px solid #d1d5db; padding: 5px 8px; text-align: center; vertical-align: middle; }
    .summary .label { font-size: 9px; font-weight: 700; }
    .summary .value { font-size: 13px; font-weight: 700; }
    .ledger { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 8px; }
    .ledger th, .ledger td { border: 1px dotted #777; padding: 4px 5px; vertical-align: top; font-size: 10px; font-weight: 700; }
    .ledger th { text-align: center; font-size: 10.5px; }
    .ledger tr { page-break-inside: avoid; }
    .ledger .center-text { text-align: center; }
    .ledger .right { text-align: right; }
    .ledger .section td { border-top: 0; border-bottom: 0; padding-top: 6px; padding-bottom: 2px; font-size: 11px; }
    .ledger .group-total td { border-top: 1px solid #000; border-bottom: 1px solid #000; font-size: 11px; }
    .ledger .group-total .label { text-align: right; padding-right: 8px; }
    .student-name { font-size: 11px; }
    .academic-info { font-size: 9px; line-height: 1.35; }
    .footer { margin-top: 8px; font-size: 10px; font-style: italic; }
</style>
</head>
<body>
@php
    $totalRecords = $freeStudentships->count();
    $activeRecords = $freeStudentships->where('status', 'active')->count();
    $fixedRecords = $freeStudentships->where('type', 'fixed')->count();
    $percentageRecords = $freeStudentships->where('type', 'percentage')->count();
@endphp

@include('partials.report-pdf-header')

<div class="center">
    <div class="report-title">Free Studentships Report</div>
    <div class="report-subtitle">Student discount and approval register</div>
</div>

<table class="summary">
    <tr>
        <td><div class="label">Total Records</div><div class="value">{{ $totalRecords }}</div></td>
        <td><div class="label">Active</div><div class="value">{{ $activeRecords }}</div></td>
        <td><div class="label">Fixed Amount</div><div class="value">{{ $fixedRecords }}</div></td>
        <td><div class="label">Percentage</div><div class="value">{{ $percentageRecords }}</div></td>
    </tr>
</table>

<div class="meta-line">
    Academic Session: {{ $session?->name_en ?? 'All Sessions' }}
    &nbsp;&nbsp; | &nbsp;&nbsp;
    Discount Category: {{ $discountCategory?->name ?? 'All Categories' }}
    &nbsp;&nbsp; | &nbsp;&nbsp;
    Generated: {{ now()->format('d M Y, h:i A') }}
</div>

<table class="ledger">
    <colgroup>
        <col style="width: 4%;">
        <col style="width: 9%;">
        <col style="width: 16%;">
        <col style="width: 19%;">
        <col style="width: 11%;">
        <col style="width: 13%;">
        <col style="width: 8%;">
        <col style="width: 8%;">
        <col style="width: 7%;">
        <col style="width: 5%;">
    </colgroup>
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Academic Info</th>
            <th>Fee Category</th>
            <th>Discount Category</th>
            <th>Type</th>
            <th>Value</th>
            <th>Permitted By</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($freeStudentships as $i => $freeStudentship)
            @php $ai = $freeStudentship->studentAcademicInformation; @endphp
            <tr>
                <td class="center-text">{{ $i + 1 }}</td>
                <td>{{ $freeStudentship->student->student_cid ?? '—' }}</td>
                <td>
                    <span class="student-name">{{ $freeStudentship->student->full_name_en ?? '—' }}</span><br>
                    Father: {{ $freeStudentship->student->father_name ?? '—' }}
                </td>
                <td class="academic-info">
                    Class: {{ $ai?->schoolClass?->name_en ?? '—' }}<br>
                    Section: {{ $ai?->section?->name_en ?? '—' }}<br>
                    Group: {{ $ai?->group?->name_en ?? '—' }}<br>
                    Roll: {{ $ai?->roll ?? '—' }}
                </td>
                <td>{{ $freeStudentship->feeCategory->name ?? '—' }}</td>
                <td>{{ $freeStudentship->discountCategory->name ?? '—' }}</td>
                <td class="center-text">{{ ucfirst($freeStudentship->type) }}</td>
                <td class="right">
                    @if ($freeStudentship->type === 'fixed')
                        {{ number_format($freeStudentship->amount, 2) }}
                    @else
                        {{ $freeStudentship->percentage }}%
                    @endif
                </td>
                <td>{{ $freeStudentship->permitted_by ?? '—' }}</td>
                <td class="center-text">{{ ucfirst($freeStudentship->status) }}</td>
            </tr>
        @empty
            <tr><td colspan="10" class="center-text">No free studentships found</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="group-total">
            <td colspan="7" class="right">Total Records</td>
            <td class="right">{{ $totalRecords }}</td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
</table>

<div class="footer">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</body>
</html>
