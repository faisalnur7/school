@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Income & Expenditure — {{ $year }}</h3>
            <div class="flex gap-2 pr-3 py-2 items-end justify-center ml-auto">
                <form method="GET" class="flex gap-2 items-end">
                    <div>
                        <label style="font-size:12px;color:#FFF">Year</label>
                        <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:120px">
                    </div>
                    <button class="btn btn-sm btn-dark" style="margin-top:18px">Go</button>
                </form>
                <a href="{{ route('reports.income-expenditure.pdf', ['year' => $year]) }}" class="btn btn-sm btn-danger" style="margin-top:18px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Income --}}
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
                {{-- Expenditure --}}
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

            {{-- Surplus / Deficit --}}
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
