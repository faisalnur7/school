@extends('layouts.master')
@section('styles')
    <style>
        .trial-balance-table tfoot {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 700;
        }

        .trial-balance-table tfoot td {
            background: transparent;
            color: inherit;
            border-top: 2px solid #cbd5e1;
        }

        html[data-theme='dark'] .trial-balance-table tfoot {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .trial-balance-table tfoot td {
            border-top-color: rgba(148, 163, 184, 0.28);
        }
    </style>
@endsection
@section('contents')
    @php
        $fmt = fn($v) => $v > 0 ? number_format($v, 2) : '—';
        $begTotDr = collect($rows)->sum('beg_debit');
        $begTotCr = collect($rows)->sum('beg_credit');
        $perTotDr = collect($rows)->sum('per_debit');
        $perTotCr = collect($rows)->sum('per_credit');
        $endTotDr = collect($rows)->sum(
            fn($r) => isset($r['balance_only']) ? $r['balance_only'] : $r['beg_debit'] + $r['per_debit'],
        );
        $endTotCr = collect($rows)->sum(fn($r) => isset($r['balance_only']) ? 0 : $r['beg_credit'] + $r['per_credit']);
    @endphp
    <div class="container-fluid">
    @include('partials.report-header')

        <div class="card">
            <div class="card-header shadow p-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h3 class="card-title text-white pl-3 mb-0 text-lg" style="font-size:15px">
                    Detailed Trial Balance &mdash; {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to
                    {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
                </h3>
                <div class="d-flex align-items-center flex-wrap gap-2 pr-3 py-2 ml-auto">
                    <form method="GET" class="flex flex-rows align-items-center gap-2 mb-0">
                        <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}"
                            style="width:160px">

                        <span class="text-white small">to</span>

                        <input type="date" name="to" class="form-control form-control-sm"
                            value="{{ $to }}" style="width:160px">

                        <button type="submit" class="btn btn-sm btn-dark">
                            Go
                        </button>

                        <a href="{{ route('reports.details-trial-balance.pdf', ['from' => $from, 'to' => $to]) }}"
                            class="btn btn-sm btn-danger flex gap-1 justify-center items-center">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 trial-balance-table" style="font-size:12px;min-width:800px">
                        <thead style="background:#1e293b;color:#fff">
                            <tr>
                                <th rowspan="2" style="vertical-align:middle;width:30%">Account</th>
                                <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Beginning
                                    Balance</th>
                                <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Period
                                    Activity</th>
                                <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Ending
                                    Balance</th>
                            </tr>
                            <tr style="background:#334155">
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                @php
                                    $endDr = isset($row['balance_only'])
                                        ? $row['balance_only']
                                        : $row['beg_debit'] + $row['per_debit'];
                                    $endCr = isset($row['balance_only']) ? 0 : $row['beg_credit'] + $row['per_credit'];
                                @endphp
                                <tr>
                                    <td>{{ $row['account'] }}</td>
                                    {{-- Beginning --}}
                                    <td class="text-right"
                                        style="color:{{ isset($row['balance_only']) ? '#94a3b8' : ($row['beg_debit'] > 0 ? '#e11d48' : '#94a3b8') }}">
                                        {{ isset($row['balance_only']) ? '—' : $fmt($row['beg_debit']) }}
                                    </td>
                                    <td class="text-right"
                                        style="color:{{ isset($row['balance_only']) ? '#94a3b8' : ($row['beg_credit'] > 0 ? '#16a34a' : '#94a3b8') }}">
                                        {{ isset($row['balance_only']) ? '—' : $fmt($row['beg_credit']) }}
                                    </td>
                                    {{-- Period --}}
                                    <td class="text-right"
                                        style="color:{{ isset($row['balance_only']) ? '#94a3b8' : ($row['per_debit'] > 0 ? '#e11d48' : '#94a3b8') }}">
                                        {{ isset($row['balance_only']) ? '—' : $fmt($row['per_debit']) }}
                                    </td>
                                    <td class="text-right"
                                        style="color:{{ isset($row['balance_only']) ? '#94a3b8' : ($row['per_credit'] > 0 ? '#16a34a' : '#94a3b8') }}">
                                        {{ isset($row['balance_only']) ? '—' : $fmt($row['per_credit']) }}
                                    </td>
                                    {{-- Ending --}}
                                    <td class="text-right" style="color:{{ $endDr > 0 ? '#e11d48' : '#94a3b8' }}">
                                        {{ $fmt($endDr) }}
                                    </td>
                                    <td class="text-right" style="color:{{ $endCr > 0 ? '#16a34a' : '#94a3b8' }}">
                                        {{ $fmt($endCr) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td class="text-right">{{ number_format($begTotDr, 2) }}</td>
                                <td class="text-right">{{ number_format($begTotCr, 2) }}</td>
                                <td class="text-right">{{ number_format($perTotDr, 2) }}</td>
                                <td class="text-right">{{ number_format($perTotCr, 2) }}</td>
                                <td class="text-right">{{ number_format($endTotDr, 2) }}</td>
                                <td class="text-right">{{ number_format($endTotCr, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
