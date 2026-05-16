@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header shadow p-0 flex justify-between items-center">
            <h3 class="card-title flex text-white pl-3 text-medium">Cash Flow Statement</h3>
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
                <a href="{{ route('reports.cash-flow.pdf', request()->query()) }}" class="btn btn-sm btn-danger" style="margin-top:10px"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body" style="max-width:600px">

            @php
                $row = fn($label, $amount, $indent = false, $bold = false, $color = null) =>
                    [$label, $amount, $indent, $bold, $color];

                $sections = [
                    ['title' => 'A. Operating Activities', 'color' => '#2563eb', 'rows' => [
                        $row('Total Income Received',    $operatingIn,   false, false, '#16a34a'),
                        $row('Less: Total Expenses Paid', $operatingOut, false, false, '#e11d48'),
                        $row('Net Cash from Operations', $netOperating,  false, true,  $netOperating >= 0 ? '#16a34a' : '#e11d48'),
                    ]],
                    ['title' => 'B. Financing Activities', 'color' => '#7c3aed', 'rows' => [
                        $row('Capital Contributions',    $financingIn,   false, false, '#16a34a'),
                        $row('Less: Withdrawals',        $financingOut,  false, false, '#e11d48'),
                        $row('Net Cash from Financing',  $netFinancing,  false, true,  $netFinancing >= 0 ? '#16a34a' : '#e11d48'),
                    ]],
                ];
            @endphp

            @foreach($sections as $section)
            <div class="mb-4">
                <div class="px-3 py-2 mb-0 rounded-top" style="background:{{ $section['color'] }}1a;border-left:4px solid {{ $section['color'] }}">
                    <strong style="color:{{ $section['color'] }};font-size:13px">{{ $section['title'] }}</strong>
                </div>
                <table class="table table-sm mb-0" style="font-size:13px;border:1px solid #e2e8f0">
                    @foreach($section['rows'] as [$label, $amount, $indent, $bold, $color])
                    <tr @if($bold) style="border-top:1px solid #e2e8f0;background:#f8fafc" @endif>
                        <td @if($indent) style="padding-left:24px" @endif>
                            @if($bold)<strong>{{ $label }}</strong>@else{{ $label }}@endif
                        </td>
                        <td class="text-right" style="color:{{ $color ?? '#1e293b' }}">
                            @if($bold)<strong>{{ number_format(abs($amount), 2) }}</strong>@else{{ number_format($amount, 2) }}@endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endforeach

            {{-- Summary --}}
            <table class="table table-sm" style="font-size:13px;border:2px solid #e2e8f0">
                <tr style="background:#f8fafc">
                    <td>Opening Cash Balance</td>
                    <td class="text-right fw-bold">{{ number_format($openingCash, 2) }}</td>
                </tr>
                <tr>
                    <td>Net Change in Cash (A + B)</td>
                    <td class="text-right fw-bold" style="color:{{ $netChange >= 0 ? '#16a34a' : '#e11d48' }}">
                        {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 2) }}
                    </td>
                </tr>
                <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0">
                    <td><strong>Closing Cash Balance</strong></td>
                    <td class="text-right" style="font-size:16px;font-weight:700;color:{{ $closingCash >= 0 ? '#16a34a' : '#e11d48' }}">
                        {{ number_format($closingCash, 2) }}
                    </td>
                </tr>
            </table>

        </div>
    </div>
</div>
@endsection
