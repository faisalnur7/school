@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-id-badge mr-2"></i>Designations
                </h4>
                <a href="{{ route('hr.designations.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="employee_type" class="form-control form-control-sm" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="teacher" {{ request('employee_type') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="staff"   {{ request('employee_type') === 'staff'   ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('hr.designations.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr><th>#</th><th>Name</th><th>Type</th><th>Level</th><th>Employees</th><th>Status</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($designations as $d)
                        <tr>
                            <td>{{ $designations->firstItem() + $loop->index }}</td>
                            <td class="font-weight-bold">{{ $d->name }}</td>
                            <td><span class="badge badge-{{ $d->employee_type === 'teacher' ? 'primary' : 'info' }}">{{ ucfirst($d->employee_type) }}</span></td>
                            <td><span class="badge badge-secondary">Level {{ $d->hierarchy_level }}</span></td>
                            <td>{{ $d->employees_count }}</td>
                            <td>
                                <form action="{{ route('hr.designations.toggle-status', $d) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="badge badge-{{ $d->status === 'active' ? 'success' : 'secondary' }} border-0" style="cursor:pointer">{{ ucfirst($d->status) }}</button>
                                </form>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('hr.designations.edit', $d) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('hr.designations.destroy', $d) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No designations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $designations->links() }}
        </div>
    </div>
</div>
@endsection
