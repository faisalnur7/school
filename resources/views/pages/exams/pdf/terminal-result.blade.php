<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'SolaimanLipi', Arial, sans-serif; font-size: 9px; color: #333; }
    h2 { text-align: center; margin: 0 0 3px; font-size: 14px; }
    h4 { text-align: center; margin: 0 0 8px; font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2c3e50; color: #fff; padding: 4px 5px; text-align: center; font-size: 8px; }
    td { padding: 3px 5px; border: 1px solid #ccc; text-align: center; }
    tr:nth-child(even) { background: #f9f9f9; }
    .fail-row { background: #fde8e8 !important; }
    .fail-cell { background: #f5c6cb; color: #721c24; font-weight: bold; }
    .pass { color: #155724; font-weight: bold; }
    .fail { color: #721c24; font-weight: bold; }
    .rank-1 { background: #ffd700; font-weight: bold; }
    .rank-2 { background: #c0c0c0; font-weight: bold; }
    .rank-3 { background: #cd7f32; font-weight: bold; }
    .stats { display: flex; gap: 15px; margin: 6px 0; font-size: 9px; }
    .stat-box { border: 1px solid #ddd; padding: 3px 8px; border-radius: 3px; }
</style>
</head>
<body>
<h2>{{ $exam->name }} — Terminal Result</h2>
<h4>
    Class: {{ $selectedClass->name_en ?? '' }}
    &mdash; Session: {{ $exam->academicSession->name_en ?? $exam->academicSession->name_bn ?? '' }}
    &mdash; Year: {{ $exam->year }}
</h4>

@php
    $totalStudents = count($results);
    $passedCount   = count(array_filter($results, fn($r) => !$r['has_failed']));
    $failedCount   = $totalStudents - $passedCount;
    $avgGpa        = $totalStudents > 0 ? round(array_sum(array_column($results, 'gpa')) / $totalStudents, 2) : 0;
@endphp

<div class="stats">
    <div class="stat-box">Total: <strong>{{ $totalStudents }}</strong></div>
    <div class="stat-box">Passed: <strong>{{ $passedCount }}</strong></div>
    <div class="stat-box">Failed: <strong>{{ $failedCount }}</strong></div>
    <div class="stat-box">Pass Rate: <strong>{{ $totalStudents > 0 ? round(($passedCount/$totalStudents)*100, 1) : 0 }}%</strong></div>
    <div class="stat-box">Avg GPA: <strong>{{ $avgGpa }}</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th rowspan="2">Rank</th>
            <th rowspan="2">Student Name</th>
            @foreach($subjects as $subject)
            <th>{{ Str::limit($subject->name, 10) }}</th>
            @endforeach
            <th rowspan="2">Total</th>
            <th rowspan="2">%</th>
            <th rowspan="2">GPA</th>
            <th rowspan="2">Grade</th>
            <th rowspan="2">Status</th>
        </tr>
        <tr>
            @foreach($subjects as $subject)
            @php $cfg = $subject->getEffectiveMarksForClass($selectedClass->id ?? 0); @endphp
            <th style="color:#ffd700">/{{ $cfg['total_marks'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($results as $row)
        <tr class="{{ $row['has_failed'] ? 'fail-row' : '' }}">
            <td class="{{ $row['rank'] == 1 ? 'rank-1' : ($row['rank'] == 2 ? 'rank-2' : ($row['rank'] == 3 ? 'rank-3' : '')) }}">
                {{ $row['rank'] }}
            </td>
            <td style="text-align:left">{{ $row['student']->full_name_en }}</td>
            @foreach($subjects as $subject)
            @php $sr = $row['subject_results'][$subject->id] ?? null; @endphp
            <td class="{{ $sr && !$sr['passed'] ? 'fail-cell' : '' }}">
                @if($sr)
                    {{ $sr['is_absent'] ? 'AB' : number_format($sr['obtained'], 0) }}
                    ({{ $sr['is_absent'] ? 'AB' : $sr['letter_grade'] }})
                @else
                    0 (F)
                @endif
            </td>
            @endforeach
            <td><strong>{{ number_format($row['total_obtained'], 0) }}</strong></td>
            <td>{{ $row['percentage'] }}%</td>
            <td><strong>{{ $row['gpa'] }}</strong></td>
            <td>{{ $row['gpa_label'] }}</td>
            <td class="{{ $row['has_failed'] ? 'fail' : 'pass' }}">{{ $row['status'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align:right; margin-top:15px; font-size:8px; color:#999;">
    Generated: {{ now()->format('d M Y H:i') }}
</p>
</body>
</html>
