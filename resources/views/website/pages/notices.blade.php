@extends('website.layouts.app')
@section('content')

{{-- Page Header --}}
<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14"
     style="background: {{ !empty($page?->cover_image) ? 'url('.asset($page->cover_image).') center/cover no-repeat' : 'linear-gradient(135deg,#0284c7,#4f46e5,#7c3aed)' }}">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl"></div>
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Information Center
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'School Notices' }}</h1>
        <p class="mt-2 text-base text-sky-100 lg:text-lg">{{ $page?->excerpt ?? 'Stay updated with the latest announcements' }}</p>
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

{{-- Notices List --}}
<div class="space-y-4">
    @forelse($notices as $notice)
        <a href="{{ route('website.notice.show', $notice) }}" class="group relative block overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl lg:p-6">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-500/0 to-indigo-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
            
            <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 transition-colors group-hover:text-indigo-700">{{ $notice->title ?? 'Notice #'. $notice->id }}</h2>
                        @if(!empty($notice->content))
                            <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ Str::limit(strip_tags($notice->content), 150) }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                    <span class="rounded-full bg-slate-100 px-4 py-1.5 text-xs font-semibold text-slate-600">
                        {{ $notice->published_at?->format('d M Y') ?? $notice->created_at?->format('d M Y') }}
                    </span>
                    <span class="text-xs text-slate-400">
                        {{ $notice->published_at ? 'Published '.$notice->published_at->diffForHumans() : $notice->created_at?->diffForHumans() }}
                    </span>
                </div>
            </div>
        </a>
    @empty
        <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-lg font-medium text-slate-600">No notices found.</p>
            <p class="text-sm text-slate-500">Please check back later for updates!</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($notices->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $notices->links() }}
    </div>
@endif
@endsection
