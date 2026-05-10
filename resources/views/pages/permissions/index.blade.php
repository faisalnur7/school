@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Permissions</h3>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Permission
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($permissions->isEmpty())
                <div class="text-center text-muted py-4">No permissions found</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><code>{{ $permission->name }}</code></td>
                                    <td>{{ $permission->display_name }}</td>
                                    <td>
                                        @if ($permission->category)
                                            <span class="badge badge-info">{{ $permission->category->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($permission->description, 40) }}</td>
                                    <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-sm btn-dark">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" class="btn btn-sm btn-danger d-inline m-0" onsubmit="return confirm('Delete this permission?')">
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
                    {{ $permissions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
