@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Budget vs Actual — {{ $year }}</h3>
            <form method="GET" class="d-flex gap-2 align-items-center">
                <input type="number" name="year" class="form-control form-control-sm" value="{{ $year }}" style="width:90px">
                <button class="btn btn-sm btn-dark">Go</button>
            </form>
        </div>
        <div class="card-body px-0 pb-0 pt-0">
            {{-- Summary --}}
            <div class="px-3 pt-3 pb-2 d-flex gap-2 flex-wrap">
                <span class="badge" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;font-size:12px;padding:6px 14px">
                    Total Budget: {{ number_format($totalBudget, 2) }}
                </span>
                <span class="badge" style="background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;font-size:12px;padding:6px 14px">
                    Total Actual: {{ number_format($totalActual, 2) }}
                </span>
                <span class="badge" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;font-size:12px;padding:6px 14px">
                    Remaining: {{ number_format($totalBudget - $totalActual, 2) }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th>Budget Head</th><th>Category</th><th>Period</th>
                            <th class="text-right">Budget</th>
                            <th class="text-right">Actual</th>
                            <th class="text-right">Remaining</th>
                            <th class="text-right">Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allocations as $a)
                        @php
                            $over = $a['actual'] > $a['budget'];
                            $util = $a['utilization'];
                        @endphp
                        <tr class="{{ $over ? 'table-danger' : '' }}">
                            <td class="fw-bold">{{ $a['head'] }}</td>
                            <td>{{ $a['category'] }}</td>
                            <td>{{ ucfirst($a['period']) }}</td>
                            <td class="text-right">{{ number_format($a['budget'], 2) }}</td>
                            <td class="text-right" style="color:{{ $over ? '#e11d48' : '#334155' }}">{{ number_format($a['actual'], 2) }}</td>
                            <td class="text-right" style="color:{{ $over ? '#e11d48' : '#16a34a' }}">
                                {{ $over ? '(' . number_format($a['actual'] - $a['budget'], 2) . ') Over' : number_format($a['remaining'], 2) }}
                            </td>
                            <td class="text-right">
                                <div class="progress" style="height:16px;min-width:80px">
                                    <div class="progress-bar {{ $over ? 'bg-danger' : ($util > 80 ? 'bg-warning' : 'bg-success') }}"
                                         style="width:{{ min($util, 100) }}%">
                                        {{ $util }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
