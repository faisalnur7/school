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
                <div class="col-md-3">
                    <label class="form-label mb-1" style="font-size:12px">Report Type</label>
                    <select name="report_type" class="form-control">
                        <option value="summary" {{ request('report_type', 'summary') === 'summary' ? 'selected' : '' }}>Summary</option>
                        <option value="detailed" {{ request('report_type') === 'detailed' ? 'selected' : '' }}>Detailed</option>
                    </select>
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr>
                                @if($reportType === 'summary')
                                    <th>Category / Head</th><th class="text-right">Debit</th><th class="text-right">Credit</th>
                                @else
                                    <th>Date</th><th>Reference</th><th>Type</th><th>Description</th><th class="text-right">Debit</th><th class="text-right">Credit</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if($reportType === 'summary')
                                <tr style="background:#eff6ff;font-weight:700">
                                    <td>Opening Balance</td>
                                    <td class="text-right">{{ number_format(abs($openingBalance), 2) }}</td>
                                    <td class="text-right">{{ number_format(abs($openingBalance), 2) }}</td>
                                </tr>
                                @forelse($summaryRows as $group)
                                    <tr>
                                        <td>{{ $group['label'] }}</td>
                                        <td class="text-right">{{ number_format($group['totalDebit'], 2) }}</td>
                                        <td class="text-right">{{ number_format($group['totalCredit'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">No transactions on this date</td></tr>
                                @endforelse
                                <tr style="background:#eff6ff;font-weight:700">
                                    <td>Closing Balance</td>
                                    <td class="text-right">{{ number_format(abs($closingBalance), 2) }}</td>
                                    <td class="text-right">{{ number_format(abs($closingBalance), 2) }}</td>
                                </tr>
                            @else
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_date?->format('d/m/Y') }}</td>
                                        <td>{{ $transaction->reference_no ?? '-' }}</td>
                                        <td>{{ ucfirst($transaction->type) }}</td>
                                        <td>{{ $transaction->description ?: '-' }}</td>
                                        <td class="text-right">{{ in_array($transaction->type, ['expense', 'withdrawal']) ? number_format($transaction->amount, 2) : '0.00' }}</td>
                                        <td class="text-right">{{ in_array($transaction->type, ['income', 'capital']) ? number_format($transaction->amount, 2) : '0.00' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">No transactions on this date</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
