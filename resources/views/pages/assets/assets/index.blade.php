@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
                    <h3 class="card-title mb-0">Add Asset</h3>
                </div>

                <div class="card-body">
                    <form action="{{ route('assets.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="asset_category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('asset_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-control" required>
                                @foreach(['active', 'inactive', 'disposed'] as $s)
                                    <option value="{{ $s }}" {{ old('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Save Asset</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="font-bold text-lg">📦 Assets</h4>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Quantity</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $asset->name }}</td>
                        <td class="px-4 py-3">{{ $asset->category->name ?? '—' }}</td>
                        <td class="px-4 py-3"><span class="badge bg-info">{{ $asset->quantity }}</span></td>
                        <td class="px-4 py-3">
                            @if($asset->status === 'active')
                                <span class="badge rounded-pill" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0">Active</span>
                            @elseif($asset->status === 'inactive')
                                <span class="badge rounded-pill" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca">Inactive</span>
                            @else
                                <span class="badge rounded-pill" style="background:#f3f4f6;color:#6b7280;border:1px solid #d1d5db">Disposed</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this asset?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">No assets yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>
@endsection
