@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">Permission Categories</h3>
            <a href="{{ route('permission-categories.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                + Add Category
            </a>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($categories->isEmpty())
                <div class="text-center text-muted py-4">No categories found</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Permissions</th>
                                <th>Sort Order</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->permissions_count }}</span>
                                    </td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('permission-categories.edit', $category->id) }}" class="btn btn-sm btn-dark">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('permission-categories.destroy', $category->id) }}" method="POST" class="btn btn-sm btn-danger d-inline m-0" onsubmit="return confirm('Delete this category?')">
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
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
