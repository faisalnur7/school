@extends('layouts.master')

@section('contents')
<div class="container-fluid py-3 px-3 payment-edit-page">

    {{-- ── Student Banner ── --}}
    <div class="student-header-card mb-3 payment-edit-hero">
        <div class="card-inner">
            <a href="{{ url()->previous() }}" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                Back
            </a>
            <div class="header-divider"></div>
            <div class="student-identity">
                <div class="avatar-ring">
                    <span class="avatar-initials">{{ strtoupper(substr($payment->student->full_name_en, 0, 2)) }}</span>
                </div>
                <div>
                    <p class="label-tag">STUDENT</p>
                    <h5 class="student-name">{{ $payment->student->full_name_en }}</h5>
                </div>
            </div>
            @php $info = $payment->student->academicInformations->last(); @endphp
            <div class="meta-chips ms-auto">
                <div class="chip">
                    <span class="chip-label">RECEIPT NO</span>
                    <span class="chip-value">{{ $payment->receipt_no }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">DATE</span>
                    <span class="chip-value">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">CLASS</span>
                    <span class="chip-value">{{ $info->schoolClass->name_en ?? '—' }}</span>
                </div>
                <div class="chip">
                    <span class="chip-label">SECTION</span>
                    <span class="chip-value">{{ $info->section->name_en ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('payments.update', $payment) }}" method="POST">
        @csrf
        @method('PUT')
        @php
            $calculatedAmount = $payment->calculated_amount;
            $calculatedGross = $payment->calculated_gross_amount;
        @endphp

    <div class="row g-3 align-items-start">
        {{-- LEFT: Payment Items ── --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 payment-panel">
                <div class="card-header bg-white border-bottom px-3 py-2" style="border-color:#f1f5f9!important">
                    <div class="d-flex align-items-center gap-2">
                        <span class="section-badge">PAYMENT ITEMS</span>
                        @php $lineItemCount = $payment->items->count() + (($payment->inventorySale?->items ?? collect())->count()); @endphp
                        <span id="paymentItemCountBadge" class="badge rounded-pill ms-auto" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                            {{ $lineItemCount }} item(s)
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="paymentItemsList" class="compact-list">
                        @if ($payment->inventorySale && $payment->inventorySale->items->isNotEmpty())
                        <div class="px-3 pt-3 pb-2">
                            <span class="badge rounded-pill" style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0">Inventory Items</span>
                        </div>
                        @foreach ($payment->inventorySale->items as $saleItem)
                        @php
                            $saleGross = (float) $saleItem->subtotal;
                            $salePaid = (float) ($saleItem->paid_amount ?? $saleItem->subtotal);
                            $saleDue = max(0, $saleGross - $salePaid);
                        @endphp
                        <div class="payment-item-row card-like-row inventory-row px-3 py-3 border-bottom" style="border-color:#e5e7eb">
                            <div class="card-like-top d-flex justify-content-between align-items-start gap-3">
                                <div class="flex-grow-1">
                                    <div class="row-head mb-3">
                                        <h6 class="fw-semibold mb-1 row-title">
                                            {{ $saleItem->inventoryItem->name ?? 'Inventory Item' }}
                                        </h6>
                                        <small class="row-subtitle">
                                            {{ $saleItem->inventoryItem->category->name ?? 'Uncategorized' }}
                                        </small>
                                    </div>

                                    <div class="row-metrics row-metrics-3">
                                        <div class="metric-tile">
                                            <span class="metric-label">QTY</span>
                                            <input type="number" min="0" step="1" name="sale_items[{{ $saleItem->id }}][quantity]"
                                                value="{{ $saleItem->quantity }}" class="form-control form-control-sm metric-input">
                                        </div>
                                        <div class="metric-tile">
                                            <span class="metric-label">UNIT PRICE</span>
                                            <input type="number" min="0" step="0.01" name="sale_items[{{ $saleItem->id }}][unit_price]"
                                                value="{{ number_format($saleItem->unit_price, 2, '.', '') }}" class="form-control form-control-sm metric-input">
                                        </div>
                                        <div class="metric-tile">
                                            <span class="metric-label">PAID</span>
                                            <input type="number" min="0" step="0.01" name="sale_items[{{ $saleItem->id }}][paid_amount]"
                                                value="{{ number_format($salePaid, 2, '.', '') }}" class="form-control form-control-sm metric-input metric-input-paid">
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn rounded-2 compact-remove-btn"
                                    data-item-id="{{ $saleItem->id }}" data-item-type="inventory" style="font-size:12px">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="row-summary">
                                <div class="summary-box-mini">
                                    <span class="summary-label">GROSS</span>
                                    <span class="summary-value summary-gross-value">{{ number_format($saleGross, 2) }}</span>
                                </div>
                                <div class="summary-box-mini">
                                    <span class="summary-label">PAID</span>
                                    <span class="summary-value summary-value-paid summary-paid-value">{{ number_format($salePaid, 2) }}</span>
                                </div>
                                <div class="summary-box-mini summary-box-mini-due">
                                    <span class="summary-label">AMOUNT DUE</span>
                                    <span class="summary-value summary-value-due summary-due-value">{{ number_format($saleDue, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @if ($payment->items->isNotEmpty())
                        <div class="px-3 pt-3 pb-2">
                            <span class="badge rounded-pill" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">Fee Items</span>
                        </div>
                        @foreach ($payment->items as $item)
                        @php
                            $feeGross = (float) $item->fee->net_amount;
                            $feePaid = (float) $item->amount;
                            $feeDue = (float) $item->fee->due_amount;
                        @endphp
                        <div class="payment-item-row card-like-row fee-row px-3 py-3 border-bottom" style="border-color:#e5e7eb" data-item-id="{{ $item->id }}">
                            <div class="card-like-top d-flex justify-content-between align-items-start gap-3">
                                <div class="flex-grow-1">
                                    <div class="row-head mb-3">
                                        <h6 class="fw-semibold mb-1 row-title">
                                            {{ $item->fee->feeSet->name ?? 'Fee' }}
                                        </h6>
                                        <small class="row-subtitle">
                                            {{ \Carbon\Carbon::parse($item->fee->due_date)->format('F - Y') }}
                                        </small>
                                    </div>

                                    <div class="item-breakdown item-breakdown-compact">
                                        @foreach ($item->fee->feeSet->items as $feeItem)
                                        <div class="d-flex justify-content-between text-muted mb-1 gap-2">
                                            <span class="text-truncate">• {{ $feeItem->category->name }}</span>
                                            <span class="mono compact-line-amount">{{ number_format($feeItem->amount, 2) }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn rounded-2 compact-remove-btn"
                                    data-item-id="{{ $item->id }}" data-item-type="fee" style="font-size:12px">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </div>

                            <div class="row-metrics row-metrics-3">
                                <div class="metric-tile">
                                    <span class="metric-label">NET TOTAL</span>
                                    <div class="metric-static summary-gross-value">{{ number_format($feeGross, 2) }}</div>
                                </div>
                                <div class="metric-tile">
                                    <span class="metric-label">PAID</span>
                                    <input type="number" step="0.01" name="items[{{ $item->id }}][amount]" class="form-control form-control-sm metric-input metric-input-paid"
                                        value="{{ number_format($feePaid, 2, '.', '') }}" required style="border-color:#334155">
                                </div>
                                <div class="metric-tile">
                                    <span class="metric-label">DUE</span>
                                    <div class="metric-static metric-static-due summary-due-value">{{ number_format($feeDue, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @php
                            $hasInventoryItems = ($payment->inventorySale?->items ?? collect())->isNotEmpty();
                        @endphp
                        @if (!$hasInventoryItems && $payment->items->isEmpty())
                        <div class="text-center text-muted py-4">
                            <p>No payment items</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Edit Form ── --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 payment-panel payment-sticky">
                <div class="card-header bg-white border-bottom px-3 py-2" style="border-color:#f1f5f9!important">
                    <div class="d-flex align-items-center gap-2">
                        <span class="section-badge section-badge-alt">EDIT PAYMENT</span>
                    </div>
                </div>
                <div class="card-body p-3">
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold compact-label mb-1">Payment Amount</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" style="background:#f1f5f9;border-color:#e2e8f0">BDT</span>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                    value="{{ number_format($calculatedAmount, 2, '.', '') }}" readonly style="border-color:#e2e8f0;background:#f8fafc">
                            </div>
                            <small class="text-muted d-block mt-1 compact-help">
                                Auto-calculated from fee and inventory line payments.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label fw-semibold compact-label mb-1">Payment Date</label>
                            <input type="date" class="form-control form-control-sm compact-input" id="payment_date" name="payment_date" 
                                value="{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}" 
                                required style="border-color:#e2e8f0">
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label fw-semibold compact-label mb-1">Payment Method</label>
                            <select class="form-control form-control-sm compact-input" id="payment_method" name="payment_method" required style="border-color:#e2e8f0">
                                <option value="Cash" {{ $payment->payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ $payment->payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Cheque" {{ $payment->payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Mobile Banking" {{ $payment->payment_method == 'Mobile Banking' ? 'selected' : '' }}>Mobile Banking</option>
                                <option value="Other" {{ $payment->payment_method == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold compact-label mb-1">Description</label>
                            <textarea class="form-control compact-input" id="description" name="description" rows="3"
                                style="border-color:#e2e8f0">{{ $payment->description }}</textarea>
                        </div>

                        {{-- Summary ── --}}
                        <div class="summary-box summary-box-compact p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <div class="d-flex justify-content-between mb-2 compact-summary-row">
                                <span class="text-muted">Gross Amount:</span>
                                <span id="paymentGrossAmount" class="mono fw-semibold">{{ number_format($calculatedGross, 2) }} BDT</span>
                            </div>
                            @if ($payment->scholarship_amount > 0)
                            <div class="d-flex justify-content-between mb-2 compact-summary-row compact-summary-green">
                                <span class="text-muted">Scholarship:</span>
                                <span class="mono">-{{ number_format($payment->scholarship_amount, 2) }} BDT</span>
                            </div>
                            @endif
                            @if ($payment->discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 compact-summary-row compact-summary-amber">
                                <span class="text-muted">Discount:</span>
                                <span class="mono">-{{ number_format($payment->discount_amount, 2) }} BDT</span>
                            </div>
                            @endif
                        </div>

                        <button type="submit" class="btn w-100 fw-bold text-white rounded-3 py-2 mb-2 compact-primary-btn"
                            style="background:linear-gradient(135deg,#6366f1,#4338ca);font-size:14px">
                            ✓ Update Payment
                        </button>
                        <a href="{{ route('fees.collect_payment', ['student_id' => $payment->student_id]) }}" class="btn btn-outline-secondary w-100 fw-bold rounded-3 py-2 compact-secondary-btn"
                            style="font-size:14px">
                            Cancel
                        </a>
                </div>
            </div>
        </div>
    </div>

    </form>

</div>
@endsection

@section('styles')
<style>
    .payment-edit-page .payment-edit-hero {
        margin-bottom: 0.75rem;
    }

    .payment-edit-page .payment-panel {
        overflow: hidden;
    }

    .payment-edit-page .payment-sticky {
        position: sticky;
        top: 1rem;
    }

    .payment-edit-page .section-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #4338ca, #6366f1);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .payment-edit-page .section-badge-alt {
        background: linear-gradient(135deg, #0f766e, #14b8a6);
    }

    .payment-edit-page .compact-title {
        font-size: 0.92rem;
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }

    .payment-edit-page .card-like-row {
        background: #ffffff;
        border-radius: 1rem;
        margin: 0.75rem 0.75rem 0;
        border: 1px solid #e5e7eb;
        color: #111827;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .payment-edit-page .card-like-row .row-head {
        display: flex;
        flex-direction: column;
    }

    .payment-edit-page .payment-panel .card-body {
        background: #ffffff;
    }

    .payment-edit-page .payment-panel .card-header {
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .payment-edit-page .payment-panel .badge,
    .payment-edit-page .payment-panel .row-subtitle,
    .payment-edit-page .payment-panel .compact-title,
    .payment-edit-page .payment-panel .compact-due,
    .payment-edit-page .payment-panel .compact-muted,
    .payment-edit-page .payment-panel .compact-line-amount {
        color: #374151;
    }

    .payment-edit-page .payment-panel .text-white {
        color: #111827 !important;
    }

    .payment-edit-page .payment-panel .text-white-50,
    .payment-edit-page .payment-panel .text-muted {
        color: #6b7280 !important;
    }

    .payment-edit-page .row-title {
        font-size: 1rem;
        line-height: 1.2;
        margin-bottom: 0.1rem;
    }

    .payment-edit-page .row-subtitle {
        font-size: 0.78rem;
        color: #6b7280;
    }

    .payment-edit-page .row-metrics {
        display: grid;
        gap: 0.65rem;
    }

    .payment-edit-page .row-metrics-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .payment-edit-page .metric-tile {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.7rem 0.8rem 0.75rem;
        min-height: 70px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .payment-edit-page .metric-label {
        font-size: 0.7rem;
        letter-spacing: 0.09em;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .payment-edit-page .metric-static,
    .payment-edit-page .metric-input {
        font-size: 1rem;
        line-height: 1.2;
        color: #111827;
        font-weight: 600;
    }

    .payment-edit-page .metric-static-due,
    .payment-edit-page .metric-input-paid {
        color: #0f766e;
    }

    .payment-edit-page .metric-input {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        padding: 0;
        height: auto;
        min-height: 0;
        width: 100%;
    }

    .payment-edit-page .metric-input::placeholder {
        color: #cbd5e1;
    }

    .payment-edit-page .metric-input:focus {
        outline: none;
        box-shadow: none;
    }

    .payment-edit-page .metric-input-narrow {
        width: 100%;
    }

    .payment-edit-page .row-summary {
        margin: 0.75rem 0 0;
        padding: 0.9rem 1rem 0.1rem;
        border-top: 1px solid #e5e7eb;
        background: #fff7ed;
        color: #9a3412;
        border-radius: 0 0 0.95rem 0.95rem;
        display: grid;
        grid-template-columns: 1fr auto auto;
        gap: 0.75rem;
        align-items: center;
    }

    .payment-edit-page .summary-box-mini {
        min-width: 0;
    }

    .payment-edit-page .summary-box-mini-due {
        text-align: right;
        justify-self: end;
    }

    .payment-edit-page .summary-label {
        display: block;
        font-size: 0.68rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.12rem;
        color: #c2410c;
        font-weight: 700;
    }

    .payment-edit-page .summary-value {
        display: block;
        font-size: 1.08rem;
        font-weight: 700;
        color: #9a3412;
        line-height: 1.1;
    }

    .payment-edit-page .summary-value-paid {
        color: #0f766e;
    }

    .payment-edit-page .summary-value-due {
        color: #9a3412;
    }

    .payment-edit-page .compact-subtitle,
    .payment-edit-page .compact-help,
    .payment-edit-page .compact-muted,
    .payment-edit-page .compact-due {
        font-size: 0.72rem;
    }

    .payment-edit-page .compact-label {
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
    }

    .payment-edit-page .compact-input {
        border-radius: 0.75rem;
        font-size: 0.92rem;
        min-height: 2.2rem;
        box-shadow: none !important;
    }

    .payment-edit-page .compact-input-narrow {
        width: 92px;
    }

    .payment-edit-page .item-compact-controls {
        max-width: 275px;
        display: grid;
        gap: 0.2rem;
    }

    .payment-edit-page .item-breakdown {
        font-size: 0.74rem;
        max-width: 100%;
    }

    .payment-edit-page .compact-line-amount {
        font-size: 0.74rem;
        white-space: nowrap;
        color: #4b5563;
    }

    .payment-edit-page .compact-metrics {
        min-width: 132px;
    }

    .payment-edit-page .compact-amount {
        font-size: 1rem;
        color: #4338ca;
    }

    .payment-edit-page .compact-amount-paid {
        color: #059669;
    }

    .payment-edit-page .compact-footer {
        font-size: 0.7rem;
    }

    .payment-edit-page .compact-summary-row {
        font-size: 0.84rem;
    }

    .payment-edit-page .summary-box-compact {
        border-radius: 1rem !important;
    }

    .payment-edit-page .compact-primary-btn,
    .payment-edit-page .compact-secondary-btn {
        border-radius: 0.85rem !important;
        font-size: 0.92rem !important;
    }

    @media (max-width: 991.98px) {
        .payment-edit-page .payment-sticky {
            position: static;
        }

        .payment-edit-page .item-compact-controls {
            max-width: 220px;
        }

        .payment-edit-page .compact-metrics {
            min-width: 104px;
        }

        .payment-edit-page .row-summary {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .payment-edit-page {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .payment-edit-page .student-header-card .card-inner {
            gap: 0.5rem;
        }

        .payment-edit-page .meta-chips {
            width: 100%;
        }

        .payment-edit-page .item-compact-controls {
            max-width: 100%;
        }

        .payment-edit-page .payment-item-row .d-flex.justify-content-between.align-items-start {
            flex-direction: column;
        }

        .payment-edit-page .compact-metrics {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }

        .payment-edit-page .compact-input-narrow {
            width: 100%;
        }

        .payment-edit-page .row-metrics-3,
        .payment-edit-page .row-summary {
            grid-template-columns: 1fr;
        }

        .payment-edit-page .summary-box-mini-due {
            justify-self: start;
            text-align: left;
        }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function() {
        function parseMoney(value) {
            const amount = parseFloat(value);
            return Number.isFinite(amount) ? amount : 0;
        }

        function formatMoney(value) {
            return (Math.max(0, value) || 0).toFixed(2);
        }

        function recalcPaymentTotals() {
            let totalPaid = 0;
            let totalGross = 0;

            $('#paymentItemsList .payment-item-row').each(function() {
                const $row = $(this);

                if ($row.hasClass('inventory-row')) {
                    const qty = parseMoney($row.find('input[name*="[quantity]"]').val());
                    const unit = parseMoney($row.find('input[name*="[unit_price]"]').val());
                    const paid = parseMoney($row.find('input[name*="[paid_amount]"]').val());
                    const gross = qty * unit;
                    const due = Math.max(0, gross - paid);

                    $row.find('.summary-gross-value').text(formatMoney(gross));
                    $row.find('.summary-paid-value').text(formatMoney(paid));
                    $row.find('.summary-due-value').text(formatMoney(due));

                    totalGross += gross;
                    totalPaid += paid;
                    return;
                }

                const gross = parseMoney($row.find('.summary-gross-value').text());
                const paidInput = parseMoney($row.find('input[name*="[amount]"]').val());
                const due = Math.max(0, gross - paidInput);

                $row.find('.summary-paid-value').text(formatMoney(paidInput));
                $row.find('.summary-due-value').text(formatMoney(due));

                totalGross += gross;
                totalPaid += paidInput;
            });

            $('#amount').val(formatMoney(totalPaid));
            $('#paymentGrossAmount').text(formatMoney(totalGross) + ' BDT');
        }

        $(document).on('input change', '.metric-input', recalcPaymentTotals);
        recalcPaymentTotals();

        // Handle remove payment item
        $(document).on('click', '.remove-item-btn', function() {
            const $btn = $(this);
            const itemId = $btn.data('item-id');
            const itemType = $btn.data('item-type') || 'fee';
            const $row = $btn.closest('.payment-item-row');
            const url = itemType === 'inventory'
                ? '/inventory-sale-items/' + itemId
                : '/payment-items/' + itemId;

            Swal.fire({
                icon: 'warning',
                title: 'Remove Payment Item?',
                text: itemType === 'inventory'
                    ? 'This will remove the inventory item and recalculate the amounts.'
                    : 'This will remove the payment from this fee and recalculate the amounts.',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Remove',
                cancelButtonColor: '#6c757d',
                showCancelButton: true,
            }).then((result) => {
                if (!result.isConfirmed) return;

                $btn.prop('disabled', true).html('⏳ Removing...');

                $.ajax({
                    url: url,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                    },
                    dataType: 'json',
                    success: function(res) {
                        // Remove the row with animation
                        $row.fadeOut(300, function() {
                            $(this).remove();

                            // Update badge count
                            const count = $('#paymentItemsList .payment-item-row').length;
                            $('#paymentItemCountBadge').text(count + ' item(s)');

                            // If no items left, show empty message
                            if (count === 0) {
                                $('#paymentItemsList').html(
                                    '<div class="text-center text-muted py-4"><p>No payment items</p></div>'
                                );
                            }

                            recalcPaymentTotals();

                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Item Removed',
                                text: res.message,
                                confirmButtonColor: '#4338ca',
                                timer: 2000
                            });

                            // Refresh the page to update the payment amount
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message || 'Failed to remove item';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg,
                            confirmButtonColor: '#4338ca'
                        });
                        $btn.prop('disabled', false).html(
                            '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">' +
                            '<polyline points="3 6 5 6 21 6"></polyline>' +
                            '<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>' +
                            '<line x1="10" y1="11" x2="10" y2="17"></line>' +
                            '<line x1="14" y1="11" x2="14" y2="17"></line>' +
                            '</svg> Remove'
                        );
                    }
                });
            });
        });
    });
</script>
@endsection
