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
                        <a href="{{ route('reports.income-expenditure') }}" class="btn btn-light" title="Reset" aria-label="Reset">
                            <i class="fas fa-undo-alt"></i>
                        </a>
                        <a href="{{ route('reports.income-expenditure.pdf', array_merge(request()->query(), ['from' => $from->format('d/m/Y'), 'to' => $to->format('d/m/Y')])) }}" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header shadow p-0 flex justify-between items-center">
                <h3 class="card-title flex text-white pl-3 text-medium">
                    Income & Expenditure - {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 style="color:#16a34a;font-size:13px;text-transform:uppercase;letter-spacing:1px">Income</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
                            <tbody>
                                @foreach($incomeByCategory as $row)
                                <tr><td>{{ $row['name'] }}</td><td class="text-right">{{ number_format($row['amount'], 2) }}</td></tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:700;border-top:2px solid #e2e8f0">
                                    <td>Total Income</td>
                                    <td class="text-right" style="color:#16a34a">{{ number_format($totalIncome, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 style="color:#e11d48;font-size:13px;text-transform:uppercase;letter-spacing:1px">Expenditure</h5>
                        <table class="table table-sm">
                            <thead><tr><th>Category</th><th class="text-right">Amount</th></tr></thead>
                            <tbody>
                                @foreach($expenseByCategory as $row)
                                <tr><td>{{ $row['name'] }}</td><td class="text-right">{{ number_format($row['amount'], 2) }}</td></tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="font-weight:700;border-top:2px solid #e2e8f0">
                                    <td>Total Expenditure</td>
                                    <td class="text-right" style="color:#e11d48">{{ number_format($totalExpense, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mt-3 p-3 rounded text-center" style="background:{{ $surplus >= 0 ? '#f0fdf4' : '#fff1f2' }};border:1px solid {{ $surplus >= 0 ? '#bbf7d0' : '#fecdd3' }}">
                    <div style="font-size:12px;color:#64748b">{{ $surplus >= 0 ? 'Surplus' : 'Deficit' }}</div>
                    <div style="font-size:24px;font-weight:700;color:{{ $surplus >= 0 ? '#16a34a' : '#e11d48' }}">
                        {{ number_format(abs($surplus), 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
