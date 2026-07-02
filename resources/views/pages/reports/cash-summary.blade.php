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
                    <label class="form-label mb-1" style="font-size:12px">From</label>
                    <input type="text" name="from" class="form-control datepicker" value="{{ request('from', $from->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">To</label>
                    <input type="text" name="to" class="form-control datepicker" value="{{ request('to', $to->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" autocomplete="off">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-dark" type="submit" title="Filter" aria-label="Filter">
                        <i class="fas fa-search"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('reports.cash-summary') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                        <i class="fas fa-undo-alt"></i>
                    </a>
                    <a href="{{ route('reports.cash-summary.pdf', request()->query()) }}" class="btn btn-danger" title="PDF" aria-label="PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Cash Summary</h3>
        </div>

        <div class="card-body">
            <table class="table mb-0" style="font-size:13px">
                <thead style="background:#f8fafc">
                    <tr>
                        <th>Account</th>
                        <th class="text-right">Opening Balance</th>
                        <th class="text-right">Total In (Credit)</th>
                        <th class="text-right">Total Out (Debit)</th>
                        <th class="text-right">Closing Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                    <tr>
                        <td class="fw-bold">{{ $acc['label'] }}</td>
                        <td class="text-right">{{ number_format($acc['openingBalance'], 2) }}</td>
                        <td class="text-right" style="color:#16a34a">{{ number_format($acc['totalIn'], 2) }}</td>
                        <td class="text-right" style="color:#e11d48">{{ number_format($acc['totalOut'], 2) }}</td>
                        <td class="text-right fw-bold" style="color:{{ $acc['closingBalance'] >= 0 ? '#16a34a' : '#e11d48' }}">
                            {{ number_format($acc['closingBalance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No active accounts found</td></tr>
                    @endforelse
                </tbody>
                @if($accounts->count())
                <tfoot style="background:#f8fafc;font-weight:700">
                    <tr>
                        <td>Total</td>
                        <td class="text-right">{{ number_format($accounts->sum('openingBalance'), 2) }}</td>
                        <td class="text-right" style="color:#16a34a">{{ number_format($accounts->sum('totalIn'), 2) }}</td>
                        <td class="text-right" style="color:#e11d48">{{ number_format($accounts->sum('totalOut'), 2) }}</td>
                        <td class="text-right" style="color:{{ $accounts->sum('closingBalance') >= 0 ? '#16a34a' : '#e11d48' }}">
                            {{ number_format($accounts->sum('closingBalance'), 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
