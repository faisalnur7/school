@extends('website.layouts.app')
@section('content')

{{-- Swiper Slider --}}
@php
$defaultSlides = [
    ['image' => 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1400&q=80', 'title' => 'Welcome to ' . ($site['name'] ?? 'Our School'), 'subtitle' => $site['tagline'] ?? 'Empowering minds, shaping futures.', 'cta_text' => 'Learn More', 'cta_url' => route('website.about')],
    ['image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1400&q=80', 'title' => 'Excellence in Education', 'subtitle' => 'A nurturing environment where every student thrives.', 'cta_text' => 'About Us', 'cta_url' => route('website.about')],
    ['image' => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=1400&q=80', 'title' => "Building Tomorrow's Leaders", 'subtitle' => 'Quality education with modern facilities and dedicated staff.', 'cta_text' => 'Contact Us', 'cta_url' => route('website.contact')],
    ['image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=1400&q=80', 'title' => 'A Community of Learners', 'subtitle' => 'Fostering curiosity, creativity, and lifelong learning.', 'cta_text' => 'View Calendar', 'cta_url' => route('website.academic-calendar')],
];
$heroSlides = $sliders->isNotEmpty()
    ? $sliders->map(fn($s) => ['image' => $s->image_path ? asset($s->image_path) : null, 'title' => $s->title, 'subtitle' => $s->subtitle ?? '', 'cta_text' => $s->cta_text ?? '', 'cta_url' => $s->cta_url ?? ''])->toArray()
    : $defaultSlides;
@endphp
<section class="relative mb-12 overflow-hidden rounded-2xl shadow-2xl">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <style>
        .school-hero-swiper .swiper-button-prev,
        .school-hero-swiper .swiper-button-next {
            width: 48px; height: 48px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border-radius: 50%;
            color: #fff;
        }
        .school-hero-swiper .swiper-button-prev::after,
        .school-hero-swiper .swiper-button-next::after { font-size: 18px; font-weight: 700; }
        .school-hero-swiper .swiper-button-prev:hover,
        .school-hero-swiper .swiper-button-next:hover { background: rgba(255,255,255,0.30); }
        .school-hero-swiper .swiper-pagination-bullet {
            width: 10px; height: 10px;
            background: rgba(255,255,255,0.55);
            opacity: 1;
            transition: all .3s;
        }
        .school-hero-swiper .swiper-pagination-bullet-active {
            width: 32px;
            border-radius: 5px;
            background: #fff;
        }
    </style>

    <div class="swiper school-hero-swiper h-[360px] md:h-[460px] lg:h-[560px]">
        <div class="swiper-wrapper">
            @foreach($heroSlides as $slide)
            <div class="swiper-slide relative overflow-hidden">
                @if(!empty($slide['image']))
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="absolute inset-0 h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-600 via-indigo-700 to-purple-700"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/45 to-black/10"></div>
                <div class="relative z-10 flex h-full items-center px-8 md:px-14 lg:px-20">
                    <div class="max-w-2xl">
                        <span class="inline-block rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-wider text-white backdrop-blur-sm">
                            {{ $site['name'] ?? 'Our School' }}
                        </span>
                        <h2 class="mt-4 text-3xl font-bold leading-tight text-white md:text-4xl lg:text-5xl">{{ $slide['title'] }}</h2>
                        @if(!empty($slide['subtitle']))
                        <p class="mt-3 text-base text-white/85 md:text-lg">{{ $slide['subtitle'] }}</p>
                        @endif
                        @if(!empty($slide['cta_text']) && !empty($slide['cta_url']))
                        <a href="{{ $slide['cta_url'] }}" class="mt-8 inline-flex items-center gap-2 rounded-full bg-white px-7 py-3.5 text-sm font-bold text-indigo-800 transition-all duration-300 hover:scale-105 hover:shadow-xl">
                            {{ $slide['cta_text'] }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.school-hero-swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 800,
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    </script>
</section>

@if(!empty($page) && ($page->content || $page->sections->isNotEmpty()))
<section class="mb-10 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        @if(!empty($page->content))
            <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl lg:p-8">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-cyan-500/0 transition-all duration-300 group-hover:from-indigo-500/5 group-hover:to-cyan-500/5"></div>
                <div class="relative">
                    <div class="mb-4 inline-flex rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-md">
                        {{ $page->title ?? 'Welcome' }}
                    </div>
                    <div class="prose prose-slate prose-lg max-w-none text-slate-600">
                        {!! nl2br(e($page->content)) !!}
                    </div>
                </div>
            </article>
        @endif

        @if($page->sections->isNotEmpty())
            <div class="mt-6 space-y-6">
                @foreach($page->sections->where('is_active', true) as $section)
                    <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
                        <h3 class="text-lg font-bold text-slate-900">{{ $section->title }}</h3>
                        @if($section->content)
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{!! nl2br(e($section->content)) !!}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <aside class="space-y-4">
        <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-sky-50 to-indigo-50 p-5 shadow-lg">
            <h3 class="text-lg font-bold text-slate-900">School Profile</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $site['tagline'] ?? 'Learning for life' }}</p>
            <a href="{{ route('website.about') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:text-sky-700">
                Read More
            </a>
        </div>
    </aside>
</section>
@endif

{{-- Quick Stats Banner --}}
<div class="mb-10 grid grid-cols-2 gap-4 md:grid-cols-4">
    @foreach($stats as $stat)
    <div class="group relative rounded-2xl bg-white/80 p-5 text-center shadow-lg backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-sky-500/5 to-indigo-500/5 opacity-0 transition-opacity group-hover:opacity-100"></div>
        <div class="relative">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path></svg>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $stat['value'] }}</div>
            <div class="text-xs font-medium text-slate-500">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Main Grid: Notices & Events --}}
