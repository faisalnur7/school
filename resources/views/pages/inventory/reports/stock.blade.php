@extends('layouts.master')

@section('styles')
    <style>
        .inventory-filter-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.875rem;
            padding: 1rem;
        }

        .inventory-filter-panel .btn {
            min-width: 96px;
        }

        html[data-theme='dark'] .inventory-filter-panel {
            background: #0f172a;
            border-color: rgba(148, 163, 184, 0.22);
        }
    </style>
@endsection

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header bg-white flex justify-between items-center">
            <h3 class="card-title font-bold text-slate-900">Stock Report</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.hub') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-th-large"></i> Inventory Hub
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-primary border-0 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <strong>Total Inventory Value:</strong>
                    <span class="ml-1">৳{{ number_format((float) $totalInventoryValue, 2) }}</span>
                </div>
                <div class="text-muted small">Based on current stock and average cost</div>
            </div>

            <form method="GET" class="mb-3 inventory-filter-panel">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search product...">
                    </div>
                    <div class="col-md-6 d-flex flex-wrap gap-2">
                        <button class="btn btn-dark btn-sm" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
                        <a href="{{ route('inventory.reports.stock') }}" class="btn btn-light btn-sm" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                            <span>Reset</span>
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
<thead>
                         <tr>
                             <th>#</th>
                             <th>Category</th>
                             <th>Product</th>
                             <th class="text-right">Stock</th>
                             <th class="text-right">Avg Cost</th>
                             <th class="text-right">Stock Value</th>
                             <th class="text-right">Min Alert</th>
                             <th class="text-right">Purchase Price</th>
                             <th>Low Stock</th>
                         </tr>
                     </thead>
                     <tbody>
                         @forelse($items as $item)
                             @php
                                 $isLow = $item->minimum_stock_alert > 0 && $item->current_stock < $item->minimum_stock_alert;
                             @endphp
                             <tr>
                                 <td>{{ $item->id }}</td>
                                 <td>{{ $item->category?->name }}</td>
                                 <td>{{ $item->name }}</td>
                                 <td class="text-right">{{ $item->current_stock }}</td>
                                 <td class="text-right">{{ number_format((float) ($item->average_cost ?? $item->purchase_price), 2) }}</td>
                                 <td class="text-right">{{ number_format((float) $item->stockValue(), 2) }}</td>
                                 <td class="text-right">{{ $item->minimum_stock_alert }}</td>
                                 <td class="text-right">{{ number_format((float)$item->purchase_price, 2) }}</td>
                                 <td>
                                     @if($isLow)
                                         <span class="badge badge-danger">Yes</span>
                                     @else
                                         <span class="badge badge-success">No</span>
                                     @endif
                                 </td>
                             </tr>
                             @empty
                             <tr><td colspan="9" class="text-center text-muted">No data.</td></tr>
                         @endforelse
                     </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
