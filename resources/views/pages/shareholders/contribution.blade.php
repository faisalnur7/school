@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-600 via-cyan-500 to-sky-400 p-8 mb-8">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl animate-pulse"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-white/10 blur-3xl animate-pulse" style="animation-delay:1s"></div>
        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                    <i class="fas fa-hand-holding-usd text-white text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-white text-3xl font-bold m-0">Shareholder Contribution</h3>
                    <p class="text-cyan-100 text-base mt-1 mb-0">Capital invested & ownership percentage</p>
                </div>
            </div>
            <a href="{{ route('shareholders.contribution.pdf') }}"
               class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white font-semibold px-5 py-2.5 rounded-xl backdrop-blur-sm transition">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3 text-slate-600 font-semibold">#</th>
                    <th class="text-left px-5 py-3 text-slate-600 font-semibold">Shareholder Name</th>
                    <th class="text-right px-5 py-3 text-slate-600 font-semibold">Investment (৳)</th>
                    <th class="text-right px-5 py-3 text-slate-600 font-semibold">Share (%)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shareholders as $i => $s)
                @php $pct = $totalCapital > 0 ? ($s['capital'] / $totalCapital) * 100 : 0; @endphp
                <tr class="border-b border-slate-100 hover:bg-cyan-50 transition-colors">
                    <td class="px-5 py-3 text-slate-400">{{ $i + 1 }}</td>
                    <td class="px-5 py-3 font-medium text-slate-800">{{ $s['name'] }}</td>
                    <td class="px-5 py-3 text-right text-slate-700">{{ number_format($s['capital'], 2) }}</td>
                    <td class="px-5 py-3 text-right">
                        <span class="inline-block bg-cyan-100 text-cyan-700 font-semibold px-2.5 py-0.5 rounded-full text-xs">
                            {{ number_format($pct, 1) }}%
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-slate-400">No shareholders found.</td>
                </tr>
                @endforelse
            </tbody>
            @if($shareholders->isNotEmpty())
            <tfoot>
                <tr class="bg-slate-50 border-t-2 border-slate-300">
                    <td colspan="2" class="px-5 py-3 font-bold text-slate-700">Total</td>
                    <td class="px-5 py-3 text-right font-bold text-slate-800">{{ number_format($totalCapital, 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold text-cyan-700">100.0%</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
