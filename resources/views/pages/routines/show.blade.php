@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-clock mr-2"></i>Routine Details
                    </h4>
                    <small class="text-white-50">{{ $routine->schoolClass?->name_en }} | {{ $routine->section?->name_en }}</small>
                </div>
                <div class="d-flex gap-2">
                    @if(auth()->user()?->hasPermission('edit_routines'))
                        <a href="{{ route('routines.edit', $routine->id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    @endif
                    <a href="{{ route('routines.index') }}" class="btn btn-outline-light btn-sm">
                        Back
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Class</div>
                        <div class="font-weight-bold">{{ $routine->schoolClass?->name_en ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Section</div>
                        <div class="font-weight-bold">{{ $routine->section?->name_en ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Subject</div>
                        <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                        @if($routine->subject?->code)
                            <div class="text-muted small">{{ $routine->subject->code }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Teacher</div>
                        <div class="font-weight-bold">{{ $routine->teacher?->name ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Classroom</div>
                        <div class="font-weight-bold">{{ $routine->classroom?->name_en ?? '—' }}</div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded p-3 h-100">
                        <div class="text-muted small">Schedule</div>
                        <div class="font-weight-bold">{{ $routine->day }}</div>
                        <div>{{ substr($routine->start_time, 0, 5) }} - {{ substr($routine->end_time, 0, 5) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
