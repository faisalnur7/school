@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Trial Balance — {{ $year }}</h3>
            <div class="flex gap-2 pr-3 pt-3 items-center justify-center ml-auto">
                <form method="GET" class="flex gap-2 items-center">
                    <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:190px">
                    <button class="btn btn-sm btn-dark">Go</button>
                </form>
                <a href="{{ route('reports.trial-balance.pdf', ['year' => $year]) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0" style="font-size:13px">
                <thead style="background:#f8fafc">
                    <tr><th>Account</th><th class="text-right">Debit</th><th class="text-right">Credit</th></tr>
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
                            Note: Debit/Credit totals differ — this is a simplified view, not full double-entry.
                        </td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
