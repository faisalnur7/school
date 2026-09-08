@extends('layouts.master')

@section('contents')
<style>
    .approved-admissions-page { color: #172033; }
    .approved-admissions-page .approved-hero { background: linear-gradient(120deg, #10233d 0%, #155e75 62%, #0f766e 100%); border-radius: 16px; color: #fff; overflow: hidden; position: relative; }
    .approved-admissions-page .approved-hero::after { background: rgba(255,255,255,.08); border-radius: 50%; content: ''; height: 230px; position: absolute; right: -45px; top: -145px; width: 230px; }
    .approved-admissions-page .approved-hero > * { position: relative; z-index: 1; }
    .approved-admissions-page .stat-card, .approved-admissions-page .approved-card { border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.07); }
    .approved-admissions-page .stat-label { color: #718096; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .approved-admissions-page .stat-value { color: #172033; font-size: 1.55rem; font-weight: 800; line-height: 1.1; }
    .approved-admissions-page .approved-card { border: 1px solid #e5eaf1; overflow: hidden; }
    .approved-admissions-page .approved-card .table { margin-bottom: 0; }
    .approved-admissions-page .approved-card .table thead th { background: #f7f9fc; border-bottom: 1px solid #e5eaf1; color: #667085; font-size: .7rem; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
    .approved-admissions-page .approved-card .table tbody td { border-top: 1px solid #edf1f5; padding: 14px 12px; vertical-align: middle; }
    .approved-admissions-page .applicant-cell { align-items: center; display: flex; gap: 10px; min-width: 210px; }
    .approved-admissions-page .applicant-avatar { align-items: center; background: #dff7f2; border-radius: 11px; color: #0f766e; display: inline-flex; flex: 0 0 42px; font-weight: 800; height: 42px; justify-content: center; overflow: hidden; width: 42px; }
    .approved-admissions-page .applicant-avatar img { height: 100%; object-fit: cover; width: 100%; }
    .approved-admissions-page .application-number { color: #0f6170; font-size: .78rem; font-weight: 800; }
    .approved-admissions-page .meta { color: #8490a3; display: block; font-size: .72rem; margin-top: 3px; }
    .approved-admissions-page .status-badge { border-radius: 999px; display: inline-block; font-size: .7rem; font-weight: 700; padding: 5px 9px; white-space: nowrap; }
    .approved-admissions-page .status-approved { background: #dcfce7; color: #166534; }
    .approved-admissions-page .status-passed { background: #eef2ff; color: #4338ca; }
    .approved-admissions-page .status-failed { background: #fee2e2; color: #991b1b; }
    .approved-admissions-page .empty-state { padding: 58px 20px; }
    .approved-admissions-page .filter-card { border: 1px solid #e5eaf1; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.05); }
    .approved-admissions-page .filter-card label { color: #536176; font-size: .78rem; font-weight: 700; }
    .approved-admissions-page .filter-card .form-control { border-color: #d9e1eb; border-radius: 9px; min-height: 42px; }
</style>

@php
    $pageApplications = $applications->getCollection();
    $passedCount = $pageApplications->where('result_status', 'passed')->count();
    $failedCount = $pageApplications->where('result_status', 'failed')->count();
@endphp

<div class="container-fluid py-3 approved-admissions-page">
    <div class="approved-hero d-flex flex-wrap justify-content-between align-items-center mb-3 p-4">
        <div>
            <div class="small font-weight-bold text-uppercase" style="letter-spacing:.14em;opacity:.72;">Admission management</div>
            <h2 class="mb-1 mt-1 text-white">Approved student list</h2>
            <p class="mb-0" style="opacity:.78;">Review approved applicants and proceed with their student admission.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('admissions.results') }}" class="btn btn-light mr-1"><i class="fas fa-chart-line mr-1"></i> Results</a>
            <a href="{{ route('admissions.converted') }}" class="btn btn-light"><i class="fas fa-user-graduate mr-1"></i> Converted Students</a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Awaiting admission</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value">{{ number_format($applications->total()) }}</span><i class="fas fa-user-check text-success"></i></div></div></div></div>
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Passed</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-primary">{{ $passedCount }}</span><i class="fas fa-check-circle text-primary"></i></div></div></div></div>
        <div class="col-sm-4"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Failed</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-danger">{{ $failedCount }}</span><i class="fas fa-times-circle text-danger"></i></div></div></div></div>
    </div>

    <div class="card filter-card mb-3">
        <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><h5 class="mb-1">Find approved applicants</h5><small class="text-muted">Search by name or mobile number, then narrow the list by session or class.</small></div>
                @if($search !== '' || $classId || $sessionId)<a href="{{ route('admissions.approved') }}" class="small font-weight-bold text-secondary">Clear filters</a>@endif
            </div>
            <form method="GET" action="{{ route('admissions.approved') }}" class="form-row align-items-end">
                <div class="form-group col-lg-5 mb-3 mb-lg-0"><label for="approvedSearch">Name / mobile number</label><input id="approvedSearch" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Applicant name or father, mother, guardian mobile"></div>
                <div class="form-group col-lg-3 mb-3 mb-lg-0"><label for="approvedSession">Academic session</label><select id="approvedSession" name="academic_session_id" class="form-control"><option value="">All sessions</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId == $session->id)>{{ $session->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-2 mb-3 mb-lg-0"><label for="approvedClass">Class</label><select id="approvedClass" name="school_class_id" class="form-control"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-2 mb-0"><button class="btn btn-primary btn-block" type="submit"><i class="fas fa-filter mr-1"></i> Apply filters</button></div>
            </form>
        </div>
    </div>

    <div class="card approved-card">
        <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center pt-3">
            <div><h5 class="mb-1">Applicants ready for admission</h5><small class="text-muted">{{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} approved applicants</small></div>
            <span class="small text-muted mt-2 mt-md-0"><i class="fas fa-info-circle mr-1"></i>Open an applicant to complete admission</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Application</th><th>Applicant</th><th>Class</th><th>Result</th><th>Approval</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                    @forelse($applications as $application)
                        @php
                            $data = $application->applicant_data ?? [];
                            $name = $data['full_name_en'] ?? $application->full_name_en ?? '-';
                            $image = $application->image ?? ($data['image'] ?? null);
                            $imagePath = $image && file_exists(public_path($image)) ? asset($image) : null;
                        @endphp
                        <tr>
                            <td><span class="application-number">{{ $application->application_number }}</span><span class="meta">{{ $application->exam?->name ?? 'Admission exam' }}</span></td>
                            <td><div class="applicant-cell"><span class="applicant-avatar">@if($imagePath)<img src="{{ $imagePath }}" alt="{{ $name }}">@else{{ strtoupper(substr($name, 0, 1)) }}@endif</span><span><strong>{{ $name }}</strong><span class="meta">{{ $data['father_phone'] ?? $application->father_phone ?? 'No phone' }}</span></span></div></td>
                            <td><span class="badge badge-light px-2 py-2">{{ $application->schoolClass?->name_en ?? 'Unassigned' }}</span></td>
                            <td><span class="status-badge {{ $application->result_status === 'passed' ? 'status-passed' : 'status-failed' }}">{{ ucfirst($application->result_status) }}</span><span class="meta">{{ number_format((float) $application->total_marks, 0) }} / {{ number_format((float) $application->pass_mark_snapshot, 0) }}</span></td>
                            <td><span class="status-badge status-approved"><i class="fas fa-check mr-1"></i>Approved</span></td>
                            <td class="text-right"><a href="{{ route('admissions.applications.show', $application) }}" class="btn btn-sm btn-success"><i class="fas fa-arrow-right mr-1"></i>Proceed / Review</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted empty-state"><i class="fas fa-user-check d-block mb-3" style="font-size:2.4rem;opacity:.35;"></i><strong>No approved applications</strong><span class="d-block mt-1">Approved applicants will appear here when they are ready for admission.</span></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())<div class="p-3">{{ $applications->links() }}</div>@endif
    </div>
</div>
@endsection
