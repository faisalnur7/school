@extends('layouts.master')

@section('contents')
<div class="purchase-page w-full">
    <!-- Header -->
    <div class="purchase-header card-header">
        <div class="d-flex align-items-center gap-2">
            <div class="header-icon"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <h5 class="mb-0 fw-bold">New Purchase Order</h5>
                <small class="text-muted">Add products from the panel on the right</small>
            </div>
        </div>
        <a href="{{ route('inventory.purchases.index') }}" class="btn btn-outline-light btn-sm ml-auto">
            <i class="fas fa-arrow-left ml-auto"></i> Back
        </a>
    </div>

    <form method="POST" action="{{ route('inventory.purchases.store') }}" id="purchaseForm">
        @csrf
        <div class="purchase-body">

            <!-- LEFT: Form -->
            <div class="purchase-left">

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><strong>Please fix the errors below:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <!-- Order Info -->
                <div class="order-info-card mb-3">
                    <div class="order-info-header  card-header">
                        <i class="fas fa-file-invoice me-2"></i>Order Information
                    </div>
                    <div class="order-info-body">
                        <div class="order-field-group">
                            <div class="order-field">
                                <label class="order-label">Supplier <span class="text-danger">*</span></label>
                                <div class="order-input-wrap">
                                    <i class="fas fa-truck order-input-icon"></i>
                                    <select name="supplier_id" class="order-select @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('supplier_id')<div class="order-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="order-field">
                                <label class="order-label">Purchase Date <span class="text-danger">*</span></label>
                                <div class="order-input-wrap">
                                    <i class="fas fa-calendar-alt order-input-icon"></i>
                                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="order-input @error('purchase_date') is-invalid @enderror" required>
                                </div>
                                @error('purchase_date')<div class="order-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="order-field">
                                <label class="order-label">Reference No</label>
                                <div class="order-input-wrap">
                                    <i class="fas fa-hashtag order-input-icon"></i>
                                    <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="order-input @error('reference_no') is-invalid @enderror" placeholder="e.g. PO-2024-001">
                                </div>
                                @error('reference_no')<div class="order-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="order-field order-field-full">
                            <label class="order-label">Notes</label>
                            <textarea name="notes" class="order-textarea" rows="2" placeholder="Additional notes or remarks...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="section-card">
                    <div class="section-title d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-boxes me-2 text-primary"></i>Purchase Items</span>
                        <span class="badge bg-primary" id="item_count">0 items</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm purchase-table mb-0" id="items_table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center" style="width:90px">Last Price</th>
                                    <th class="text-center" style="width:75px">Qty</th>
                                    <th class="text-center" style="width:90px">Unit Price</th>
                                    <th class="text-center" style="width:90px">Total</th>
                                    <th style="width:36px"></th>
                                </tr>
                            </thead>
                            <tbody id="items_body">
                                <!-- rows injected by JS -->
                            </tbody>
                            <tfoot>
                                <tr class="grand-total-row">
                                    <td colspan="4" class="text-end fw-semibold">Grand Total</td>
                                    <td class="text-center fw-bold text-primary" id="grand_total">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div id="empty_state" class="empty-state">
                        <i class="fas fa-shopping-basket"></i>
                        <p>No items added yet.<br><small>Click a product card on the right to add it.</small></p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="purchase-footer mt-3">
                    <a href="{{ route('inventory.purchases.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm" id="submit_btn" disabled>
                        <i class="fas fa-save me-1"></i>Save Purchase
                    </button>
                </div>
            </div>

            <!-- RIGHT: Product Browser -->
            <div class="purchase-right">
                <div class="product-panel">
                    <div class="panel-header">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-th-large text-primary"></i>
                            <span class="fw-semibold">Product Catalog</span>
                        </div>
                        <!-- Search -->
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="product_search" class="form-control border-start-0 ps-0" placeholder="Search products...">
                        </div>
                        <!-- Item Type Filter -->
                        <div class="type-filter mb-2">
                            <button type="button" class="type-btn active" data-type="all">All</button>
                            <button type="button" class="type-btn" data-type="common"><i class="fas fa-cube me-1"></i>Common</button>
                            <button type="button" class="type-btn" data-type="classwise"><i class="fas fa-book me-1"></i>Classwise</button>
                        </div>
                        <!-- Classwise Dropdowns (shown only when Classwise is active) -->
                        <div class="classwise-filters mb-2" id="classwise_filters">
                            <select id="filter_class">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                            <select id="filter_group">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}">{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Category Tabs -->
                        <div class="cat-tabs" id="cat_tabs">
                            <button type="button" class="cat-tab active" data-cat="all">All</button>
                            @foreach($categories as $cat)
                                <button type="button" class="cat-tab" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div class="panel-body" id="product_grid">
                        @foreach($products as $p)
                        <div class="product-card"
                             data-id="{{ $p->id }}"
                             data-name="{{ $p->name }}"
                             data-price="{{ (float)$p->purchase_price }}"
                             data-cat="{{ $p->category_id }}"
                             data-type="{{ $p->item_type }}"
                             data-class="{{ $p->school_class_id }}"
                             data-group="{{ $p->group_id }}"
                             data-unit="{{ $p->unit }}"
                             onclick="addProduct(this)">
                            <div class="pc-left">
                                <div class="pc-name">{{ $p->name }}</div>
                                <div class="pc-badges">
                                    <span class="pc-badge-type {{ $p->item_type }}">{{ ucfirst($p->item_type) }}</span>
                                    @if($p->item_type === 'classwise' && $p->schoolClass)
                                        <span class="pc-badge-class">{{ $p->schoolClass->name_en }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="pc-right">
                                <div class="pc-price">৳{{ number_format((float)$p->purchase_price, 2) }}</div>
                                <div class="pc-stock {{ $p->current_stock <= 0 ? 'low' : '' }}">{{ $p->current_stock }} pcs</div>
                            </div>
                        </div>
                        @endforeach

                        @if($products->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="small">No products available</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Product data for JS -->
@php
    $productsJson = $products->map(function($p) {
        return [
            'id'           => $p->id,
            'name'         => $p->name,
            'price'        => (float)$p->purchase_price,
            'category_id'  => $p->category_id,
            'item_type'    => $p->item_type,
            'school_class_id' => $p->school_class_id,
            'class_name'   => optional($p->schoolClass)->name_en,
            'group_id'     => $p->group_id,
            'group_name'   => optional($p->group)->name_en,
            'unit'         => $p->unit,
            'category_name'=> optional($p->category)->name,
        ];
    });
@endphp
<script>
    const PRODUCTS = @json($productsJson);
</script>
@endsection

@section('styles')
<style>
/* ── Layout ── */
.purchase-page { display: flex; flex-direction: column; background: #f0f2f5; }
.purchase-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; flex-shrink: 0; }
.header-icon { width: 36px; height: 36px; background: rgba(255,255,255,.2); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.purchase-body { display: flex; gap: 0; align-items: flex-start; }
.purchase-left { flex: 0 0 58.333%; max-width: 58.333%; padding: 16px; display: flex; flex-direction: column; min-width: 0; }
.purchase-right { flex: 0 0 41.667%; max-width: 41.667%; display: flex; flex-direction: column; border-left: 1px solid #e2e8f0; background: #fff; position: sticky; top: 0; height: calc(100vh - 120px); overflow: hidden; }
/* ── Classwise Filters ── */
.classwise-filters { display: none; gap: 4px; }
.classwise-filters.visible { display: flex; }
.classwise-filters select { font-size: .75rem; border-radius: 6px; border: 1.5px solid #e5e7eb; padding: 3px 6px; color: #374151; flex: 1; }
.classwise-filters select:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 .1rem rgba(102,126,234,.15); }
.purchase-footer { display: flex; justify-content: space-between; align-items: center; }

/* ── Section Cards ── */
.section-card { background: #fff; border-radius: 10px; padding: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.06); margin-bottom: 12px; }
.section-title { font-size: .85rem; font-weight: 700; color: #374151; margin-bottom: 10px; }
.field-label { font-size: .78rem; font-weight: 600; color: #4b5563; display: block; margin-bottom: 3px; }

/* ── Order Info Card ── */
.order-info-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 12px; }
.order-info-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-size: .82rem; font-weight: 700; padding: 10px 16px; letter-spacing: .3px; }
.order-info-body { padding: 14px 16px; display: flex; flex-direction: column; gap: 10px; }
.order-field-group { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.order-field { display: flex; flex-direction: column; gap: 4px; }
.order-field-full { display: flex; flex-direction: column; gap: 4px; }
.order-label { font-size: .75rem; font-weight: 600; color: #6b7280; letter-spacing: .2px; }
.order-input-wrap { position: relative; }
.order-input-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: .75rem; pointer-events: none; }
.order-input, .order-select { width: 100%; padding: 7px 10px 7px 30px; font-size: .82rem; color: #1f2937; background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 8px; outline: none; transition: border-color .15s, box-shadow .15s; appearance: none; -webkit-appearance: none; }
.order-input:focus, .order-select:focus { border-color: #667eea; background: #fff; box-shadow: 0 0 0 3px rgba(102,126,234,.12); }
.order-input.is-invalid, .order-select.is-invalid { border-color: #ef4444; }
.order-textarea { width: 100%; padding: 7px 10px; font-size: .82rem; color: #1f2937; background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 8px; outline: none; resize: none; transition: border-color .15s, box-shadow .15s; }
.order-textarea:focus { border-color: #667eea; background: #fff; box-shadow: 0 0 0 3px rgba(102,126,234,.12); }
.order-error { font-size: .72rem; color: #ef4444; margin-top: 2px; }
@media (max-width: 640px) { .order-field-group { grid-template-columns: 1fr; } }

/* ── Table ── */
.purchase-table thead th { background: #f8fafc; font-size: .78rem; font-weight: 700; color: #374151; border-bottom: 2px solid #e5e7eb; padding: 8px 6px; }
.purchase-table tbody td { font-size: .82rem; padding: 5px 6px; vertical-align: middle; }
.purchase-table tfoot td { font-size: .85rem; padding: 8px 6px; border-top: 2px solid #e5e7eb; }
.grand-total-row td { background: #f8fafc; }
.remove-row { width: 26px; height: 26px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 6px; }

/* ── Empty State ── */
.empty-state { text-align: center; padding: 30px 20px; color: #9ca3af; }
.empty-state i { font-size: 2.5rem; margin-bottom: 10px; display: block; }
.empty-state p { font-size: .85rem; margin: 0; }

/* ── Product Panel ── */
.product-panel { display: flex; flex-direction: column; height: 100%; }
.panel-header { padding: 12px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0; }
.panel-body { flex: 1; overflow-y: auto; padding: 8px; display: flex; flex-direction: column; gap: 5px; }

/* ── Type Filter ── */
.type-filter { display: flex; gap: 4px; }
.type-btn { flex: 1; padding: 4px 6px; font-size: .75rem; font-weight: 600; border: 1.5px solid #e5e7eb; background: #fff; border-radius: 6px; cursor: pointer; color: #6b7280; transition: all .15s; }
.type-btn.active { background: #667eea; border-color: #667eea; color: #fff; }
.type-btn:hover:not(.active) { border-color: #667eea; color: #667eea; }

/* ── Category Tabs ── */
.cat-tabs { display: flex; gap: 4px; flex-wrap: wrap; }
.cat-tab { padding: 3px 10px; font-size: .72rem; font-weight: 600; border: 1.5px solid #e5e7eb; background: #fff; border-radius: 20px; cursor: pointer; color: #6b7280; transition: all .15s; white-space: nowrap; }
.cat-tab.active { background: #764ba2; border-color: #764ba2; color: #fff; }
.cat-tab:hover:not(.active) { border-color: #764ba2; color: #764ba2; }

/* ── Product Card ── */
#product_grid .product-card { background: #fff; border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 7px 9px; cursor: pointer; transition: border-color .15s, box-shadow .15s; position: relative; box-sizing: border-box; display: flex; justify-content: space-between; align-items: center; gap: 6px; }
#product_grid .product-card:hover { border-color: #667eea; box-shadow: 0 3px 10px rgba(102,126,234,.18); }
#product_grid .product-card.in-cart { border-color: #10b981; background: #f0fdf4; }
#product_grid .product-card.hidden { display: none !important; }
#product_grid .pc-left { flex: 1; min-width: 0; }
#product_grid .pc-name { font-size: .75rem; font-weight: 700; color: #1f2937; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
#product_grid .pc-badges { display: flex; flex-wrap: wrap; gap: 2px; margin-top: 3px; }
#product_grid .pc-badge-type { font-size: .6rem; background: #ede9fe; color: #7c3aed; padding: 1px 4px; border-radius: 3px; }
#product_grid .pc-badge-type.classwise { background: #dbeafe; color: #2563eb; }
#product_grid .pc-badge-class { font-size: .6rem; background: #f0fdf4; color: #059669; padding: 1px 4px; border-radius: 3px; }
#product_grid .pc-right { text-align: right; flex-shrink: 0; }
#product_grid .pc-price { font-size: .78rem; font-weight: 700; color: #667eea; white-space: nowrap; }
#product_grid .pc-stock { font-size: .65rem; color: #6b7280; white-space: nowrap; margin-top: 2px; }
#product_grid .pc-stock.low { color: #ef4444; }
#product_grid .in-cart-badge { position: absolute; top: -5px; right: -5px; width: 14px; height: 14px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 7px; }

/* ── Buttons ── */
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
.btn-primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(102,126,234,.35); }
.btn-primary:disabled { opacity: .6; cursor: not-allowed; }
.gap-2 { gap: .5rem; }

/* ── Form Controls ── */
.form-control-sm, .form-select-sm { border-radius: 6px; font-size: .82rem; }
.form-control-sm:focus, .form-select-sm:focus { border-color: #667eea; box-shadow: 0 0 0 .15rem rgba(102,126,234,.15); }

@media (max-width: 768px) {
    .purchase-body { flex-direction: column; }
    .purchase-right { position: static; height: 420px; flex: 0 0 100%; max-width: 100%; border-left: none; border-top: 1px solid #e2e8f0; }
    .purchase-left { flex: 0 0 100%; max-width: 100%; }
    .panel-body { grid-template-columns: repeat(3, 1fr); }
}
</style>
@endsection

@section('scripts')
<script>
$(function () {
    let rowIndex = 0;
    let activeType = 'all';
    let activeCat = 'all';
    let activeClass = '';
    let activeGroup = '';
    let cartItems = {}; // id -> rowIndex

    // ── Render a new row ──
    function addRow(id, name, price) {
        const idx = rowIndex++;
        const options = PRODUCTS.map(p =>
            `<option value="${p.id}" data-last-price="${p.price}" ${p.id == id ? 'selected' : ''}>${p.name}${p.category_name ? ' (' + p.category_name + ')' : ''}</option>`
        ).join('');

        const row = $(`
        <tr class="item-row" data-product-id="${id}">
            <td>
                <select name="items[${idx}][inventory_item_id]" class="form-select form-select-sm product-select" required>
                    <option value="">Select Product</option>
                    ${options}
                </select>
            </td>
            <td class="last-price text-muted text-center small">${price > 0 ? price.toFixed(2) : '—'}</td>
            <td><input type="number" min="1" name="items[${idx}][quantity]" class="form-control form-control-sm qty text-center" value="1" required></td>
            <td><input type="number" step="0.01" min="0" name="items[${idx}][unit_price]" class="form-control form-control-sm unit-price text-center" value="${price.toFixed(2)}" required></td>
            <td class="line-total text-center small fw-bold">${price.toFixed(2)}</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-row" title="Remove"><i class="fas fa-times"></i></button>
            </td>
        </tr>`);

        $('#items_body').append(row);
        bindRow(row);
        updateUI();
        return row;
    }

    // ── Add product from card click ──
    window.addProduct = function(el) {
        const id = parseInt(el.dataset.id);
        const name = el.dataset.name;
        const price = parseFloat(el.dataset.price) || 0;

        if (cartItems[id] !== undefined) {
            // increment qty
            const $row = $(`#items_body tr[data-product-id="${id}"]`);
            const $qty = $row.find('.qty');
            $qty.val(parseInt($qty.val()) + 1);
            recalcRow($row);
            recalcGrand();
            $row[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        cartItems[id] = rowIndex;
        addRow(id, name, price);
        $(el).addClass('in-cart');
        $(el).append('<div class="in-cart-badge"><i class="fas fa-check"></i></div>');
    };

    // ── Bind row events ──
    function bindRow($row) {
        $row.on('change', '.product-select', function () {
            const last = $(this).find('option:selected').data('last-price') || 0;
            $row.find('.last-price').text(last > 0 ? parseFloat(last).toFixed(2) : '—');
            $row.find('.unit-price').val(parseFloat(last).toFixed(2));
            recalcRow($row);
            recalcGrand();
        });
        $row.on('input', '.qty, .unit-price', function () {
            recalcRow($row);
            recalcGrand();
        });
        $row.find('.remove-row').on('click', function () {
            const pid = parseInt($row.data('product-id'));
            delete cartItems[pid];
            $(`#product_grid .product-card[data-id="${pid}"]`).removeClass('in-cart').find('.in-cart-badge').remove();
            $row.remove();
            renumberNames();
            recalcGrand();
            updateUI();
        });
    }

    function recalcRow($row) {
        const qty = parseFloat($row.find('.qty').val() || 0);
        const price = parseFloat($row.find('.unit-price').val() || 0);
        $row.find('.line-total').text((qty * price).toFixed(2));
    }

    function recalcGrand() {
        let grand = 0;
        $('#items_body tr').each(function () {
            grand += parseFloat($(this).find('.line-total').text() || 0);
        });
        $('#grand_total').text(grand.toFixed(2));
    }

    function renumberNames() {
        $('#items_body tr').each(function (i) {
            $(this).find('.product-select').attr('name', `items[${i}][inventory_item_id]`);
            $(this).find('.qty').attr('name', `items[${i}][quantity]`);
            $(this).find('.unit-price').attr('name', `items[${i}][unit_price]`);
        });
    }

    function updateUI() {
        const count = $('#items_body tr').length;
        $('#item_count').text(count + ' item' + (count !== 1 ? 's' : ''));
        $('#empty_state').toggle(count === 0);
        $('#submit_btn').prop('disabled', count === 0);
        $('#items_table').toggle(count > 0);
    }

    // ── Filtering ──
    function filterProducts() {
        const q = $('#product_search').val().toLowerCase();
        $('#product_grid .product-card').each(function () {
            const matchType  = activeType === 'all' || $(this).data('type') === activeType;
            const matchCat   = activeCat === 'all' || String($(this).data('cat')) === String(activeCat);
            const matchQ     = !q || $(this).data('name').toLowerCase().includes(q);
            const matchClass = !activeClass || String($(this).data('class')) === String(activeClass);
            const matchGroup = !activeGroup || String($(this).data('group')) === String(activeGroup);
            $(this).toggleClass('hidden', !(matchType && matchCat && matchQ && matchClass && matchGroup));
        });
    }

    $('.type-btn').on('click', function () {
        $('.type-btn').removeClass('active');
        $(this).addClass('active');
        activeType = $(this).data('type');
        // show/hide classwise dropdowns
        if (activeType === 'classwise') {
            $('#classwise_filters').addClass('visible');
        } else {
            $('#classwise_filters').removeClass('visible');
            activeClass = '';
            activeGroup = '';
            $('#filter_class, #filter_group').val('');
        }
        filterProducts();
    });

    $('#filter_class').on('change', function () {
        activeClass = $(this).val();
        filterProducts();
    });

    $('#filter_group').on('change', function () {
        activeGroup = $(this).val();
        filterProducts();
    });

    $('#cat_tabs').on('click', '.cat-tab', function () {
        $('.cat-tab').removeClass('active');
        $(this).addClass('active');
        activeCat = $(this).data('cat');
        filterProducts();
    });

    $('#product_search').on('input', filterProducts);

    // ── Init ──
    updateUI();
    $('#items_table').hide();
});
</script>
@endsection
