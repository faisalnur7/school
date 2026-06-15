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
                <div class="col-md-3"><strong>Paid:</strong> {{ number_format((float)($purchase->paid_amount ?? 0), 2) }}</div>
                <div class="col-md-3"><strong>Due:</strong> {{ number_format((float)($purchase->due_amount ?? 0), 2) }}</div>
                <div class="col-md-3"><strong>Status:</strong> {{ ucfirst($purchase->status ?? 'unpaid') }}</div>
                <div class="col-md-3"><strong>Last Paid:</strong> {{ optional($purchase->last_paid_at)->format('Y-m-d') ?? '—' }}</div>
            </div>
            @if($purchase->notes)
                <div class="alert alert-light border">{{ $purchase->notes }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-header"><strong>Record Payment</strong></div>
                <div class="card-body">
                    @if($purchase->balance > 0)
                    <form method="POST" action="{{ route('inventory.purchases.payments.store', $purchase->id) }}" class="row g-2">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">Amount</label>
                            <input type="number" min="0.01" step="0.01" name="amount" value="{{ old('amount', $purchase->balance) }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Reference No</label>
                            <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button class="btn btn-primary"><i class="fas fa-save"></i> Save Payment</button>
                        </div>
                    </form>
                    @else
                        <div class="alert alert-success mb-0">This invoice is fully paid.</div>
                    @endif
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Method</th>
                            <th class="text-right">Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->payments as $i => $payment)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $payment->payment_date?->format('Y-m-d') }}</td>
                                <td>{{ $payment->reference_no }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td class="text-right">{{ number_format((float)$payment->amount, 2) }}</td>
                                <td>{{ $payment->notes }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

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
