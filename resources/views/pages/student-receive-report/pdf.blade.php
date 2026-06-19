<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; }
    h2 { text-align: center; margin-bottom: 4px; font-size: 16px; }
    p.sub { text-align: center; margin: 0 0 12px; font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 5px 6px; }
    th { background: #444; color: #fff; font-size: 10px; }
    td { font-size: 10px; vertical-align: top; }
    tfoot td { font-weight: bold; background: #f0f0f0; }
    .text-right { text-align: right; }
    .section-total { background: #f8fafc; font-weight: bold; }
</style>
</head>
<body>
<h2>Student Receive Report</h2>
<p class="sub">Date Range: {{ $fromDate ?? '—' }} to {{ $toDate ?? '—' }}</p>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Description</th>
            @foreach($months as $monthLabel)
                <th class="text-right">{{ $monthLabel }}</th>
            @endforeach
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $student)
            @foreach($student->lines as $lineIndex => $line)
                <tr>
                    @if($lineIndex === 0)
                        <td rowspan="{{ $student->lines->count() + 1 }}" class="text-right">{{ $index + 1 }}</td>
                        <td rowspan="{{ $student->lines->count() + 1 }}">{{ $student->student_cid ?? '—' }}</td>
                        <td rowspan="{{ $student->lines->count() + 1 }}">{{ $student->student_name }}</td>
                        <td rowspan="{{ $student->lines->count() + 1 }}">{{ $student->class_name }}</td>
                        <td rowspan="{{ $student->lines->count() + 1 }}">{{ $student->section_name }}</td>
                    @endif
                    <td>{{ $line->description }}</td>
                    @foreach($months as $monthKey => $monthLabel)
                        <td class="text-right">{{ number_format($line->monthTotals[$monthKey] ?? 0, 2) }}</td>
                    @endforeach
                    <td class="text-right">{{ number_format($line->total, 2) }}</td>
                </tr>
            @endforeach
            <tr class="section-total">
                <td></td>
                @foreach($months as $monthKey => $monthLabel)
                    <td class="text-right">{{ number_format($student->monthTotals[$monthKey] ?? 0, 2) }}</td>
                @endforeach
                <td class="text-right">{{ number_format($student->student_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Grand Total</td>
            @foreach($months as $monthKey => $monthLabel)
                <td class="text-right">{{ number_format($totals['months'][$monthKey] ?? 0, 2) }}</td>
            @endforeach
            <td class="text-right">{{ number_format($totals['total'], 2) }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>