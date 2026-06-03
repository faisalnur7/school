@extends('website.layouts.app')
@section('content')

@php
    $heroBg = !empty($page?->cover_image)
        ? 'url('.asset($page->cover_image).') center/cover no-repeat'
        : 'linear-gradient(135deg,#0284c7,#4f46e5,#7c3aed)';
@endphp

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: {{ $heroBg }}">
    <div class="absolute inset-0 bg-black/35"></div>
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl"></div>

    <div class="relative z-10 max-w-3xl">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Notice Details
        </div>
        <h1 class="mt-4 text-3xl font-bold leading-tight lg:text-4xl">{{ $notice->title }}</h1>
        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-white/85">
            <span class="rounded-full bg-white/15 px-3 py-1">{{ $notice->created_at?->format('d M Y') }}</span>
            @if($notice->published_at)
                <span class="rounded-full bg-white/15 px-3 py-1">Published {{ $notice->published_at->format('d M Y H:i') }}</span>
            @endif
        </div>
    </div>
</div>

<div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg lg:p-8">
            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Announcement</p>
                    <h2 class="text-2xl font-bold text-slate-900">{{ $notice->title }}</h2>
                </div>
                <a href="{{ route('website.notices') }}" class="shrink-0 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-200">
                    Back to Notices
                </a>
            </div>

            @if(!empty($notice->content))
                <div class="prose prose-slate prose-lg max-w-none text-slate-600">
                    {!! nl2br(e($notice->content)) !!}
                </div>
            @else
                <p class="text-slate-500">No additional details were provided for this notice.</p>
            @endif
        </article>
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-sky-50 to-indigo-50 p-5 shadow-lg">
            <h3 class="text-lg font-bold text-slate-900">Notice Info</h3>
            <ul class="mt-4 space-y-3 text-sm text-slate-700">
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Created</span>
                    <span class="font-semibold">{{ $notice->created_at?->format('d M Y H:i') }}</span>
                </li>
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Status</span>
                    <span class="font-semibold text-emerald-600">{{ $notice->is_published ? 'Published' : 'Draft' }}</span>
                </li>
                @if($notice->published_at)
                <li class="flex items-center justify-between gap-3">
                    <span class="text-slate-500">Published At</span>
                    <span class="font-semibold">{{ $notice->published_at->format('d M Y H:i') }}</span>
                </li>
                @endif
            </ul>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
            <h3 class="text-lg font-bold text-slate-900">More Updates</h3>
            <p class="mt-2 text-sm text-slate-600">Browse the full notice board for other announcements.</p>
            <a href="{{ route('website.notices') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition-all hover:shadow-lg">
                View Notice Board
            </a>
        </div>
    </aside>
</div>
@endsection
