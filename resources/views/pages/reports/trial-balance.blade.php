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
                        <label class="form-label mb-1" style="font-size:12px">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}" placeholder="Year">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-dark" title="Filter" aria-label="Filter">
                            <i class="fas fa-search"></i>
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('reports.trial-balance') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                        <a href="{{ route('reports.trial-balance.pdf', request()->query()) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header shadow p-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title text-white pl-3 mb-0 text-lg" style="font-size:15px">
                    Trial Balance - {{ $year }}
                </h3>
            </div>

            <div class="card-body">
                <div class="px-3 pb-3 d-flex flex-wrap gap-2">
                    <span class="badge" style="background:#f8fafc;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 14px">
                        Opening Balance: <strong style="color:#111827">{{ number_format($openingBalance, 2) }}</strong>
                    </span>
                    <span class="badge" style="background:#f8fafc;color:#334155;border:1px solid #e2e8f0;font-size:12px;padding:6px 14px">
                        Closing Balance: <strong style="color:#111827">{{ number_format($closingBalance, 2) }}</strong>
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th>Account</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                            <tr>
                                <td>{{ $row['account'] }}</td>
                                <td class="text-right" style="color:{{ $row['debit'] > 0 ? '#e11d48' : '#94a3b8' }}">
                                    {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '—' }}
                                </td>
                                <td class="text-right" style="color:{{ $row['credit'] > 0 ? '#16a34a' : '#94a3b8' }}">
                                    {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:#f8fafc;font-weight:700">
                            <tr>
                                <td>Total</td>
                                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                            </tr>
                            @if($totalDebit !== $totalCredit)
                            <tr>
                                <td colspan="3" class="text-center text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Note: Debit/Credit totals differ - this is a simplified view, not full double-entry.
                                </td>
                            </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
