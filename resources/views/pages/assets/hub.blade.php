@extends('layouts.master')

@section('contents')
<div class="container-fluid hub-container">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 via-cyan-600 to-teal-500 p-8 mb-8">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl animate-pulse"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-emerald-500/20 blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        
        <div class="relative z-10 flex items-center gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                <i class="fas fa-building text-white text-4xl"></i>
            </div>
            <div>
                <h3 class="text-white text-3xl font-bold m-0">{{ __('Assets') }}</h3>
                <p class="text-blue-100 text-base mt-1 mb-0">{{ __('Manage assets, purchases & stock') }}</p>
            </div>
        </div>
    </div>
    
    <x-hub-card-browser :cards="$cards" storage-key="assets" default-view="medium" />
</div>
@endsection
