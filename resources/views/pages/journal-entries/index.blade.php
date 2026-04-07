@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Journal Entries</h3>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm" placeholder="From">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm" placeholder="To">
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search ref / description...">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary btn-sm w-100">Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Ref No</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th class="text-right">Debit</th>
                            <th class="text-right">Credit</th>
                            <th>By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            @php $entry->load('lines'); @endphp
                            <tr>
                                <td><a href="{{ route('journal-entries.show', $entry) }}">{{ $entry->reference_no }}</a></td>
                                <td>{{ $entry->date->format('d M Y') }}</td>
                                <td>{{ $entry->description }}</td>
                                <td class="text-right">{{ number_format($entry->total_debit, 2) }}</td>
                                <td class="text-right">{{ number_format($entry->total_credit, 2) }}</td>
                                <td>{{ $entry->createdBy?->name ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-xs btn-info">View</a>
                                    <form action="{{ route('journal-entries.destroy', $entry) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this journal entry?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger">Del</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No journal entries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $entries->links() }}
        </div>
    </div>
</div>
@endsection
