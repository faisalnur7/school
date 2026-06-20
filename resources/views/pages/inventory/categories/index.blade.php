@extends('layouts.master')

@section('styles')
    <style>
        .inventory-filter-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1rem;
        }

        .inventory-filter-panel .btn {
            min-width: 96px;
        }

        html[data-theme='dark'] .inventory-filter-panel {
            background: #0f172a;
            border-color: rgba(148, 163, 184, 0.22);
        }
    </style>
@endsection

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header bg-white flex justify-between items-center">
            <h3 class="card-title font-bold text-slate-900">Inventory Categories</h3>
            <div class="card-tools ml-auto">
                @if(auth()->user()?->hasPermission('manage_inventory_categories'))
                    <a href="{{ route('inventory.categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Category
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <form method="GET" class="mb-3 inventory-filter-panel">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search category...">
                    </div>
                    <div class="col-md-3 d-flex flex-wrap gap-2">
                        <button class="btn btn-dark btn-sm" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="{{ route('inventory.categories.index') }}" class="btn btn-light btn-sm" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th width="160">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inventory.categories.edit', $category->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventory.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No categories found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection
