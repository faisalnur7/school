@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Receipt & Payment Statement</h3>
            <div class="flex gap-2 pr-3 py-2 items-end justify-center ml-auto">
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
                    <button class="btn btn-sm btn-dark" style="margin-top:18px">Filter</button>
                </form>
                <a href="{{ route('reports.receipt-payment.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:18px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row no-gutters">
                {{-- Receipts --}}
                <div class="col-md-6" style="border-right:1px solid #e2e8f0">
                    <div class="px-3 py-2" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0">
                        <strong style="color:#16a34a">Receipts</strong>
                    </div>
                    <table class="table table-sm mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr><th>Head</th><th class="text-right">Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $r)
                            <tr>
                                <td>{{ $r['head'] }}</td>
                                <td class="text-right">{{ number_format($r['amount'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">No receipts</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background:#f8fafc;font-weight:700">
                            <tr>
                                <td>Total Receipts</td>
                                <td class="text-right" style="color:#16a34a">{{ number_format($totalReceipts, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Payments --}}
                <div class="col-md-6">
                    <div class="px-3 py-2" style="background:#fff1f2;border-bottom:1px solid #fecdd3">
                        <strong style="color:#e11d48">Payments</strong>
                    </div>
                    <table class="table table-sm mb-0" style="font-size:13px">
                        <thead style="background:#f8fafc">
                            <tr><th>Head</th><th class="text-right">Amount</th></tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                            <tr>
                                <td>{{ $p['head'] }}</td>
                                <td class="text-right">{{ number_format($p['amount'], 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center text-muted py-3">No payments</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot style="background:#f8fafc;font-weight:700">
                            <tr>
                                <td>Total Payments</td>
                                <td class="text-right" style="color:#e11d48">{{ number_format($totalPayments, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Net --}}
            <div class="px-4 py-3 d-flex justify-content-end gap-3" style="border-top:2px solid #e2e8f0;background:#f8fafc">
                @php $net = $totalReceipts - $totalPayments; @endphp
                <span style="font-size:13px">Net Surplus / (Deficit):</span>
                <strong style="font-size:15px;color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }}">
                    {{ $net >= 0 ? number_format($net, 2) : '(' . number_format(abs($net), 2) . ')' }}
                </strong>
            </div>
        </div>
    </div>
</div>
@endsection
