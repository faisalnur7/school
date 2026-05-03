@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Buildings</h3>
            <a href="{{ route('buildings.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus"></i> Add Building</a>
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
                        <a href="{{ route('buildings.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
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
                                    <a href="{{ route('buildings.edit', $building) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this building?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
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
