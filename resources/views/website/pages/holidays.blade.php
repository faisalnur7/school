@extends('website.layouts.app')
@section('content')

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: linear-gradient(135deg,#f59e0b,#ea580c,#dc2626);">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            Holiday List
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'School Holidays' }}</h1>
        <p class="mt-2 text-base text-orange-100 lg:text-lg">{{ $page?->excerpt ?? 'Important school holiday dates and closures.' }}</p>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-lg">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Day</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($holidays as $holiday)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $holiday->date->format('d M Y') }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $holiday->date->format('D') }}</td>
                        <td class="px-5 py-4 text-slate-900">{{ $holiday->title ?: '-' }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ $holiday->description ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-slate-500">No holidays found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($holidays->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $holidays->links() }}
    </div>
@endif
@endsection
