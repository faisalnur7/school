<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Yearly Final Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        .report-table th, .report-table td { border: 1px solid #333; padding: 6px; text-align: center; }
        .report-header { margin-bottom: 16px; }
        .report-header .title { font-size: 16px; font-weight: bold; }
        .meta { margin-bottom: 8px; }
        .meta span { display: inline-block; min-width: 170px; }
    </style>
</head>
<body>
    <div class="report-header">
        <div class="title">Yearly Final Report</div>
        <div class="meta">
            <span>Session: {{ App\Models\AcademicSession::find(optional($filters)['session_id'])?->name_en ?? '—' }}</span>
            <span>Class: {{ App\Models\SchoolClass::find(optional($filters)['class_id'])?->name_en ?? '—' }}</span>
            <span>Section: {{ App\Models\Section::find(optional($filters)['section_id'])?->name_en ?? 'All' }}</span>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Pair 1 Total</th>
                <th>Pair 1 Weighted ({{ $pairWeights[1] ?? 0 }}%)</th>
                <th>Pair 2 Total</th>
                <th>Pair 2 Weighted ({{ $pairWeights[2] ?? 0 }}%)</th>
                <th>Pair 3 Total</th>
                <th>Pair 3 Weighted ({{ $pairWeights[3] ?? 0 }}%)</th>
                <th>Grand Total</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                <td>{{ $row['student']->full_name_en ?? $row['student']->full_name_bn }}</td>
                <td>{{ $row['totals'][1]['total'] ?? 0 }}</td>
                <td>{{ $row['totals'][1]['weighted'] ?? 0 }}</td>
                <td>{{ $row['totals'][2]['total'] ?? 0 }}</td>
                <td>{{ $row['totals'][2]['weighted'] ?? 0 }}</td>
                <td>{{ $row['totals'][3]['total'] ?? 0 }}</td>
                <td>{{ $row['totals'][3]['weighted'] ?? 0 }}</td>
                <td>{{ $row['grand_total'] }}</td>
                <td>{{ $row['position'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div><strong>Highest Grand Total:</strong> {{ $highest }}</div>
</body>
</html>
