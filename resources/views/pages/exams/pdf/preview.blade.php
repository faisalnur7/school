<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: 'SolaimanLipi', Arial, sans-serif; font-size: 12px; color: #1e293b; font-weight: bold; background: #f8fafc; }
    h2 { text-align: center; margin: 0 0 4px; font-size: 18px; color: #172554; }
    h4 { text-align: center; margin: 0 0 10px; font-size: 12px; color: #64748b; }
    .school-header { width: 100%; margin-bottom: 14px; border-bottom: 2px solid #2563eb; padding-bottom: 7px; table-layout: fixed; }
    .school-header td { border: 0; background: transparent; padding: 0; vertical-align: middle; }
    .school-details { text-align: center; }
    .school-logo-cell { width: 58px; text-align: left; }
    .school-spacer { width: 58px; }
    .school-logo { width: 48px; height: 48px; border: 1px solid #bfdbfe; border-radius: 8px; }
    .school-name { font-size: 16px; color: #172554; font-weight: bold; }
    .school-address { margin-top: 2px; font-size: 10px; color: #64748b; }
    .school-report-title { margin-top: 3px; font-size: 12px; color: #2563eb; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th { background: #2c3e50; color: #fff; padding: 6px 8px; text-align: center; }
    td { padding: 5px 8px; border: 1px solid #ddd; }
    tr:nth-child(even) { background: #f9f9f9; }
    .absent { background: #f0f0f0 !important; color: #999; }
    .stats { width: 100%; table-layout: fixed; border-spacing: 6px 0; margin: 10px -6px; font-size: 11px; border: 0; }
    .stats .stat-box { border: 1px solid #ddd; padding: 5px 12px; border-radius: 4px; }
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
        <td class="school-details" style="text-align:center;">
            <div class="school-name">{{ $school->name ?? config('app.name') }}</div>
            @if ($school->address)
                <div class="school-address">{{ $school->address }}</div>
            @endif
            <div class="school-report-title">Marks Preview</div>
        </td>
        <td class="school-spacer"></td>
    </tr>
</table>

<h2>{{ $exam->name }}</h2>
<h4>
    Subject: {{ $subject->name }} &mdash;
    Class: {{ $selectedClass->name_en ?? '' }}
    &mdash; Year: {{ $exam->year }}
</h4>

@php
    $avgMarks = $marks->where('is_absent', false)->avg('total');
    $highestMarks = $marks->where('is_absent', false)->max('total');
@endphp

<table class="stats">
    <tr>
        <td class="stat-box">Total: <strong>{{ $marks->count() }}</strong></td>
        <td class="stat-box">Average: <strong>{{ number_format($avgMarks, 1) }}</strong></td>
        <td class="stat-box">Highest Marks: <strong>{{ number_format($highestMarks ?? 0, 1) }}</strong></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Roll / ID</th>
            <th>Student Name</th>
            <th>Obtained</th>
        </tr>
    </thead>
    <tbody>
        @foreach($marks as $i => $mark)
        @php $academicInfo = $mark->student->academicInformations->firstWhere('is_current', true) ?? $mark->student->academicInformations->first(); @endphp
        <tr class="{{ $mark->is_absent ? 'absent' : '' }}">
            <td style="text-align:center">{{ $academicInfo?->roll ?: $mark->student_id }}</td>
            <td>{{ $mark->student->full_name_en }}</td>
            <td style="text-align:center">
                {{ $mark->is_absent ? 'Absent' : number_format($mark->total, 1) }}
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
