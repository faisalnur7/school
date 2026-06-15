@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title font-bold text-white">Purchases</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.purchases.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Purchase
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by supplier or reference...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary">Search</button>
                        <a href="{{ route('inventory.purchases.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>Notes</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->purchase_date?->format('Y-m-d') }}</td>
                                <td>{{ $purchase->supplier?->name }}</td>
                                <td>{{ $purchase->reference_no }}</td>
                                <td>{{ $purchase->notes }}</td>
                                <td>{{ number_format((float)$purchase->total_amount, 2) }}</td>
                                <td>{{ number_format((float)($purchase->paid_amount ?? 0), 2) }}</td>
                                <td>{{ number_format((float)($purchase->due_amount ?? 0), 2) }}</td>
                                <td><span class="badge badge-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($purchase->status ?? 'unpaid') }}</span></td>
                                <td>
                                    <a href="{{ route('inventory.purchases.voucher', $purchase->id) }}" class="btn btn-sm btn-secondary" title="Print Voucher" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('inventory.purchases.show', $purchase->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No purchases found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
