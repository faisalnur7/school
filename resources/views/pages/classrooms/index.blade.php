@extends('layouts.master')

@section('contents')
<div class="container-fluid classrooms-page">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-door-open mr-2"></i>Classrooms
                </h4>
                @if(auth()->user()?->hasPermission('create_classrooms'))
                    <a href="{{ route('classrooms.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus mr-1"></i> Add Classroom
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="classrooms-filter-panel mb-3" role="search">
                <div class="classrooms-filter-row">
                    <div class="classrooms-filter-search">
                        <label class="sr-only" for="classroom-search">Search name or location</label>
                        <div class="input-group classrooms-filter-input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text classrooms-filter-icon" aria-hidden="true">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input
                                type="text"
                                id="classroom-search"
                                name="search"
                                class="form-control classrooms-filter-input"
                                value="{{ request('search') }}"
                                placeholder="Search name or location"
                            >
                        </div>
                    </div>
                    <div class="classrooms-filter-actions">
                        <button type="submit" class="btn btn-primary classrooms-filter-btn" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary classrooms-filter-btn" title="Reset" aria-label="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name (English)</th>
                            <th>Name (Bangla)</th>
                            <th>Capacity</th>
                            <th>Location</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classrooms as $classroom)
                            <tr>
                                <td>{{ $classrooms->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">{{ $classroom->name_en }}</td>
                                <td>{{ $classroom->name_bn }}</td>
                            <td>{{ $classroom->capacity ?? '—' }}</td>
                            <td>{{ $classroom->location ?: '—' }}</td>
                            <td class="text-center">
                                @if(auth()->user()?->hasPermission('edit_classrooms'))
                                    <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-xs classroom-action-btn classroom-action-edit" title="Edit" aria-label="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                @if(auth()->user()?->hasPermission('delete_classrooms'))
                                    <form action="{{ route('classrooms.delete', $classroom->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this classroom?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-xs classroom-action-btn classroom-action-delete" title="Delete" aria-label="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No classrooms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $classrooms->links() }}
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    html[data-theme='dark'] .classrooms-page .card,
    html.dark .classrooms-page .card,
    html[data-theme='dark'] .classrooms-page .card-body,
    html.dark .classrooms-page .card-body,
    html[data-theme='dark'] .classrooms-page .classrooms-filter-panel,
    html.dark .classrooms-page .classrooms-filter-panel {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .classrooms-page .card-header,
    html.dark .classrooms-page .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .classrooms-page .classrooms-filter-input,
    html.dark .classrooms-page .classrooms-filter-input {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .classrooms-page .classrooms-filter-input::placeholder,
    html.dark .classrooms-page .classrooms-filter-input::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .classrooms-page .classrooms-filter-icon,
    html.dark .classrooms-page .classrooms-filter-icon {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #93c5fd !important;
    }

    html[data-theme='dark'] .classrooms-page .table,
    html.dark .classrooms-page .table {
        --bs-table-bg: #111827;
        --bs-table-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .classrooms-page .table thead,
    html.dark .classrooms-page .table thead,
    html[data-theme='dark'] .classrooms-page .table thead th,
    html.dark .classrooms-page .table thead th {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .classrooms-page .table td,
    html.dark .classrooms-page .table td {
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .classrooms-page .text-muted,
    html.dark .classrooms-page .text-muted {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .classrooms-page .btn-outline-secondary,
    html.dark .classrooms-page .btn-outline-secondary {
        background: #111827 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }

    .classrooms-page .classroom-action-btn {
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

    .classrooms-page .classroom-action-edit {
        background: #334155 !important;
        border: 1px solid #475569 !important;
    }

    .classrooms-page .classroom-action-delete {
        background: #ef3340 !important;
    }
</style>
@endsection
