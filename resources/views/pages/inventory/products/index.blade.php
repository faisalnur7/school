@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title font-bold text-white">Products</h3>
            <div class="card-tools ml-auto">
                <a href="{{ route('inventory.products.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name / sku / barcode...">
                    </div>
                    <div class="col-md-4">
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-secondary">Filter</button>
                        <a href="{{ route('inventory.products.index') }}" class="btn btn-light">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Unit</th>
                            <th>Purchase Price</th>
                            <th>Stock</th>
                            <th>Min Alert</th>
                            <th>Status</th>
                            <th width="160">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->category?->name }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format((float)$item->purchase_price, 2) }}</td>
                                <td>
                                    @if($item->minimum_stock_alert > 0 && $item->current_stock < $item->minimum_stock_alert)
                                        <span class="badge badge-danger">{{ $item->current_stock }}</span>
                                    @else
                                        <span class="badge badge-info">{{ $item->current_stock }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->minimum_stock_alert }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('inventory.products.edit', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventory.products.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

