@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Accounting Periods</h3>
            <button class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#createModal">+ New Period</button>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Closed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $period->name }}</td>
                            <td>{{ $period->start_date->format('d M Y') }}</td>
                            <td>{{ $period->end_date->format('d M Y') }}</td>
                            <td>
                                @if($period->is_closed)
                                    <span class="badge badge-danger">Closed</span>
                                @else
                                    <span class="badge badge-success">Open</span>
                                @endif
                            </td>
                            <td>{{ $period->closedBy?->name ?? '—' }}</td>
                            <td style="display:flex;gap:5px; justify-content: center; align-items: center;">
                                @if(!$period->is_closed)
                                    <form action="{{ route('accounting-periods.close', $period) }}" method="POST"
                                          onsubmit="return confirm('Close period {{ $period->name }}? This cannot be undone.')">
                                        @csrf
                                        <button class="btn btn-sm btn-warning">Lock Period</button>
                                    </form>
                                    <form action="{{ route('accounting-periods.destroy', $period) }}" method="POST"
                                          onsubmit="return confirm('Delete this period?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Del</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No periods defined yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('accounting-periods.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Accounting Period</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name (e.g. FY-2025)</label>
                        <input type="text" name="name" class="form-control" required placeholder="FY-2025">
                    </div>
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
