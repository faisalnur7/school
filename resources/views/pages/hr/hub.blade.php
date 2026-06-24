@extends('layouts.master')

@section('contents')
<div class="container-fluid hub-container">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-800 via-slate-700 to-zinc-700 p-8 mb-8">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl animate-pulse"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-blue-500/20 blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        
        <div class="relative z-10 flex items-center gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                <i class="fas fa-user-tie text-white text-4xl"></i>
            </div>
            <div>
                <h3 class="text-white text-3xl font-bold m-0">HR & Payroll</h3>
                <p class="text-slate-300 text-base mt-1 mb-0">Manage employees, payroll & leave</p>
            </div>
        </div>
    </div>
    
    <x-hub-card-browser :cards="$cards" storage-key="hr" default-view="medium" />
</div>
@endsection
