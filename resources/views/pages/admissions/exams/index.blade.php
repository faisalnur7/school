@extends('layouts.master')
@section('contents')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Admission Exams</h2><p class="text-muted mb-0">Create exams and classwise pass marks.</p></div><a class="btn btn-primary" href="{{ route('admissions.exams.create') }}"><i class="fas fa-plus mr-1"></i>Create Exam</a></div>
    <div class="row">
        @forelse($exams as $exam)
        <div class="col-md-6 col-xl-4 mb-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="d-flex justify-content-between"><span class="badge badge-{{ $exam->status ? 'success' : 'secondary' }}">{{ $exam->status ? 'Active' : 'Inactive' }}</span><small class="text-muted">{{ $exam->applications_count }} applications</small></div><h4 class="mt-3">{{ $exam->name }}</h4><p class="text-muted mb-1">{{ $exam->academicSession?->name_en }}</p><p class="mb-3"><i class="far fa-calendar mr-1"></i>{{ $exam->exam_date?->format('d M Y') }}</p><form method="POST" action="{{ route('admissions.exams.toggle', $exam) }}">@csrf<a href="{{ route('admissions.exams.edit', $exam) }}" class="btn btn-sm btn-outline-primary">Edit</a><button class="btn btn-sm btn-outline-dark ml-1">{{ $exam->status ? 'Deactivate' : 'Activate' }}</button><a href="{{ route('admissions.marks', $exam) }}" class="btn btn-sm btn-outline-primary ml-1">Marks</a></form></div></div></div>
        @empty <div class="col-12"><div class="alert alert-light border">No admission exams created yet.</div></div> @endforelse
    </div>
</div>
@endsection
