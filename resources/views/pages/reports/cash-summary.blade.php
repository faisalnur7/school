@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Cash Summary</h3>
            <div class="flex gap-2 pr-3 py-2 items-center justify-center ml-auto">
                <form method="GET" class="flex gap-2 items-end">
                    <div>
                        <label style="font-size:12px;color:#FFF">From</label>
                        <input type="text" name="from" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('from', $from->format('d/m/Y')) }}" autocomplete="off">
                    </div>
                    <div>
                        <label style="font-size:12px;color:#FFF">To</label>
                        <input type="text" name="to" datepicker datepicker-format="dd/mm/yyyy"
                               class="form-control form-control-sm" value="{{ request('to', $to->format('d/m/Y')) }}" autocomplete="off">
                    </div>
                    <button class="btn btn-sm btn-dark" style="margin-top:10px">Filter</button>
                </form>
                <a href="{{ route('reports.cash-summary.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:10px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0" style="font-size:13px">
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
