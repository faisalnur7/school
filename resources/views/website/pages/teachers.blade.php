@extends('website.layouts.app')
@section('content')

{{-- Page Header --}}
<div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-sky-600 via-indigo-700 to-purple-700 px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14">
    {{-- Background Decorations --}}
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl"></div>
    
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Faculty
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">Our Teachers</h1>
        <p class="mt-2 text-base text-sky-100 lg:text-lg">Meet our dedicated teaching staff</p>
    </div>
</div>

{{-- Teachers Grid --}}
<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    @forelse($teachers as $teacher)
        @php
            $deptObj = $teacher->relationLoaded('department') && $teacher->department ? $teacher->department : null;
            $deptName = $deptObj && is_object($deptObj) ? $deptObj->name : null;
            $desigObj = $teacher->relationLoaded('designation') && $teacher->designation ? $teacher->designation : null;
            $desigName = $desigObj && is_object($desigObj) ? $desigObj->name : 'Teacher';
        @endphp
        <a href="{{ route('website.teacher.show', $teacher) }}" class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-500/0 to-indigo-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
            
            <div class="relative">
                <div class="relative mx-auto mb-4 inline-block">
                    @if($teacher->photo_url)
                        <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-md transition-transform duration-300 group-hover:scale-110">
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-md">
                            <span class="text-3xl font-bold">{{ substr($teacher->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-green-600 text-white shadow-lg">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <h3 class="font-bold text-slate-900 transition-colors group-hover:text-indigo-700">{{ $teacher->name }}</h3>
                <p class="mt-1 text-sm font-semibold text-indigo-600">{{ $desigName }}</p>
                @if($deptName)
                    <p class="mt-1 text-xs text-slate-500">{{ $deptName }}</p>
                @endif
                <div class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 opacity-0 transition-all duration-300 group-hover:opacity-100">
                    View Profile
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 0V2.5a1 1 0 00-1-1h-2a1 1 0 00-1 1v1.854a4 4 0 110 0V17a1 1 0 102 0V4.354z"></path></svg>
            </div>
            <p class="text-lg font-medium text-slate-600">No teachers found.</p>
            <p class="text-sm text-slate-500">Please check back later!</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($teachers->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $teachers->links() }}
    </div>
@endif
@endsection