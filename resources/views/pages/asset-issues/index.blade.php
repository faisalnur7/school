@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Asset Issue Register</h3>
            <a href="{{ route('asset-issues.create') }}" class="btn btn-primary btn-sm ml-auto">+ Issue Asset</a>
        </div>
        <div class="card-body px-0 pb-0 pt-0">
            @if(session('success'))<div class="alert alert-success m-3">{{ session('success') }}</div>@endif

            {{-- Filters --}}
            <form method="GET" action="{{ route('asset-issues.index') }}" class="px-3 pt-3 pb-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label style="font-size:12px">Asset</label>
                        <select name="asset_id" class="form-control form-control-sm">
                            <option value="">All Assets</option>
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}" {{ request('asset_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="font-size:12px">Status</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                            <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-sm btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('asset-issues.index') }}" class="btn btn-sm btn-secondary" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead><tr>
                        <th>#</th><th>Asset</th><th>Issued To</th><th>Qty</th>
                        <th>Issue Date</th><th>Return Date</th><th>Status</th><th width="130">Actions</th>
                    </tr></thead>
                    <tbody>
                        @forelse($issues as $issue)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-bold">{{ $issue->asset->name ?? '—' }}</td>
                            <td>{{ $issue->issued_to }}<br><small class="text-muted">{{ $issue->issued_to_type }}</small></td>
                            <td>{{ $issue->quantity }}</td>
                            <td>{{ $issue->issue_date->format('d/m/Y') }}</td>
                            <td>{{ $issue->return_date?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                @php $sc = match($issue->status) { 'issued'=>'warning','returned'=>'success','lost'=>'danger',default=>'secondary' }; @endphp
                                <span class="badge badge-{{ $sc }}">{{ ucfirst($issue->status) }}</span>
                            </td>
                            <td style="display:flex;gap:4px;flex-wrap:wrap">
                                @if($issue->status === 'issued')
                                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#returnModal{{ $issue->id }}">Return</button>
                                @endif
                                <form action="{{ route('asset-issues.destroy', $issue->id) }}"  class="m-0" method="POST" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        {{-- Return Modal --}}
                        @if($issue->status === 'issued')
                        <div class="modal fade" id="returnModal{{ $issue->id }}">
                            <div class="modal-dialog"><div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Record Return — {{ $issue->asset->name }}</h5></div>
                                <form method="POST" action="{{ route('asset-issues.return', $issue->id) }}">
                                    @csrf @method('PATCH')
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Return Date</label>
                                            <input type="text" name="return_date" datepicker datepicker-format="dd/mm/yyyy"
                                                   class="form-control" value="{{ now()->format('d/m/Y') }}" placeholder="dd/mm/yyyy" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success">Confirm Return</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div></div>
                        </div>
                        @endif
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No records found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 pt-3">{{ $issues->links() }}</div>
        </div>
    </div>
</div>
@endsection
