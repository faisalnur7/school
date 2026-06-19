@extends('layouts.master')
@section('contents')
    <div class="container-fluid">
    @include('partials.report-header')

        <div class="card">
            <div class="card-header shadow p-0 flex justify-between items-center">
                <h3 class="card-title flex text-white pl-3 text-medium">Cash Book</h3>
                <div class="flex gap-2 pr-3 pt-3 items-center justify-center ml-auto">
                    <form method="GET" class="flex gap-2 items-end">
                        <div>
                            <label style="font-size:12px; color: #FFF;">From</label>
                            <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                                class="form-control form-control-sm" value="{{ request('from', $from->format('d/m/Y')) }}"
                                placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <div>
                            <label style="font-size:12px; color: #FFF;">To</label>
                            <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                                class="form-control form-control-sm" value="{{ request('to', $to->format('d/m/Y')) }}"
                                placeholder="dd/mm/yyyy" autocomplete="off">
                        </div>
                        <button class="btn btn-sm btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                    <a href="{{ route('reports.cash-book.pdf', request()->query()) }}" class="btn btn-sm btn-danger" title="PDF" aria-label="PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>
            <div class="card-body px-0 pb-0 pt-0">
                <div class="px-3 pt-3 pb-2 d-flex gap-2">
                    <span class="badge"
                        style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">Cash
                        In: {{ number_format($totalIn, 2) }}</span>
                    <span class="badge"
                        style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">Cash
                        Out: {{ number_format($totalOut, 2) }}</span>
                    <span class="badge"
                        style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 14px">Balance:
                        {{ number_format($totalIn - $totalOut, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-right">Cash In</th>
                                <th class="text-right">Cash Out</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                                @php $isIn = in_array($txn->type, ['income','capital']); @endphp
                                <tr>
                                    <td>{{ $txn->transaction_date->format('d/m/Y') }}</td>
                                    <td style="font-family:monospace;font-size:11px">{{ $txn->reference_no }}</td>
                                    <td>{{ $txn->description ?? '—' }}</td>
                                    <td>
                                        @php
                                            $sc = match ($txn->type) {
                                                'income' => 'success',
                                                'expense' => 'danger',
                                                'capital' => 'primary',
                                                'withdrawal' => 'warning',
                                                default => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $sc }}">{{ ucfirst($txn->type) }}</span>
                                    </td>
                                    <td class="text-right" style="color:#16a34a">
                                        {{ $isIn ? number_format($txn->amount, 2) : '—' }}</td>
                                    <td class="text-right" style="color:#e11d48">
                                        {{ !$isIn ? number_format($txn->amount, 2) : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No cash transactions in this
                                        period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
