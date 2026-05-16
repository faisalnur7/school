@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Chart of Accounts</h3>
            <div class="flex gap-2 pr-3 py-2 items-center justify-center ml-auto">
                <a href="{{ route('reports.chart-of-accounts.pdf') }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($groups as $group)
            <div class="px-3 py-2" style="background:#f1f5f9;border-bottom:1px solid #e2e8f0">
                <strong style="font-size:13px;color:#334155">{{ $group->name }}</strong>
                @if($group->parent)
                    <small class="text-muted ml-2">under {{ $group->parent->name }}</small>
                @endif
            </div>
            @if($group->accounts->count())
            <table class="table table-sm mb-0" style="font-size:13px">
                <thead style="background:#f8fafc">
                    <tr>
                        <th style="padding-left:24px">Account Name</th>
                        <th>Linked To</th>
                        <th class="text-right">Opening Balance</th>
                        <th class="text-right">Total Debit</th>
                        <th class="text-right">Total Credit</th>
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
                        <td style="padding-left:24px">{{ $acc->name }}</td>
                        <td style="color:#64748b;font-size:12px">{{ $acc->reference_label }}</td>
                        <td class="text-right">{{ number_format($opening, 2) }}</td>
                        <td class="text-right" style="color:#e11d48">{{ $debit > 0 ? number_format($debit, 2) : '—' }}</td>
                        <td class="text-right" style="color:#16a34a">{{ $credit > 0 ? number_format($credit, 2) : '—' }}</td>
                        <td class="text-right fw-bold" style="color:{{ $net >= 0 ? '#16a34a' : '#e11d48' }}">
                            {{ number_format($net, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="px-4 py-2 text-muted" style="font-size:12px">No accounts in this group</div>
            @endif
            @empty
            <div class="text-center text-muted py-5">No account groups found. Set up your Chart of Accounts first.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
