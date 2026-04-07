@php $content = ''; ob_start(); @endphp
@forelse($groups as $group)
<div class="section-title" style="margin-top:{{ $loop->first ? '0' : '10px' }}">
    {{ $group->name }}@if($group->parent) <span style="font-weight:400;color:#64748b;font-size:10px"> — under {{ $group->parent->name }}</span>@endif
</div>
@if($group->accounts->count())
<table>
    <thead>
        <tr>
            <th>Account Name</th>
            <th>Linked To</th>
            <th class="text-right">Opening</th>
            <th class="text-right">Debit</th>
            <th class="text-right">Credit</th>
            <th class="text-right">Net Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group->accounts as $acc)
        @php
            $debit   = $acc->journalLines->sum('debit');
            $credit  = $acc->journalLines->sum('credit');
            $opening = (float) ($acc->opening_balance ?? 0);
            $net     = $acc->balance;
        @endphp
        <tr>
            <td>{{ $acc->name }}</td>
            <td style="color:#64748b;font-size:10px">{{ $acc->reference_label }}</td>
            <td class="text-right">{{ number_format($opening, 2) }}</td>
            <td class="text-right red">{{ $debit > 0 ? number_format($debit, 2) : '—' }}</td>
            <td class="text-right green">{{ $credit > 0 ? number_format($credit, 2) : '—' }}</td>
            <td class="text-right bold {{ $net >= 0 ? 'green' : 'red' }}">{{ number_format($net, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p style="padding:4px 8px;color:#94a3b8;font-size:10px">No accounts in this group</p>
@endif
@empty
<p class="text-center muted">No account groups found.</p>
@endforelse
@php $content = ob_get_clean(); @endphp
@include('pages.reports.pdf.layout', ['title' => 'Chart of Accounts', 'subtitle' => 'Generated: ' . now()->format('d M Y')])
