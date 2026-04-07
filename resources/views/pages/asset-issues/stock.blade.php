@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-3"><h3 class="card-title mb-0 text-white text-lg">Current Asset Stock</h3></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0" style="font-size:13px">
                <thead style="background:#f8fafc">
                    <tr>
                        <th>#</th><th>Asset</th><th>Category</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-center">Issued</th>
                        <th class="text-center">Available</th>
                        <th>Purchase Price</th><th>Current Value</th><th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $a)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $a->name }}</td>
                        <td>{{ $a->category?->name ?? '—' }}</td>
                        <td class="text-center">{{ $a->quantity }}</td>
                        <td class="text-center" style="color:#e11d48">{{ $a->issued_quantity }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $a->available_stock > 0 ? 'success' : 'danger' }}">
                                {{ $a->available_stock }}
                            </span>
                        </td>
                        <td>{{ number_format($a->purchase_price, 2) }}</td>
                        <td>{{ number_format($a->current_value, 2) }}</td>
                        <td><span class="badge badge-{{ $a->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($a->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No assets found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
