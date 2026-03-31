@extends('layouts.master')

@section('contents')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">🛒 Purchase Details — <code>{{ $assetPurchase->reference_no }}</code></h4>
        <a href="{{ route('asset-purchases.index') }}" class="btn btn-secondary">← Back</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Items Purchased</div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="px-3 py-2">Asset</th>
                                <th class="px-3 py-2">Category</th>
                                <th class="px-3 py-2">Qty</th>
                                <th class="px-3 py-2">Unit Price</th>
                                <th class="px-3 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetPurchase->items as $item)
                            <tr>
                                <td class="px-3 py-2">{{ $item->asset->name }}</td>
                                <td class="px-3 py-2">{{ $item->asset->category->name ?? '—' }}</td>
                                <td class="px-3 py-2">{{ $item->quantity }}</td>
                                <td class="px-3 py-2">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-3 py-2 fw-bold">৳{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold px-3 py-2">Grand Total</td>
                                <td class="fw-bold px-3 py-2" style="color:#4338ca">৳{{ number_format($assetPurchase->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">Purchase Info</div>
                <div class="card-body">
                    <p><strong>Date:</strong> {{ $assetPurchase->purchase_date->format('d M Y') }}</p>
                    <p><strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $assetPurchase->payment_type)) }}</p>
                    <p><strong>Recorded By:</strong> {{ $assetPurchase->recorder->name ?? '—' }}</p>
                    @if($assetPurchase->notes)
                        <p><strong>Notes:</strong> {{ $assetPurchase->notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
