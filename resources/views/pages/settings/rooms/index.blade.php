@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-door-open mr-2"></i>Rooms
                </h4>
                <a href="{{ route('rooms.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Room
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search name or code">
                    </div>
                    <div class="col-md-3">
                        <select name="building_id" class="form-control form-control-sm">
                            <option value="">All Buildings</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}" {{ (string) request('building_id') === (string) $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="department_id" class="form-control form-control-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ (string) request('department_id') === (string) $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="room_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach($roomTypes as $roomType)
                                <option value="{{ $roomType->value }}" {{ request('room_type') === $roomType->value ? 'selected' : '' }}>{{ ucfirst($roomType->value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-sm" aria-label="{{ __('Search') }}" title="{{ __('Search') }}"><i class="fas fa-search" aria-hidden="true"></i></button>
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm" aria-label="{{ __('Close') }}" title="{{ __('Close') }}"><i class="fas fa-times" aria-hidden="true"></i></a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Building</th>
                            <th>Department</th>
                            <th>Floor</th>
                            <th>Type</th>
                            <th>Seats</th>
                            <th>Assets</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td>{{ $rooms->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">{{ $room->name }}</td>
                                <td>{{ $room->code }}</td>
                                <td>{{ $room->building->name ?? '—' }}</td>
                                <td>{{ $room->department->name ?? '—' }}</td>
                                <td>{{ $room->floor_number }}</td>
                                <td>{{ $room->room_type?->value ? ucfirst($room->room_type->value) : '—' }}</td>
                                <td>{{ $room->seating_capacity ?? '—' }}</td>
                                <td>{{ $room->assets_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $room->is_active ? 'success' : 'secondary' }}">
                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-xs btn-warning" aria-label="{{ __('Edit') }}" title="{{ __('Edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline" data-confirm="{{ __('Delete this room?') }}" data-confirm-action="{{ __('Delete room') }}" data-confirm-cancel="{{ __('Keep room') }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger" aria-label="{{ __('Delete') }}" title="{{ __('Delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center text-muted py-4">No rooms found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection
