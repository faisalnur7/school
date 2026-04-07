@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title mb-0 text-white text-lg">Add Asset Category</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                    @endif

                    <form action="{{ route('asset-categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-success">Save Category</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title mb-0 text-white text-lg">Asset Categories</h3>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Description</th>
                                <th class="px-4 py-3">Assets</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">{{ $category->name }}</td>
                                    <td class="px-4 py-3">{{ $category->description ?? '—' }}</td>
                                    <td class="px-4 py-3"><span class="badge bg-primary">{{ $category->assets_count }}</span></td>
                                    <td class="px-4 py-3 text-center">
                                        <button class="btn btn-sm btn-warning me-1" onclick="openEdit({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('asset-categories.destroy', $category) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No categories yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Asset Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="editName" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openEdit(id, name, description) {
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editForm').action = '/asset-categories/' + id;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>
@endsection
