@extends('layouts.master')

@section('styles')
    <style>
        .inventory-sales-panel--sticky {
            position: sticky;
            top: 0.75rem;
            z-index: 40;
            backdrop-filter: blur(12px);
        }

        #inventorySalesPanel {
            overflow: hidden;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.96) 100%) !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
        }

        .inventory-sales-panel--sticky .card-body {
            background: transparent !important;
        }

        .inventory-sales-filter .form-control,
        .inventory-sales-filter .form-select {
            min-height: 2.9rem;
        }

        .inventory-sales-receipt-col {
            width: 11rem;
            white-space: nowrap;
        }

        .inventory-sales-receipt,
        .inventory-sales-receipt-method {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: normal;
        }

        .inventory-sales-receipt-method {
            max-width: 100%;
        }

        .inventory-sales-list .btn-outline-primary {
            min-width: 5.75rem;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        html[data-theme='dark'] .inventory-sales-panel--sticky .card-body {
            background: transparent !important;
        }

        html[data-theme='dark'] #inventorySalesPanel {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%) !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26) !important;
        }

        html[data-theme='dark'] .inventory-sales-receipt-col,
        html[data-theme='dark'] .inventory-sales-receipt,
        html[data-theme='dark'] .inventory-sales-receipt-method {
            color: #e2e8f0;
        }
    </style>
@endsection

