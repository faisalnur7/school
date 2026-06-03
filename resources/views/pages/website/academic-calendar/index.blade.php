@extends('layouts.master')
@section('title', 'Academic Calendar')
@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Academic Calendar</h3>
            <a href="{{ route('website.academic-calendar.create') }}" class="btn btn-primary btn-sm">Add Item</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Title</th><th>Date Range</th><th>Status</th><th>Sort</th><th width="160">Action</th></tr></thead>
                <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->start_date?->format('d M Y') }} - {{ $item->end_date?->format('d M Y') ?? '-' }}</td>
                        <td><span class="badge {{ $item->is_published ? 'badge-success' : 'badge-secondary' }}">{{ $item->is_published ? 'Published' : 'Draft' }}</span></td>
                        <td>{{ $item->sort_order }}</td>
                        <td>
                            <a href="{{ route('website.academic-calendar.edit', $item) }}" class="btn btn-dark btn-sm">Edit</a>
                            <form action="{{ route('website.academic-calendar.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No calendar items yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $items->links() }}</div>
    </div>
</div>
@endsection
