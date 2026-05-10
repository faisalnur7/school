@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-slate-800 to-slate-600 rounded-2xl p-8 mb-8 flex items-center gap-5">
        <i class="fas fa-chart-line text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Financials</h3>
            <p class="text-slate-300 text-sm mt-1 mb-0">Manage incomes, expenses & transactions</p>
        </div>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-5">
        @foreach($cards as $card)
        <a href="{{ route($card['route']) }}" class="group no-underline">
            <div class="rounded-2xl overflow-hidden shadow-md transition-all duration-200 group-hover:-translate-y-2 group-hover:shadow-xl bg-white h-full">
                <div class="flex items-center justify-center py-7"
                     style="background: linear-gradient(135deg, {{ $card['from'] }}, {{ $card['to'] }});">
                    <i class="fas {{ $card['icon'] }} text-4xl text-white"></i>
                </div>
                <div class="px-3 py-4 text-center">
                    <p class="text-slate-800 font-bold text-sm mb-1">{{ $card['title'] }}</p>
                    <p class="text-slate-400 text-xs leading-tight mb-0">{{ $card['subtitle'] }}</p>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
