@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Users</h3>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add User
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($users->isEmpty())
                <div class="text-center text-muted py-4">No users found</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Super Admin</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role)
                                            <span class="badge badge-info">{{ $user->role->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">No Role</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->is_super_admin)
                                            <span class="badge badge-danger">Yes</span>
                                        @else
                                            <span class="badge badge-light">No</span>
                                        @endif
                                    </td>
                                    <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-dark">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('users.delete', $user->id) }}" method="POST" class="btn btn-sm btn-danger d-inline m-0" onsubmit="return confirm('Delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <i class="fas fa-trash"></i>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
