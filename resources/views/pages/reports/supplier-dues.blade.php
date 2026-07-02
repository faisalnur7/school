@extends('layouts.master')

@section('styles')
    <style>
        .supplier-dues-filters {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
        }

        .supplier-dues-filters .btn {
            min-width: 108px;
        }

        html[data-theme='dark'] .supplier-dues-filters {
            background: #0f172a;
            border-color: rgba(148, 163, 184, 0.22);
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')

    <div class="report-toolbar">
        <form method="GET" class="supplier-dues-filters">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="supplier_id" class="form-control">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        @foreach(['unpaid','partial','paid'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="from" value="{{ request('from') }}" class="form-control" placeholder="From dd/mm/yyyy">
                </div>
                <div class="col-md-2">
                    <input type="text" name="to" value="{{ request('to') }}" class="form-control" placeholder="To dd/mm/yyyy">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-dark" title="Filter" aria-label="Filter">
                        <i class="fas fa-search"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('reports.supplier-dues') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                        <i class="fas fa-undo-alt"></i>
                    </a>
                    <a href="{{ route('reports.supplier-dues.pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Supplier Due Report</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><div class="alert alert-primary mb-0">Total Amount: <strong>{{ number_format($totals['amount'], 2) }}</strong></div></div>
                <div class="col-md-4"><div class="alert alert-success mb-0">Paid: <strong>{{ number_format($totals['paid'], 2) }}</strong></div></div>
                <div class="col-md-4"><div class="alert alert-danger mb-0">Due: <strong>{{ number_format($totals['due'], 2) }}</strong></div></div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $i => $purchase)
                            <tr>
                                <td>{{ $purchases->firstItem() + $i }}</td>
                                <td>{{ $purchase->purchase_date?->format('Y-m-d') }}</td>
                                <td>{{ $purchase->supplier?->name }}</td>
                                <td>
                                    <a href="{{ route('inventory.purchases.show', $purchase->id) }}">{{ $purchase->reference_no }}</a>
                                </td>
                                <td class="text-right">{{ number_format((float)$purchase->total_amount, 2) }}</td>
                                <td class="text-right">{{ number_format((float)($purchase->paid_amount ?? 0), 2) }}</td>
                                <td class="text-right">{{ number_format((float)($purchase->due_amount ?? 0), 2) }}</td>
                                <td><span class="badge badge-{{ $purchase->status === 'paid' ? 'success' : ($purchase->status === 'partial' ? 'warning' : 'danger') }}">{{ ucfirst($purchase->status ?? 'unpaid') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No supplier dues found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
