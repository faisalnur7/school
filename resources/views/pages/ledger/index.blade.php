@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">

        {{-- Header --}}
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <h3 class="card-title mb-0 text-white text-lg">
                <i class="fas fa-book-open mr-2"></i> Debit / Credit Ledger
            </h3>
        </div>

        <div class="card-body px-0 pb-4 pt-0">

            {{-- ── Filters ─────────────────────────────────────────── --}}
            <form method="GET" action="{{ route('ledger.index') }}" class="px-3 pt-3 pb-2">
                <div class="row g-2 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:12px">Type</label>
                        <select name="type" class="form-control form-control-sm">
                            <option value="">All Types</option>
                            @foreach (['income', 'expense', 'capital', 'withdrawal'] as $t)
                                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
                                    {{ ucfirst($t) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:12px">Payment Method</label>
                        <select name="payment_method" class="form-control form-control-sm">
                            <option value="">All Methods</option>
                            @foreach (['Cash', 'Bank Transfer', 'Cheque', 'Mobile Banking', 'Other'] as $m)
                                <option value="{{ $m }}" {{ request('payment_method') === $m ? 'selected' : '' }}>
                                    {{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:12px">Shareholder</label>
                        <select name="shareholder_id" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach ($shareholders as $sh)
                                <option value="{{ $sh->id }}" {{ request('shareholder_id') == $sh->id ? 'selected' : '' }}>
                                    {{ $sh->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:12px">From</label>
                        <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm"
                               value="{{ request('from') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label mb-1" style="font-size:12px">To</label>
                        <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm"
                               value="{{ request('to') }}" placeholder="dd/mm/yyyy" autocomplete="off">
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('ledger.index') }}" class="btn btn-sm btn-secondary" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                    </div>

                </div>
            </form>

            {{-- ── Summary Badges ──────────────────────────────────── --}}
            <div class="px-3 pb-3 d-flex flex-wrap gap-2">
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">
                    Total Debit: {{ number_format($totalDebit, 2) }}
                </span>
                <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">
                    Total Credit: {{ number_format($totalCredit, 2) }}
                </span>
                @php $net = $totalCredit - $totalDebit; @endphp
                <span class="badge" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 14px">
                    Net: {{ number_format($net, 2) }}
                </span>
            </div>

            {{-- ── Table ───────────────────────────────────────────── --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th style="width:90px">Date</th>
                            <th style="width:140px">Reference</th>
                            <th>Description</th>
                            <th style="width:80px">Type</th>
                            <th>Debit Account</th>
                            <th class="text-right">Credit Account</th>
                            <th style="width:110px;text-align:right">Amount</th>
                            <th style="width:110px">Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $txn)
                            <tr>
                                {{-- Date --}}
                                <td style="white-space:nowrap;color:#64748b">
                                    {{ $txn->transaction_date->format('d/m/Y') }}
                                </td>

                                {{-- Reference --}}
                                <td style="font-family:monospace;font-size:11px;color:#475569">
                                    {{ $txn->reference_no ?? '—' }}
                                </td>

                                {{-- Description --}}
                                <td style="color:#334155">
                                    {{ $txn->description ?? $txn->reference_note ?? '—' }}
                                </td>

                                {{-- Type badge --}}
                                <td>
                                    @php
                                        $badgeStyle = match($txn->type) {
                                            'income'     => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0',
                                            'expense'    => 'background:#fff1f2;color:#e11d48;border:1px solid #fecdd3',
                                            'capital'    => 'background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe',
                                            'withdrawal' => 'background:#fefce8;color:#ca8a04;border:1px solid #fde68a',
                                            default      => 'background:#f1f5f9;color:#475569;border:1px solid #e2e8f0',
                                        };
                                    @endphp
                                    <span class="badge" style="{{ $badgeStyle }};font-size:10px;padding:3px 7px">
                                        {{ ucfirst($txn->type) }}
                                    </span>
                                </td>

                                {{-- Debit Account (left-aligned, red tint) --}}
                                <td>
                                    <span style="color:#b91c1c;font-weight:500">
                                        {{ $txn->debit_account_name }}
                                    </span>
                                </td>

                                {{-- Credit Account (right-aligned within cell, green tint) --}}
                                <td style="text-align:right">
                                    <span style="color:#15803d;font-weight:500">
                                        {{ $txn->credit_account_name }}
                                    </span>
                                </td>

                                {{-- Amount --}}
                                <td style="text-align:right;font-family:monospace;font-weight:600;color:#1e293b">
                                    {{ number_format($txn->amount, 2) }}
                                </td>

                                {{-- Payment Method --}}
                                <td>
                                    <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;font-size:10px;padding:3px 7px">
                                        {{ $txn->payment_method ?? '—' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    No transactions found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-3 pt-3">
                {{ $transactions->links() }}
            </div>

        </div>
    </div>
</div>
@endsection
