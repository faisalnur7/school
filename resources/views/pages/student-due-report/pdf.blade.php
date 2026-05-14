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
    .section-title { background: #eef2f7; font-weight: bold; }
    .line-row td { border-top: none; }
</style>
</head>
<body>
<h2>Classwise Due Report</h2>
<p class="sub">
    Academic Year: {{ $session?->name_en ?? '—' }}
    &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Class</th>
            <th>Section</th>
            <th>Description</th>
            <th class="text-right">Amount</th>
            <th class="text-right">Paid Amount</th>
            <th class="text-right">Due</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $index => $student)
            @foreach($student->lines as $lineIndex => $line)
                <tr>
                    <td class="text-right">{{ $lineIndex === 0 ? $index + 1 : '' }}</td>
                    <td>{{ $lineIndex === 0 ? ($student->cid ?? '—') : '' }}</td>
                    <td>{{ $lineIndex === 0 ? $student->name : '' }}</td>
                    <td>{{ $lineIndex === 0 ? $student->class_name : '' }}</td>
                    <td>{{ $lineIndex === 0 ? $student->section_name : '' }}</td>
                    <td>{{ $line->description }}</td>
                    <td class="text-right">{{ number_format($line->amount, 2) }}</td>
                    <td class="text-right">{{ number_format($line->paid, 2) }}</td>
                    <td class="text-right">{{ number_format($line->due, 2) }}</td>
                </tr>
            @endforeach
            <tr class="section-title">
                <td colspan="6" class="text-right">Student Total</td>
                <td class="text-right">{{ number_format($student->fees_total, 2) }}</td>
                <td class="text-right">{{ number_format($student->paid_amount, 2) }}</td>
                <td class="text-right">{{ number_format($student->due, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6">Grand Total</td>
            <td class="text-right">{{ number_format($totals['amount'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['paid'], 2) }}</td>
            <td class="text-right">{{ number_format($totals['due'], 2) }}</td>
        </tr>
    </tfoot>
</table>
</body>
</html>