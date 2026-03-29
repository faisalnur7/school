@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">🚌 Transport Fee Management</h4>
            <a href="{{ route('transports.create') }}" class="btn btn-primary">
                + Assign Transport Fee
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold">Filter Students</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('transports.index') }}">
                    @include('transports.filter', ['isIndex' => true])
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Student Name</th>
                                <th class="px-4 py-3">Fee Category</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Session</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transports as $transport)
                                <tr>
                                    <td class="px-4 py-3">
                                        <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px">
                                            {{ $transport->student->student_cid }}
                                        </code>
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->student->full_name_en }}</td>
                                    <td class="px-4 py-3">{{ $transport->feeCategory->name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="fw-bold" style="color:#4338ca">
                                            ৳{{ number_format($transport->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $transport->academicSession->name_en }}</td>
                                    <td class="px-4 py-3">
                                        @if($transport->status === 'active')
                                            <span class="badge rounded-pill" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('transports.edit', $transport->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                                        <form action="{{ route('transports.destroy', $transport->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Remove this transport fee?')">
                                                Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div style="font-size:48px;opacity:.2">🚌</div>
                                        <p class="mt-3">No transport fees assigned yet</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transports->hasPages())
                <div class="card-footer bg-white">
                    {{ $transports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
