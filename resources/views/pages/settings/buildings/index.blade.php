@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-building mr-2"></i>Buildings
                </h4>
                <a href="{{ route('buildings.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Building
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Search name, code, description">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
                        <a href="{{ route('buildings.index') }}" class="btn btn-secondary btn-sm" aria-label="{{ __('Close') }}" title="{{ __('Close') }}"><i class="fas fa-times" aria-hidden="true"></i></a>
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
                            <th>Description</th>
                            <th>Rooms</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($buildings as $building)
                            <tr>
                                <td>{{ $buildings->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">{{ $building->name }}</td>
                                <td>{{ $building->code }}</td>
                                <td>{{ $building->description ?: '—' }}</td>
                                <td>{{ $building->rooms_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $building->is_active ? 'success' : 'secondary' }}">
                                        {{ $building->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('buildings.edit', $building) }}" class="btn btn-xs btn-warning" aria-label="{{ __('Edit') }}" title="{{ __('Edit') }}"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                    <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="d-inline" data-confirm="{{ __('Delete this building?') }}" data-confirm-action="{{ __('Delete building') }}" data-confirm-cancel="{{ __('Keep building') }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger" aria-label="{{ __('Delete') }}" title="{{ __('Delete') }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No buildings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $buildings->links() }}
        </div>
    </div>
</div>
@endsection
