<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 8px; color: #222; }

    /* ---- Header ---- */
    .header { width: 100%; border-bottom: 2px solid #2c3e50; padding-bottom: 6px; margin-bottom: 8px; }
    .header-inner { display: flex; align-items: center; }
    .logo-wrap { width: 60px; text-align: center; flex-shrink: 0; }
    .logo-wrap img { max-width: 55px; max-height: 55px; }
    .school-info { flex: 1; padding-left: 10px; }
    .school-name { font-size: 14px; font-weight: bold; color: #2c3e50; }
    .school-address { font-size: 8px; color: #555; margin-top: 2px; }
    .report-title { font-size: 11px; font-weight: bold; color: #2c3e50; margin-top: 5px; }

    /* ---- Meta row ---- */
    .meta { display: flex; gap: 6px; margin-bottom: 6px; flex-wrap: wrap; }
    .meta-item { border: 1px solid #ccc; border-radius: 3px; padding: 2px 7px; font-size: 7.5px; }
    .meta-item strong { color: #2c3e50; }

    /* ---- Stats row ---- */
    .stats { margin-bottom: 6px; font-size: 7.5px; }
    .stats span { display: inline-block; border: 1px solid #ccc; border-radius: 3px; padding: 2px 7px; margin-right: 4px; }

    /* ---- Table ---- */
    table { width: 100%; border-collapse: collapse; }
    thead tr th {
        background: #2c3e50;
        color: #fff;
        padding: 3px 2px;
        text-align: center;
        font-size: 7px;
        border: 1px solid #1a252f;
    }
    thead tr th.name-col { text-align: left; padding-left: 4px; }
    tbody tr td {
        padding: 2px 2px;
        border: 1px solid #ddd;
        text-align: center;
        font-size: 7px;
    }
    tbody tr td.name-col { text-align: left; padding-left: 4px; }
    tbody tr:nth-child(even) { background: #f7f7f7; }
    .cell-p    { color: #155724; font-weight: bold; }
    .cell-a    { color: #721c24; }
    .cell-off  { background: #f0f0f0; color: #aaa; }
    .col-p     { color: #155724; font-weight: bold; }
    .col-a     { color: #721c24; font-weight: bold; }
    .pct-high  { color: #155724; font-weight: bold; }
    .pct-mid   { color: #856404; font-weight: bold; }
    .pct-low   { color: #721c24; font-weight: bold; }

    /* ---- Footer ---- */
    .footer { margin-top: 10px; font-size: 7px; color: #999; text-align: right; }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-inner">
        <div class="logo-wrap">
            @if($school->logo)
            <img src="{{ public_path('uploads/school_settings/' . $school->logo) }}" alt="Logo">
            @else
            <div style="width:55px;height:55px;background:#e9ecef;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:9px;color:#999;">LOGO</div>
            @endif
        </div>
        <div class="school-info">
            <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
            @if($school->address)
            <div class="school-address">{{ $school->address }}</div>
            @endif
            <div class="report-title">Monthly Student Attendance Report</div>
        </div>
    </div>
</div>

{{-- Meta --}}
<div class="meta">
    <div class="meta-item"><strong>Session:</strong> {{ $session->name_en ?? '-' }}</div>
    <div class="meta-item"><strong>Class:</strong> {{ $class->name_en ?? '-' }}</div>
    <div class="meta-item"><strong>Section:</strong> {{ $section->name_en ?? '-' }}</div>
    <div class="meta-item"><strong>Month:</strong> {{ $monthLabel }}</div>
</div>

{{-- Stats --}}
<div class="stats">
    <span><strong>Working Days:</strong> {{ $workingDaysCount }}</span>
    <span><strong>Total Students:</strong> {{ $rows->count() }}</span>
</div>

{{-- Table --}}
@if($rows->isEmpty())
<p style="text-align:center;color:#999;margin-top:20px;">No students found.</p>
@else
<table>
    <thead>
        <tr>
            <th style="width:28px;">Roll</th>
            <th style="width:45px;">ID</th>
            <th class="name-col" style="min-width:90px;">Name</th>
            @foreach($allDates as $date)
            <th style="width:14px;{{ isset($nonWorkingDates[$date->toDateString()]) ? 'background:#3d5166;' : '' }}"
                title="{{ $date->format('D') }}">
                {{ $date->format('d') }}
            </th>
            @endforeach
            <th style="width:20px;">P</th>
            <th style="width:20px;">A</th>
            <th style="width:30px;">%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        <tr>
            <td>{{ $row['roll'] ?? '-' }}</td>
            <td>{{ $row['student_cid'] }}</td>
            <td class="name-col">{{ $row['name'] }}</td>
            @foreach($allDates as $date)
            @php $cell = $row['cells'][$date->toDateString()]; @endphp
            <td class="{{ $cell === 'P' ? 'cell-p' : ($cell === 'A' ? 'cell-a' : 'cell-off') }}
                       {{ isset($nonWorkingDates[$date->toDateString()]) ? 'cell-off' : '' }}">
                {{ $cell }}
            </td>
            @endforeach
            <td class="col-p">{{ $row['present'] }}</td>
            <td class="col-a">{{ $row['absent'] }}</td>
            <td class="{{ $row['percentage'] >= 75 ? 'pct-high' : ($row['percentage'] >= 50 ? 'pct-mid' : 'pct-low') }}">
                {{ $row['percentage'] }}%
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="footer">Generated: {{ now()->format('d M Y, h:i A') }}</div>

</body>
</html>
