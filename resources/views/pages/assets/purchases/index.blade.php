@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="font-bold text-lg">🛒 Asset Purchases</h4>
        <a href="{{ route('asset-purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc">
                    <tr>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Items</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Payment</th>
                        <th class="px-4 py-3">Recorded By</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                    <tr>
                        <td class="px-4 py-3"><code>{{ $purchase->reference_no }}</code></td>
                        <td class="px-4 py-3">{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $purchase->items->count() }}</td>
                        <td class="px-4 py-3 fw-bold" style="color:#4338ca">৳{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $purchase->payment_type)) }}</td>
                        <td class="px-4 py-3">{{ $purchase->recorder->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('asset-purchases.show', $purchase) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No purchases yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="card-footer bg-white">{{ $purchases->links() }}</div>
        @endif
    </div>
</div>
@endsection
