@extends('layouts.master')
@section('title', 'Notices')
@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Notices</h3>
            <a href="{{ route('notice.create') }}" class="btn btn-primary btn-sm">Add Notice</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notices as $notice)
                        <tr>
                            <td>
                                <strong>{{ $notice->title }}</strong>
                                @if($notice->content)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($notice->content), 120) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $notice->is_published ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $notice->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>{{ optional($notice->published_at)->format('d M Y H:i') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('notice.edit', $notice) }}" class="btn btn-dark btn-sm">Edit</a>
                                <form action="{{ route('notice.destroy', $notice) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this notice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No notices added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $notices->links() }}</div>
    </div>
</div>
@endsection
