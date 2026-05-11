@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4 px-4">

        {{-- ── Student Banner ── --}}
        <div class="student-header-card mb-4">
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
                        <span class="avatar-initials">{{ strtoupper(substr($student->full_name_en, 0, 2)) }}</span>
                    </div>
                    <div>
                        <p class="label-tag">STUDENT</p>
                        <h5 class="student-name">{{ $student->full_name_en }}</h5>
                    </div>
                </div>
                @php $info = $student->academicInformations->last(); @endphp
                <div class="meta-chips ms-auto">
                    <div class="chip">
                        <span class="chip-label">ID</span>
                        <span class="chip-value">{{ $student->student_cid }}</span>
                    </div>
                    <div class="chip" style="min-width:220px">
                        <span class="chip-label">STUDENT ID</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <input type="text" id="studentCidSwitch" class="form-control form-control-sm"
                                value="{{ $student->student_cid }}" placeholder="Enter Student ID" autocomplete="off"
                                style="border-radius:12px;border:1px solid #c7d2fe" />
                            <button type="button" id="studentCidSwitchBtn" class="btn btn-sm"
                                style="border-radius:12px;background:#4338ca;color:#fff">
                                Switch
                            </button>
                        </div>
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


        {{-- ── Tabs ── --}}
        <ul class="nav nav-tabs mb-4" id="mainTabs">
            <li class="nav-item">
                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#tabCollect">
                    💳 Collect Payment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#tabHistory">
                    🧾 Payment History
                    <span class="badge rounded-pill ms-1"
                        style="font-size:10px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                        {{ $payments->count() }}
                    </span>
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ══════════════════════════════════════
                 TAB 1 — COLLECT PAYMENT
            ══════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tabCollect">
                <div class="row g-4">

                    {{-- LEFT: Categories --}}
                    <div class="col-md-3 col-xl-2">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
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

                    {{-- MIDDLE: Pending Fees --}}
                    <div class="col-md-5 col-xl-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5">📋</span>
                                    <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">PENDING
                                        FEES</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div id="emptySelect" class="text-center py-5 text-muted">
                                    <div style="font-size:44px;opacity:.2">👈</div>
                                    <p class="mt-3 mb-0 fw-medium" style="font-size:14px">
                                        Select a category on the left<br>to view pending fees
                                    </p>
                                </div>
                                <div class="scroll-area d-none" id="feesContainer">
                                    <div class="d-flex flex-column gap-3" id="feeList">
                                        @foreach ($pendingFees as $fee)
                                            @php
                                                $feeSetName =
                                                    $fee->feeSet->frequency == 'monthly'
                                                        ? Carbon\Carbon::parse($fee->due_date)->format('F - Y')
                                                        : $fee->feeSet->name;
                                            @endphp
                                            <div class="fee-card bg-white rounded-4 p-3" data-cat="{{ $fee->fee_set_id }}"
                                                data-id="{{ $fee->id }}"
                                                data-amount="{{ $fee->calculated_net_amount ?? $fee->net_amount }}"
                                                data-gross="{{ $fee->amount }}"
                                                data-discount="{{ $fee->total_scholarship_discount ?? 0 }}"
                                                data-name="{{ $feeSetName }}" data-items='@json($fee->feeSet->items->map(fn($i) => ['category' => $i->category->name, 'amount' => $i->amount]))'
                                                style="display:none!important">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <p class="fw-semibold text-dark mb-1" style="font-size:15px">
                                                            {{ $feeSetName }}
                                                        </p>
                                                        <p class="mono text-muted mb-0" style="font-size:12px">
                                                            Due: {{ $fee->due_date }}
                                                        </p>
                                                        @if (!empty($fee->category_discounts))
                                                            @foreach ($fee->category_discounts as $catDiscount)
                                                                <p class="mono mb-0 mt-1"
                                                                    style="font-size:11px;color:#059669">
                                                                    <span class="badge rounded-pill"
                                                                        style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;font-size:10px">
                                                                        🎓 {{ $catDiscount['category'] }}:
                                                                        -৳{{ number_format($catDiscount['discount'], 2) }}
                                                                    </span>
                                                                </p>
                                                            @endforeach
                                                        @endif
                                                        @if (!empty($fee->category_transports))
                                                            @foreach ($fee->category_transports as $catTransport)
                                                                <p class="mono mb-0 mt-1"
                                                                    style="font-size:11px;color:#4338ca">
                                                                    <span class="badge rounded-pill"
                                                                        style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:10px">
                                                                        🚌 {{ $catTransport['category'] }}:
                                                                        +৳{{ number_format($catTransport['amount'], 2) }}
                                                                    </span>
                                                                </p>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                    <div class="text-end ms-3">
                                                        @if (!empty($fee->category_discounts) || !empty($fee->category_transports))
                                                            <p class="mono text-muted mb-0"
                                                                style="font-size:12px;text-decoration:line-through">
                                                                {{ number_format($fee->amount, 2) }}
                                                            </p>
                                                        @endif
                                                        <p class="mono fw-bold mb-1" style="font-size:19px;color:#4338ca">
                                                            {{ number_format($fee->calculated_net_amount ?? $fee->net_amount, 2) }}
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

                    {{-- RIGHT: Cart --}}
                    <div class="col-md-4 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
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
                            <form id="feeForm">
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
                                <div class="card-footer bg-white border-top p-4" style="border-color:#f1f5f9!important">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <span class="mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.08em">SUBTOTAL</span>
                                        <span class="mono fw-semibold" id="subtotalAmount"
                                            style="font-size:15px;color:#64748b">0.00</span>
                                    </div>
                                    <div class="discount-row mb-2" id="discountSection">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="mono text-muted fw-semibold"
                                                style="font-size:11px;letter-spacing:.08em">DISCOUNT</span>
                                            <div class="ms-auto d-flex gap-1">
                                                <button type="button" class="discount-type-btn active"
                                                    id="btnFlat">BDT</button>
                                                <button type="button" class="discount-type-btn"
                                                    id="btnPercent">%</button>
                                            </div>
                                        </div>
                                        <input type="number" id="discountInput" name="discount" min="0"
                                            step="0.01" placeholder="0.00" value="0" autocomplete="off">
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-muted" style="font-size:11px">Discount applied:</span>
                                            <span class="mono discount-amount-line" id="discountLine">- 0.00 BDT</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="discount_type" id="discountTypeHidden" value="flat">
                                    <input type="hidden" name="discount_amount" id="discountAmountHidden"
                                        value="0">
                                    <hr class="my-2" style="border-color:#f1f5f9">
                                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                                        <span class="mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.09em">TOTAL DUE</span>
                                        <div>
                                            <span class="mono text-muted me-1" style="font-size:13px">BDT</span>
                                            <span class="mono fw-bold" id="totalAmount"
                                                style="font-size:28px;color:#4338ca">0.00</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="paymentAmount" class="form-label mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.08em">PAYMENT AMOUNT</label>
                                        <input type="number" id="paymentAmount" name="payment_amount" min="0" step="0.01"
                                            placeholder="Enter payment amount" class="form-control" style="font-size:16px;border-radius:8px;border:1px solid #e2e8f0">
                                        <small class="text-muted" style="font-size:11px">Leave empty to pay full amount, or enter partial amount</small>
                                    </div>

                                    <div class="mb-3">
                                        <textarea name="description" id="descriptionInput" class="form-control" rows="2"
                                            placeholder="Add payment note or description" style="font-size:13px;border-radius:8px;border:1px solid #e2e8f0">{{ old('description') }}</textarea>
                                    </div>

                                    <button type="button" class="collect-btn btn w-100 fw-bold text-white rounded-3 py-3"
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


            {{-- ══════════════════════════════════════
                 TAB 2 — PAYMENT HISTORY
            ══════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tabHistory">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">📑</span>
                            <span class="fw-bold text-white" style="font-size:12px;letter-spacing:.06em">PAYMENT
                                HISTORY</span>
                            <span class="ms-auto text-white" style="font-size:12px">
                                Total Paid:
                                <strong class="text-white">
                                    BDT {{ number_format($payments->sum('amount'), 2) }}
                                </strong>
                                &nbsp;|&nbsp; Scholarship:
                                <strong class="text-white">
                                    BDT {{ number_format($payments->sum('scholarship_amount'), 2) }}
                                </strong>
                                &nbsp;|&nbsp; Discount:
                                <strong class="text-white">
                                    BDT {{ number_format($payments->sum('discount_amount'), 2) }}
                                </strong>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background:#f8fafc;font-size:11px;letter-spacing:.07em">
                                    <tr>
                                        <th class="mono px-4 py-3 text-muted">#</th>
                                        <th class="mono px-4 py-3 text-muted">RECEIPT NO</th>
                                        <th class="mono px-4 py-3 text-muted">DATE</th>
                                        <th class="mono px-4 py-3 text-muted">ITEMS</th>
                                        <th class="mono px-4 py-3 text-muted">METHOD</th>
                                        <th class="mono px-4 py-3 text-muted">GROSS</th>
                                        <th class="mono px-4 py-3 text-muted">SCHOLARSHIP</th>
                                        <th class="mono px-4 py-3 text-muted">DISCOUNT</th>
                                        <th class="mono px-4 py-3 text-muted">PAID</th>
                                        <th class="mono px-4 py-3 text-muted">COLLECTED BY</th>
                                        <th class="mono px-4 py-3 text-muted text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td class="px-4 py-3 text-muted mono" style="font-size:13px">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <code
                                                    style="font-size:12px;background:#f1f5f9;padding:3px 8px;border-radius:6px;color:#4338ca">
                                                    {{ $payment->receipt_no }}
                                                </code>
                                            </td>
                                            <td class="px-4 py-3 mono text-muted" style="font-size:13px">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3" style="font-size:13px">
                                                @foreach ($payment->items as $item)
                                                    <span class="badge rounded-pill me-1 mb-1"
                                                        style="font-size:10px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">
                                                        {{ $item->fee->feeSet->name ?? '—' }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="px-4 py-3" style="font-size:13px">
                                                <span class="badge rounded-pill"
                                                    style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;font-size:11px">
                                                    {{ $payment->payment_method }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="mono fw-bold" style="font-size:15px;color:#64748b">
                                                    {{ $payment->gross_amount > 0 ? number_format($payment->gross_amount, 2) : number_format($payment->amount, 2) }}
                                                </span>
                                                <span class="text-muted mono" style="font-size:11px"> BDT</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($payment->scholarship_amount > 0)
                                                    <span class="mono fw-bold" style="font-size:13px;color:#059669">
                                                        -{{ number_format($payment->scholarship_amount, 2) }}
                                                    </span>
                                                    <span class="text-muted mono" style="font-size:10px"> BDT</span>
                                                @else
                                                    <span class="text-muted mono" style="font-size:12px">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($payment->discount_amount > 0)
                                                    <span class="mono fw-bold" style="font-size:13px;color:#b45309">
                                                        -{{ number_format($payment->discount_amount, 2) }}
                                                    </span>
                                                    <span class="text-muted mono" style="font-size:10px">
                                                        {{ $payment->discount_type === 'percent' ? '%' : 'BDT' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted mono" style="font-size:12px">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="mono fw-bold" style="font-size:15px;color:#4338ca">
                                                    {{ number_format($payment->amount, 2) }}
                                                </span>
                                                <span class="text-muted mono" style="font-size:11px"> BDT</span>
                                            </td>
                                            <td class="px-4 py-3 text-muted" style="font-size:13px">
                                                {{ $payment->collector->name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="{{ route('payments.edit', $payment->id) }}"
                                                        class="btn btn-sm fw-semibold rounded-3 d-inline-flex align-items-center gap-1"
                                                        style="background:#fef3c7;color:#d97706;border:1px solid #fcd34d;font-size:12px">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                        </svg>
                                                        Edit
                                                    </a>
                                                    <a href="{{ route('payments.receipt', $payment->id) }}" target="_blank"
                                                        class="btn btn-sm fw-semibold rounded-3 d-inline-flex align-items-center gap-1"
                                                        style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:12px">
                                                        <svg width="13" height="13" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="6 9 6 2 18 2 18 9" />
                                                            <path
                                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                                            <rect x="6" y="14" width="12" height="8" />
                                                        </svg>
                                                        Print Receipt
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-5">
                                                <div style="font-size:36px;opacity:.2">🧾</div>
                                                <p class="mt-2 mb-0" style="font-size:14px">No payments recorded yet</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            let subtotal = 0;
            let cartIds = new Set();
            let discountType = 'flat';
            let cartData = [];

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

            const currentCid = @json($student->student_cid);
            const $studentCidInput = $('#studentCidSwitch');
            const $studentCidBtn = $('#studentCidSwitchBtn');

            function showSwitchError(title, message, icon) {
                Swal.fire({
                    icon: icon || 'error',
                    title: title,
                    text: message,
                    confirmButtonColor: '#4338ca',
                });
            }

            function attemptStudentSwitch() {
                const cid = ($studentCidInput.val() || '').trim();

                if (cid === '' || cid.length < 1) {
                    showSwitchError('Invalid CID', 'Please enter a valid student CID.', 'warning');
                    return;
                }

                if (cid === String(currentCid)) {
                    return;
                }

                $studentCidBtn.prop('disabled', true).text('...');

                Swal.fire({
                    title: 'Searching...',
                    text: 'Looking up student by CID',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading(),
                });

                $.ajax({
                    url: '{{ route('fees.switch_student') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('input[name="_token"]').first().val(),
                        student_cid: cid,
                    },
                    success: function(res) {
                        if (!res?.success || !res?.redirect_url) {
                            Swal.close();
                            showSwitchError('Student Not Found', 'No student found with CID: ' + cid +
                                '. Please check and try again.', 'error');
                            return;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Student Found',
                            text: 'Switching to student ' + (res.student_name || '') + '...',
                            timer: 900,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = res.redirect_url;
                        });
                    },
                    error: function(xhr) {
                        Swal.close();

                        const msg = xhr.responseJSON?.message;

                        if (xhr.status === 422) {
                            showSwitchError('Invalid CID', msg || 'Please enter a valid student CID.',
                                'warning');
                            return;
                        }

                        if (xhr.status === 404) {
                            showSwitchError('Student Not Found', msg || ('No student found with CID: ' +
                                cid + '. Please check and try again.'), 'error');
                            return;
                        }

                        showSwitchError('Error', msg || 'Something went wrong. Please try again.',
                            'error');
                    },
                    complete: function() {
                        $studentCidBtn.prop('disabled', false).text('Switch');
                    }
                });
            }

            $studentCidBtn.on('click', attemptStudentSwitch);
            $studentCidInput.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    attemptStudentSwitch();
                }
            });
            $studentCidInput.on('blur', function() {
                attemptStudentSwitch();
            });

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

            $discountInput.on('input', updateUI);

            /* ── Add fee on card click ── */
            $feeCards.on('click', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));
                let gross = parseFloat($(this).data('gross') || $(this).data('amount'));
                let discount = parseFloat($(this).data('discount') || 0);
                let name = $(this).data('name');
                let items = $(this).data('items') || [];
                if (cartIds.has(id)) return;
                cartIds.add(id);
                subtotal += amount;
                cartData.push({
                    id,
                    name,
                    amount,
                    gross,
                    discount,
                    items
                });
                $(this).addClass('in-cart');
                $cartEmpty.hide();

                let discountHtml = discount > 0 ?
                    `<span class="mono" style="font-size:11px;color:#059669">Scholarship: -${discount.toFixed(2)}</span><br>` :
                    '';
                let grossHtml = discount > 0 ?
                    `<span class="mono text-muted" style="font-size:11px;text-decoration:line-through">${gross.toFixed(2)}</span><br>` :
                    '';

                let itemsBreakdown = '';
                if (items.length > 0) {
                    itemsBreakdown = '<div class="mt-1 ps-2" style="font-size:11px;color:#64748b;">';
                    items.forEach(function(item) {
                        itemsBreakdown += '<div class="d-flex justify-content-between"><span>• ' +
                            item.category + '</span><span>' + parseFloat(item.amount).toFixed(2) +
                            '</span></div>';
                    });
                    itemsBreakdown += '</div>';
                }

                let html = `
                     <div class="cart-row d-flex align-items-start gap-2 rounded-3 px-3 py-2"
                          id="cart-${id}"
                          style="background:#f8fafc;border:1.5px solid #e2e8f0">
                         <input type="hidden" name="fees[]" value="${id}">
                         <div class="flex-grow-1" style="line-height:1.4">
                             <span class="fw-semibold text-dark" style="font-size:13px">${name}</span><br>
                             ${discountHtml}
                             ${itemsBreakdown}
                         </div>
                         <div class="text-end">
                             ${grossHtml}
                             <span class="mono fw-bold" style="font-size:13px;color:#4338ca;white-space:nowrap">${amount.toFixed(2)}</span>
                         </div>
                         <button type="button"
                                 class="remove-btn btn btn-light btn-sm border rounded-2 px-2 py-1"
                                 data-id="${id}" data-amount="${amount}"
                                 style="font-size:13px;line-height:1">✕</button>
                     </div>`;
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
                let raw = Math.max(0, parseFloat($discountInput.val()) || 0);
                if (discountType === 'flat') return Math.min(raw, subtotal);
                if (discountType === 'percent') return (subtotal * Math.min(raw, 100)) / 100;
                return 0;
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

                // Keep payment amount in sync with selected fees
                $('#paymentAmount').val(finalTotal.toFixed(2));
            }



            /* ── AJAX Collect ── */
            $('#collectBtn').on('click', function() {

                if (cartIds.size === 0) return;

                const $btn = $(this);
                $btn.prop('disabled', true).html('⏳ &nbsp;Processing...');

                // Build payload
                const payload = {
                    _token: $('input[name="_token"]').first().val(),
                    fees: [...cartIds],
                    payment_amount: $('#paymentAmount').val() || $totalEl.text(),
                    discount: $('#discountInput').val() || 0,
                    discount_type: $('#discountTypeHidden').val(),
                    discount_amount: $('#discountAmountHidden').val(),
                    description: $('#descriptionInput').val() || '',
                };

                $.ajax({
                    url: '{{ route('fees.pay') }}',
                    method: 'POST',
                    data: payload,
                    dataType: 'json',

                    success: function(res) {

                        // ── Show toast ──
                        $('#toastReceiptNo').text('Receipt: ' + res.receipt_no);
                        const $toast = $('#paymentToast');
                        $toast.css('display', 'flex').hide().fadeIn(250);
                        setTimeout(function() {
                            $toast.fadeOut(400);
                        }, 3500);

                        // ── Reset cart ──
                        cartIds.clear();
                        subtotal = 0;
                        cartData = [];
                        $('#cartItems').empty();
                        $('#cartEmpty').show();
                        $('#discountInput').val(0);
                        $('#descriptionInput').val('');
                        $btn.prop('disabled', true).html('✓ &nbsp;COLLECT PAYMENT');
                        updateUI();

                        // ── Open receipt in new tab & auto-print ──
                        const receiptUrl = '{{ url('payments') }}/' + res.payment_id +
                            '/receipt';
                        const win = window.open(receiptUrl, '_blank');

                        // Fire print once the new tab has loaded
                        if (win) {
                            win.addEventListener('load', function() {
                                setTimeout(function() {
                                    win.focus();
                                    win.print();
                                }, 600); // small delay for fonts/styles to settle
                            });
                        }
                    },

                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message ??
                            'Something went wrong. Please try again.';

                        // ── Error toast ──
                        const $toast = $('#paymentToast');
                        $toast.css('background', '#dc2626');
                        $('#paymentToast span:first').text('✕');
                        $toast.find('.fw-800, [style*="font-weight:800"]').text(
                            'PAYMENT FAILED');
                        $('#toastReceiptNo').text(msg);
                        $toast.css('display', 'flex').hide().fadeIn(250);
                        setTimeout(function() {
                            $toast.fadeOut(400, function() {
                                // Reset toast to success style for next time
                                $toast.css('background', '#111');
                                $('#paymentToast span:first').text('✓');
                                $toast.find('[style*="font-weight:800"]').text(
                                    'PAYMENT COLLECTED');
                            });
                        }, 4000);

                        $btn.prop('disabled', false).html('✓ &nbsp;COLLECT PAYMENT');
                    }
                });
            });
        });
    </script>
@endsection
