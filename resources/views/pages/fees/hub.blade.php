@extends('layouts.master')

@section('contents')
<div class="container-fluid hub-container">
    {{-- Modern Hero Section with Animated Background --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-amber-600 via-orange-600 to-red-500 p-8 mb-8">
        {{-- Animated Decorations --}}
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl animate-pulse"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-yellow-500/20 blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        
        <div class="relative z-10 flex items-center gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                <i class="fas fa-money-bill text-white text-4xl"></i>
            </div>
            <div>
                <h3 class="text-white text-3xl font-bold m-0">Fees Management</h3>
                <p class="text-amber-100 text-base mt-1 mb-0">Manage fees, payments & collections</p>
            </div>
        </div>
    </div>

    {{-- Sections with Cards --}}
    @foreach($sections as $sectionName => $cards)
    <div class="mb-10">
        {{-- Modern Card Grid --}}
        <div class="hub-grid">
            @foreach($cards as $card)
            <a href="{{ route($card['route'], $card['params'] ?? []) }}" class="group no-underline">
                <div class="hub-card relative overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl h-full">
                    {{-- Gradient Overlay on Hover --}}
                    <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                         style="background: linear-gradient(135deg, {{ $card['from'] }}20, {{ $card['to'] }}20);"></div>
                    
                    {{-- Icon Section --}}
                    <div class="flex items-center justify-center py-6 relative z-10"
                         style="background: linear-gradient(135deg, {{ $card['from'] }}, {{ $card['to'] }});">
                        <i class="fas {{ $card['icon'] }} text-3xl text-white transition-transform duration-300 group-hover:scale-125"></i>
                    </div>
                    
                    {{-- Text Section --}}
                    <div class="px-4 py-4 text-center relative z-10">
                        <p class="text-slate-800 font-bold text-sm mb-1">{{ $card['title'] }}</p>
                        <p class="text-slate-500 text-xs leading-tight mb-0">{{ $card['subtitle'] }}</p>
                    </div>
                    
                    {{-- Arrow Indicator --}}
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 group-hover:translate-x-1">
                        <i class="fas fa-arrow-right text-slate-400 text-xs"></i>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
