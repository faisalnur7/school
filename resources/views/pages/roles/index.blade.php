@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Roles</h3>
            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Role
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($roles->isEmpty())
                <div class="text-center text-muted py-4">No roles found</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $role->name }}</strong></td>
                                    <td>{{ Str::limit($role->description, 50) }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $role->permissions_count }}</span>
                                    </td>
                                    <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-dark">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if ($role->name !== 'Super Admin')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="btn btn-sm btn-danger d-inline m-0" onsubmit="return confirm('Delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <i class="fas fa-trash"></i>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
