@extends('layouts.master')
@section('title', 'Website Sections')
@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Sections for: {{ $page->title }}</h3>
            <div>
                <a href="{{ route('website.pages.index') }}" class="btn btn-secondary btn-sm">Back to Pages</a>
                <a href="{{ route('website.pages.sections.create', $page) }}" class="btn btn-primary btn-sm">Add Section</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Title</th><th>Key</th><th>Sort</th><th width="160">Action</th></tr></thead>
                <tbody>
                @forelse($sections as $section)
                    <tr>
                        <td>{{ $section->title }}</td>
                        <td><code>{{ $section->section_key }}</code></td>
                        <td>{{ $section->sort_order }}</td>
                        <td>
                            <a href="{{ route('website.pages.sections.edit', [$page, $section]) }}" class="btn btn-dark btn-sm">Edit</a>
                            <form action="{{ route('website.pages.sections.destroy', [$page, $section]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this section?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No sections yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $sections->links() }}</div>
    </div>
</div>
@endsection
