@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title font-bold text-white">Purchase #{{ $purchase->id }}</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.purchases.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Back to List
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <div class="row mb-3">
                <div class="col-md-3"><strong>Date:</strong> {{ $purchase->purchase_date?->format('Y-m-d') }}</div>
                <div class="col-md-3"><strong>Supplier:</strong> {{ $purchase->supplier?->name }}</div>
                <div class="col-md-3"><strong>Reference:</strong> {{ $purchase->reference_no }}</div>
                <div class="col-md-3"><strong>Total:</strong> {{ number_format((float)$purchase->total_amount, 2) }}</div>
            </div>
            @if($purchase->notes)
                <div class="alert alert-light border">{{ $purchase->notes }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->inventoryItem?->name }}</td>
                                <td>{{ $row->inventoryItem?->category?->name }}</td>
                                <td class="text-right">{{ $row->quantity }}</td>
                                <td class="text-right">{{ number_format((float)$row->unit_price, 2) }}</td>
                                <td class="text-right">{{ number_format((float)$row->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-right">Grand Total</th>
                            <th class="text-right">{{ number_format((float)$purchase->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

