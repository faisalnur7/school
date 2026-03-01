@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4 px-4">

        {{-- ── Student Banner ── --}}
        <div class="student-header-card mb-4">
            <div class="card-inner">

                {{-- Back Button --}}
                <a href="{{ url()->previous() }}" class="back-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Back
                </a>

                {{-- Divider --}}
                <div class="header-divider"></div>

                {{-- Student Identity --}}
                <div class="student-identity">
                    <div class="avatar-ring">
                        <span class="avatar-initials">{{ strtoupper(substr($student->full_name_en, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="label-tag">STUDENT</p>
                        <h5 class="student-name">{{ $student->full_name_en }}</h5>
                    </div>
                </div>

                {{-- Meta Chips --}}
                @php $info = $student->academicInformations->last(); @endphp
                <div class="meta-chips ms-auto">

                    <div class="chip">
                        <span class="chip-label">ID</span>
                        <span class="chip-value">{{ $student->student_cid }}</span>
                    </div>

                    <div class="chip">
                        <span class="chip-label">CLASS</span>
                        <span class="chip-value">{{ $info->schoolClass->name_en ?? '—' }}</span>
                    </div>

                    <div class="chip">
                        <span class="chip-label">SECTION</span>
                        <span class="chip-value">{{ $info->section->name_en ?? '—' }}</span>
                    </div>

                    <div class="chip">
                        <span class="chip-label">GROUP</span>
                        <span class="chip-value">{{ $info->group->name_en ?? 'N/A' }}</span>
                    </div>

                </div>
            </div>
        </div>


        {{-- ── 3-Column Grid ── --}}
        <div class="row g-4">

            {{-- ── LEFT: Categories ── --}}
            <div class="col-md-3 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">🗂️</span>
                            <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">FEE
                                CATEGORIES</span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-flex flex-column gap-2" id="categoryList">
                            @php $feeSets = $pendingFees->groupBy('fee_set_id'); @endphp
                            @foreach ($feeSets as $feeSetId => $fees)
                                <div class="cat-item d-flex justify-content-between align-items-center rounded-3 px-3 py-2"
                                    style="background:#f8fafc" data-cat="{{ $feeSetId }}">
                                    <span class="cat-name text-secondary fw-medium" style="font-size:14px">
                                        {{ $fees->first()->feeSet->name }}
                                    </span>
                                    <span class="cat-badge badge rounded-pill mono"
                                        style="font-size:11px;background:#e2e8f0;color:#64748b">
                                        {{ $fees->count() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            {{-- ── MIDDLE: Pending Fees ── --}}
            <div class="col-md-5 col-xl-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">📋</span>
                            <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">PENDING FEES</span>
                        </div>
                    </div>
                    <div class="card-body p-3">

                        {{-- Empty: no category selected --}}
                        <div id="emptySelect" class="text-center py-5 text-muted">
                            <div style="font-size:44px;opacity:.2">👈</div>
                            <p class="mt-3 mb-0 fw-medium" style="font-size:14px">
                                Select a category on the left<br>to view pending fees
                            </p>
                        </div>

                        {{-- Fee cards --}}
                        <div class="scroll-area d-none" id="feesContainer">
                            <div class="d-flex flex-column gap-3" id="feeList">
                                @foreach ($pendingFees as $fee)
                                    @php
                                        $feeSetName = '';
                                        if ($fee->feeSet->frequency == 'monthly') {
                                            $feeSetName = Carbon\Carbon::parse($fee->due_date)->format('F - Y');
                                        } else {
                                            $feeSetName = $fee->feeSet->name;
                                        }
                                    @endphp
                                    <div class="fee-card bg-white rounded-4 p-3" data-cat="{{ $fee->fee_set_id }}"
                                        data-id="{{ $fee->id }}" data-amount="{{ $fee->amount }}"
                                        data-name="{{ $feeSetName }}" style="display:none!important">

                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <p class="fw-semibold text-dark mb-1" style="font-size:15px">
                                                    {{ $feeSetName }}
                                                </p>
                                                <p class="mono text-muted mb-0" style="font-size:12px">
                                                    Due: {{ $fee->due_date }}
                                                </p>
                                            </div>
                                            <div class="text-end ms-3">
                                                <p class="mono fw-bold mb-1" style="font-size:19px;color:#4338ca">
                                                    {{ number_format($fee->amount, 2) }}
                                                </p>
                                                <span class="badge rounded-pill"
                                                    style="font-size:10px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                                                    + Add
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            {{-- ── RIGHT: Cart ── --}}
            <div class="col-md-4 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">🧾</span>
                            <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">SELECTED
                                FEES</span>
                            <span id="cartBadge" class="mono ms-auto badge rounded-pill"
                                style="font-size:11px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                                0 items
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('fees.pay') }}" id="feeForm">
                        @csrf

                        <div class="card-body p-3">
                            <div class="scroll-area" style="max-height:280px">

                                <div id="cartEmpty" class="text-center py-5 text-muted">
                                    <div style="font-size:40px;opacity:.18">🛒</div>
                                    <p class="mt-3 mb-0 fw-medium" style="font-size:13px">
                                        No fees added yet.<br>Click a pending fee to add it.
                                    </p>
                                </div>

                                <div id="cartItems" class="d-flex flex-column gap-2"></div>

                            </div>
                        </div>

                        {{-- Footer / Total --}}
                        <div class="card-footer bg-white border-top p-4" style="border-color:#f1f5f9!important">

                            {{-- Subtotal --}}
                            <div class="d-flex justify-content-between align-items-baseline mb-2">
                                <span class="mono text-muted fw-semibold"
                                    style="font-size:11px;letter-spacing:.08em">SUBTOTAL</span>
                                <span class="mono fw-semibold" id="subtotalAmount"
                                    style="font-size:15px;color:#64748b">0.00</span>
                            </div>

                            {{-- Discount --}}
                            <div class="discount-row mb-2" id="discountSection">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="mono text-muted fw-semibold"
                                        style="font-size:11px;letter-spacing:.08em">DISCOUNT</span>
                                    <div class="ms-auto d-flex gap-1">
                                        <button type="button" class="discount-type-btn active"
                                            id="btnFlat">BDT</button>
                                        <button type="button" class="discount-type-btn" id="btnPercent">%</button>
                                    </div>
                                </div>
                                <input type="number" id="discountInput" name="discount" min="0" step="0.01"
                                    placeholder="0.00" value="0" autocomplete="off">
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted" style="font-size:11px">Discount applied:</span>
                                    <span class="mono discount-amount-line" id="discountLine">- 0.00 BDT</span>
                                </div>
                            </div>

                            {{-- Hidden inputs for form submission --}}
                            <input type="hidden" name="discount_type" id="discountTypeHidden" value="flat">
                            <input type="hidden" name="discount_amount" id="discountAmountHidden" value="0">

                            {{-- Divider --}}
                            <hr class="my-2" style="border-color:#f1f5f9">

                            {{-- Total --}}
                            <div class="d-flex justify-content-between align-items-baseline mb-3">
                                <span class="mono text-muted fw-semibold"
                                    style="font-size:11px;letter-spacing:.09em">TOTAL DUE</span>
                                <div>
                                    <span class="mono text-muted me-1" style="font-size:13px">BDT</span>
                                    <span class="mono fw-bold" id="totalAmount"
                                        style="font-size:28px;color:#4338ca">0.00</span>
                                </div>
                            </div>

                            <button type="submit" class="collect-btn btn w-100 fw-bold text-white rounded-3 py-3"
                                id="collectBtn" disabled
                                style="background:linear-gradient(135deg,#6366f1,#4338ca);font-size:14px">
                                ✓ &nbsp;COLLECT PAYMENT
                            </button>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script>
        $(function() {

            let subtotal = 0;
            let cartIds = new Set();
            let discountType = 'flat'; // 'flat' | 'percent'
            let cartData = []; // [{id, name, amount}]

            const $feeCards = $('.fee-card');
            const $catItems = $('.cat-item');
            const $cartItemsEl = $('#cartItems');
            const $cartEmpty = $('#cartEmpty');
            const $emptySelect = $('#emptySelect');
            const $feesContainer = $('#feesContainer');
            const $subtotalEl = $('#subtotalAmount');
            const $totalEl = $('#totalAmount');
            const $badgeEl = $('#cartBadge');
            const $collectBtn = $('#collectBtn');
            const $discountInput = $('#discountInput');
            const $discountLine = $('#discountLine');


            /* ── Category click ── */
            $catItems.on('click', function() {
                $catItems.removeClass('active');
                $(this).addClass('active');
                let sel = $(this).data('cat');
                $emptySelect.hide();
                $feesContainer.removeClass('d-none');
                $feeCards.each(function() {
                    $(this).toggle($(this).data('cat') == sel);
                });
            });


            /* ── Discount type toggle ── */
            $('#btnFlat').on('click', function() {
                discountType = 'flat';
                $(this).addClass('active');
                $('#btnPercent').removeClass('active');
                $('#discountTypeHidden').val('flat');
                $discountInput.attr('max', subtotal).attr('placeholder', '0.00');
                updateUI();
            });

            $('#btnPercent').on('click', function() {
                discountType = 'percent';
                $(this).addClass('active');
                $('#btnFlat').removeClass('active');
                $('#discountTypeHidden').val('percent');
                $discountInput.attr('max', 100).attr('placeholder', '0');
                updateUI();
            });

            $discountInput.on('input', function() {
                updateUI();
            });


            /* ── Add fee on card click ── */
            $feeCards.on('click', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));
                let name = $(this).data('name');

                if (cartIds.has(id)) return;

                cartIds.add(id);
                subtotal += amount;
                cartData.push({
                    id,
                    name,
                    amount
                });

                $(this).addClass('in-cart');
                $cartEmpty.hide();

                let html = `
            <div class="cart-row d-flex align-items-center gap-2 rounded-3 px-3 py-2"
                 id="cart-${id}"
                 style="background:#f8fafc;border:1.5px solid #e2e8f0">
                <input type="hidden" name="fees[]" value="${id}">
                <span class="fw-semibold text-dark flex-grow-1" style="font-size:13px;line-height:1.3">${name}</span>
                <span class="mono fw-bold me-1" style="font-size:13px;color:#4338ca;white-space:nowrap">${amount.toFixed(2)}</span>
                <button type="button"
                        class="remove-btn btn btn-light btn-sm border rounded-2 px-2 py-1"
                        data-id="${id}"
                        data-amount="${amount}"
                        style="font-size:13px;line-height:1">✕</button>
            </div>
        `;
                $cartItemsEl.append(html);
                updateUI();
            });


            /* ── Remove from cart ── */
            $cartItemsEl.on('click', '.remove-btn', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));

                cartIds.delete(id);
                subtotal -= amount;
                cartData = cartData.filter(x => x.id != id);

                $('#cart-' + id).remove();
                $(`.fee-card[data-id="${id}"]`).removeClass('in-cart');

                if (cartIds.size === 0) $cartEmpty.show();
                updateUI();
            });


            /* ── Compute discount ── */
            function computeDiscount() {
                let raw = parseFloat($discountInput.val()) || 0;
                raw = Math.max(0, raw);

                let discountAmt = 0;
                if (discountType === 'flat') {
                    discountAmt = Math.min(raw, subtotal);
                } else {
                    raw = Math.min(raw, 100);
                    discountAmt = (subtotal * raw) / 100;
                }
                return discountAmt;
            }


            /* ── Update all UI ── */
            function updateUI() {
                let discountAmt = computeDiscount();
                let finalTotal = Math.max(0, subtotal - discountAmt);

                $subtotalEl.text(subtotal.toFixed(2));
                $totalEl.text(finalTotal.toFixed(2));
                $discountLine.text('- ' + discountAmt.toFixed(2) + ' BDT');
                $('#discountAmountHidden').val(discountAmt.toFixed(2));

                $badgeEl.text(cartIds.size + (cartIds.size === 1 ? ' item' : ' items'));
                $collectBtn.prop('disabled', cartIds.size === 0);
            }




        });
    </script>
@endsection
