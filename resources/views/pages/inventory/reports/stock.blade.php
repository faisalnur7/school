@extends('layouts.master')

@section('contents')
<div class="col-12">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title font-bold text-white">Stock Report</h3>
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
                        <a href="{{ route('inventory.reports.stock') }}" class="btn btn-light">Reset</a>
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
                             <tr><td colspan="7" class="text-center text-muted">No data.</td></tr>
                         @endforelse
                     </tbody>
                </table>
            </div>

            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection

