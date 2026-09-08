@extends('layouts.master')

@section('contents')
<style>
    .converted-admissions-page { color: #172033; }
    .converted-admissions-page .converted-hero { background: linear-gradient(120deg, #10233d 0%, #155e75 62%, #0f766e 100%); border-radius: 16px; color: #fff; overflow: hidden; position: relative; }
    .converted-admissions-page .converted-hero::after { background: rgba(255,255,255,.08); border-radius: 50%; content: ''; height: 230px; position: absolute; right: -45px; top: -145px; width: 230px; }
    .converted-admissions-page .converted-hero > * { position: relative; z-index: 1; }
    .converted-admissions-page .stat-card, .converted-admissions-page .converted-card, .converted-admissions-page .filter-card { border: 0; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.07); }
    .converted-admissions-page .stat-label { color: #718096; font-size: .7rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
    .converted-admissions-page .stat-value { color: #172033; font-size: 1.55rem; font-weight: 800; line-height: 1.1; }
    .converted-admissions-page .filter-card, .converted-admissions-page .converted-card { border: 1px solid #e5eaf1; }
    .converted-admissions-page .filter-card label { color: #536176; font-size: .78rem; font-weight: 700; }
    .converted-admissions-page .filter-card .form-control { border-color: #d9e1eb; border-radius: 9px; min-height: 42px; }
    .converted-admissions-page .converted-card { overflow: hidden; }
    .converted-admissions-page .converted-card .table { margin-bottom: 0; }
    .converted-admissions-page .converted-card .table thead th { background: #f7f9fc; border-bottom: 1px solid #e5eaf1; color: #667085; font-size: .7rem; letter-spacing: .05em; text-transform: uppercase; white-space: nowrap; }
    .converted-admissions-page .converted-card .table tbody td { border-top: 1px solid #edf1f5; padding: 14px 12px; vertical-align: middle; }
    .converted-admissions-page .applicant-cell { align-items: center; display: flex; gap: 10px; min-width: 210px; }
    .converted-admissions-page .applicant-avatar { align-items: center; background: #dff7f2; border-radius: 11px; color: #0f766e; display: inline-flex; flex: 0 0 42px; font-weight: 800; height: 42px; justify-content: center; overflow: hidden; width: 42px; }
    .converted-admissions-page .applicant-avatar img { height: 100%; object-fit: cover; width: 100%; }
    .converted-admissions-page .application-number { color: #0f6170; font-size: .78rem; font-weight: 800; }
    .converted-admissions-page .student-id { color: #166534; font-size: .9rem; font-weight: 800; }
    .converted-admissions-page .meta { color: #8490a3; display: block; font-size: .72rem; margin-top: 3px; }
    .converted-admissions-page .status-badge { border-radius: 999px; display: inline-block; font-size: .7rem; font-weight: 700; padding: 5px 9px; white-space: nowrap; }
    .converted-admissions-page .status-converted { background: #dcfce7; color: #166534; }
    .converted-admissions-page .empty-state { padding: 58px 20px; }
</style>

@php
    $pageApplications = $applications->getCollection();
    $paidCount = $pageApplications->where('payment_status', 'paid')->count();
    $passedCount = $pageApplications->where('result_status', 'passed')->count();
@endphp

<div class="container-fluid py-3 converted-admissions-page">
    <div class="converted-hero d-flex flex-wrap justify-content-between align-items-center mb-3 p-4">
        <div>
            <div class="small font-weight-bold text-uppercase" style="letter-spacing:.14em;opacity:.72;">Admission management</div>
            <h2 class="mb-1 mt-1 text-white">Converted students</h2>
            <p class="mb-0" style="opacity:.78;">Track applicants who have been successfully added to the student register.</p>
        </div>
        <div class="mt-3 mt-md-0"><a href="{{ route('admissions.approved') }}" class="btn btn-light mr-1"><i class="fas fa-user-check mr-1"></i> Approved List</a><a href="{{ route('admissions.applications') }}" class="btn btn-light"><i class="fas fa-list mr-1"></i> All Applications</a></div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Converted students</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value">{{ number_format($applications->total()) }}</span><i class="fas fa-user-graduate text-success"></i></div></div></div></div>
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Paid on page</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-success">{{ $paidCount }}</span><i class="fas fa-wallet text-success"></i></div></div></div></div>
        <div class="col-sm-4"><div class="card stat-card h-100"><div class="card-body"><span class="stat-label">Passed results</span><div class="d-flex justify-content-between align-items-end mt-2"><span class="stat-value text-primary">{{ $passedCount }}</span><i class="fas fa-check-circle text-primary"></i></div></div></div></div>
    </div>

    <div class="card filter-card mb-3">
        <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div><h5 class="mb-1">Find converted students</h5><small class="text-muted">Search by applicant name, mobile number, or Student ID.</small></div>
                @if($search !== '' || $classId || $sessionId)<a href="{{ route('admissions.converted') }}" class="small font-weight-bold text-secondary">Clear filters</a>@endif
            </div>
            <form method="GET" action="{{ route('admissions.converted') }}" class="form-row align-items-end">
                <div class="form-group col-lg-5 mb-3 mb-lg-0"><label for="convertedSearch">Name / mobile / Student ID</label><input id="convertedSearch" type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Applicant name, mobile number, or Student ID"></div>
                <div class="form-group col-lg-3 mb-3 mb-lg-0"><label for="convertedSession">Academic session</label><select id="convertedSession" name="academic_session_id" class="form-control"><option value="">All sessions</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId == $session->id)>{{ $session->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-2 mb-3 mb-lg-0"><label for="convertedClass">Class</label><select id="convertedClass" name="school_class_id" class="form-control"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId == $class->id)>{{ $class->name_en }}</option>@endforeach</select></div>
                <div class="form-group col-lg-2 mb-0"><button class="btn btn-primary btn-block" type="submit"><i class="fas fa-filter mr-1"></i> Apply filters</button></div>
            </form>
        </div>
    </div>

    <div class="card converted-card">
        <div class="card-header bg-white border-0 d-flex flex-wrap justify-content-between align-items-center pt-3"><div><h5 class="mb-1">Student register entries</h5><small class="text-muted">{{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} converted students</small></div><span class="small text-muted mt-2 mt-md-0"><i class="fas fa-check-circle mr-1 text-success"></i>Conversion completed</span></div>
        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Application</th><th>Student</th><th>Student ID</th><th>Class</th><th>Result</th><th>Status</th><th class="text-right">Action</th></tr></thead><tbody>
            @forelse($applications as $application)
                @php $data = $application->applicant_data ?? []; $name = $data['full_name_en'] ?? $application->full_name_en ?? '-'; $image = $application->image ?? ($data['image'] ?? null); $imagePath = $image && file_exists(public_path($image)) ? asset($image) : null; @endphp
                <tr><td><span class="application-number">{{ $application->application_number }}</span><span class="meta">{{ $application->exam?->name ?? 'Admission exam' }}</span></td><td><div class="applicant-cell"><span class="applicant-avatar">@if($imagePath)<img src="{{ $imagePath }}" alt="{{ $name }}">@else{{ strtoupper(substr($name, 0, 1)) }}@endif</span><span><strong>{{ $name }}</strong><span class="meta">{{ $data['father_phone'] ?? $application->father_phone ?? 'No phone' }}</span></span></div></td><td><span class="student-id">{{ $application->convertedStudent?->student_cid ?? $application->converted_student_id }}</span></td><td><span class="badge badge-light px-2 py-2">{{ $application->schoolClass?->name_en ?? 'Unassigned' }}</span></td><td><span class="meta">{{ number_format((float) $application->total_marks, 0) }} / {{ number_format((float) $application->pass_mark_snapshot, 0) }}</span></td><td><span class="status-badge status-converted"><i class="fas fa-check mr-1"></i>Converted</span></td><td class="text-right"><a href="{{ route('admissions.applications.show', $application) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye mr-1"></i>View details</a></td></tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted empty-state"><i class="fas fa-user-graduate d-block mb-3" style="font-size:2.4rem;opacity:.35;"></i><strong>No converted students</strong><span class="d-block mt-1">Converted applicants will appear here after admission is completed.</span></td></tr>
            @endforelse
        </tbody></table></div>
        @if($applications->hasPages())<div class="p-3">{{ $applications->links() }}</div>@endif
    </div>
</div>
@endsection
