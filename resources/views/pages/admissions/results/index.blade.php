@extends('layouts.master')

@section('contents')
<style>
    .admission-results-page { color: #172033; }
    .admission-results-page .results-hero { background: linear-gradient(120deg, #10233d, #155e75 65%, #0f766e); border-radius: 16px; color: #fff; overflow: hidden; position: relative; }
    .admission-results-page .results-hero::after { background: rgba(255,255,255,.08); border-radius: 50%; content: ''; height: 230px; position: absolute; right: -45px; top: -145px; width: 230px; }
    .admission-results-page .results-hero > * { position: relative; z-index: 1; }
    .admission-results-page .summary-card, .admission-results-page .filter-card, .admission-results-page .results-card { border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.07); }
    .admission-results-page .summary-label, .admission-results-page .filter-card label { color: #718096; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .admission-results-page .summary-value { color: #172033; font-size: 1.55rem; font-weight: 800; line-height: 1.1; }
    .admission-results-page .filter-card .form-control { border-color: #d9e1eb; border-radius: 9px; min-height: 42px; }
    .admission-results-page .results-card { border: 1px solid #e5eaf1; overflow: hidden; }
    .admission-results-page .results-card .table thead th { background: #f7f9fc; border-bottom: 1px solid #e5eaf1; color: #667085; font-size: .7rem; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
    .admission-results-page .results-card .table tbody td { border-top: 1px solid #edf1f5; padding: 14px 12px; vertical-align: middle; }
    .admission-results-page .rank { align-items: center; background: #e8f5f3; border-radius: 9px; color: #0f766e; display: inline-flex; font-weight: 800; height: 30px; justify-content: center; min-width: 30px; }
    .admission-results-page .application-number { color: #0f6170; font-size: .75rem; font-weight: 700; }
    .admission-results-page .status-badge { border-radius: 999px; font-size: .7rem; font-weight: 700; padding: 5px 9px; }
    .admission-results-page .status-passed { background: #dcfce7; color: #166534; }
    .admission-results-page .status-failed { background: #fee2e2; color: #991b1b; }
    .admission-results-page .column-menu { min-width: 210px; padding: 10px; }
    .admission-results-page .column-menu .custom-control-label { color: #344054; font-size: .8rem; font-weight: 600; }
</style>

@php
    $passedCount = $applications->where('result_status', 'passed')->count();
    $failedCount = $applications->where('result_status', 'failed')->count();
    $averageMarks = $applications->avg('total_marks');
    $pdfQuery = request()->query();
    $pdfQuery['columns'] = $selectedColumns;
@endphp

<div class="container-fluid py-3 admission-results-page">
    <div class="results-hero d-flex flex-wrap justify-content-between align-items-center mb-3 p-4">
        <div>
            <div class="small font-weight-bold text-uppercase" style="letter-spacing:.14em;opacity:.72;">Admission management</div>
            <h2 class="mb-1 mt-1 text-white">Merit and results</h2>
            <p class="mb-0" style="opacity:.78;">Review ranked applicants and make decisions for passed or failed students.</p>
        </div>
        <div class="mt-3 mt-md-0"><a href="{{ route('admissions.results.pdf', $pdfQuery) }}" class="btn btn-light mr-1"><i class="fas fa-file-pdf mr-1"></i> Export PDF</a><a href="{{ route('admissions.exams') }}" class="btn btn-light"><i class="fas fa-pen mr-1"></i> Enter marks</a></div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Results found</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value">{{ number_format($applications->count()) }}</span><i class="fas fa-list-ol text-info"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Passed</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value text-success">{{ $passedCount }}</span><i class="fas fa-check-circle text-success"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Failed</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value text-danger">{{ $failedCount }}</span><i class="fas fa-times-circle text-danger"></i></div></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card summary-card h-100"><div class="card-body"><span class="summary-label">Average mark</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="summary-value text-primary">{{ $averageMarks !== null ? number_format($averageMarks, 1) : '-' }}</span><i class="fas fa-chart-line text-primary"></i></div></div></div></div>
    </div>

    <div class="card filter-card mb-3">
        <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><h5 class="mb-1">Filter merit list</h5><small class="text-muted">Choose a class, academic session, or result status.</small></div>
                @if($classId || $sessionId || $resultStatus !== '')
                    <a href="{{ route('admissions.results') }}" class="small font-weight-bold text-secondary">Clear filters</a>
                @endif
            </div>
            <form id="resultsFilterForm" method="GET" action="{{ route('admissions.results') }}" class="form-row align-items-end">
                <div class="form-group col-lg-4 mb-3 mb-lg-0"><label for="resultClass">Class</label><select id="resultClass" name="school_class_id" class="form-control"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-4 mb-3 mb-lg-0"><label for="resultSession">Academic session</label><select id="resultSession" name="academic_session_id" class="form-control"><option value="">All sessions</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId == $session->id)>{{ $session->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-2 mb-3 mb-lg-0"><label for="resultStatus">Result status</label><select id="resultStatus" name="result_status" class="form-control"><option value="">All results</option><option value="passed" @selected($resultStatus === 'passed')>Passed</option><option value="failed" @selected($resultStatus === 'failed')>Failed</option></select></div>
                <div class="form-group col-lg-2 mb-0"><button class="btn btn-primary btn-block" type="submit"><i class="fas fa-filter mr-1"></i> Apply filters</button></div>
            </form>
        </div>
    </div>

    <div class="card results-card">
        <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center pt-3"><div><h5 class="mb-1">Merit list</h5><small class="text-muted">{{ $applications->count() }} ranked result{{ $applications->count() === 1 ? '' : 's' }}</small></div><div class="d-flex align-items-center mt-2 mt-md-0 ml-auto"><div class="dropdown"><button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown"><i class="fas fa-columns mr-1"></i> Columns</button><div class="dropdown-menu dropdown-menu-right column-menu"><div class="small font-weight-bold text-uppercase text-muted mb-2">Show columns</div>@foreach($resultColumnOptions as $columnKey => $columnLabel)<div class="custom-control custom-checkbox mb-2"><input type="checkbox" class="custom-control-input result-column-toggle" id="result-column-{{ $columnKey }}" data-column="{{ $columnKey }}" @checked(in_array($columnKey, $selectedColumns, true))><label class="custom-control-label" for="result-column-{{ $columnKey }}">{{ $columnLabel }}</label></div>@endforeach</div></div></div></div>
        <div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Rank</th>@if(in_array('applicant', $selectedColumns, true))<th data-result-column="applicant">Applicant</th>@endif @if(in_array('class', $selectedColumns, true))<th data-result-column="class">Class</th>@endif @if(in_array('session', $selectedColumns, true))<th data-result-column="session">Session</th>@endif @if(in_array('total_mark', $selectedColumns, true))<th data-result-column="total_mark">Total mark</th>@endif @if(in_array('pass_mark', $selectedColumns, true))<th data-result-column="pass_mark">Pass mark</th>@endif @if(in_array('status', $selectedColumns, true))<th data-result-column="status">Status</th>@endif<th class="text-right">Review</th></tr></thead><tbody>
            @forelse($applications as $position => $application)
                <tr><td><span class="rank">{{ $position + 1 }}</span></td>@if(in_array('applicant', $selectedColumns, true))<td data-result-column="applicant"><strong>{{ $application->applicant_data['full_name_en'] ?? $application->full_name_en ?? '-' }}</strong><small class="d-block application-number">{{ $application->application_number }}</small></td>@endif @if(in_array('class', $selectedColumns, true))<td data-result-column="class"><span class="badge badge-light px-2 py-2">{{ $application->schoolClass?->name_en ?? 'Unassigned' }}</span></td>@endif @if(in_array('session', $selectedColumns, true))<td data-result-column="session" class="text-muted">{{ $application->academicSession?->name_en ?? $application->exam?->academicSession?->name_en ?? '-' }}</td>@endif @if(in_array('total_mark', $selectedColumns, true))<td data-result-column="total_mark"><strong>{{ number_format((float) $application->total_marks, 0) }}</strong></td>@endif @if(in_array('pass_mark', $selectedColumns, true))<td data-result-column="pass_mark" class="text-muted">{{ number_format((float) $application->pass_mark_snapshot, 0) }}</td>@endif @if(in_array('status', $selectedColumns, true))<td data-result-column="status"><span class="status-badge {{ $application->result_status === 'passed' ? 'status-passed' : 'status-failed' }}">{{ ucfirst($application->result_status) }}</span></td>@endif<td class="text-right"><a href="{{ route('admissions.applications.show', $application) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye mr-1"></i> Review Applicant</a></td></tr>
            @empty
                <tr><td colspan="{{ count($selectedColumns) + 2 }}" class="text-center py-5 text-muted"><i class="fas fa-clipboard-list d-block mb-2" style="font-size:2rem;opacity:.35;"></i>No results match the selected filters.</td></tr>
            @endforelse
        </tbody></table></div>
    </div>
</div>
<script>
    (function () {
        const storageKey = 'admission-results-columns';
        const table = document.querySelector('.admission-results-page .results-card table');
        const toggles = Array.from(document.querySelectorAll('.result-column-toggle'));
        const pdfLink = document.querySelector('a[href*="admissions/results/pdf"]');
        const filterForm = document.getElementById('resultsFilterForm');
        const applyColumns = (columns) => {
            if (!table) return;
            table.querySelectorAll('[data-result-column]').forEach((cell) => {
                cell.style.display = columns.includes(cell.dataset.resultColumn) ? '' : 'none';
            });
        };
        const selected = () => toggles.filter((toggle) => toggle.checked).map((toggle) => toggle.dataset.column);
        const update = () => {
            let columns = selected();
            if (!columns.length) {
                toggles[0].checked = true;
                columns = selected();
            }
            applyColumns(columns);
            localStorage.setItem(storageKey, JSON.stringify(columns));
            if (pdfLink) {
                const url = new URL(pdfLink.href, window.location.origin);
                Array.from(url.searchParams.keys()).filter((key) => key.startsWith('columns['))
                    .forEach((key) => url.searchParams.delete(key));
                columns.forEach((column) => url.searchParams.append('columns[]', column));
                pdfLink.href = url.toString();
            }
        };
        const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
        if (Array.isArray(saved) && !new URLSearchParams(window.location.search).has('columns[]')) {
            toggles.forEach((toggle) => { toggle.checked = saved.includes(toggle.dataset.column); });
        }
        toggles.forEach((toggle) => toggle.addEventListener('change', update));
        if (filterForm) filterForm.addEventListener('submit', () => {
            filterForm.querySelectorAll('input[name="columns[]"]').forEach((input) => input.remove());
            selected().forEach((column) => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'columns[]'; input.value = column; filterForm.appendChild(input); });
        });
        update();
    })();
</script>
@endsection
