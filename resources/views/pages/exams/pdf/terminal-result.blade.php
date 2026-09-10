<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'SolaimanLipi', Arial, sans-serif; font-size: 12px; color: #1e293b; font-weight: bold; background: #f8fafc; }
    h2 { text-align: center; margin: 0 0 4px; font-size: 18px; color: #172554; }
    h4 { text-align: center; margin: 0 0 10px; font-size: 12px; color: #64748b; }
    .school-header { width: 100%; margin-bottom: 8px; border-bottom: 2px solid #2563eb; padding-bottom: 7px; }
    .school-header td { border: 0; background: transparent; padding: 0; vertical-align: middle; }
    .school-logo-cell { width: 58px; text-align: left; }
    .school-logo { width: 48px; height: 48px; border: 1px solid #bfdbfe; border-radius: 8px; }
    .school-name { font-size: 16px; color: #172554; font-weight: bold; }
    .school-address { margin-top: 2px; font-size: 10px; color: #64748b; }
    .school-report-title { margin-top: 3px; font-size: 12px; color: #2563eb; font-weight: bold; }
    .terminal-pdf-result-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 8px; border: 1px solid #dbe4ef; }
    th { background: #eff6ff; color: #1e3a8a; padding: 4px 5px; border: 1px solid #dbe4ef; border-top: 2px solid #bfdbfe; text-align: center; font-size: 12px !important; font-weight: bold; }
    td { padding: 4px 5px; border: 1px solid #e2e8f0; background: #fff; text-align: center; font-size: 12px !important; font-weight: bold; }
    .terminal-mark-value { font-size: 12px !important; font-weight: bold; }
    .terminal-student-name { font-size: 13px !important; font-weight: bold; white-space: nowrap; }
    .terminal-summary-cell { font-size: 12px !important; font-weight: bold; }
    tr:nth-child(even) td { background: #f8fafc; }
    .fail-row { background: #fde8e8 !important; }
    .fail-cell { background: #f5c6cb; color: #721c24; font-weight: bold; }
    .pass { color: #155724; font-weight: bold; }
    .fail { color: #721c24; font-weight: bold; }
    .rank-1 { background: #ffd700; font-weight: bold; }
    .rank-2 { background: #c0c0c0; font-weight: bold; }
    .rank-3 { background: #cd7f32; font-weight: bold; }
    .stats { width: 100%; table-layout: fixed; border-spacing: 6px 0; margin: 8px -6px 10px; font-size: 10px; border: 0; }
    .stats .stat-box { background: #fff; border: 1px solid #dbe4ef; padding: 6px 7px; border-radius: 7px; color: #475569; }
    .stat-box strong { display: block; margin-top: 2px; font-size: 14px; color: #172554; }
</style>
</head>
<body>
<table class="school-header">
    <tr>
        <td class="school-logo-cell">
            @if ($school->logo && file_exists(public_path($school->logo)))
                <img class="school-logo" src="{{ public_path($school->logo) }}" alt="Logo">
            @else
                <div class="school-logo" style="text-align:center; line-height:48px; color:#2563eb; font-size:9px;">LOGO</div>
            @endif
        </td>
        <td>
            <div class="school-name">{{ $school->name ?? config('app.name') }}</div>
            @if ($school->address)
                <div class="school-address">{{ $school->address }}</div>
            @endif
            <div class="school-report-title">Terminal Result</div>
        </td>
    </tr>
</table>

<h2>{{ $exam->name }}</h2>
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

<table class="stats">
    <tr>
        <td class="stat-box">Total: <strong>{{ $totalStudents }}</strong></td>
        <td class="stat-box">Passed: <strong>{{ $passedCount }}</strong></td>
        <td class="stat-box">Failed: <strong>{{ $failedCount }}</strong></td>
        <td class="stat-box">Pass Rate: <strong>{{ $totalStudents > 0 ? round(($passedCount/$totalStudents)*100, 1) : 0 }}%</strong></td>
        <td class="stat-box">Avg GPA: <strong>{{ $avgGpa }}</strong></td>
        <td class="stat-box">Working Days: <strong>{{ $totalWorkingDays }}</strong></td>
    </tr>
</table>

<table class="terminal-pdf-result-table">
    <thead>
        <tr>
            <th rowspan="2">Rank</th>
            <th rowspan="2">Student ID</th>
            <th rowspan="2">Student Name</th>
            @foreach($displaySubjects as $subject)
            <th>{!! preg_replace('/\s+/', '<br>', e($subject->name)) !!}</th>
            @endforeach
            <th class="terminal-summary-cell" rowspan="2">Total</th>
            <th class="terminal-summary-cell" rowspan="2">%</th>
            <th class="terminal-summary-cell" rowspan="2">GPA</th>
            <th class="terminal-summary-cell" rowspan="2">Grade</th>
            <th class="terminal-summary-cell" rowspan="2">Status</th>
        </tr>
        <tr>
            @foreach($displaySubjects as $subject)
            @php $cfg = $subject->getEffectiveMarksForClass($selectedClass->id ?? 0); @endphp
            <th style="color:#ffd700">{{ $cfg['total_marks'] }} | {{ $cfg['pass_mark'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($results as $row)
        <tr class="{{ $row['has_failed'] ? 'fail-row' : '' }}">
            <td class="{{ $row['rank'] == 1 ? 'rank-1' : ($row['rank'] == 2 ? 'rank-2' : ($row['rank'] == 3 ? 'rank-3' : '')) }}">
                {{ $row['rank'] }}
            </td>
            <td>{{ $row['student']->student_cid ?? $row['student']->id }}</td>
            <td class="terminal-student-name" style="text-align:left">{{ $row['student']->full_name_en }}</td>
            @foreach($displaySubjects as $subject)
            @php $sr = $row['subject_results'][$subject->id] ?? null; @endphp
            <td class="{{ $sr && !$sr['passed'] ? 'fail-cell' : '' }}">
                @if($sr)
                    <span class="terminal-mark-value">{{ $sr['is_absent'] ? 'AB' : number_format($sr['obtained'], 0) }}@if (! $sr['is_absent']) ({{ $sr['letter_grade'] }})@endif</span>
                @else
                    —
                @endif
            </td>
            @endforeach
            <td class="terminal-summary-cell"><strong>{{ number_format($row['total_obtained'], 0) }}</strong></td>
            <td class="terminal-summary-cell">{{ $row['percentage'] }}%</td>
            <td class="terminal-summary-cell"><strong>{{ $row['gpa'] }}</strong></td>
            <td class="terminal-summary-cell">{{ $row['gpa_label'] }}</td>
            <td class="terminal-summary-cell {{ $row['has_failed'] ? 'fail' : 'pass' }}">{{ $row['status'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p style="text-align:right; margin-top:15px; font-size:10px; color:#999;">
    Generated: {{ now()->format('d M Y H:i') }}
</p>
</body>
</html>
