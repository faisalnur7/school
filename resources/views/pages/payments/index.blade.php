@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @include('pages.payments.filter')
        <div class="card-header text-white rounded-top d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center shadow p-3">
            <h3 class="card-title">
                Payments
                @if ($payments->count() > 0)
                    <span class="badge badge-light">{{ $payments->count() }}</span>
                @endif
            </h3>
            <div class="ml-sm-auto d-flex flex-column flex-sm-row gap-2 w-100 w-sm-auto">
                <a href="{{ route('students.export', request()->all()) }}" class="btn btn-success btn-sm w-100 w-sm-auto">
                    <i class="fas fa-file-excel"></i> Export
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm text-bold w-100 w-sm-auto">
                    <i class="fas fa-plus"></i> {{ __('Add Student') }}
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background:#f8fafc;font-size:11px;letter-spacing:.07em">
                        <tr>
                            <th class="mono px-4 py-3 text-muted">#</th>
                            <th class="mono px-4 py-3 text-muted">Student Details</th>
                            <th class="mono px-4 py-3 text-muted">RECEIPT NO</th>
                            <th class="mono px-4 py-3 text-muted">DATE</th>
                            <th class="mono px-4 py-3 text-muted">ITEMS</th>
                            <th class="mono px-4 py-3 text-muted">METHOD</th>
                            <th class="mono px-4 py-3 text-muted">AMOUNT</th>
                            <th class="mono px-4 py-3 text-muted">COLLECTED BY</th>
                            <th class="mono px-4 py-3 text-muted text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            @php
                                $student = $payment->student;
                                $studentAcademicInformation = $payment->student->academicInformations->last();
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-muted mono" style="font-size:13px">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3">
                                    <code
                                        style="font-size:14px;padding:3px 0px;color:#4338ca">
                                        {{ $student->student_cid }} - {{ $student->full_name_en }}<br>
                                        {{ $studentAcademicInformation->schoolClass->name_en }}<br>
                                        {{ $studentAcademicInformation->section->name_en }}<br>
                                        {{ $studentAcademicInformation->group?->name_en }}<br>

                                    </code>
                                </td>
                                <td class="px-4 py-3">
                                    <code
                                        style="font-size:14px;background:#f1f5f9;padding:3px 8px;border-radius:6px;color:#4338ca">
                                        {{ $payment->receipt_no }}
                                    </code>
                                </td>
                                <td class="px-4 py-3 mono text-muted" style="font-size:14px">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3" style="font-size:13px">
                                    @foreach ($payment->items as $item)
                                        <span class="badge rounded-pill me-1 mb-1 bg-green-700 text-white"
                                            style="font-size:11px;border:1px solid #e2e8f0">
                                            {{ $item->fee->feeSet->name ?? 'Fee' }}
                                        </span>
                                    @endforeach
                                                @if ($payment->inventorySale?->items?->isNotEmpty())
                                                    @foreach ($payment->inventorySale->items as $saleItem)
                                                        <span class="badge rounded-pill me-1 mb-1 bg-blue-600 text-white"
                                                            style="font-size:11px;border:1px solid #bfdbfe">
                                                            {{ $saleItem->inventoryItem->name ?? 'Inventory' }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                                @php
                                                    $validDueItems = method_exists($payment, 'validInventoryDueItems')
                                                        ? $payment->validInventoryDueItems()
                                                        : ($payment->inventoryDueItems ?? collect())->filter(fn ($dueItem) => $dueItem->inventorySaleItem?->inventoryItem);
                                                @endphp
                                                @if ($validDueItems->isNotEmpty())
                                                    @foreach ($validDueItems as $dueItem)
                                                        <span class="badge rounded-pill me-1 mb-1 bg-orange-500 text-white"
                                                            style="font-size:11px;border:1px solid #fdba74">
                                                            Due: {{ $dueItem->inventorySaleItem?->inventoryItem?->name ?? 'Inventory' }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </td>
                                <td class="px-4 py-3" style="font-size:13px">
                                    <span class="badge rounded-pill"
                                        style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;font-size:11px">
                                        {{ $payment->payment_method }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="mono fw-bold" style="font-size:15px;color:#4338ca">
                                        {{ number_format($payment->calculated_amount, 2) }}
                                    </span>
                                    <span class="text-muted mono" style="font-size:11px"> BDT</span>
                                </td>
                                <td class="px-4 py-3 text-muted" style="font-size:13px">
                                    {{ $payment->collector->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('payments.receipt', $payment->id) }}" target="_blank"
                                        class="btn btn-sm fw-semibold rounded-3 d-inline-flex align-items-center gap-1 w-100"
                                        style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:12px">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <polyline points="6 9 6 2 18 2 18 9" />
                                            <path
                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                            <rect x="6" y="14" width="12" height="8" />
                                        </svg>
                                        Print Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
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
@endsection
