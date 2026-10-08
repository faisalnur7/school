<style>
    .school-header-wrap { border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; margin-bottom: 10pt; }
    .school-header-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .school-header-table td { border: 0 !important; padding: 0 !important; vertical-align: middle; }
    .school-header-logo-cell { width: 62px; }
    .school-header-info-cell { padding-left: 10px !important; }
    .school-logo-box { width: 52px; height: 52px; border: 1px solid #cbd5e1; border-radius: 8px; text-align: center; line-height: 50px; overflow: hidden; background: #fff; }
    .school-logo-img { max-width: 50px; max-height: 50px; display: inline-block; vertical-align: middle; }
    .school-logo-fallback { font-size: 20px; font-weight: 700; color: #334155; }
    .school-title { color: #163b70; font-size: 16pt; font-weight: 700; line-height: 1.2; }
    .school-line, .school-slogan { color: #475569; font-size: 9pt; line-height: 1.35; margin-top: 2pt; }
</style>
@php
    $school = \App\Models\SchoolSetting::current();
    $schoolName = $school->name ?: 'School Name';
    $schoolLogoPath = !empty($school->logo) ? public_path($school->logo) : null;
    $schoolHasLogo = $schoolLogoPath && file_exists($schoolLogoPath);
@endphp
<div class="school-header-wrap">
    <table class="school-header-table">
        <tr>
            <td class="school-header-logo-cell"><div class="school-logo-box">
                @if($schoolHasLogo)<img src="{{ $schoolLogoPath }}" alt="{{ $schoolName }} logo" class="school-logo-img">@else<span class="school-logo-fallback">{{ strtoupper(substr($schoolName, 0, 1)) }}</span>@endif
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

<?php if ($viewType === 'teacherwise'): ?>
<table style="width:100%; border-collapse:collapse; table-layout:fixed;">
    <thead>
        <tr>
            <th style="width:12%; padding:6pt 3pt; border:0.5pt solid #cbd5e1; background:#0f172a; color:#fff; text-align:left;">Teacher</th>
            @foreach($periods as $period)
                <th style="padding:6pt 3pt; border:0.5pt solid #cbd5e1; background:#0f172a; color:#fff; text-align:center;">
                    <strong style="font-size:8pt;">{{ $period->name }}</strong>
                    <small style="display:block; margin-top:2pt; color:#bfdbfe; font-size:6.5pt; font-weight:normal;">{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</small>
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($teacherwiseRows as $row)
            <tr>
                <th style="height:48pt; padding:5pt; border:0.5pt solid #cbd5e1; background:#f8fafc; font-size:8pt; text-align:left; vertical-align:top;">{{ $row['teacher'] }}</th>
                @foreach($periods as $period)
                    @php $lessons = $row['periods']['id:' . $period->id] ?? $row['periods']['start:' . $period->start_time] ?? []; @endphp
                    <td style="height:48pt; padding:5pt; border:0.5pt solid #cbd5e1; vertical-align:top;{{ count($lessons) ? '' : ' color:#94a3b8; text-align:center; vertical-align:middle;' }}">
                        @forelse($lessons as $lesson)
                            @php $routine = $lesson['routine']; @endphp
                            <div style="color:#1e3a8a; font-size:7.5pt; font-weight:bold;">{{ $routine->subject?->name ?? '—' }}</div>
                            <div style="margin-top:3pt; color:#64748b; font-size:6.5pt;">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }} ({{ $lesson['day_range'] }})</div>
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
    <table style="width:100%; margin-bottom:10pt;">
        <tr>
            <td style="padding:0; vertical-align:bottom;">
                <div style="color:#2563eb; font-size:7pt; font-weight:bold; letter-spacing:1pt; text-transform:uppercase;">Weekly class routine</div>
                <h1>{{ $schoolClass?->name_en ?? 'All classes' }}{{ $section ? ' — ' . $section->name_en : '' }}</h1>
                <div style="color:#64748b; font-size:8pt;">Academic session: {{ $academicSession->name_en }}</div>
            </td>
            <td style="width:32%; padding:0; text-align:right; vertical-align:bottom;">
                <span style="color:#64748b; font-size:7pt; text-transform:uppercase;">Working days</span>
                <strong style="font-size:9pt;">{{ count($days) }}</strong>
                &nbsp;&nbsp;
                <span style="color:#64748b; font-size:7pt; text-transform:uppercase;">Teaching periods</span>
                <strong style="font-size:9pt;">{{ $periods->count() }}</strong>
            </td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
        <thead>
            <tr>
                <th style="width:12%; padding:6pt 3pt; border:0.5pt solid #cbd5e1; background:#0f172a; color:#fff; text-align:left;">Day</th>
                @foreach($periods as $period)
                    <th style="padding:6pt 3pt; border:0.5pt solid #cbd5e1; background:#0f172a; color:#fff; text-align:center;">
                        <strong style="font-size:8pt;">{{ $period->name }}</strong>
                        <small style="display:block; margin-top:2pt; color:#bfdbfe; font-size:6.5pt; font-weight:normal;">{{ substr($period->start_time, 0, 5) }} – {{ substr($period->end_time, 0, 5) }}</small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    <th style="height:48pt; padding:5pt; border:0.5pt solid #cbd5e1; background:#f8fafc; font-size:8pt; text-align:left; vertical-align:top;">{{ $day }}</th>
                    @foreach($periods as $period)
                        @php
                            $cellRoutines = $routineMap[$day . ':id:' . $period->id]
                                ?? $routineMap[$day . ':start:' . $period->start_time]
                                ?? [];
                        @endphp
                        <td style="height:48pt; padding:5pt; border:0.5pt solid #cbd5e1; vertical-align:top;{{ count($cellRoutines) ? '' : ' color:#94a3b8; text-align:center; vertical-align:middle;' }}">
                            @if(count($cellRoutines))
                                @foreach($cellRoutines as $routine)
                                    <div style="color:#1e3a8a; font-size:7.5pt; font-weight:bold;">{{ $routine->subject?->name ?? '—' }}</div>
                                    <div style="margin-top:3pt; color:#475569; font-size:6.5pt;">Teacher: {{ $routine->teacher?->name ?? 'Not assigned' }}</div>
                                    @if(!$schoolClass || !$section)<div style="margin-top:3pt; color:#64748b; font-size:6.5pt;">{{ $routine->schoolClass?->name_en ?? '—' }} - {{ $routine->section?->name_en ?? '—' }}</div>@endif
                                    @if($routine->classroom)
                                        <div style="margin-top:3pt; color:#64748b; font-size:6.5pt;">Room: {{ $routine->classroom->name_en }}</div>
                                    @endif
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

    <div style="margin-top:8pt; color:#94a3b8; font-size:6.5pt; text-align:right;">Generated {{ now()->format('d M Y, h:i A') }}</div>
<?php endif; ?>
