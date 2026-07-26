@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3 product-form-page">
    <div class="card shadow-sm border-0 product-form-card">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                <div class="flex flex-col">
                    <div class="text-uppercase small font-weight-bold text-muted mb-1">Inventory</div>
                    <h4 class="card-title mb-1 font-weight-bold text-slate-900">
                        <i class="fas fa-clipboard-list mr-2 text-primary"></i>Opening Stock
                    </h4>
                    <p class="mb-0 text-muted product-form-subtitle">Enter the current quantity and average unit cost before going live.</p>
                </div>
                <a href="{{ route('inventory.hub') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger border-0 mb-4">
                    <strong>Please fix the errors below.</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('inventory.opening-stock.create') }}" class="mb-3">
                <div class="d-flex flex-wrap align-items-end gap-2">
                    <div style="min-width: 260px; flex: 1 1 320px;">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search product name">
                    </div>
                    <div style="min-width: 240px; flex: 0 1 280px;">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="{{ route('inventory.opening-stock.create') }}" class="btn btn-light">
                            <i class="fas fa-undo-alt mr-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ route('inventory.opening-stock.store') }}">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Opening Date <span class="text-danger">*</span></label>
                        <input type="date" name="opening_date" value="{{ old('opening_date', date('Y-m-d')) }}" class="form-control @error('opening_date') is-invalid @enderror" required>
                        @error('opening_date')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference No</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control @error('reference_no') is-invalid @enderror" placeholder="Optional reference">
                        @error('reference_no')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Note</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" class="form-control @error('notes') is-invalid @enderror" placeholder="Optional note">
                        @error('notes')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="mb-2 text-muted small">
                    Showing {{ $items->count() }} product{{ $items->count() === 1 ? '' : 's' }}
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th style="width: 36%">Product</th>
                                <th class="text-right" style="width: 16%">Current Stock</th>
                                <th class="text-right" style="width: 16%">Opening Qty</th>
                                <th class="text-right" style="width: 16%">Avg Unit Cost</th>
                                <th class="text-right" style="width: 16%">Opening Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td>
                                        <div class="font-weight-600">{{ $item->name }}</div>
                                        <div class="text-muted small">{{ $item->category?->name ?? 'Uncategorized' }} @if($item->unit) | {{ $item->unit }} @endif</div>
                                    </td>
                                    <td class="text-right">
                                        <span class="badge badge-info">{{ $item->current_stock }}</span>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $item->id }}][inventory_item_id]" value="{{ $item->id }}">
                                        <input type="number" min="0" step="1" name="items[{{ $item->id }}][quantity]" value="{{ old('items.' . $item->id . '.quantity') }}" class="form-control text-right js-opening-qty @error('items.' . $item->id . '.quantity') is-invalid @enderror" placeholder="0">
                                        @error('items.' . $item->id . '.quantity')<small class="text-danger">{{ $message }}</small>@enderror
                                    </td>
                                    <td>
                                        <input type="number" min="0" step="0.01" name="items[{{ $item->id }}][unit_cost]" value="{{ old('items.' . $item->id . '.unit_cost', $item->average_cost ?: $item->purchase_price) }}" class="form-control text-right js-opening-cost @error('items.' . $item->id . '.unit_cost') is-invalid @enderror" placeholder="0.00">
                                        @error('items.' . $item->id . '.unit_cost')<small class="text-danger">{{ $message }}</small>@enderror
                                    </td>
                                    <td class="text-right text-muted">
                                        <span class="js-opening-value">0.00</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No stocked products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white border-top py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('inventory.hub') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i>Save Opening Stock
                        </button>
                    </div>
                </div>
            </form>
            <script>
                (function () {
                    const rows = document.querySelectorAll('.js-opening-qty');

                    function formatMoney(value) {
                        const amount = Number.isFinite(value) ? value : 0;
                        return amount.toFixed(2);
                    }

                    function recalcRow(row) {
                        const qtyInput = row.querySelector('.js-opening-qty');
                        const costInput = row.querySelector('.js-opening-cost');
                        const valueEl = row.querySelector('.js-opening-value');

                        const qty = parseFloat(qtyInput?.value || '0');
                        const cost = parseFloat(costInput?.value || '0');
                        valueEl.textContent = formatMoney((qty || 0) * (cost || 0));
                    }

                    rows.forEach((qtyInput) => {
                        const row = qtyInput.closest('tr');
                        const costInput = row?.querySelector('.js-opening-cost');

                        if (!row || !costInput) {
                            return;
                        }

                        ['input', 'change'].forEach((eventName) => {
                            qtyInput.addEventListener(eventName, () => recalcRow(row));
                            costInput.addEventListener(eventName, () => recalcRow(row));
                        });

                        recalcRow(row);
                    });
                })();
            </script>
        </div>
    </div>
</div>
@endsection
