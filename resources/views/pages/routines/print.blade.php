@php
    $renderForPdf = $renderForPdf ?? false;
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Class Routine - {{ $schoolClass?->name_en ?? 'All classes' }}{{ $section ? ' - ' . $section->name_en : '' }}</title>
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #0f172a; background: #fff; font-family: DejaVu Sans, Arial, sans-serif; }
        .print-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 16px; margin-bottom: 18px; border: 1px solid #dbe3ef; border-radius: 10px; background: #f8fafc; }
        .print-toolbar button, .print-toolbar a { display: inline-block; padding: 8px 13px; border: 1px solid #cbd5e1; border-radius: 7px; background: #fff; color: #334155; text-decoration: none; font-size: 12px; cursor: pointer; }
        .print-toolbar button { border-color: #2563eb; background: #2563eb; color: #fff; }
        .school-header-wrap { border: 1px solid #dbe3ee; border-radius: 8px; padding: 8px 10px; margin-bottom: 18px; background: #fff; }
        .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .school-header-table td { border: 0; padding: 0; vertical-align: middle; }
        .school-header-logo-cell { width: 62px; }
        .school-header-info-cell { padding-left: 10px !important; }
        .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; line-height: 50px; overflow: hidden; background: #fff; }
        .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
        .school-logo-fallback { font-size: 20px; font-weight: 700; color: #334155; }
        .school-title { color: #163b70; font-size: 16px; font-weight: 700; line-height: 1.2; }
        .school-line { color: #475569; font-size: 10px; line-height: 1.35; margin-top: 2px; }
        .school-slogan { color: #475569; font-size: 10px; line-height: 1.35; margin-top: 2px; }
        .routine-heading { display: table; width: 100%; margin-bottom: 18px; }
        .routine-heading-main, .routine-heading-meta { display: table-cell; vertical-align: bottom; }
        .routine-heading-meta { width: 35%; text-align: right; }
        .eyebrow { margin-bottom: 5px; color: #2563eb; font-size: 10px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        h1 { margin: 0; font-size: 23px; line-height: 1.15; }
        .subtitle { margin-top: 5px; color: #64748b; font-size: 11px; }
        .meta-item { display: inline-block; min-width: 95px; margin-left: 8px; padding-left: 8px; border-left: 1px solid #cbd5e1; text-align: left; }
        .meta-label { display: block; color: #64748b; font-size: 9px; text-transform: uppercase; }
        .meta-value { display: block; margin-top: 2px; font-size: 12px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #cbd5e1; }
        thead th { padding: 9px 6px; background: #0f172a; color: #fff; text-align: center; }
        thead th:first-child { width: 12%; text-align: left; }
        .period-name { display: block; font-size: 10px; font-weight: bold; }
        .period-time { display: block; margin-top: 3px; color: #bfdbfe; font-size: 8px; font-weight: normal; }
        .day-cell { padding: 8px; background: #f8fafc; font-size: 11px; font-weight: bold; vertical-align: top; }
        .routine-cell { height: 62px; padding: 6px; vertical-align: top; }
        .routine-cell.empty { color: #94a3b8; text-align: center; vertical-align: middle; }
        .subject { display: block; color: #1e3a8a; font-size: 10px; font-weight: bold; }
        .teacher, .room { display: block; margin-top: 4px; color: #475569; font-size: 8px; line-height: 1.2; }
        .room { color: #64748b; }
        .footer { margin-top: 14px; color: #94a3b8; font-size: 9px; text-align: right; }
        @media print {
            .print-toolbar { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @unless($renderForPdf)
        <div class="print-toolbar">
            <strong>Routine print view</strong>
            <span>
                <a href="javascript:history.back()">Back</a>
                <button type="button" onclick="window.print()">Print routine</button>
            </span>
        </div>
    @endunless

    @php
        $school = \App\Models\SchoolSetting::current();
        $schoolName = $school->name ?: 'School Name';
        $schoolLogoUrl = !empty($school->logo) ? asset($school->logo) : null;
    @endphp
    <div class="school-header-wrap">
        <table class="school-header-table">
            <tr>
                <td class="school-header-logo-cell"><div class="school-logo-box">
                    @if($schoolLogoUrl)<img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }} logo" class="school-logo-img">@else<span class="school-logo-fallback">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>@endif
                </div></td>
                <td class="school-header-info-cell">
                    <div class="school-title">{{ $schoolName }}</div>
                    @if($school->address)<div class="school-line">{{ $school->address }}</div>@endif
                    @if($school->contact_number_1 || $school->contact_number_2)<div class="school-line">{{ implode(' | ', array_filter([$school->contact_number_1, $school->contact_number_2])) }}</div>@endif
                    @if($school->slogan)<div class="school-slogan">{{ $school->slogan }}</div>@endif
                </td>
            </tr>
        </table>
    </div>

    <header class="routine-heading">
        <div class="routine-heading-main">
            <div class="eyebrow">Weekly class routine</div>
            <h1>{{ $schoolClass?->name_en ?? 'All classes' }}{{ $section ? ' — ' . $section->name_en : '' }}</h1>
            <div class="subtitle">Academic session: {{ $academicSession->name_en }}</div>
        </div>
        <div class="routine-heading-meta">
            <span class="meta-item"><span class="meta-label">Working days</span><span class="meta-value">{{ count($days) }}</span></span>
            <span class="meta-item"><span class="meta-label">Teaching periods</span><span class="meta-value">{{ $periods->count() }}</span></span>
        </div>
    </header>

    <?php if ($viewType === 'teacherwise'): ?>
    <table>
        <thead>
            <tr>
                <th>Teacher</th>
                @foreach($periods as $period)
                    <th>
                        <span class="period-name">{{ $period->name }}</span>
                        <span class="period-time">{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($teacherwiseRows as $row)
                <tr>
                    <th class="day-cell">{{ $row['teacher'] }}</th>
                    @foreach($periods as $period)
                        @php $lessons = $row['periods']['id:' . $period->id] ?? $row['periods']['start:' . $period->start_time] ?? []; @endphp
                        <td class="routine-cell {{ count($lessons) ? '' : 'empty' }}">
                            @forelse($lessons as $lesson)
                                @php $routine = $lesson['routine']; @endphp
                                <span class="subject">{{ $routine->subject?->name ?? '—' }}</span>
                                <span class="teacher">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }} ({{ $lesson['day_range'] }})</span>
                            @empty
                                —
                            @endforelse
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <?php else: ?>
    @php
        $routineMap = [];
        foreach ($routines as $item) {
            if ($item->time_schedule_id) {
                $routineMap[$item->day . ':id:' . $item->time_schedule_id][] = $item;
            }
            $routineMap[$item->day . ':start:' . $item->start_time][] = $item;
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>Day</th>
                @foreach($periods as $period)
                    <th>
                        <span class="period-name">{{ $period->name }}</span>
                        <span class="period-time">{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    <th class="day-cell">{{ $day }}</th>
                    @foreach($periods as $period)
                        @php $cellRoutines = $routineMap[$day . ':id:' . $period->id] ?? $routineMap[$day . ':start:' . $period->start_time] ?? []; @endphp
                        <td class="routine-cell {{ count($cellRoutines) ? '' : 'empty' }}">
                            @if(count($cellRoutines))
                                @foreach($cellRoutines as $routine)
                                    <span class="subject">{{ $routine->subject?->name ?? '—' }}</span>
                                    <span class="teacher">Teacher: {{ $routine->teacher?->name ?? 'Not assigned' }}</span>
                                    @if(!$schoolClass || !$section)<span class="room">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }}</span>@endif
                                    @if($routine->classroom)<span class="room">Room: {{ $routine->classroom->name_en }}</span>@endif
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer">Generated {{ now()->format('d M Y, h:i A') }}</div>
    <script>
        window.addEventListener('load', function () {
            window.setTimeout(function () { window.print(); }, 150);
        });
    </script>
</body>
</html>
