@extends('layouts.master')

@section('contents')
<div class="container-fluid class-schedules-page">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div><h4 class="card-title mb-0 font-weight-bold text-white"><i class="fas fa-calendar-alt mr-2"></i>Time Schedule</h4><small class="text-white-50">Manage periods, assembly, tiffin, and prayer times.</small></div>
                <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">
                    <a href="{{ route('academics.hub') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                    @if(auth()->user()?->hasPermission('create_routines'))
                        <a href="{{ route('class-schedules.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus mr-1"></i>Add Schedule
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="GET" class="form-inline mb-3">
                <input type="search" name="search" class="form-control mr-2" value="{{ request('search') }}" placeholder="Search schedule name">
                <button class="btn btn-primary btn-sm mr-2"><i class="fas fa-search"></i> Search</button>
                <a href="{{ route('class-schedules.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark"><tr><th>#</th><th>Schedule</th><th>Type</th><th>Time</th><th>Order</th><th>Status</th><th class="text-center">Actions</th></tr></thead>
                    <tbody>
                    @forelse($schedules as $schedule)
                        <tr><td>{{ $schedules->firstItem() + $loop->index }}</td><td class="font-weight-bold">{{ $schedule->name }}</td><td>{{ ucfirst($schedule->kind) }}</td><td>{{ $schedule->formatted_start_time }} - {{ $schedule->formatted_end_time }}</td><td>{{ $schedule->sort_order }}</td><td>{!! $schedule->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' !!}</td><td class="text-center">@if(auth()->user()?->hasPermission('edit_routines'))<a href="{{ route('class-schedules.edit', $schedule->id) }}" class="btn btn-xs schedule-action-btn schedule-action-edit" title="Edit" aria-label="Edit"><i class="fas fa-edit"></i></a>@endif @if(auth()->user()?->hasPermission('delete_routines'))<form action="{{ route('class-schedules.delete', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this schedule?')">@csrf @method('DELETE')<button class="btn btn-xs schedule-action-btn schedule-action-delete" title="Delete" aria-label="Delete"><i class="fas fa-trash"></i></button></form>@endif</td></tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No time schedules found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $schedules->links() }}
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    html[data-theme='dark'] .class-schedules-page .card,
    html.dark .class-schedules-page .card,
    html[data-theme='dark'] .class-schedules-page .card-body,
    html.dark .class-schedules-page .card-body {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .class-schedules-page .card-header,
    html.dark .class-schedules-page .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .class-schedules-page form,
    html.dark .class-schedules-page form {
        background: #111827 !important;
    }

    html[data-theme='dark'] .class-schedules-page .form-control,
    html.dark .class-schedules-page .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .class-schedules-page .form-control::placeholder,
    html.dark .class-schedules-page .form-control::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .class-schedules-page .table,
    html.dark .class-schedules-page .table {
        --bs-table-bg: #111827;
        --bs-table-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .class-schedules-page .table thead,
    html.dark .class-schedules-page .table thead,
    html[data-theme='dark'] .class-schedules-page .table thead th,
    html.dark .class-schedules-page .table thead th {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .class-schedules-page .table td,
    html.dark .class-schedules-page .table td {
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .class-schedules-page .text-muted,
    html.dark .class-schedules-page .text-muted,
    html[data-theme='dark'] .class-schedules-page .btn-outline-secondary,
    html.dark .class-schedules-page .btn-outline-secondary {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .class-schedules-page .btn-outline-secondary,
    html.dark .class-schedules-page .btn-outline-secondary {
        background: #111827 !important;
        border-color: #475569 !important;
    }

    .class-schedules-page .schedule-action-btn {
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

    .class-schedules-page .schedule-action-edit {
        background: #334155 !important;
        border: 1px solid #475569 !important;
    }

    .class-schedules-page .schedule-action-delete {
        background: #ef3340 !important;
    }

    .class-schedules-page .schedule-action-btn:hover,
    .class-schedules-page .schedule-action-btn:focus {
        filter: brightness(1.08);
        transform: translateY(-1px);
    }
</style>
@endsection
