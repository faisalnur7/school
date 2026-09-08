@extends('layouts.master')

@section('contents')
@php
    $data = $application->applicant_data ?? [];
    $name = $data['full_name_en'] ?? $application->full_name_en ?? '-';
    $image = $application->image ?? ($data['image'] ?? null);
    $imagePath = $image && file_exists(public_path($image)) ? asset($image) : null;
    $resultClass = $application->result_status === 'passed' ? 'status-passed' : ($application->result_status === 'failed' ? 'status-failed' : 'status-pending');
    $reviewClass = $application->review_status === 'approved' ? 'status-approved' : ($application->review_status === 'rejected' ? 'status-rejected' : 'status-pending');
@endphp

<style>
    .admission-application-page { color: #172033; }
    .admission-application-page .application-hero { background: linear-gradient(120deg, #10233d 0%, #155e75 62%, #0f766e 100%); border-radius: 16px; color: #fff; overflow: hidden; position: relative; }
    .admission-application-page .application-hero::after { background: rgba(255,255,255,.08); border-radius: 50%; content: ''; height: 240px; position: absolute; right: -55px; top: -155px; width: 240px; }
    .admission-application-page .application-hero > * { position: relative; z-index: 1; }
    .admission-application-page .hero-photo { border: 4px solid rgba(255,255,255,.75); border-radius: 14px; box-shadow: 0 8px 22px rgba(0,0,0,.18); height: 92px; object-fit: cover; width: 74px; }
    .admission-application-page .hero-avatar { align-items: center; background: rgba(255,255,255,.16); border-radius: 14px; display: inline-flex; font-size: 2rem; font-weight: 800; height: 92px; justify-content: center; width: 74px; }
    .admission-application-page .application-number { color: #b8f3e8; font-size: .8rem; font-weight: 800; letter-spacing: .08em; }
    .admission-application-page .status-card, .admission-application-page .details-card { border: 1px solid #e5eaf1; border-radius: 14px; box-shadow: 0 6px 20px rgba(23,32,51,.06); }
    .admission-application-page .status-label, .admission-application-page .detail-label { color: #8490a3; display: block; font-size: .68rem; font-weight: 800; letter-spacing: .05em; text-transform: uppercase; }
    .admission-application-page .status-value { color: #172033; font-size: 1.1rem; font-weight: 800; margin-top: 6px; }
    .admission-application-page .status-badge { border-radius: 999px; display: inline-block; font-size: .7rem; font-weight: 700; padding: 5px 9px; }
    .admission-application-page .status-approved, .admission-application-page .status-paid { background: #dcfce7; color: #166534; }
    .admission-application-page .status-passed { background: #eef2ff; color: #4338ca; }
    .admission-application-page .status-failed, .admission-application-page .status-rejected { background: #fee2e2; color: #991b1b; }
    .admission-application-page .status-pending { background: #f1f5f9; color: #64748b; }
    .admission-application-page .details-card .card-header { background: #fff; border-bottom: 1px solid #edf1f5; }
    .admission-application-page .details-card h5 { color: #0f766e; font-weight: 800; }
    .admission-application-page .detail-item { border-bottom: 1px solid #edf1f5; min-height: 58px; padding: 11px 0; }
    .admission-application-page .detail-item:last-child { border-bottom: 0; }
    .admission-application-page .detail-value { color: #26364a; display: block; font-size: .92rem; font-weight: 600; margin-top: 3px; word-break: break-word; }
    .admission-application-page .review-card { background: #f8fafc; border: 1px solid #dfe7f0; border-radius: 14px; }
    .admission-application-page .review-card .form-control { border-color: #d9e1eb; border-radius: 9px; min-height: 42px; }
    .admission-application-page .history-item { border-left: 3px solid #0f766e; padding-left: 12px; }
    .admission-application-page .history-item + .history-item { margin-top: 14px; }
</style>

<div class="container-fluid py-3 admission-application-page">
    <div class="application-hero d-flex flex-wrap align-items-center justify-content-between mb-3 p-4">
        <div class="d-flex align-items-center">
            @if($imagePath)<img src="{{ $imagePath }}" alt="{{ $name }}" class="hero-photo mr-3">@else<span class="hero-avatar mr-3">{{ strtoupper(substr($name, 0, 1)) }}</span>@endif
            <div><span class="application-number">APPLICATION {{ $application->application_number }}</span><h2 class="mb-1 mt-1 text-white">{{ $name }}</h2><p class="mb-0" style="opacity:.78;">{{ $application->exam?->name ?? 'Admission exam' }} · {{ $application->schoolClass?->name_en ?? 'Class not assigned' }}</p></div>
        </div>
        <div class="mt-3 mt-md-0"><a href="{{ route('admissions.applications.download', $application) }}" class="btn btn-light mr-1"><i class="fas fa-file-pdf mr-1"></i> Application PDF</a><a href="{{ route('admissions.applications') }}" class="btn btn-outline-light"><i class="fas fa-arrow-left mr-1"></i> Back</a></div>
    </div>

    <div class="row mb-3">
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card status-card h-100"><div class="card-body"><span class="status-label">Payment</span><div class="status-value"><span class="status-badge {{ $application->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">{{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}</span></div><small class="text-muted">{{ $application->payment?->amount !== null ? '৳ ' . number_format((float) $application->payment->amount, 2) : 'No payment entry' }}</small></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-xl-0"><div class="card status-card h-100"><div class="card-body"><span class="status-label">Exam result</span><div class="status-value"><span class="status-badge {{ $resultClass }}">{{ ucfirst(str_replace('_', ' ', $application->result_status)) }}</span></div><small class="text-muted">{{ $application->total_marks !== null ? number_format((float) $application->total_marks, 0) . ' / ' . number_format((float) $application->pass_mark_snapshot, 0) : 'Marks not entered' }}</small></div></div></div>
        <div class="col-sm-6 col-xl-3 mb-3 mb-sm-0"><div class="card status-card h-100"><div class="card-body"><span class="status-label">Review decision</span><div class="status-value"><span class="status-badge {{ $reviewClass }}">{{ ucfirst($application->review_status) }}</span></div><small class="text-muted">{{ $application->reviews->count() }} review entr{{ $application->reviews->count() === 1 ? 'y' : 'ies' }}</small></div></div></div>
        <div class="col-sm-6 col-xl-3"><div class="card status-card h-100"><div class="card-body"><span class="status-label">Admission status</span><div class="status-value"><span class="status-badge {{ $application->conversion_status === 'converted' ? 'status-approved' : 'status-pending' }}">{{ ucfirst(str_replace('_', ' ', $application->conversion_status)) }}</span></div><small class="text-muted">{{ $application->convertedStudent?->student_cid ? 'Student ID: ' . $application->convertedStudent->student_cid : 'Not converted yet' }}</small></div></div></div>
    </div>

    @if($application->conversion_status === 'converted' && $application->convertedStudent)
        <div class="alert alert-success d-flex flex-wrap justify-content-between align-items-center mb-3"><span><strong>Admission converted successfully.</strong> New Student ID: <strong>{{ $application->convertedStudent->student_cid }}</strong></span><a href="{{ route('students.show', $application->convertedStudent->id) }}" class="btn btn-sm btn-success mt-2 mt-md-0">View Student</a></div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card details-card mb-3"><div class="card-header"><h5 class="mb-0">Applicant information</h5></div><div class="card-body"><div class="row">
                @foreach([['Full name', $name], ['Name in Bangla', $data['full_name_bn'] ?? $application->full_name_bn ?? '-'], ['Date of birth', $data['date_of_birth'] ?? $application->date_of_birth ?? '-'], ['Gender', $application->gender == 1 ? 'Male' : 'Female'], ['Religion', \App\Models\Student::RELIGIONS[(int) ($application->religion ?? 0)] ?? '-'], ['Blood group', \App\Models\Student::BLOOD_GROUPS[(int) ($application->blood_group ?? 0)] ?? '-'], ['Birth certificate', $data['birth_certificate_number'] ?? $application->birth_certificate_number ?? '-'], ['Previous school', $data['previous_school'] ?? $application->previous_school ?? '-']] as [$label, $value])<div class="col-md-6"><div class="detail-item"><span class="detail-label">{{ $label }}</span><span class="detail-value">{{ $value }}</span></div></div>@endforeach
            </div></div></div>

            <div class="card details-card mb-3"><div class="card-header"><h5 class="mb-0">Parents and contact</h5></div><div class="card-body"><div class="row">
                @foreach([['Father', $data['father_name'] ?? $application->father_name ?? '-'], ['Father phone', $data['father_phone'] ?? $application->father_phone ?? '-'], ['Father email', $data['father_email'] ?? $application->father_email ?? '-'], ['Mother', $data['mother_name'] ?? $application->mother_name ?? '-'], ['Mother phone', $data['mother_phone'] ?? $application->mother_phone ?? '-'], ['Mother email', $data['mother_email'] ?? $application->mother_email ?? '-']] as [$label, $value])<div class="col-md-6"><div class="detail-item"><span class="detail-label">{{ $label }}</span><span class="detail-value">{{ $value }}</span></div></div>@endforeach
            </div></div></div>

            <div class="card details-card mb-3"><div class="card-header"><h5 class="mb-0">Address</h5></div><div class="card-body"><div class="row"><div class="col-md-6"><div class="detail-item"><span class="detail-label">Present address</span><span class="detail-value">{{ $data['present_address'] ?? $application->present_address ?? '-' }}</span></div></div><div class="col-md-6"><div class="detail-item"><span class="detail-label">Permanent address</span><span class="detail-value">{{ $data['permanent_address'] ?? $application->permanent_address ?? '-' }}</span></div></div></div></div></div>
        </div>

        <div class="col-lg-4">
            <div class="card review-card mb-3"><div class="card-body"><h5 class="mb-1">Review application</h5><p class="small text-muted">Update the decision and keep an internal note for the admission team.</p><form method="POST" action="{{ route('admissions.applications.review', $application) }}">@csrf<div class="form-group"><label for="decision">Decision</label><select id="decision" name="decision" class="form-control"><option value="pending" @selected($application->review_status === 'pending')>Pending</option><option value="approved" @selected($application->review_status === 'approved')>Approve</option><option value="rejected" @selected($application->review_status === 'rejected')>Reject</option></select></div><div class="form-group"><label for="notes">Admin notes</label><textarea id="notes" name="notes" class="form-control" rows="3">{{ $application->admin_notes }}</textarea></div><button class="btn btn-primary btn-block"><i class="fas fa-save mr-1"></i> Save decision</button></form></div></div>
            @if($application->review_status === 'approved' && $application->conversion_status !== 'converted')<div class="card border-success mb-3"><div class="card-body"><h5 class="text-success">Ready for admission</h5><p class="small text-muted">Select the section where this student will be admitted.</p><form method="POST" action="{{ route('admissions.applications.convert', $application) }}">@csrf<div class="form-group"><label for="admissionSection">Admission section</label><select id="admissionSection" name="section_id" class="form-control" required><option value="">Select section</option>@foreach($sections as $section)<option value="{{ $section->id }}">{{ $section->name_en }}</option>@endforeach</select>@if($sections->isEmpty())<small class="text-danger">No section is configured for this class.</small>@endif</div><button class="btn btn-success btn-block" @disabled($sections->isEmpty())><i class="fas fa-user-plus mr-1"></i> Proceed to Admission</button></form></div></div>@endif
            @if($application->reviews->isNotEmpty())<div class="card details-card"><div class="card-header"><h5 class="mb-0">Review history</h5></div><div class="card-body">@foreach($application->reviews->sortByDesc('reviewed_at') as $review)<div class="history-item"><strong>{{ ucfirst($review->decision) }}</strong><small class="d-block text-muted">{{ $review->reviewed_at?->format('d M Y, h:i A') ?? '-' }}</small>@if($review->notes)<span class="small text-muted">{{ $review->notes }}</span>@endif</div>@endforeach</div></div>@endif
        </div>
    </div>
</div>
@endsection
