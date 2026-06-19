@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <div>
                <h3 class="card-title mb-0 text-white text-lg">Audit Trail</h3>
                <div class="text-white-50 small">Action history with user, date, and time</div>
            </div>
            <span class="badge badge-light text-dark px-3 py-2">{{ $auditTrails->total() }} records</span>
        </div>

        <div class="card-body px-0 pb-4 pt-0">
            @if ($auditTrails->isEmpty())
                <div class="text-center text-muted py-5">No audit trail entries found.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Action Name</th>
                                <th>Important Description</th>
                                <th>Username</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Method</th>
                                <th>Route</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($auditTrails as $trail)
                                <tr>
                                    <td>{{ $loop->iteration + (($auditTrails->currentPage() - 1) * $auditTrails->perPage()) }}</td>
                                    <td>
                                        <span class="badge badge-primary px-3 py-2">{{ $trail->action_name }}</span>
                                    </td>
                                    <td class="text-wrap" style="min-width: 260px;">{{ $trail->important_description }}</td>
                                    <td>
                                        <strong>{{ $trail->username }}</strong>
                                    </td>
                                    <td>{{ optional($trail->action_date)->format('d M Y') ?? $trail->action_date }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($trail->action_time)->format('h:i A') }}</td>
                                    <td>
                                        @if($trail->http_method)
                                            <span class="badge badge-info">{{ $trail->http_method }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $trail->route_name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $auditTrails->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
