@extends('layouts.master')
@section('title', 'Website Pages')
@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Website Pages</h3>
            <div>
                <a href="{{ route('website.academic-calendar.index') }}" class="btn btn-info btn-sm">Academic Calendar</a>
                <a href="{{ route('website.pages.create') }}" class="btn btn-primary btn-sm">Create Page</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th><th>Slug</th><th>Status</th><th>Published</th><th width="320">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    @php
                        $liveUrl = $page->status === 'published'
                            ? \App\Models\WebsitePage::publicUrlFor($page)
                            : '#';
                    @endphp
                    <tr>
                        <td>{{ $page->title }}</td>
                        <td><code>{{ $page->slug }}</code></td>
                        <td><span class="badge {{ $page->status === 'published' ? 'badge-success' : 'badge-secondary' }}">{{ $page->status }}</span></td>
                        <td>{{ optional($page->published_at)->format('d M Y H:i') ?? '-' }}</td>
                        <td>
                            <a href="{{ $liveUrl }}" target="_blank" class="btn btn-success btn-sm {{ $liveUrl === '#' ? 'disabled' : '' }}">View Live</a>
                            <a href="{{ route('website.pages.sections.index', $page) }}" class="btn btn-info btn-sm">Sections</a>
                            <a href="{{ route('website.pages.edit', $page) }}" class="btn btn-dark btn-sm">Edit</a>
                            <form action="{{ route('website.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted">No pages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $pages->links() }}</div>
    </div>
</div>
@endsection