<section class="mb-10 grid gap-6 lg:grid-cols-2">
    {{-- Notices Card --}}
    <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-0 shadow-lg transition-all duration-300 hover:shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-sky-50/50 to-indigo-50/30 opacity-0 transition-opacity group-hover:opacity-100"></div>
        
        {{-- Card Header --}}
        <div class="relative border-b border-slate-100 bg-gradient-to-r from-sky-50 to-indigo-50/30 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-md">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h6v-2H4v2zM4 11h6V9H4v2zM4 7h6V5H4v2z"></path></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-slate-900">Latest Notices</h2>
                    <p class="text-xs text-slate-500">Stay updated with school announcements</p>
                </div>
                <a href="{{ route('website.notices') }}" class="rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-sky-600 shadow-sm transition-all hover:bg-sky-50 hover:text-sky-700">View All →</a>
            </div>
        </div>
        
        {{-- Card Content --}}
        <div class="relative space-y-1 p-4">
            @forelse($notices as $notice)
                <a href="{{ route('website.notice.show', $notice) }}" class="flex items-center gap-3 rounded-xl border border-transparent px-4 py-3 transition-all duration-200 hover:border-slate-100 hover:bg-slate-50/80">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-slate-700">{{ Str::limit($notice->title ?? 'Notice #'. $notice->id, 45) }}</p>
                        <p class="text-xs text-slate-500">Posted {{ $notice->created_at?->diffForHumans() }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">{{ $notice->created_at?->format('d M') }}</span>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-sm text-slate-500">No notices available yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Events Card --}}
    <div class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-0 shadow-lg transition-all duration-300 hover:shadow-xl">
        <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 to-orange-50/30 opacity-0 transition-opacity group-hover:opacity-100"></div>
        
        {{-- Card Header --}}
        <div class="relative border-b border-slate-100 bg-gradient-to-r from-amber-50 to-orange-50/30 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-slate-900">Upcoming Events</h2>
                    <p class="text-xs text-slate-500">Don't miss our upcoming activities</p>
                </div>
                <a href="{{ route('website.events') }}" class="rounded-full bg-white px-4 py-1.5 text-xs font-semibold text-amber-600 shadow-sm transition-all hover:bg-amber-50 hover:text-amber-700">View All →</a>
            </div>
        </div>
        
        {{-- Card Content --}}
        <div class="relative space-y-1 p-4">
            @forelse($events as $event)
                <a href="{{ route('website.event.show', $event) }}" class="flex items-center gap-3 rounded-xl border border-transparent px-4 py-3 transition-all duration-200 hover:border-slate-100 hover:bg-slate-50/80">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium text-slate-700">{{ Str::limit($event->title ?? 'Event #'. $event->id, 45) }}</p>
                        <p class="text-xs text-slate-500">
                            {{ $event->event_date ? 'On '.$event->event_date->format('d M Y') : ($event->published_at ? 'Published '.$event->published_at->diffForHumans() : 'Date TBA') }}
                        </p>
                    </div>
                    <span class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                        {{ $event->event_date?->format('d M') ?? $event->published_at?->format('d M') ?? 'TBA' }}
                    </span>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center">
                    <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                        <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-sm text-slate-500">No upcoming events scheduled.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- Gallery Preview --}}
