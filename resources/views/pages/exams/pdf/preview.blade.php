<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'SolaimanLipi', Arial, sans-serif; font-size: 11px; color: #333; }
    h2 { text-align: center; margin: 0 0 4px; font-size: 16px; }
    h4 { text-align: center; margin: 0 0 10px; font-size: 12px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #2c3e50; color: #fff; padding: 6px 8px; text-align: center; }
    td { padding: 5px 8px; border: 1px solid #ddd; }
    tr:nth-child(even) { background: #f9f9f9; }
    .fail { background: #fde8e8 !important; }
    .absent { background: #f0f0f0 !important; color: #999; }
    .badge-pass { color: #155724; font-weight: bold; }
    .badge-fail { color: #721c24; font-weight: bold; }
    .stats { display: flex; gap: 20px; margin: 10px 0; font-size: 11px; }
    .stat-box { border: 1px solid #ddd; padding: 5px 12px; border-radius: 4px; }
</style>
</head>
<body>
<h2>{{ $exam->name }}</h2>
<h4>
    Subject: {{ $subject->name }} &mdash;
    Class: {{ $selectedClass->name_en ?? '' }}
    &mdash; Year: {{ $exam->year }}
</h4>

@php
    $passedCount = $marks->filter(fn($m) => !$m->is_absent && $m->total >= $passMark)->count();
    $failedCount = $marks->filter(fn($m) => $m->is_absent || $m->total < $passMark)->count();
    $avgMarks    = $marks->where('is_absent', false)->avg('total');
@endphp

<div class="stats">
    <div class="stat-box">Total: <strong>{{ $marks->count() }}</strong></div>
    <div class="stat-box">Passed: <strong>{{ $passedCount }}</strong></div>
    <div class="stat-box">Failed: <strong>{{ $failedCount }}</strong></div>
    <div class="stat-box">Average: <strong>{{ number_format($avgMarks, 1) }}</strong></div>
    <div class="stat-box">Pass Mark: <strong>{{ $passMark }}</strong></div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Student Name</th>
            <th>Obtained</th>
            <th>Grade</th>
            <th>GPA</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($marks as $i => $mark)
        <tr class="{{ $mark->is_absent ? 'absent' : ($mark->total < $passMark ? 'fail' : '') }}">
            <td style="text-align:center">{{ $i + 1 }}</td>
            <td>{{ $mark->student->full_name_en }}</td>
            <td style="text-align:center">
                {{ $mark->is_absent ? 'Absent' : number_format($mark->total, 1) }}
            </td>
            <td style="text-align:center">{{ $mark->is_absent ? 'AB' : $mark->letter_grade }}</td>
            <td style="text-align:center">{{ $mark->is_absent ? '-' : $mark->gpa }}</td>
            <td style="text-align:center">
                @if($mark->is_absent)
                    <span>Absent</span>
                @elseif($mark->total >= $passMark)
                    <span class="badge-pass">Passed</span>
                @else
                    <span class="badge-fail">Failed</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align:right; margin-top:20px; font-size:10px; color:#999;">
    Generated: {{ now()->format('d M Y H:i') }}
</p>
</body>
</html>
