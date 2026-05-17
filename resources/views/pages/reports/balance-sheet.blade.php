@extends('layouts.master')
@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header shadow p-0 flex justify-between items-center">
                <h3 class="card-title flex text-white pl-3 text-medium">Balance Sheet — {{ $year }}</h3>
                <div class="flex gap-2 pr-3 py-2 items-center justify-center ml-auto">
                    <form method="GET" class="flex gap-2 items-end">
                        <div>
                            <label style="font-size:12px;color:#FFF">Year</label>
                            <input type="number" name="year" class="form-control form-control-sm"
                                value="{{ $year }}" style="width:120px">
                        </div>
                        <button class="btn btn-sm btn-dark" style="margin-top:10px">Go</button>
                    </form>
                    <a href="{{ route('reports.balance-sheet.pdf', ['year' => $year]) }}" class="btn btn-sm btn-danger"
                        style="margin-top:10px"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Equity Side --}}
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
                    {{-- Summary --}}
                    <div class="col-md-6">
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
