@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-building mr-2"></i>Departments
                </h4>
                <a href="{{ route('hr.departments.create') }}" class="btn btn-light btn-sm">
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
                            <option value="staff" {{ request('employee_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('hr.departments.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                            <tr>
                                <td>{{ $departments->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold">{{ $department->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $department->employee_type === 'teacher' ? 'primary' : 'info' }}">
                                        {{ ucfirst($department->employee_type) }}
                                    </span>
                                </td>
                                <td>{{ $department->employees_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $department->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($department->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('hr.departments.edit', $department) }}" class="btn btn-xs btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('hr.departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No departments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $departments->links() }}
        </div>
    </div>
</div>
@endsection