@section('contents')
<div class="container-fluid py-4 inventory-sales-hub">
    <div class="inventory-sales-hero shadow-sm overflow-hidden mb-4">
        <div class="inventory-sales-hero__inner position-relative p-4 p-md-5">
            <div class="inventory-sales-hero__glow"></div>
            <div class="d-flex flex-wrap align-items-center gap-3 position-relative">
                <div class="inventory-sales-hero__icon">
                    <i class="fas fa-cash-register inventory-sales-hero__icon-mark" aria-hidden="true"></i>
                </div>
                <div class="inventory-sales-hero__copy text-white">
                    <h3 class="mb-1 fw-bold inventory-sales-hero__title">Inventory Sales Hub</h3>
                    <p class="mb-0 inventory-sales-hero__subtitle">Browse sales and filter by the purchase items behind the stock source.</p>
                </div>
                <div class="ml-auto d-flex flex-wrap gap-2 inventory-sales-hero__actions">
                    <a href="{{ route('inventory.hub') }}" class="btn btn-light btn-sm fw-semibold rounded-3">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Hub
                    </a>
                    <a href="{{ route('inventory.purchases.index') }}" class="btn btn-outline-light btn-sm fw-semibold rounded-3">
                        Purchases
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div
        id="inventorySalesPanel"
        class="card border-0 shadow-sm rounded-4 mb-4 inventory-sales-panel inventory-sales-panel--sticky"
    >
        <div
            class="card-body p-4 inventory-sales-panel__body"
            style="background: transparent !important;"
        >
            <form method="GET" action="{{ route('inventory.sales.hub') }}" class="row g-3 align-items-end inventory-sales-filter">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-semibold text-muted small">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-3" placeholder="Receipt, student, item...">
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-semibold text-muted small">Purchase Source</label>
                    <select name="purchase_id" class="form-select rounded-3">
                        <option value="">All purchases</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" @selected((string) request('purchase_id') === (string) $purchase->id)>
                                {{ $purchase->reference_no }} - {{ $purchase->supplier?->name ?? 'Supplier' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-semibold text-muted small">Item</label>
                    <select name="inventory_item_id" class="form-select rounded-3">
                        <option value="">All items</option>
                        @foreach($allItems as $item)
                            <option value="{{ $item->id }}" @selected((string) request('inventory_item_id') === (string) $item->id)>
                                {{ $item->name }}{{ $item->category?->name ? ' - ' . $item->category->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label fw-semibold text-muted small">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control rounded-3">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label class="form-label fw-semibold text-muted small">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control rounded-3">
                </div>
                <div class="col-md-12 col-lg-1 d-grid">
                    <button class="btn btn-dark rounded-3 fw-semibold" title="Filter" aria-label="Filter">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            @if(request()->filled('purchase_id'))
                <div class="alert alert-info mt-3 mb-0 rounded-3">
                    Filter is applied by inventory items included in the selected purchase. It is not batch-level stock tracking.
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4 inventory-sales-stats">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-semibold">Sales Count</div>
                    <div class="fs-2 fw-bold text-teal-700">{{ $sales->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-semibold">Selected Purchase Items</div>
                    <div class="fs-2 fw-bold text-amber-600">{{ $purchaseFilteredItems->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="text-muted small text-uppercase fw-semibold">Total Sales Value</div>
                    <div class="fs-2 fw-bold text-indigo-600">BDT {{ number_format($sales->getCollection()->sum('total_amount'), 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($selectedPurchase)
        <div class="card border-0 shadow-sm rounded-4 mb-4 inventory-sales-summary">
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Purchase Filter</div>
                        <h5 class="mb-0">{{ $selectedPurchase->reference_no }}</h5>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold">{{ $selectedPurchase->supplier?->name ?? 'Supplier' }}</div>
                        <div class="text-muted small">{{ optional($selectedPurchase->purchase_date)->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pt-0 pb-4">
                <div class="d-flex flex-wrap gap-2">
                    @foreach($purchaseFilteredItems as $item)
                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                            {{ $item->name }}{{ $item->category?->name ? ' • ' . $item->category->name : '' }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 inventory-sales-list">
        <div class="card-header bg-white border-0 py-3 px-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Sales List</div>
                    <h5 class="mb-0">Inventory Sales</h5>
                </div>
                <div class="text-muted small">{{ $sales->total() }} records</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 inventory-sales-receipt-col">Receipt</th>
                        <th>Student / Buyer</th>
                        <th>Items</th>
                        <th class="text-end">Gross</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                        <th>Date</th>
                        <th class="text-end px-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        @php
                            $items = $sale->items ?? collect();
                            $itemLabels = $items->map(function ($item) {
                                return ($item->inventoryItem?->name ?? 'Item') . ($item->inventoryItem?->category?->name ? ' • ' . $item->inventoryItem->category->name : '');
                            })->unique()->values();
                        @endphp
                        <tr>
                            <td class="px-4 inventory-sales-receipt-col">
                                <div class="fw-semibold inventory-sales-receipt">{{ $sale->payment?->receipt_no ?? '—' }}</div>
                                <div class="text-muted small inventory-sales-receipt-method">{{ $sale->payment?->payment_method ?? 'Sale' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $sale->student?->full_name_en ?? 'Walk-in / Unknown' }}</div>
                                <div class="text-muted small">By {{ $sale->createdBy?->name ?? 'System' }}</div>
                            </td>
                            <td style="min-width:280px">
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($itemLabels->take(4) as $label)
                                        <span class="badge rounded-pill bg-light text-dark border">{{ $label }}</span>
                                    @endforeach
                                    @if($itemLabels->count() > 4)
                                        <span class="badge rounded-pill bg-secondary">{{ $itemLabels->count() - 4 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end fw-semibold">BDT {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end fw-semibold text-success">BDT {{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end fw-semibold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                BDT {{ number_format($sale->due_amount, 2) }}
                            </td>
                            <td class="text-muted">{{ optional($sale->created_at)->format('d M Y') }}</td>
                            <td class="text-end px-4">
                                @if($sale->payment)
                                    <a href="{{ route('payments.receipt', $sale->payment->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3">
                                        Receipt
                                    </a>
                                @else
                                    <span class="text-muted small">No payment</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="fs-1 opacity-25">🧾</div>
                                <div class="fw-semibold">No sales found</div>
                                <div class="small">Try a different purchase, item, or date range.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
