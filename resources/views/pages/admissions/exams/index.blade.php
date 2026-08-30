@extends('layouts.master')

@section('contents')
@php
    $activeExams = $exams->where('status', true)->count();
    $totalApplications = $exams->sum('applications_count');
@endphp
<div class="container-fluid py-4 exams-page">
    <div class="exams-hero mb-4">
        <div class="hero-copy"><span class="eyebrow"><i class="fas fa-graduation-cap mr-2"></i>Admission management</span><h1>Admission exams</h1><p>Create, configure, and monitor every admission examination from one place.</p></div>
        <a class="btn btn-light hero-button" href="{{ route('admissions.exams.create') }}"><i class="fas fa-plus mr-1"></i>Create exam</a>
    </div>

    <div class="row mb-4">
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="overview-card"><span class="overview-icon icon-blue"><i class="fas fa-layer-group"></i></span><div><strong>{{ $exams->count() }}</strong><span>Total exams</span></div></div></div>
        <div class="col-sm-4 mb-3 mb-sm-0"><div class="overview-card"><span class="overview-icon icon-green"><i class="fas fa-bolt"></i></span><div><strong>{{ $activeExams }}</strong><span>Active exam{{ $activeExams === 1 ? '' : 's' }}</span></div></div></div>
        <div class="col-sm-4"><div class="overview-card"><span class="overview-icon icon-cyan"><i class="fas fa-users"></i></span><div><strong>{{ $totalApplications }}</strong><span>Total applications</span></div></div></div>
    </div>

    <div class="section-title"><div><h3>Examination list</h3><p>Select an exam to view its pipeline, marks, or settings.</p></div><span class="live-label"><i class="fas fa-circle mr-1"></i>{{ $exams->count() }} records</span></div>
    <div class="row">
        @forelse($exams as $exam)
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="exam-card h-100">
                    <div class="exam-card-top"><span class="status-pill {{ $exam->status ? 'status-active' : 'status-inactive' }}"><i class="fas fa-circle mr-1"></i>{{ $exam->status ? 'Active' : 'Inactive' }}</span><span class="application-count"><strong>{{ $exam->applications_count }}</strong> applications</span></div>
                    <h4>{{ $exam->name }}</h4>
                    <div class="exam-meta"><span><i class="fas fa-calendar-alt"></i>{{ $exam->exam_date?->format('d M Y') }}</span><span><i class="fas fa-bookmark"></i>{{ $exam->academicSession?->name_en ?? 'No session' }}</span></div>
                    <div class="exam-summary"><div><small>Form fee</small><strong>৳ {{ number_format((float) $exam->form_fee, 2) }}</strong></div><div><small>Applications</small><strong>{{ $exam->applications_count }}</strong></div><div><small>Access</small><strong>{{ $exam->status ? 'Open' : 'Closed' }}</strong></div></div>
                    <div class="exam-actions"><a href="{{ route('admissions.exams.details', $exam) }}" class="btn btn-primary btn-sm"><i class="fas fa-chart-pie mr-1"></i>Details</a><a href="{{ route('admissions.marks', $exam) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-pen mr-1"></i>Marks</a><div class="dropdown ml-auto"><button class="btn btn-light btn-sm" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></button><div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="{{ route('admissions.exams.edit', $exam) }}"><i class="fas fa-edit fa-fw mr-2 text-muted"></i>Edit exam</a><form method="POST" action="{{ route('admissions.exams.toggle', $exam) }}">@csrf<button class="dropdown-item"><i class="fas fa-power-off fa-fw mr-2 text-muted"></i>{{ $exam->status ? 'Deactivate exam' : 'Activate exam' }}</button></form></div></div></div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="empty-state"><i class="fas fa-calendar-plus"></i><h4>No admission exams yet</h4><p>Create the first examination to start accepting applications.</p><a href="{{ route('admissions.exams.create') }}" class="btn btn-primary"><i class="fas fa-plus mr-1"></i>Create exam</a></div></div>
        @endforelse
    </div>
</div>

