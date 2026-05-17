@php
    $fmt = fn($v) => $v > 0 ? number_format($v, 2) : '—';
    $begTotDr = collect($rows)->sum('beg_debit');
    $begTotCr = collect($rows)->sum('beg_credit');
    $perTotDr = collect($rows)->sum('per_debit');
    $perTotCr = collect($rows)->sum('per_credit');
    $endTotDr = collect($rows)->sum(fn($r) => isset($r['balance_only']) ? $r['balance_only'] : ($r['beg_debit'] + $r['per_debit']));
    $endTotCr = collect($rows)->sum(fn($r) => isset($r['balance_only']) ? 0 : ($r['beg_credit'] + $r['per_credit']));
    $content = '';
    ob_start();
@endphp
<table>
    <thead>
        <tr style="background:#1e293b;color:#fff">
            <th rowspan="2" style="vertical-align:middle;width:32%">Account</th>
            <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Beginning Balance</th>
            <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Period Activity</th>
            <th colspan="2" class="text-center" style="border-bottom:1px solid #334155">Ending Balance</th>
        </tr>
        <tr style="background:#334155;color:#fff">
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
        @php
            $endDr = isset($row['balance_only']) ? $row['balance_only'] : ($row['beg_debit'] + $row['per_debit']);
            $endCr = isset($row['balance_only']) ? 0 : ($row['beg_credit'] + $row['per_credit']);
        @endphp
        <tr>
            <td>{{ $row['account'] }}</td>
            <td class="text-right {{ isset($row['balance_only']) ? 'muted' : ($row['beg_debit'] > 0 ? 'red' : 'muted') }}">
                {{ isset($row['balance_only']) ? '—' : $fmt($row['beg_debit']) }}
            </td>
            <td class="text-right {{ isset($row['balance_only']) ? 'muted' : ($row['beg_credit'] > 0 ? 'green' : 'muted') }}">
                {{ isset($row['balance_only']) ? '—' : $fmt($row['beg_credit']) }}
            </td>
            <td class="text-right {{ isset($row['balance_only']) ? 'muted' : ($row['per_debit'] > 0 ? 'red' : 'muted') }}">
                {{ isset($row['balance_only']) ? '—' : $fmt($row['per_debit']) }}
            </td>
            <td class="text-right {{ isset($row['balance_only']) ? 'muted' : ($row['per_credit'] > 0 ? 'green' : 'muted') }}">
                {{ isset($row['balance_only']) ? '—' : $fmt($row['per_credit']) }}
            </td>
            <td class="text-right {{ $endDr > 0 ? 'red' : 'muted' }}">{{ $fmt($endDr) }}</td>
            <td class="text-right {{ $endCr > 0 ? 'green' : 'muted' }}">{{ $fmt($endCr) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="bold">Total</td>
            <td class="text-right bold red">{{ number_format($begTotDr, 2) }}</td>
            <td class="text-right bold green">{{ number_format($begTotCr, 2) }}</td>
            <td class="text-right bold red">{{ number_format($perTotDr, 2) }}</td>
            <td class="text-right bold green">{{ number_format($perTotCr, 2) }}</td>
            <td class="text-right bold red">{{ number_format($endTotDr, 2) }}</td>
            <td class="text-right bold green">{{ number_format($endTotCr, 2) }}</td>
        </tr>
    </tfoot>
</table>
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', [
    'title'    => 'Detailed Trial Balance',
    'subtitle' => \Carbon\Carbon::parse($from)->format('d M Y') . ' — ' . \Carbon\Carbon::parse($to)->format('d M Y'),
])
