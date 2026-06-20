@extends('layouts.master')
@section('title', 'Events')
@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Events</h3>
            <div>
                <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">Add Event</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th width="220">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>
                                <strong>{{ $event->title }}</strong>
                                @if($event->description)
                                    <div class="text-muted small">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 120) }}</div>
                                @endif
                            </td>
                            <td>{{ optional($event->event_date)->format('d M Y H:i') ?? '-' }}</td>
                            <td>{{ $event->location ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $event->is_published ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $event->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('website.event.show', $event) }}" target="_blank" class="btn btn-success btn-sm">View</a>
                                <a href="{{ route('events.edit', $event) }}" class="btn btn-dark btn-sm">Edit</a>
                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this event?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No events added yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $events->links() }}</div>
    </div>
</div>
@endsection
