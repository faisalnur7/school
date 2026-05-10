@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title font-bold text-white">Low Stock Products</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.hub') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-th-large"></i> Inventory Hub
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search product...">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-secondary">Search</button>
                        <a href="{{ route('inventory.reports.lowStock') }}" class="btn btn-light">Reset</a>
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
                            <th>SKU</th>
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
                                <td>{{ $item->sku }}</td>
                                <td class="text-right"><span class="badge badge-danger">{{ $item->current_stock }}</span></td>
                                <td class="text-right">{{ $item->minimum_stock_alert }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No low stock items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

