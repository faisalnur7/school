@extends('layouts.master')

@section('contents')
<div class="container-fluid">
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
                        <tr><td>{{ $schedules->firstItem() + $loop->index }}</td><td class="font-weight-bold">{{ $schedule->name }}</td><td>{{ ucfirst($schedule->kind) }}</td><td>{{ $schedule->formatted_start_time }} - {{ $schedule->formatted_end_time }}</td><td>{{ $schedule->sort_order }}</td><td>{!! $schedule->is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>' !!}</td><td class="text-center">@if(auth()->user()?->hasPermission('edit_routines'))<a href="{{ route('class-schedules.edit', $schedule->id) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>@endif @if(auth()->user()?->hasPermission('delete_routines'))<form action="{{ route('class-schedules.delete', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this schedule?')">@csrf @method('DELETE')<button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button></form>@endif</td></tr>
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
