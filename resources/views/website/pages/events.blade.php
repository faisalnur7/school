@extends('website.layouts.app')
@section('content')

{{-- Page Header --}}
<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14"
     style="background: {{ !empty($page?->cover_image) ? 'url('.asset($page->cover_image).') center/cover no-repeat' : 'linear-gradient(135deg,#d97706,#ea580c,#ef4444)' }}">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-red-500/20 blur-3xl"></div>
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Campus Life
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Events' }}</h1>
        <p class="mt-2 text-base text-amber-100 lg:text-lg">{{ $page?->excerpt ?? "Don't miss our upcoming activities" }}</p>
    </div>
</div>

@if(!empty($page?->content) || ($page?->sections?->isNotEmpty()))
    <div class="mb-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-md">
        <h2 class="text-lg font-bold text-slate-900">Page Details</h2>
        @if(!empty($page?->content))
            <div class="prose prose-slate mt-3 max-w-none text-slate-600">{!! nl2br(e($page->content)) !!}</div>
        @endif
        @if(!empty($page?->sections) && $page->sections->isNotEmpty())
            <div class="mt-6 space-y-4">
                @foreach($page->sections->where('is_active', true) as $section)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <h3 class="font-bold text-slate-900">{{ $section->title }}</h3>
                        @if($section->content)
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($section->content)) !!}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif

{{-- Events List --}}
<div class="space-y-4">
    @forelse($events as $event)
        <a href="{{ route('website.event.show', $event) }}" class="group relative block overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl lg:p-6">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/0 to-orange-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
            
            <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 transition-colors group-hover:text-amber-700">{{ $event->title ?? 'Event #'. $event->id }}</h2>
                        @if(!empty($event->description))
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ Str::limit(strip_tags($event->description), 150) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                    <span class="rounded-full bg-amber-50 px-4 py-1.5 text-xs font-semibold text-amber-700">
                        {{ $event->event_date?->format('d M Y') ?? $event->published_at?->format('d M Y') ?? 'TBA' }}
                    </span>
                    <span class="text-xs text-slate-400">
                        {{ $event->event_date ? 'Scheduled' : ($event->published_at ? 'Published '.$event->published_at?->diffForHumans() : 'Date TBA') }}
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <p class="text-lg font-medium text-slate-600">No events found.</p>
            <p class="text-sm text-slate-500">Please check back later for upcoming events!</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($events->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $events->links() }}
    </div>
@endif
@endsection
