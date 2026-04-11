@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Budget vs Actual — {{ $year }}</h3>
            <div class="flex gap-2 pr-3 py-2 items-end ml-auto">
                <form method="GET" class="flex gap-2 items-end">
                    <div>
                        <label style="font-size:12px;color:#FFF">Year</label>
                        <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:100px">
                    </div>
                    <button class="btn btn-sm btn-dark" style="margin-top:18px">Go</button>
                </form>
                <a href="{{ route('budget-allocations.report.pdf', ['year' => $year]) }}"
                   class="btn btn-sm btn-danger" style="margin-top:18px">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
            </div>
        </div>

        <div class="card-body px-0 pb-0 pt-0">

            {{-- Summary Cards --}}
            <div class="px-3 pt-3 pb-2 d-flex flex-wrap gap-2">
                <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:12px;padding:6px 14px">
                    Total Budget: {{ number_format($totalBudget, 2) }}
                </span>
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">
                    Total Actual: {{ number_format($totalActual, 2) }}
                </span>
                @php $remaining = $totalBudget - $totalActual; @endphp
                <span class="badge" style="background:{{ $remaining >= 0 ? '#f0fdf4' : '#fff1f2' }};color:{{ $remaining >= 0 ? '#16a34a' : '#e11d48' }};border:1px solid {{ $remaining >= 0 ? '#bbf7d0' : '#fecdd3' }};font-size:12px;padding:6px 14px">
                    {{ $remaining >= 0 ? 'Remaining' : 'Over Budget' }}: {{ number_format(abs($remaining), 2) }}
                </span>
                @if($overCount > 0)
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">
                    <i class="fas fa-exclamation-triangle"></i> {{ $overCount }} Over-budget line(s)
                </span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th>Account</th>
                            <th>Group</th>
                            <th>Category</th>
                            <th>Period</th>
                            <th class="text-right">Budget</th>
                            <th class="text-right">Actual</th>
                            <th class="text-right">Remaining</th>
                            <th style="min-width:120px">Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allocations as $a)
                        @php $over = $a['actual'] > $a['budget']; @endphp
                        <tr style="{{ $over ? 'background:#fff1f2' : '' }}">
                            <td class="fw-bold">{{ $a['account'] }}</td>
                            <td style="color:#64748b;font-size:12px">{{ $a['group'] }}</td>
                            <td style="font-size:12px">{{ $a['category'] }}</td>
                            <td>
                                <span class="badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;font-size:11px">
                                    {{ ucfirst($a['period']) }}{{ $a['month'] ? ' / ' . $a['month'] : '' }}
                                </span>
                            </td>
                            <td class="text-right" style="color:#2563eb;font-weight:600">{{ number_format($a['budget'], 2) }}</td>
                            <td class="text-right" style="color:{{ $over ? '#e11d48' : '#334155' }};font-weight:600">
                                {{ number_format($a['actual'], 2) }}
                            </td>
                            <td class="text-right" style="color:{{ $over ? '#e11d48' : '#16a34a' }};font-weight:600">
                                @if($over)
                                    <span title="Over budget">({{ number_format($a['actual'] - $a['budget'], 2) }})</span>
                                @else
                                    {{ number_format($a['remaining'], 2) }}
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:14px">
                                        <div class="progress-bar {{ $over ? 'bg-danger' : ($a['utilization'] > 80 ? 'bg-warning' : 'bg-success') }}"
                                             style="width:{{ min($a['utilization'], 100) }}%"></div>
                                    </div>
                                    <small style="white-space:nowrap;color:{{ $over ? '#e11d48' : '#475569' }}">
                                        {{ $a['utilization'] }}%
                                    </small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">No budget allocations for {{ $year }}</td></tr>
                        @endforelse
                    </tbody>
                    @if($allocations->count())
                    <tfoot style="background:#f8fafc;font-weight:700">
                        <tr>
                            <td colspan="4">Total</td>
                            <td class="text-right" style="color:#2563eb">{{ number_format($totalBudget, 2) }}</td>
                            <td class="text-right" style="color:{{ $totalActual > $totalBudget ? '#e11d48' : '#334155' }}">{{ number_format($totalActual, 2) }}</td>
                            <td class="text-right" style="color:{{ $remaining >= 0 ? '#16a34a' : '#e11d48' }}">
                                {{ $remaining >= 0 ? number_format($remaining, 2) : '(' . number_format(abs($remaining), 2) . ')' }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