<style>
    .exams-page { color:#172033; }.exams-hero { align-items:center; background:linear-gradient(118deg,#102c46,#116b70 60%,#1599a1); border-radius:16px; box-shadow:0 11px 25px rgba(15,91,100,.17); color:#fff; display:flex; justify-content:space-between; min-height:165px; overflow:hidden; padding:30px 34px; position:relative; }.exams-hero:after { border:1px solid rgba(255,255,255,.16); border-radius:50%; content:''; height:290px; position:absolute; right:-80px; top:-170px; width:290px; }.hero-copy,.hero-button { position:relative; z-index:1; }.eyebrow { color:#a7f3d0; font-size:11px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase; }.exams-hero h1 { font-size:30px; margin:9px 0 6px; }.exams-hero p { color:rgba(255,255,255,.76); font-size:13px; margin:0; }.hero-button { color:#0b5962; font-weight:600; }.overview-card { align-items:center; background:#fff; border:1px solid #e5ebf2; border-radius:12px; box-shadow:0 4px 12px rgba(30,41,59,.05); display:flex; min-height:82px; padding:15px 18px; }.overview-icon { align-items:center; border-radius:10px; display:inline-flex; font-size:17px; height:40px; justify-content:center; margin-right:13px; width:40px; }.icon-blue { background:#eaf2ff;color:#2563eb; }.icon-green { background:#eaf8ef;color:#16a34a; }.icon-cyan { background:#e6f9fb;color:#0891b2; }.overview-card strong,.overview-card span { display:block; }.overview-card strong { font-size:24px; line-height:1; }.overview-card span { color:#8492a6; font-size:11px; margin-top:5px; }.section-title { align-items:center; display:flex; justify-content:space-between; margin-bottom:15px; }.section-title h3 { font-size:18px; margin:0; }.section-title p { color:#8492a6; font-size:12px; margin:4px 0 0; }.live-label { background:#f0fdf4; border-radius:20px; color:#16803c; font-size:10px; padding:7px 11px; }.live-label i { font-size:7px; }.exam-card { background:#fff; border:1px solid #e5ebf2; border-radius:14px; box-shadow:0 4px 13px rgba(30,41,59,.05); padding:20px; transition:box-shadow .18s ease, transform .18s ease; }.exam-card:hover { box-shadow:0 11px 24px rgba(30,41,59,.11); transform:translateY(-3px); }.exam-card-top { align-items:center; display:flex; justify-content:space-between; }.status-pill { border-radius:20px; font-size:10px; font-weight:600; padding:5px 9px; }.status-pill i { font-size:7px; }.status-active { background:#eaf8ef;color:#16803c; }.status-inactive { background:#f1f5f9;color:#64748b; }.application-count { color:#94a3b8; font-size:10px; }.application-count strong { color:#334155; font-size:13px; }.exam-card h4 { color:#26364a; font-size:18px; margin:20px 0 12px; }.exam-meta { border-bottom:1px solid #edf1f5; display:flex; flex-wrap:wrap; padding-bottom:15px; }.exam-meta span { color:#718096; font-size:11px; margin-right:18px; }.exam-meta i { color:#0f766e; margin-right:6px; }.exam-summary { display:flex; justify-content:space-between; padding:16px 0; }.exam-summary small,.exam-summary strong { display:block; }.exam-summary small { color:#94a3b8; font-size:9px; margin-bottom:4px; }.exam-summary strong { color:#334155; font-size:12px; }.exam-actions { align-items:center; border-top:1px solid #edf1f5; display:flex; padding-top:15px; }.exam-actions .btn { margin-right:6px; }.exam-actions .dropdown .btn { margin-right:0; }.empty-state { background:#fff; border:1px dashed #cbd5e1; border-radius:14px; padding:65px 20px; text-align:center; width:100%; }.empty-state i { color:#b8c5d3; font-size:32px; }.empty-state h4 { color:#526174; font-size:16px; margin:14px 0 5px; }.empty-state p { color:#94a3b8; font-size:12px; margin-bottom:18px; }
    @media (max-width:767.98px) { .exams-hero { align-items:flex-start; display:block; padding:24px; }.hero-button { margin-top:22px; }.section-title { align-items:flex-start; display:block; }.live-label { display:inline-block; margin-top:10px; } }
</style>
@endsection
