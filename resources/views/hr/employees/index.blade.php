@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-users mr-2"></i>Employees
                </h4>
                <a href="{{ route('hr.employees.create') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus mr-1"></i> Add Employee
                </a>
            </div>
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
                            <td><img src="{{ $emp->photo_url }}" class="elevation-1" style="width:64px;height:64px;object-fit:cover;border-radius:12px;"></td>
                            <td><code>{{ $emp->employee_id }}</code></td>
                            <td class="font-weight-bold"><a href="{{ route('hr.employees.show', $emp) }}">{{ $emp->name }}</a></td>
                            <td><span class="badge badge-{{ $emp->employee_type === 'teacher' ? 'primary' : 'info' }}">{{ ucfirst($emp->employee_type) }}</span></td>
                            <td>{{ $emp->designation->name ?? '—' }}</td>
                            <td>{{ $emp->department?->name ?? '—' }}</td>
                            <td><span class="badge badge-{{ $emp->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($emp->status) }}</span></td>
                            <td class="text-center employee-actions">
                                <a href="{{ route('hr.employees.show', $emp) }}" class="employee-action-btn employee-action-view" title="View" aria-label="View"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('hr.employees.edit', $emp) }}" class="employee-action-btn employee-action-edit" title="Edit" aria-label="Edit"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                <form action="{{ route('hr.employees.destroy', $emp) }}" method="POST" class="employee-action-form" onsubmit="return confirm('Delete/deactivate this employee?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="employee-action-btn employee-action-delete" title="Delete" aria-label="Delete"><i class="fas fa-trash" aria-hidden="true"></i></button>
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

@section('styles')
    <style>
        .employee-actions { white-space: nowrap; }
        .employee-action-btn {
            width: 2.15rem;
            height: 2.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0 0 0 .2rem;
            border: 0;
            border-radius: .55rem;
            color: #fff !important;
            font-size: .85rem;
            line-height: 1;
            text-decoration: none;
            box-shadow: none;
            transition: transform .15s ease, background-color .15s ease;
        }

        .employee-action-form { display: inline-block; margin: 0; }
        .employee-action-view { background: #0f9fb3 !important; }
        .employee-action-edit { background: #334155 !important; border: 1px solid #475569; }
        .employee-action-delete { background: #ef3340 !important; }
        .employee-action-btn:hover,
        .employee-action-btn:focus { transform: translateY(-1px); }
        .employee-action-view:hover,
        .employee-action-view:focus { background: #0b8294 !important; }
        .employee-action-edit:hover,
        .employee-action-edit:focus { background: #1e293b !important; }
        .employee-action-delete:hover,
        .employee-action-delete:focus { background: #dc2636 !important; }
    </style>
@endsection
