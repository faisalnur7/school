<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th, td { border: 1px solid #999; padding: 3px 5px; }
    th.text-right, td.text-right { white-space: nowrap; }
    thead { background: #333; color: #fff; }
    tfoot { background: #e9ecef; font-weight: bold; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .bg-light { background: #f8f9fa; font-weight: bold; }
    h2 { text-align: center; margin-bottom: 4px; }
    .subtitle { text-align: center; margin-bottom: 12px; color: #555; }
    .section-title { font-weight: bold; margin: 12px 0 4px; }
</style>
</head>
<body>
<h2>Student Receivable Report</h2>
<p class="subtitle">Date Range: {{ $fromDate }} to {{ $toDate }}</p>

<table>
    <thead>
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">Student ID</th>
            <th rowspan="2">Student Name</th>
            <th rowspan="2">Class</th>
            <th rowspan="2">Section</th>
            <th rowspan="2">Fee Category</th>
            @foreach($months as $monthLabel)
                <th class="text-right">{{ $monthLabel }}</th>
            @endforeach
            <th class="text-right" rowspan="2">Total</th>
        </tr>
        <tr>
            @foreach($months as $monthKey => $monthLabel)
                <th class="text-right" style="font-size:9px;">{{ number_format($totals['months'][$monthKey] ?? 0, 0) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $student)
            @php
                $visibleCats = $categories->filter(fn($c) => isset($student->categories[$c->id]) && array_sum($student->categories[$c->id]) > 0)->values();
            @endphp
            @if($visibleCats->isEmpty()) @continue @endif
            @foreach($visibleCats as $cat)
                @php $catMonths = $student->categories[$cat->id]; $catTotal = array_sum($catMonths); @endphp
                <tr>
                    @if($loop->first)
                        <td rowspan="{{ $visibleCats->count() + 3 }}" class="text-center">{{ $index + 1 }}</td>
                        <td rowspan="{{ $visibleCats->count() + 3 }}">{{ $student->student_cid ?? '—' }}</td>
                        <td rowspan="{{ $visibleCats->count() + 3 }}">{{ $student->student_name }}</td>
                        <td rowspan="{{ $visibleCats->count() + 3 }}">{{ $student->class_name }}</td>
                        <td rowspan="{{ $visibleCats->count() + 3 }}">{{ $student->section_name }}</td>
                    @endif
                    <td>{{ $cat->name }}</td>
                    @foreach($months as $monthKey => $monthLabel)
                        <td class="text-right">{{ ($catMonths[$monthKey] ?? 0) > 0 ? number_format($catMonths[$monthKey], 2) : '—' }}</td>
                    @endforeach
                    <td class="text-right"><strong>{{ number_format($catTotal, 2) }}</strong></td>
                </tr>
            @endforeach
            <tr class="bg-light">
                <td>TOTAL</td>
                @foreach($months as $monthKey => $monthLabel)
                    <td class="text-right">{{ number_format($student->months[$monthKey] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($student->total, 2) }}</td>
            </tr>
            <tr class="bg-light">
                <td>PAID</td>
                @foreach($months as $monthKey => $monthLabel)
                    <td class="text-right">{{ number_format($student->paidMonths[$monthKey] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($student->paid_total ?? 0, 2) }}</td>
            </tr>
            <tr class="bg-light">
                <td>DUE</td>
                @foreach($months as $monthKey => $monthLabel)
                    <td class="text-right">{{ number_format($student->dueMonths[$monthKey] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($student->due_total ?? 0, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" class="text-right">Grand Total</td>
            <td></td>
            @foreach($months as $monthKey => $monthLabel)
                <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
            @endforeach
            <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
        </tr>
    </tfoot>
</table>

<p class="section-title">Category-wise Monthly Summary</p>
<table>
    <thead>
        <tr>
            <th>Fee Category</th>
            @foreach($months as $monthLabel)
                <th class="text-right">{{ $monthLabel }}</th>
            @endforeach
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $cat)
            @php $catTotal = array_sum($totals['categories'][$cat->id] ?? []); @endphp
            @if($catTotal <= 0) @continue @endif
            <tr>
                <td>{{ $cat->name }}</td>
                @foreach($months as $monthKey => $monthLabel)
                    <td class="text-right">{{ number_format($totals['categories'][$cat->id][$monthKey] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right"><strong>{{ number_format($catTotal, 2) }}</strong></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>Grand Total</td>
            @foreach($months as $monthKey => $monthLabel)
                <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
            @endforeach
            <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>
