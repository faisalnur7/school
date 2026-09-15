@extends('layouts.master')

@section('contents')
<div class="container-fluid routines-page">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-clock mr-2"></i>Class Routines
                    </h4>
                    <small class="text-white-50">Manage weekly class schedules.</small>
                </div>
                @if(auth()->user()?->hasPermission('create_routines'))
                    <a href="{{ route('routines.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus mr-1"></i>Add Routine
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="routines-filter-form mb-3">
                <div class="routines-filter-row">
                    <div class="routines-filter-field routines-filter-search">
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search class, section, subject, teacher">
                    </div>
                    <div class="routines-filter-field routines-filter-session">
                        <select name="academic_session_id" class="form-control">
                            <option value="">All academic sessions</option>
                            @foreach ($academicSessions as $academicSession)
                                <option value="{{ $academicSession->id }}" @selected((string) request('academic_session_id') === (string) $academicSession->id)>
                                    {{ $academicSession->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-class">
                        <select name="school_class_id" class="form-control">
                            <option value="">All classes</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected((string) request('school_class_id') === (string) $class->id)>
                                    {{ $class->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-section">
                        <select name="section_id" class="form-control">
                            <option value="">All sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>
                                    {{ $section->name_en }} @if($section->schoolClass) ({{ $section->schoolClass->name_en }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-field routines-filter-day">
                        <select name="day" class="form-control">
                            <option value="">All days</option>
                            @foreach ($days as $day)
                                <option value="{{ $day }}" @selected(request('day') === $day)>{{ $day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="routines-filter-actions">
                        <button class="btn btn-primary btn-sm" type="submit" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('routines.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Academic session</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Room</th>
                            <th>Day</th>
                            <th>Time</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($routines as $routine)
                            <tr>
                                <td>{{ $routines->firstItem() + $loop->index }}</td>
                                <td>{{ $routine->academicSession?->name_en ?? '—' }}</td>
                                <td>{{ $routine->schoolClass?->name_en ?? '—' }}</td>
                                <td>{{ $routine->section?->name_en ?? '—' }}</td>
                                <td>
                                    <div class="font-weight-bold">{{ $routine->subject?->name ?? '—' }}</div>
                                    @if($routine->subject?->code)
                                        <small class="text-muted">{{ $routine->subject->code }}</small>
                                    @endif
                                </td>
                                <td>{{ $routine->teacher?->name ?? '—' }}</td>
                                <td>{{ $routine->classroom?->name_en ?? '—' }}</td>
                                <td>{{ $routine->day }}</td>
                                <td>{{ $routine->timeSchedule?->name ?? '—' }}<br><small class="text-muted">{{ substr($routine->start_time, 0, 5) }} - {{ substr($routine->end_time, 0, 5) }}</small></td>
                                <td class="text-center">
                                    @if(auth()->user()?->hasPermission('view_routines'))
                                        <a href="{{ route('routines.show', $routine->id) }}" class="btn btn-xs routines-action-btn routines-action-view" title="View" aria-label="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()?->hasPermission('edit_routines'))
                                        <a href="{{ route('routines.edit', $routine->id) }}" class="btn btn-xs routines-action-btn routines-action-edit" title="Edit" aria-label="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()?->hasPermission('delete_routines'))
                                        <form action="{{ route('routines.delete', $routine->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this routine?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs routines-action-btn routines-action-delete" title="Delete" aria-label="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No routines found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $routines->links() }}
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    html[data-theme='dark'] .routines-page .card,
    html.dark .routines-page .card,
    html[data-theme='dark'] .routines-page .card-body,
    html.dark .routines-page .card-body {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .card-header,
    html.dark .routines-page .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .routines-page form,
    html.dark .routines-page form {
        background: #111827 !important;
    }

    html[data-theme='dark'] .routines-page .form-control,
    html.dark .routines-page .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .form-control::placeholder,
    html.dark .routines-page .form-control::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .routines-page .form-control option,
    html.dark .routines-page .form-control option {
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .routines-page .table,
    html.dark .routines-page .table {
        --bs-table-bg: #111827;
        --bs-table-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .routines-page .table thead,
    html.dark .routines-page .table thead,
    html[data-theme='dark'] .routines-page .table thead th,
    html.dark .routines-page .table thead th {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .table td,
    html.dark .routines-page .table td {
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .routines-page .text-muted,
    html.dark .routines-page .text-muted {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .routines-page .btn-outline-secondary,
    html.dark .routines-page .btn-outline-secondary {
        background: #111827 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    .routines-filter-form {
        padding: 0.8rem;
        border: 1px solid #dbe3ef;
        border-radius: 1rem;
    }

    .routines-filter-row {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        flex-wrap: nowrap;
    }

    .routines-filter-field {
        min-width: 0;
        flex: 1 1 0;
    }

    .routines-filter-search {
        flex: 1.35 1 0;
    }

    .routines-filter-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 0 0 auto;
    }

    .routines-filter-actions .btn {
        width: 2.8rem;
        height: 2.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 0.7rem;
    }

    .routines-page .routines-action-btn {
        width: 2.15rem;
        height: 2.15rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: 0;
        border-radius: 0.55rem;
        color: #fff !important;
        box-shadow: none;
    }

    .routines-page .routines-action-view {
        background: #16a6b9 !important;
    }

    .routines-page .routines-action-edit {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #fff !important;
    }

    .routines-page .routines-action-delete {
        background: #ef3340 !important;
    }

    .routines-page .routines-action-btn:hover,
    .routines-page .routines-action-btn:focus {
        filter: brightness(1.08);
        transform: translateY(-1px);
    }

    html[data-theme='dark'] .routines-filter-form,
    html.dark .routines-filter-form {
        background: #111827 !important;
        border-color: #334155 !important;
    }

    @media (max-width: 1100px) {
        .routines-filter-row {
            flex-wrap: wrap;
        }

        .routines-filter-field {
            flex: 1 1 calc(33.333% - 0.7rem);
        }

        .routines-filter-actions {
            margin-left: auto;
        }
    }

    @media (max-width: 767.98px) {
        .routines-filter-field {
            flex: 1 1 100%;
        }

        .routines-filter-actions {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endsection
