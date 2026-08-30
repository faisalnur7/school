@php
    $school = \App\Models\SchoolSetting::current();
    $logoPath = !empty($school->logo) && file_exists(public_path($school->logo)) ? public_path($school->logo) : null;
    $passedCount = $applications->where('result_status', 'passed')->count();
    $failedCount = $applications->where('result_status', 'failed')->count();
@endphp
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { color: #172033; font-family: Arial, sans-serif; font-size: 9px; }
        .header { border-bottom: 2px solid #0f766e; padding-bottom: 9px; }
        .header-table, .results { border-collapse: collapse; width: 100%; }
        .logo { height: 46px; width: 46px; object-fit: contain; }
        .school { text-align: center; }
        .school h1 { color: #0f766e; font-size: 17px; margin: 0; }
        .school p { color: #475569; font-size: 9px; margin: 3px 0 0; }
        .title { margin: 12px 0 8px; }
        .title h2 { color: #0f766e; font-size: 15px; margin: 0 0 4px; }
        .title p { color: #475569; margin: 0; }
        .summary { background: #f0fdfa; border: 1px solid #ccfbf1; padding: 7px; }
        .summary span { margin-right: 24px; }
        .results { margin-top: 12px; }
        .results th { background: #0f766e; color: #fff; font-size: 8px; padding: 6px 5px; text-align: left; }
        .results td { border-bottom: 1px solid #dbe3ee; padding: 6px 5px; }
        .results tr:nth-child(even) td { background: #f8fafc; }
        .rank { color: #0f766e; font-weight: bold; }
        .status { font-weight: bold; }
        .passed { color: #166534; }
        .failed { color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table"><tr>
            <td style="width:60px">@if($logoPath)<img class="logo" src="{{ $logoPath }}" alt="Logo">@endif</td>
            <td class="school"><h1>{{ $school->name ?? config('app.name') }}</h1><p>{{ $school->address ?? '' }}</p></td>
            <td style="width:60px"></td>
        </tr></table>
    </div>
    <div class="title"><h2>Admission Merit and Results</h2><p>Generated {{ now()->format('d M Y, h:i A') }}</p></div>
    <div class="summary"><span><b>Class:</b> {{ $filters['class'] ?? 'All classes' }}</span><span><b>Session:</b> {{ $filters['session'] ?? 'All sessions' }}</span><span><b>Status:</b> {{ $filters['status'] ?? 'All results' }}</span><span><b>Total:</b> {{ $applications->count() }}</span><span><b>Passed:</b> {{ $passedCount }}</span><span><b>Failed:</b> {{ $failedCount }}</span></div>
    <table class="results">
        <thead><tr><th style="width:8%">Rank</th>@if(in_array('applicant', $selectedColumns, true))<th>Applicant</th>@endif @if(in_array('class', $selectedColumns, true))<th>Class</th>@endif @if(in_array('session', $selectedColumns, true))<th>Session</th>@endif @if(in_array('total_mark', $selectedColumns, true))<th>Total mark</th>@endif @if(in_array('pass_mark', $selectedColumns, true))<th>Pass mark</th>@endif @if(in_array('status', $selectedColumns, true))<th>Status</th>@endif</tr></thead>
        <tbody>
            @forelse($applications as $position => $application)
                <tr><td class="rank">{{ $position + 1 }}</td>@if(in_array('applicant', $selectedColumns, true))<td>{{ $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-' }}<br><small>{{ $application->application_number }}</small></td>@endif @if(in_array('class', $selectedColumns, true))<td>{{ $application->schoolClass?->name_en ?? '-' }}</td>@endif @if(in_array('session', $selectedColumns, true))<td>{{ $application->academicSession?->name_en ?? $application->exam?->academicSession?->name_en ?? '-' }}</td>@endif @if(in_array('total_mark', $selectedColumns, true))<td><b>{{ number_format((float) $application->total_marks, 0) }}</b></td>@endif @if(in_array('pass_mark', $selectedColumns, true))<td>{{ number_format((float) $application->pass_mark_snapshot, 0) }}</td>@endif @if(in_array('status', $selectedColumns, true))<td class="status {{ $application->result_status === 'passed' ? 'passed' : 'failed' }}">{{ ucfirst($application->result_status) }}</td>@endif</tr>
            @empty
                <tr><td colspan="{{ count($selectedColumns) + 1 }}">No results found for the selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
