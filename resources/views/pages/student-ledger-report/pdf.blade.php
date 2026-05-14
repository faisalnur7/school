<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; }
    .header { text-align: center; margin-bottom: 8px; }
    .header h2 { font-size: 15px; margin: 0; }
    .header h4 { font-size: 12px; margin: 2px 0; color: #555; }
    .header p  { font-size: 10px; margin: 2px 0; color: #777; }
    .info-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
    .info-table td { padding: 3px 6px; font-size: 10px; }
    .info-table th { padding: 3px 6px; font-size: 10px; font-weight: bold; width: 120px; }
    table.ledger { width: 100%; border-collapse: collapse; margin-top: 4px; }
    table.ledger th { background: #2d3748; color: #fff; padding: 5px 6px; text-align: left; font-size: 9px; }
    table.ledger td { padding: 4px 6px; border-bottom: 1px solid #ddd; font-size: 9px; }
    table.ledger tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .month-header { background: #eef2f7; font-weight: bold; padding: 4px 6px; margin-top: 8px; font-size: 10px; border-left: 3px solid #2d3748; }
    .text-right { text-align: right; }
    .text-danger { color: #c0392b; }
    .text-success { color: #27ae60; }
    .summary-table { width: 40%; margin-left: 60%; border-collapse: collapse; margin-top: 10px; }
    .summary-table th, .summary-table td { padding: 4px 8px; border: 1px solid #ddd; font-size: 10px; }
    .summary-table th { background: #f5f5f5; }
    hr { border: none; border-top: 1px solid #ccc; margin: 6px 0; }
</style>
</head>
<body>

<div class="header">
    @if(!empty($school->logo) && file_exists(public_path($school->logo)))
        <img src="{{ public_path($school->logo) }}" height="50" style="margin-bottom:4px;"><br>
    @endif
    <h2>{{ $school->name ?? 'School Name' }}</h2>
    <h4>Student Payment Ledger</h4>
    <p>Session: {{ $session->name_en }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>

<hr>

@php $ai = $student->academicInformations->first(); @endphp

<table class="info-table">
    <tr>
        <td><th>Student Name</th> {{ $student->full_name_en }}</td>
        <td><th>CID</th> {{ $student->student_cid }}</td>
        <td><th>Roll</th> {{ $ai?->roll ?? '—' }}</td>
    </tr>
    <tr>
        <td><th>Class</th> {{ $ai?->schoolClass?->name_en ?? '—' }}</td>
        <td><th>Section</th> {{ $ai?->section?->name_en ?? '—' }}</td>
        <td><th>Group</th> {{ $ai?->group?->name_en ?? '—' }}</td>
    </tr>
    <tr>
        <td><th>Parent</th> {{ $student->guardian_name ?? $student->father_name ?? '—' }}</td>
        <td><th>Mobile</th> {{ $student->guardian_phone ?? $student->father_phone ?? '—' }}</td>
        <td>
            <th>Balance</th>
            @if($closing_balance > 0)
                <span class="text-danger">Due: {{ number_format($closing_balance, 2) }}</span>
            @else
                <span class="text-success">Advance: {{ number_format(abs($closing_balance), 2) }}</span>
            @endif
        </td>
    </tr>
</table>

<hr>

@if($months->isEmpty())
    <p style="text-align:center; color:#999;">No transactions found for this session.</p>
@else
    @php $runningBalance = 0.0; @endphp
    @foreach($months as $month)
        <div class="month-header">{{ $month->label }}</div>
        <table class="ledger">
            <thead>
                <tr>
                    <th>Voucher</th>
                    <th>Date</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th class="text-right">Dues</th>
                    <th class="text-right">Received</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($month->rows as $row)
                    @php $runningBalance = $runningBalance + $row['dues'] - $row['received']; @endphp
                    <tr>
                        <td>{{ $row['voucher'] }}</td>
                        <td>{{ $row['date'] }}</td>
                        <td>{{ $row['code'] }}</td>
                        <td>{{ $row['description'] }}</td>
                        <td class="text-right">{{ $row['dues'] > 0 ? number_format($row['dues'], 2) : '—' }}</td>
                        <td class="text-right">{{ $row['received'] > 0 ? number_format($row['received'], 2) : '—' }}</td>
                        <td class="text-right {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? 'Dr' : 'Cr' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Month Total</td>
                    <td class="text-right">{{ number_format($month->month_dues, 2) }}</td>
                    <td class="text-right">{{ number_format($month->month_recv, 2) }}</td>
                    <td class="text-right {{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format(abs($runningBalance), 2) }} {{ $runningBalance > 0 ? 'Dr' : 'Cr' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    @endforeach

    <table class="summary-table">
        <tr><th>Total Dues</th><td class="text-right text-danger">{{ number_format($total_dues, 2) }}</td></tr>
        <tr><th>Total Received</th><td class="text-right text-success">{{ number_format($total_received, 2) }}</td></tr>
        <tr>
            <th>{{ $closing_balance > 0 ? 'Net Due' : 'Advance' }}</th>
            <td class="text-right {{ $closing_balance > 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format(abs($closing_balance), 2) }}
            </td>
        </tr>
    </table>
@endif

</body>
</html>
