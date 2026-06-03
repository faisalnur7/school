@extends('website.layouts.app')
@section('content')

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: linear-gradient(135deg,#0f766e,#0ea5e9,#6366f1);">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            School Life
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Gallery' }}</h1>
        <p class="mt-2 text-base text-sky-100 lg:text-lg">{{ $page?->excerpt ?? 'Photos from classroom, events, sports and celebrations.' }}</p>
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($items as $item)
        <figure class="group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
            <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            </div>
            <figcaption class="p-4">
                <h2 class="font-bold text-slate-900">{{ $item->title }}</h2>
                @if($item->caption)
                    <p class="mt-2 text-sm text-slate-600">{{ $item->caption }}</p>
                @endif
            </figcaption>
        </figure>
    @empty
        <div class="col-span-full rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <p class="text-lg font-medium text-slate-600">No gallery items found.</p>
        </div>
    @endforelse
</div>

@if($items->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $items->links() }}
    </div>
@endif
@endsection
