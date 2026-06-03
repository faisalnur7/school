@extends('layouts.master')
@section('title', 'Gallery')
@section('contents')
<div class="col-12">
    <div class="card card-outline card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title font-bold">Gallery Items</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('website.cms.hub') }}" class="btn btn-sm btn-light border">Website Hub</a>
                <a href="{{ route('website.gallery.create') }}" class="btn btn-sm btn-primary">Add Item</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" style="width:90px;height:60px;object-fit:cover;" class="rounded"></td>
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    @if($item->caption)
                                        <div class="text-muted small">{{ Str::limit($item->caption, 80) }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td><span class="badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $item->is_active ? 'Active' : 'Hidden' }}</span></td>
                                <td class="text-right">
                                    <a href="{{ route('website.gallery.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('website.gallery.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this gallery item?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No gallery items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">{{ $items->links() }}</div>
        @endif
    </div>
</div>
@endsection
