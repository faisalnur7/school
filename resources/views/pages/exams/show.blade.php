@extends('layouts.master')

@section('styles')
    <style>
        .exam-class-card {
            border-left: 4px solid #007bff !important;
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="card-title mb-0 mr-3">
                            <i class="fas fa-info-circle mr-2"></i>Exam Details
                        </h5>
                        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list mr-1"></i>Back to Exam List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr><th width="40%">Name</th><td><strong>{{ $exam->name }}</strong></td></tr>
                        <tr><th>Type</th>
                            <td><span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }} badge-pill">
                                {{ $exam->type_label }}
                            </span></td>
                        </tr>
                        <tr><th>Session</th><td>{{ $exam->academicSession->name_en ?? $exam->academicSession->name_bn ?? '-' }}</td></tr>
                        <tr><th>Year</th><td>{{ $exam->year }}</td></tr>
                        <tr><th>Start</th><td>{{ $exam->start_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr><th>End</th><td>{{ $exam->end_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr><th>Status</th>
                            <td><span class="badge badge-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($exam->status) }}
                            </span></td>
                        </tr>
                        <tr><th>Total Students</th><td><strong>{{ $studentCount }}</strong></td></tr>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit mr-1"></i>Edit Exam
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-th-large mr-2"></i>Select a Class to Manage
                    </h5>
                    <small class="text-muted">This exam applies to all classes. Pick a class to enter marks or view results.</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($classes as $class)
                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                            <div class="card h-100 border shadow-sm exam-class-card">
                                <div class="card-body py-3 px-3 d-flex flex-column">
                                    <h6 class="font-weight-bold mb-1">{{ $class->name_en }}</h6>
                                    @if($class->name_bn)
                                        <small class="text-muted d-block mb-2">{{ $class->name_bn }}</small>
                                    @endif
                                    <div class="mt-auto">
                                        <a href="{{ route('exams.marks-entry', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                                            class="btn btn-success btn-sm btn-block mb-2" title="Enter Marks">
                                            <i class="fas fa-keyboard mr-1"></i>Marks
                                        </a>
                                        <a href="{{ route('exams.preview', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                                            class="btn btn-info btn-sm btn-block" title="Preview">
                                            <i class="fas fa-chart-bar mr-1"></i>Preview
                                        </a>
                                        @if($exam->type === 'term')
                                            <a href="{{ route('exams.terminal-result', ['exam' => $exam->id, 'class_id' => $class->id]) }}"
                                                class="btn btn-warning btn-sm btn-block mt-2" title="Result">
                                                <i class="fas fa-trophy mr-1"></i>Result
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
