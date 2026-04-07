@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Employees</h3>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-primary btn-sm ml-auto"><i class="fas fa-plus"></i> Add Employee</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3 form-group mb-0">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Name or Employee ID..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="employee_type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            <option value="teacher" {{ request('employee_type') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="staff"   {{ request('employee_type') === 'staff'   ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="designation_id" class="form-control form-control-sm">
                            <option value="">All Designations</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}" {{ request('designation_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All Status</option>
                            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group mb-0 d-flex gap-1" style="gap:6px">
                        <button type="submit" class="btn btn-primary btn-sm" title="Search"><i class="fas fa-search"></i></button>
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary btn-sm" title="Reset"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr><th>#</th><th>Photo</th><th>Employee ID</th><th>Name</th><th>Type</th><th>Designation</th><th>Department</th><th>Status</th><th class="text-center">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                        <tr>
                            <td>{{ $employees->firstItem() + $loop->index }}</td>
                            <td><img src="{{ $emp->photo_url }}" class="img-circle" style="width:36px;height:36px;object-fit:cover"></td>
                            <td><code>{{ $emp->employee_id }}</code></td>
                            <td class="font-weight-bold"><a href="{{ route('hr.employees.show', $emp) }}">{{ $emp->name }}</a></td>
                            <td><span class="badge badge-{{ $emp->employee_type === 'teacher' ? 'primary' : 'info' }}">{{ ucfirst($emp->employee_type) }}</span></td>
                            <td>{{ $emp->designation->name ?? '—' }}</td>
                            <td>{{ $emp->department?->name ?? '—' }}</td>
                            <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($emp->status) }}</span></td>
                            <td class="text-center">
                                <a href="{{ route('hr.employees.show', $emp) }}" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('hr.employees.edit', $emp) }}" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('hr.employees.destroy', $emp) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete/deactivate this employee?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No employees found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection
