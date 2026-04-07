<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color: #222; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 11px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 6px 8px; text-align: left; }
    td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
    tr.due { background: #ffe0e0; }
    tr.paid { background: #e0ffe0; }
    tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .text-right { text-align: right; }
    .badge-success { color: #155724; background: #d4edda; padding: 2px 6px; border-radius: 3px; }
    .badge-warning { color: #856404; background: #fff3cd; padding: 2px 6px; border-radius: 3px; }
    .badge-danger  { color: #721c24; background: #f8d7da; padding: 2px 6px; border-radius: 3px; }
</style>
</head>
<body>
<h2>Fee Due Report</h2>
<p class="sub">
    Academic Year: {{ $session?->name_en ?? '—' }}
    @if($month) &nbsp;|&nbsp; Month: {{ $month }} @endif
    &nbsp;|&nbsp; Mode: {{ ucfirst($mode) }}
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</p>

@php
    $sumFees = $rows->sum('total_fees');
    $sumPaid = $rows->sum('total_paid');
    $sumDue  = $rows->sum('due');
@endphp

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Class</th>
            @if($mode === 'monthly')
                <th>Section</th>
                <th>Group</th>
            @endif
            <th class="text-right">Total Fees</th>
            <th class="text-right">Total Paid</th>
            <th class="text-right">Due</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            @php $isDue = $row->due > 0; @endphp
            <tr class="{{ $isDue ? 'due' : 'paid' }}">
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $row->class_name }}</strong></td>
                @if($mode === 'monthly')
                    <td>{{ $row->section_name }}</td>
                    <td>{{ $row->group_name }}</td>
                @endif
                <td class="text-right">{{ number_format($row->total_fees, 2) }}</td>
                <td class="text-right">{{ number_format($row->total_paid, 2) }}</td>
                <td class="text-right">{{ number_format($row->due, 2) }}</td>
                <td>
                    @if($row->due <= 0)
                        <span class="badge-success">Paid</span>
                    @elseif($row->total_paid > 0)
                        <span class="badge-warning">Partial</span>
                    @else
                        <span class="badge-danger">Unpaid</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="{{ $mode === 'monthly' ? 4 : 2 }}">Total</td>
            <td class="text-right">{{ number_format($sumFees, 2) }}</td>
            <td class="text-right">{{ number_format($sumPaid, 2) }}</td>
            <td class="text-right">{{ number_format($sumDue, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>
</body>
</html>
