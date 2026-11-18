@if($rows->isEmpty())
<div class="card card-body text-center text-muted py-5">
    <i class="fas fa-users fa-3x mb-3"></i>
    <p>No students found for the selected filters.</p>
</div>
@else
@php
    $avgPct = round($rows->avg('percentage'), 1);
    $lowCount = $rows->filter(fn ($r) => $r['percentage'] < 75)->count();
    $dayMeta = [];
    foreach ($allDates as $d) {
        $key = $d->toDateString();
        $dayMeta[] = ['key' => $key, 'day' => $d->format('d'), 'dow' => $d->format('D'), 'full' => $d->format('D, d M Y'), 'off' => isset($nonWorkingDates[$key])];
    }
@endphp
<style>
    .mar-card { border: 0; border-radius: .8rem; overflow: hidden; box-shadow: 0 1px 3px rgba(15,23,42,.08), 0 10px 28px -16px rgba(15,23,42,.18); }
    .mar-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; padding: .5rem .75rem; border-bottom: 1px solid #e2e8f0; background: #fff; }
    .mar-stats { display: flex; flex-wrap: wrap; gap: .5rem; }
    .mar-stat { display: inline-flex; align-items: baseline; gap: .4rem; padding: .2rem .55rem; border-radius: .5rem; background: #f1f5f9; color: #475569; font-size: .7rem; font-weight: 600; }
    .mar-stat b { font-size: .85rem; color: #0f172a; }
    .mar-stat--good b { color: #15803d; }
    .mar-stat--bad b { color: #dc2626; }
    .mar-legend { display: flex; flex-wrap: wrap; gap: .75rem; padding: .3rem .75rem; font-size: .68rem; color: #64748b; border-bottom: 1px solid #eef2f7; }
    .mar-legend i { display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: .3rem; vertical-align: -1px; }
    .mar-scroll { max-height: 72vh; overflow: auto; -webkit-overflow-scrolling: touch; }
    .mar-table { border-collapse: separate; border-spacing: 0; width: max-content; min-width: 100%; font-size: .74rem; }
    .mar-table th, .mar-table td { padding: .22rem .2rem; text-align: center; border-bottom: 1px solid #eef2f7; background: #fff; white-space: nowrap; }
    .mar-table thead th { position: sticky; top: 0; z-index: 3; background: #f1f5f9; color: #334155; font-size: .72rem; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
    .mar-table thead th small { display: block; font-weight: 500; color: #94a3b8; font-size: .6rem; text-transform: uppercase; }
    .mar-table thead th.is-off { background: #e2e8f0; }
    .mar-table tbody tr:hover td { background: #f0f7ff; }
    .mar-table .c-roll, .mar-table .c-cid, .mar-table .c-name { position: sticky; z-index: 2; }
    .mar-table thead .c-roll, .mar-table thead .c-cid, .mar-table thead .c-name { z-index: 4; }
    .mar-table .c-roll { left: 0; min-width: 56px; max-width: 56px; }
    .mar-table .c-cid { left: 56px; min-width: 76px; max-width: 76px; }
    .mar-table .c-name { left: 132px; min-width: 190px; text-align: left; padding-left: .75rem; font-weight: 600; color: #0f172a; box-shadow: 6px 0 8px -6px rgba(15,23,42,.18); }
    .mar-table .d { min-width: 26px; }
    .mar-table .d span { display: inline-flex; align-items: center; justify-content: center; width: 20px; height: 20px; border-radius: 5px; font-size: .64rem; font-weight: 700; }
    .mar-table .d.p span { background: #dcfce7; color: #15803d; }
    .mar-table .d.a span { background: #fee2e2; color: #dc2626; }
    .mar-table td.d.off { background: #f1f5f9; }
    .mar-table .sum { font-weight: 700; min-width: 42px; }
    .mar-table .sum.p { color: #15803d; }
    .mar-table .sum.a { color: #dc2626; }
    .mar-table .c-pct { min-width: 100px; }
    .mar-pct { display: flex; align-items: center; gap: .4rem; }
    .mar-pct__track { flex: 1; height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
    .mar-pct__fill { display: block; height: 100%; border-radius: 99px; }
    .mar-pct__val { min-width: 42px; text-align: right; font-weight: 700; }
    @media (max-width: 767.98px) {
        .mar-bar { padding: .5rem; }
        .mar-bar .btn { width: 100%; }
        .mar-table .c-cid { display: none; }
        .mar-table .c-roll { min-width: 44px; max-width: 44px; }
        .mar-table .c-name { left: 44px; min-width: 130px; max-width: 130px; white-space: normal; padding-left: .5rem; font-size: .74rem; }
        .mar-scroll { max-height: 75vh; }
    }
</style>
<div class="card mar-card">
    <div class="mar-bar">
        <div class="mar-stats">
            <span class="mar-stat">Working days <b>{{ $workingDaysCount }}</b></span>
            <span class="mar-stat">Students <b>{{ $rows->count() }}</b></span>
            <span class="mar-stat mar-stat--good">Avg. attendance <b>{{ $avgPct }}%</b></span>
            <span class="mar-stat mar-stat--bad">Below 75% <b>{{ $lowCount }}</b></span>
        </div>
        <button type="button" id="btnDownloadPdf" class="btn btn-sm btn-danger py-1">
            <i class="fas fa-file-pdf mr-1"></i>Export PDF
        </button>
    </div>
    <div class="mar-legend">
        <span><i style="background:#dcfce7"></i>Present</span>
        <span><i style="background:#fee2e2"></i>Absent</span>
        <span><i style="background:#e2e8f0"></i>Weekend / holiday</span>
    </div>
    <div class="mar-scroll">
        <table class="mar-table">
            <thead>
                <tr>
                    <th class="c-roll">Roll</th>
                    <th class="c-cid">CID</th>
                    <th class="c-name">Name</th>
                    @foreach($dayMeta as $m)
                    <th class="d {{ $m['off'] ? 'is-off' : '' }}" title="{{ $m['full'] }}">{{ $m['day'] }}<small>{{ substr($m['dow'], 0, 2) }}</small></th>
                    @endforeach
                    <th class="sum p">P</th>
                    <th class="sum a">A</th>
                    <th class="c-pct">Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                @php
                    $pct = $row['percentage'];
                    $color = $pct >= 75 ? '#16a34a' : ($pct >= 50 ? '#d97706' : '#dc2626');
                @endphp
                <tr>
                    <td class="c-roll">{{ $row['roll'] ?? '-' }}</td>
                    <td class="c-cid">{{ $row['student_cid'] }}</td>
                    <td class="c-name">{{ $row['name'] }}</td>
                    @foreach($dayMeta as $m)
                        @php $cell = $row['cells'][$m['key']]; @endphp
                        @if($m['off'])
                            <td class="d off"></td>
                        @else
                            <td class="d {{ $cell === 'P' ? 'p' : 'a' }}"><span>{{ $cell }}</span></td>
                        @endif
                    @endforeach
                    <td class="sum p">{{ $row['present'] }}</td>
                    <td class="sum a">{{ $row['absent'] }}</td>
                    <td class="c-pct">
                        <div class="mar-pct">
                            <span class="mar-pct__track"><span class="mar-pct__fill" style="width:{{ min(100, $pct) }}%;background:{{ $color }}"></span></span>
                            <span class="mar-pct__val" style="color:{{ $color }}">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
