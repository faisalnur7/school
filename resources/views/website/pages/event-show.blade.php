@extends('website.layouts.app')
@section('content')

@php
    $heroImage = !empty($event->image) ? 'url('.asset($event->image).') center/cover no-repeat' : 'linear-gradient(135deg,#d97706,#ea580c,#ef4444)';
@endphp

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: {{ $heroImage }}">
    <div class="absolute inset-0 bg-black/35"></div>
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-red-500/20 blur-3xl"></div>

    <div class="relative z-10 max-w-3xl">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Event Details
        </div>
        <h1 class="mt-4 text-3xl font-bold leading-tight lg:text-4xl">{{ $event->title }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-white/85">
            @if($event->event_date)
                <span class="rounded-full bg-white/15 px-3 py-1">{{ $event->event_date->format('d M Y H:i') }}</span>
            @endif
            @if($event->location)
                <span class="rounded-full bg-white/15 px-3 py-1">{{ $event->location }}</span>
            @endif
        </div>
    </div>
</div>

<div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg lg:p-8">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">School Event</p>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $event->title }}</h2>
                </div>
                <a href="{{ route('website.events') }}" class="shrink-0 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200">
                    Back to Events
                </a>
            </div>

            @if(!empty($event->description))
                <div class="prose prose-slate prose-lg max-w-none text-slate-600">
                    {!! nl2br(e($event->description)) !!}
                </div>
            @else
                <p class="text-slate-500">No additional event description has been added.</p>
            @endif
        </article>
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-amber-50 to-orange-50 p-5 shadow-lg">
            <h3 class="text-lg font-bold text-slate-900">Event Info</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-700">
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Published</span>
                    <span class="font-semibold">{{ $event->published_at?->format('d M Y H:i') ?? $event->created_at?->format('d M Y H:i') }}</span>
                </li>
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Status</span>
                    <span class="font-semibold text-emerald-600">{{ $event->is_published ? 'Published' : 'Draft' }}</span>
                </li>
                @if($event->event_date)
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Event Date</span>
                    <span class="font-semibold">{{ $event->event_date->format('d M Y H:i') }}</span>
                </li>
                @endif
                @if($event->location)
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Location</span>
                    <span class="font-semibold">{{ $event->location }}</span>
                </li>
                @endif
            </ul>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
            <h3 class="text-lg font-bold text-slate-900">More Activities</h3>
            <p class="mt-2 text-sm text-slate-600">Browse all upcoming school events and activities.</p>
            <a href="{{ route('website.events') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-2.5 text-sm font-bold text-white transition-all hover:shadow-lg">
                View Events
            </a>
        </div>
    </aside>
</div>
@endsection
