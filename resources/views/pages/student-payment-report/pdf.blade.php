<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; color: #222; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    h4 { font-size: 12px; margin: 15px 0 8px 0; border-bottom: 2px solid #333; padding-bottom: 4px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 11px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 6px 8px; text-align: left; }
    td { padding: 5px 8px; border-bottom: 1px solid #ddd; }
    tfoot td { font-weight: bold; background: #f0f0f0; border-top: 2px solid #aaa; }
    .text-right { text-align: right; }
    .grand-total-box { text-align: center; margin: 15px 0; padding: 10px; background: #e8f4f8; border: 2px solid #2d3748; }
    .grand-total-box strong { font-size: 13px; }
    .grand-total-box .amount { font-size: 16px; font-weight: bold; color: #0066cc; }
</style>
</head>
<body>
<h2>Student Payment Report</h2>
<p class="sub">{{ $dateLabel ?? 'All Dates' }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>

@php
    $totalGrandTotal = $rows->sum(fn($g) => $g->students->sum('grand_total'));
@endphp

<div class="grand-total-box">
    <strong>Grand Total of All Classes:</strong><br>
    <span class="amount">{{ number_format($totalGrandTotal, 2) }}</span>
</div>

@foreach($rows as $group)
    <h4>Class: {{ $group->class_name }} | Section: {{ $group->section_name }}</h4>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                @foreach($feeCategories as $category)
                    <th class="text-right">{{ $category->name }}</th>
                @endforeach
                @foreach($invCategories as $category)
                    <th class="text-right">{{ $category->name }}</th>
                @endforeach
                <th class="text-right">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group->students as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->student_cid }}</td>
                    <td>{{ $row->student_name }}</td>
                    @foreach($feeCategories as $category)
                        <td class="text-right">{{ number_format($row->{'fee_' . $category->id}, 2) }}</td>
                    @endforeach
                    @foreach($invCategories as $category)
                        <td class="text-right">{{ number_format($row->{'inv_' . $category->id}, 2) }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($row->grand_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">Subtotal</td>
                @foreach($feeCategories as $category)
                    <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{'fee_' . $category->id}), 2) }}</td>
                @endforeach
                @foreach($invCategories as $category)
                    <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{'inv_' . $category->id}), 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endforeach
</body>
</html>
