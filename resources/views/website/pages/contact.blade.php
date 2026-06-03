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
            Get In Touch
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Contact Office' }}</h1>
        <p class="mt-2 text-base text-sky-100 lg:text-lg">{{ $page?->excerpt ?? "We'd love to hear from you" }}</p>
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

{{-- Main Content Grid --}}
<div class="grid gap-6 lg:grid-cols-3">
    {{-- Contact Info Sidebar --}}
    <aside class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl lg:p-8">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
        
        <div class="relative mb-6 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Contact Details</h2>
        </div>
        
        {{-- Contact Items --}}
        <div class="space-y-5">
            {{-- Phone --}}
            <div class="group/item flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition-all group-hover/item:bg-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">Phone</p>
                    <p class="font-medium text-slate-800">{{ $site['phone_primary'] ?? 'Not set' }}</p>
                    @if(!empty($site['phone_secondary']))
                        <p class="text-sm text-slate-500">{{ $site['phone_secondary'] }}</p>
                    @endif
                </div>
            </div>
            
            {{-- Email --}}
            <div class="group/item flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition-all group-hover/item:bg-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">Email</p>
                    <p class="font-medium text-slate-800">{{ $site['email'] ?? 'Not set' }}</p>
                </div>
            </div>
            
            {{-- Address --}}
            <div class="group/item flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 transition-all group-hover/item:bg-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">Address</p>
                    <p class="font-medium text-slate-800">{{ $site['address'] ?? 'Not set' }}</p>
                </div>
            </div>
        </div>
        
        {{-- Social Links --}}
        <div class="mt-8 border-t border-slate-100 pt-6">
            <p class="mb-3 text-xs font-semibold uppercase text-slate-400">Follow Us</p>
            <div class="flex gap-2">
                <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-indigo-500 hover:text-white">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 5.599-.98-.525-1.795-1.218-2.162-2.141-.976.582-1.838 1.012-2.539 1.297-.882-.311-2.032-.766-2.577-1.45-.563-.68-.91-1.469-.91-2.354v-.066c0-1.789 1.452-3.341 3.332-3.694-.314.097-.641.156-1.009.156-1.234 0-2.365-.731-2.961-1.834-.621.937-1.522 1.623-2.49 1.956-.797-.585-1.752-.921-2.75-.906v2.313h2.313v-2.313h2.313v2.313h2.313v-2.313l2.313-.027c.652-.025 1.275-.11 1.855-.336l1.835.049c1.488.475 2.836.748 4.274.748 2.213 0 4.275-.958 5.598-2.551.973-1.175 1.523-2.565 1.625-3.983z"></path></svg>
                </a>
                <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-600 hover:text-white">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
                </a>
                <a href="#" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-700 hover:text-white">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                </a>
            </div>
        </div>
    </aside>

    {{-- Contact Form --}}
    <form method="POST" action="{{ route('website.contact.submit') }}" class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-md transition-all duration-300 hover:shadow-xl lg:col-span-2 lg:p-8">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
        
        @csrf
        <div class="relative grid gap-5 md:grid-cols-2">
            <div>
                <label class="text-sm font-semibold text-slate-700">Name <span class="text-red-500">*</span></label>
                <input name="name" value="{{ old('name') }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="Your full name" required>
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="your@email.com" required>
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Phone</label>
                <input name="phone" value="{{ old('phone') }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="+1 234 567 890">
            </div>
            <div>
                <label class="text-sm font-semibold text-slate-700">Subject <span class="text-red-500">*</span></label>
                <input name="subject" value="{{ old('subject') }}" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="How can we help?" required>
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="text-sm font-semibold text-slate-700">Message <span class="text-red-500">*</span></label>
                <textarea name="message" rows="5" class="mt-2 w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="group/btn inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg transition-all hover:scale-105 hover:shadow-xl hover:shadow-indigo-500/25">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Send Message
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
