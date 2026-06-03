@extends('website.layouts.app')
@section('content')

@php
    $heroBg = !empty($page?->cover_image)
        ? 'url('.asset($page->cover_image).') center/cover no-repeat'
        : 'linear-gradient(135deg,#0f766e,#0ea5e9,#4f46e5)';
@endphp

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: {{ $heroBg }}">
    <div class="absolute inset-0 bg-black/30"></div>
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>

    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            {{ $page->page_type === 'custom' ? 'Custom Page' : ucfirst($page->page_type) }}
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Page' }}</h1>
        @if(!empty($page?->excerpt))
            <p class="mt-2 text-base text-sky-100 lg:text-lg">{{ $page->excerpt }}</p>
        @endif
    </div>
</div>

<div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-8">
        @if(!empty($page?->content))
            <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl lg:p-8">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-cyan-500/0 transition-all duration-300 group-hover:from-indigo-500/5 group-hover:to-cyan-500/5"></div>
                <div class="relative prose prose-slate prose-lg max-w-none text-slate-600">
                    {!! nl2br(e($page->content)) !!}
                </div>
            </article>
        @endif

        @if($page->sections->isNotEmpty())
            <div class="space-y-6">
                @foreach($page->sections->where('is_active', true) as $section)
                    @php $hasImage = !empty($section->image); $pos = $section->image_position ?? 'right'; @endphp

                    @if($hasImage && $pos === 'background')
                        <div class="relative overflow-hidden rounded-2xl shadow-md" style="min-height:180px;">
                            <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-0 bg-black/55"></div>
                            <div class="relative z-10 p-6">
                                <h2 class="text-xl font-bold text-white">{{ $section->title }}</h2>
                                @if($section->content)
                                    <p class="mt-2 text-sm leading-relaxed text-white/85">{!! nl2br(e($section->content)) !!}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($hasImage && $pos === 'top')
                        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-md">
                            <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="w-full object-cover" style="max-height:220px;">
                            <div class="p-5">
                                <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                                @if($section->content)
                                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($section->content)) !!}</p>
                                @endif
                            </div>
                        </div>
                    @elseif($hasImage)
                        <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-md">
                            <div class="flex flex-col gap-0 {{ $pos === 'left' ? 'md:flex-row' : 'md:flex-row-reverse' }}">
                                <div class="md:w-2/5 flex-shrink-0">
                                    <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="h-full w-full object-cover" style="min-height:180px;max-height:260px;">
                                </div>
                                <div class="flex-1 p-5 flex flex-col justify-center">
                                    <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                                    @if($section->content)
                                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($section->content)) !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
                            <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                            @if($section->content)
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($section->content)) !!}</p>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <aside class="space-y-6">
        <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-sky-50 to-indigo-50 p-5 shadow-lg">
            <h3 class="text-lg font-bold text-slate-900">Quick Links</h3>
            <div class="mt-4 flex flex-col gap-2">
                <a href="{{ route('website.about') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:text-sky-700">About Us</a>
                <a href="{{ route('website.contact') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:text-sky-700">Contact</a>
                <a href="{{ route('website.notices') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:text-sky-700">Notices</a>
                <a href="{{ route('website.events') }}" class="rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:text-sky-700">Events</a>
            </div>
        </div>
    </aside>
</div>
@endsection