<section class="mb-10 rounded-2xl border border-slate-100 bg-white p-0 shadow-lg">
    <div class="flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Campus Gallery</h2>
            <p class="text-sm text-slate-500">Moments from school life and activities</p>
        </div>
        <a href="{{ route('website.gallery') }}" class="group flex items-center gap-1 text-sm font-semibold text-indigo-600 transition-all hover:text-indigo-800">
            View Gallery
            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
    <div class="grid gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($galleryItems as $item)
            <a href="{{ route('website.gallery') }}" class="group overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-sky-200 hover:shadow-lg">
                <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                    <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-slate-900">{{ $item->title }}</h3>
                    @if($item->caption)
                        <p class="mt-1 text-sm text-slate-600">{{ Str::limit($item->caption, 90) }}</p>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-200 p-8 text-center text-sm text-slate-500">
                No gallery items available yet.
            </div>
        @endforelse
    </div>
</section>

{{-- Featured Pages Section --}}
<section class="mb-10 rounded-2xl border border-slate-100 bg-white p-0 shadow-lg">
    {{-- Section Header --}}
    <div class="flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white px-6 py-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Featured Pages</h2>
            <p class="text-sm text-slate-500">Explore our school resources and information</p>
        </div>
        <a href="{{ route('website.about') }}" class="group flex items-center gap-1 text-sm font-semibold text-indigo-600 transition-all hover:text-indigo-800">
            View All 
            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
    
    {{-- Featured Pages Grid --}}
    <div class="grid gap-4 p-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($featuredPages as $index => $page)
            <a href="#" class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-slate-50/50 p-5 transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:bg-white hover:shadow-lg">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 transition-all duration-300 group-hover:from-indigo-500/5 group-hover:to-purple-500/5"></div>
                
                {{-- Page Number Badge --}}
                <div class="relative mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-md transition-transform duration-300 group-hover:scale-110">
                    <span class="text-lg font-bold">{{ $index + 1 }}</span>
                </div>
                
                <h3 class="relative text-lg font-bold text-slate-900 transition-colors group-hover:text-indigo-700">{{ $page->title }}</h3>
                <p class="relative mt-2 line-clamp-2 text-sm text-slate-600">{{ Str::limit($page->excerpt ?: 'Discover more about this page.', 100) }}</p>
                
                <span class="relative mt-4 inline-flex items-center text-xs font-semibold text-indigo-600 opacity-0 transition-all duration-300 group-hover:opacity-100">
                    Read More
                    <svg class="ml-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </a>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-lg font-medium text-slate-600">No published pages yet.</p>
            <p class="text-sm text-slate-500">Check back soon for updates!</p>
        </div>
        @endforelse
    </div>
</section>

{{-- Call to Action Section --}}
<section class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 py-12 text-center text-white shadow-2xl lg:px-12 lg:py-16">
    {{-- Decorative Elements --}}
    <div class="absolute -left-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-20 -right-16 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute left-1/2 top-1/2 h-40 w-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/5 blur-2xl"></div>
    
    <div class="relative z-10 mx-auto max-w-2xl">
        <h2 class="text-2xl font-bold md:text-3xl lg:text-4xl">Ready to Get Started?</h2>
        <p class="mt-4 text-base text-white/80 md:text-lg">Join our community of learners and experience quality education that shapes future leaders.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-4">
            <a href="{{ route('website.contact') }}" class="group rounded-full bg-white px-8 py-3.5 text-sm font-bold text-indigo-700 transition-all duration-300 hover:scale-105 hover:shadow-2xl hover:shadow-white/20">
                <span class="flex items-center gap-2">
                    Contact Us Today
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </span>
            </a>
            <a href="{{ route('website.about') }}" class="rounded-full border-2 border-white/40 bg-white/10 px-8 py-3.5 text-sm font-bold text-white backdrop-blur-sm transition-all duration-300 hover:bg-white/20">
                Learn More About Us
            </a>
        </div>
    </div>
</section>
@endsection
