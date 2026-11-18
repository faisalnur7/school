@php
    $dayMeta = [];
    foreach ($allDates as $d) {
        $key = $d->toDateString();
        $dayMeta[] = ['key' => $key, 'day' => $d->format('d'), 'dow' => $d->format('D'), 'off' => isset($nonWorkingDates[$key])];
    }
    $totalPresent = $rows->sum('present');
    $avgPct = $rows->count() ? round($rows->avg('percentage'), 1) : 0;
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: dejavusans, sans-serif; font-size: 7.5pt; color: #1e293b; }
    table { border-collapse: collapse; }

    .head { width: 100%; border-bottom: 2px solid #1e3a5f; }
    .head td { vertical-align: middle; padding-bottom: 6px; }
    .school-name { font-size: 15pt; font-weight: bold; color: #1e3a5f; }
    .school-address { font-size: 7.5pt; color: #64748b; padding-top: 2px; }
    .report-title { font-size: 11pt; font-weight: bold; color: #1e3a5f; text-align: right; }
    .report-month { font-size: 8.5pt; color: #64748b; text-align: right; padding-top: 2px; }

    .info { width: 100%; margin: 8px 0 6px; }
    .info td { width: 16.66%; padding: 5px 8px; background: #f1f5f9; border-right: 3px solid #fff; }
    .info .lbl { font-size: 6.5pt; color: #64748b; text-transform: uppercase; }
    .info .val { font-size: 10pt; font-weight: bold; color: #0f172a; }

    .grid { width: 100%; }
    .grid th { background: #1e3a5f; color: #fff; font-size: 6.5pt; padding: 3px 1px; border: 0.3pt solid #16304f; text-align: center; }
    .grid th.off { background: #475f7a; }
    .grid th .dow { font-size: 5pt; font-weight: normal; color: #cbd5e1; }
    .grid td { font-size: 6.8pt; padding: 2.4px 1px; border: 0.3pt solid #e2e8f0; text-align: center; }
    .grid td.l { text-align: left; padding-left: 4px; }
    .grid tr.alt td { background: #f8fafc; }
    .grid td.p { color: #15803d; font-weight: bold; }
    .grid td.a { color: #dc2626; }
    .grid td.off { background: #eceff3; color: #b6bfca; }
    .grid td.sum { font-weight: bold; background: #f1f5f9; }
    .pct-high { color: #15803d; font-weight: bold; }
    .pct-mid { color: #b45309; font-weight: bold; }
    .pct-low { color: #dc2626; font-weight: bold; }

    .legend { margin-top: 6px; font-size: 6.5pt; color: #64748b; }
</style>
</head>
<body>

<table class="head">
    <tr>
        @if($logoPath)
        <td style="width:50px;"><img src="{{ $logoPath }}" style="width:42px;height:42px;"></td>
        @endif
        <td>
            <div class="school-name">{{ $school->name ?? 'School Name' }}</div>
            @if($school->address)<div class="school-address">{{ $school->address }}</div>@endif
        </td>
        <td style="width:300px;">
            <div class="report-title">Monthly Attendance Report</div>
            <div class="report-month">{{ $monthLabel }}</div>
        </td>
    </tr>
</table>

<table class="info">
    <tr>
        <td><div class="lbl">Session</div><div class="val">{{ $session->name_en ?? '-' }}</div></td>
        <td><div class="lbl">Class</div><div class="val">{{ $class->name_en ?? '-' }}</div></td>
        <td><div class="lbl">Section</div><div class="val">{{ $section->name_en ?? '-' }}</div></td>
        <td><div class="lbl">Students</div><div class="val">{{ $rows->count() }}</div></td>
        <td><div class="lbl">Working Days</div><div class="val">{{ $workingDaysCount }}</div></td>
        <td><div class="lbl">Avg. Attendance</div><div class="val">{{ $avgPct }}%</div></td>
    </tr>
</table>

@if($rows->isEmpty())
<p style="text-align:center;color:#94a3b8;margin-top:30px;">No students found.</p>
@else
<table class="grid">
    <thead>
        <tr>
            <th style="width:26px;">Roll</th>
            <th style="width:38px;">CID</th>
            <th style="width:120px;text-align:left;padding-left:4px;">Name</th>
            @foreach($dayMeta as $m)
            <th class="{{ $m['off'] ? 'off' : '' }}">{{ $m['day'] }}<br><span class="dow">{{ substr($m['dow'], 0, 2) }}</span></th>
            @endforeach
            <th style="width:20px;">P</th>
            <th style="width:20px;">A</th>
            <th style="width:30px;">%</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $row)
        <tr class="{{ $i % 2 ? 'alt' : '' }}">
            <td>{{ $row['roll'] ?? '-' }}</td>
            <td>{{ $row['student_cid'] }}</td>
            <td class="l">{{ $row['name'] }}</td>
            @foreach($dayMeta as $m)
                @php $c = $row['cells'][$m['key']]; @endphp
                <td class="{{ $m['off'] ? 'off' : ($c === 'P' ? 'p' : 'a') }}">{{ $m['off'] ? '' : $c }}</td>
            @endforeach
            <td class="sum p">{{ $row['present'] }}</td>
            <td class="sum a">{{ $row['absent'] }}</td>
            <td class="sum {{ $row['percentage'] >= 75 ? 'pct-high' : ($row['percentage'] >= 50 ? 'pct-mid' : 'pct-low') }}">{{ $row['percentage'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="legend">P = Present &nbsp;·&nbsp; A = Absent &nbsp;·&nbsp; Shaded columns = weekend / holiday &nbsp;·&nbsp; % = present days ÷ working days</div>
@endif

</body>
</html>
