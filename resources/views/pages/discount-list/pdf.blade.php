<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 9px; color: #222; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 13px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 9px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 5px 6px; text-align: left; }
    td { padding: 4px 6px; border-bottom: 1px solid #ddd; }
    tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .text-right { text-align: right; }
    .discount { color: #856404; font-weight: bold; }
    .paid { color: #155724; }
</style>
</head>
<body>
<h2>Discount List</h2>
<p class="sub">
    Academic Year: {{ $session?->name_en ?? '—' }}
    &nbsp;|&nbsp; Month: {{ $month }}
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</p>

@php
    $sumGross    = $rows->sum('gross_amount');
    $sumScholar  = $rows->sum('scholarship');
    $sumDiscount = $rows->sum('discount');
    $sumPaid     = $rows->sum('paid');
@endphp

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Receipt No</th>
            <th>Date</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Group</th>
            <th class="text-right">Gross</th>
            <th class="text-right">Scholarship</th>
            <th class="text-right">Discount</th>
            <th class="text-right">Paid</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row->receipt_no }}</td>
                <td>{{ $row->payment_date }}</td>
                <td>{{ $row->cid ?? '—' }}</td>
                <td><strong>{{ $row->name }}</strong></td>
                <td>{{ $row->class_name }}</td>
                <td>{{ $row->section_name }}</td>
                <td>{{ $row->group_name }}</td>
                <td class="text-right">{{ number_format($row->gross_amount, 2) }}</td>
                <td class="text-right" style="color:#059669;font-weight:bold">{{ $row->scholarship > 0 ? '-'.number_format($row->scholarship, 2) : '—' }}</td>
                <td class="text-right discount">{{ $row->discount > 0 ? '-'.number_format($row->discount, 2) : '—' }}</td>
                <td class="text-right paid">{{ number_format($row->paid, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8">Total ({{ $rows->count() }} records)</td>
            <td class="text-right">{{ number_format($sumGross, 2) }}</td>
            <td class="text-right" style="color:#059669;font-weight:bold">-{{ number_format($sumScholar, 2) }}</td>
            <td class="text-right discount">-{{ number_format($sumDiscount, 2) }}</td>
            <td class="text-right paid">{{ number_format($sumPaid, 2) }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
