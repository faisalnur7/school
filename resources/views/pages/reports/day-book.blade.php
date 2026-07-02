@extends('layouts.master')

@section('styles')
    @include('pages.reports.partials.filter-style')
@endsection

@section('contents')
<div class="container-fluid">
    @include('partials.report-header')

    <div class="report-toolbar">
        <form method="GET" class="supplier-dues-filters">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">Date</label>
                    <input type="text" name="date" class="form-control datepicker" value="{{ request('date', $date->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-dark" title="Filter" aria-label="Filter">
                        <i class="fas fa-search"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('reports.day-book') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                        <i class="fas fa-undo-alt"></i>
                    </a>
                    <a href="{{ route('reports.day-book.pdf', request()->query()) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Day Book - {{ $date->format('d/m/Y') }}</h3>
        </div>

        <div class="card-body">
            <div class="px-0 pb-0 pt-0">
                <div class="px-3 pt-3 pb-2 d-flex gap-2">
                    <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">Total Debit: {{ number_format($totalDebit, 2) }}</span>
                    <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">Total Credit: {{ number_format($totalCredit, 2) }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr><th>Date</th><th>Reference</th><th>Description</th><th>Type</th><th>Debit Account</th><th>Credit Account</th><th class="text-right">Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $txn)
                            <tr>
                                <td style="font-size:11px;color:#64748b">{{ $txn->created_at->format('d/m/Y') }}</td>
                                <td style="font-family:monospace;font-size:11px">{{ $txn->reference_no }}</td>
                                <td>{{ $txn->description ?? '—' }}</td>
                                <td>
                                    @php $sc = match($txn->type){ 'income'=>'success','expense'=>'danger','capital'=>'primary','withdrawal'=>'warning',default=>'secondary' }; @endphp
                                    <span class="badge badge-{{ $sc }}">{{ ucfirst($txn->type) }}</span>
                                </td>
                                <td style="color:#b91c1c">{{ $txn->debit_account_name }}</td>
                                <td style="color:#15803d">{{ $txn->credit_account_name }}</td>
                                <td class="text-right fw-bold">{{ number_format($txn->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No transactions on this date</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
