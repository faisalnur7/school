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
                        <a href="{{ route('reports.balance-sheet') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                        <a href="{{ route('reports.balance-sheet.pdf', ['year' => $year]) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header shadow p-0 flex justify-between items-center">
                <h3 class="card-title flex text-white pl-3 text-medium">Balance Sheet &mdash; {{ $year }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <div style="font-size:11px;color:#64748b">Opening Balance</div>
                            <div style="font-size:18px;font-weight:700;color:#111827">{{ number_format($openingBalance, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                            <div style="font-size:11px;color:#64748b">Closing Balance</div>
                            <div style="font-size:18px;font-weight:700;color:#111827">{{ number_format($closingBalance, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                            Liabilities</h5>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td>Accounts Payable - Suppliers</td>
                                <td class="text-right fw-bold" style="color:#e11d48">
                                    {{ number_format((float) $supplierLiability, 2) }}
                                </td>
                            </tr>
                            <tr style="border-top:2px solid #e2e8f0">
                                <td class="fw-bold">Total Liabilities</td>
                                <td class="text-right fw-bold" style="font-size:15px;color:#e11d48">
                                    {{ number_format((float) $totalLiabilities, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                            Equity</h5>
                        <table class="table table-sm mb-0">
                            <tr>
                                <td>Capital Contributions</td>
                                <td class="text-right fw-bold" style="color:#16a34a">{{ number_format($capital, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Add: Net Profit / (Loss)</td>
                                <td class="text-right" style="color:{{ $netIncome >= 0 ? '#16a34a' : '#e11d48' }}">
                                    {{ $netIncome >= 0 ? number_format($netIncome, 2) : '(' . number_format(abs($netIncome), 2) . ')' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Less: Withdrawals</td>
                                <td class="text-right" style="color:#e11d48">({{ number_format($withdrawals, 2) }})</td>
                            </tr>
                            <tr style="border-top:2px solid #e2e8f0">
                                <td class="fw-bold">Total Equity</td>
                                <td class="text-right fw-bold"
                                    style="font-size:15px;color:{{ $equity >= 0 ? '#16a34a' : '#e11d48' }}">
                                    {{ number_format($equity, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12 mt-4">
                        <h5 class="text-muted mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:1px">
                            Summary</h5>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div style="font-size:11px;color:#64748b">Total Income</div>
                                <div style="font-size:18px;font-weight:700;color:#16a34a">{{ number_format($totalIncome, 2) }}</div>
                            </div>
                            <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div style="font-size:11px;color:#64748b">Total Expenses</div>
                                <div style="font-size:18px;font-weight:700;color:#e11d48">{{ number_format($totalExpense, 2) }}</div>
                            </div>
                            <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0">
                                <div style="font-size:11px;color:#64748b">Net Profit / (Loss)</div>
                                <div style="font-size:18px;font-weight:700;color:{{ $netIncome >= 0 ? '#16a34a' : '#e11d48' }}">
                                    {{ $netIncome >= 0 ? number_format($netIncome, 2) : '(' . number_format(abs($netIncome), 2) . ')' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
