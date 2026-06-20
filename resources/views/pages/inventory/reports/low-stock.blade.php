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
            <h3 class="card-title font-bold text-slate-900">Low Stock Products</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.hub') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-th-large"></i> Inventory Hub
                </a>
            </div>
        </div>
        <div class="card-body">
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
                        <a href="{{ route('inventory.reports.lowStock') }}" class="btn btn-light btn-sm" title="Reset" aria-label="Reset">
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
                             <th class="text-right">Min Alert</th>
                         </tr>
                     </thead>
                     <tbody>
                         @forelse($items as $item)
                             <tr>
                                 <td>{{ $item->id }}</td>
                                 <td>{{ $item->category?->name }}</td>
                                 <td>{{ $item->name }}</td>
                                 <td class="text-right"><span class="badge badge-danger">{{ $item->current_stock }}</span></td>
                                 <td class="text-right">{{ $item->minimum_stock_alert }}</td>
                             </tr>
                         @empty
                             <tr><td colspan="5" class="text-center text-muted">No low stock items.</td></tr>
                         @endforelse
                     </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
