@extends('layouts.master')

@section('contents')
<div class="container-fluid hub-container">
    <div class="relative mb-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-800 via-teal-700 to-sky-700 p-8 shadow-xl">
        <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="relative flex items-center gap-5">
            <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 text-white"><i class="fas fa-user-graduate text-4xl"></i></div>
            <div><h1 class="m-0 text-3xl font-bold text-white">Admission Management</h1><p class="mt-2 mb-0 text-emerald-100">Run the admission journey from application to enrollment.</p></div>
        </div>
    </div>
    <x-hub-card-browser :cards="$cards" storage-key="admission-management" default-view="medium" />
</div>
@endsection
