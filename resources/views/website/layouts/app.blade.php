<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $seo->meta_description ?? (($site['tagline'] ?? '') ?: 'School Website') }}">
    <meta name="keywords" content="{{ $seo->meta_keywords ?? '' }}">
    <title>{{ $seo->meta_title ?? (($site['name'] ?? config('app.name')) . ' | ' . ucfirst(request()->route()->getName() ? last(explode('.', request()->route()->getName())) : 'home')) }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-primary: #0ea5e9;
            --brand-secondary: #8b5cf6;
            --brand-accent: #f59e0b;
            --surface: #f8fafc;
            --ink: #0f172a;
            --muted: #64748b;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%);
            color: var(--ink);
            min-height: 100vh;
        }
        h1,h2,h3,h4 { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Enhanced Glass Effects */
        .glass { background: rgba(255,255,255,0.75); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .glass-strong { background: rgba(255,255,255,0.9); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); }
        
        /* Premium Shadows */
        .shadow-glow { box-shadow: 0 10px 40px rgba(14, 165, 233, 0.15); }
        .shadow-elevated { box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        
        /* Gradient Text */
        .gradient-text { 
            background: linear-gradient(135deg, var(--brand-primary), var(--brand-secondary)); 
            -webkit-background-clip: text; 
            -webkit-text-fill-color: transparent; 
            background-clip: text; 
        }
        
        /* Animations */
        .animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
        .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
        .animate-scale-in { animation: scaleIn 0.5s ease-out forwards; }
        
        @keyframes fadeIn { 
            from { opacity: 0; } 
            to { opacity: 1; } 
        }
        @keyframes slideUp { 
            from { opacity: 0; transform: translateY(20px); } 
            to { opacity: 1; transform: translateY(0); } 
        }
        @keyframes scaleIn { 
            from { opacity: 0; transform: scale(0.95); } 
            to { opacity: 1; transform: scale(1); } 
        }
        
        /* Staggered animation delays */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        
        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Smooth scroll */
        html { scroll-behavior: smooth; }
        
        /* Selection style */
        ::selection {
            background: var(--brand-primary);
            color: white;
        }
    </style>
</head>
<body class="antialiased">
    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-lg border-b border-slate-200/60 shadow-sm">
        <div class="relative mx-auto max-w-[1440px]">
            <div class="flex items-center justify-between px-4 py-3 lg:px-8">
                {{-- Logo --}}
                <a href="{{ route('website.home') }}" class="flex items-center gap-3 group">
                    @if(!empty($site['logo']))
                        <div class="relative">
                            <img src="{{ $site['logo'] }}" alt="School Logo" class="h-16 w-auto rounded-xl object-cover shadow-md transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-br from-sky-400 to-indigo-500 opacity-0 transition-opacity group-hover:opacity-50 blur-md"></div>
                        </div>
                    @endif
                    <div class="hidden sm:block">
                        <span class="block text-lg font-bold text-slate-900">{{ $site['name'] ?? 'School Website' }}</span>
                        <span class="block text-xs font-medium text-slate-500">{{ $site['tagline'] ?? 'Learning for life' }}</span>
                    </div>
                </a>
                
                {{-- Desktop Navigation --}}
                <nav class="hidden items-center gap-1 lg:flex">
                    @php $links = [
                        ['route' => 'website.home', 'label' => 'Home'],
                        ['route' => 'website.about', 'label' => 'About'],
                        ['route' => 'website.notices', 'label' => 'Notices'],
                        ['route' => 'website.events', 'label' => 'Events'],
                        ['route' => 'website.academic-calendar', 'label' => 'Calendar'],
                        ['route' => 'website.gallery', 'label' => 'Gallery'],
                        ['route' => 'website.contact', 'label' => 'Contact'],
                    ]; @endphp
                    @foreach($links as $item)
                        <a href="{{ route($item['route']) }}" class="relative rounded-full px-4 py-2.5 text-sm font-semibold transition-all duration-200 {{ request()->routeIs($item['route']) ? 'text-white bg-gradient-to-r from-sky-500 to-indigo-600 shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            {{ $item['label'] }}
                            @if(request()->routeIs($item['route']))
                                <span class="absolute -bottom-1 left-1/2 h-1 w-1 -translate-x-1/2 rounded-full bg-gradient-to-r from-sky-500 to-indigo-600"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
                
                {{-- Mobile Menu Button --}}
                <details class="lg:hidden">
                    <summary class="flex cursor-pointer items-center gap-2 rounded-xl bg-slate-100/80 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200/80">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <span>Menu</span>
                    </summary>
                    <nav class="absolute right-4 mt-3 w-72 rounded-2xl border border-slate-200 bg-white/98 p-2 shadow-2xl">
                        @foreach($navLinks ?? $links as $item)
                            <a href="{{ $item['url'] ?? route($item['route']) }}" class="flex items-center justify-between rounded-xl px-4 py-3 text-sm font-medium transition-colors {{ isset($item['route_name']) && request()->routeIs($item['route_name']) ? 'text-white bg-gradient-to-r from-sky-500 to-indigo-600' : 'text-slate-700 hover:bg-slate-100' }}">
                                {{ $item['label'] }}
                                @if(isset($item['route_name']) && request()->routeIs($item['route_name']))
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </details>
            </div>
        </div>
    </header>

    {{-- Spacer for fixed header --}}
    <div class="h-20"></div>

    {{-- Main Content --}}
    <main class="w-full">
        <div class="mx-auto max-w-[1440px] px-4 py-6 lg:px-8 lg:py-8">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-8 animate-fade-in">
                <div class="rounded-2xl border border-emerald-200/60 bg-gradient-to-r from-emerald-50 via-teal-50 to-cyan-50 px-5 py-4 shadow-lg">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif
        @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="mt-16 border-t border-slate-200/70 bg-white/80">
        <div class="mx-auto max-w-[1440px] px-4 py-12 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-4">
                {{-- Brand Column --}}
                <div class="lg:col-span-2">
                    <a href="{{ route('website.home') }}" class="flex items-center gap-3">
                        @if(!empty($site['logo']))
                            <img src="{{ $site['logo'] }}" alt="School Logo" class="h-12 w-12 rounded-xl object-cover shadow-md">
                        @endif
                        <div>
                            <span class="text-xl font-bold text-slate-900">{{ $site['name'] ?? config('app.name') }}</span>
                            <p class="text-sm text-slate-500">{{ $site['tagline'] ?? 'Education with excellence and care.' }}</p>
                        </div>
                    </a>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600">
                        {{ $site['footer_about'] ?? 'We are committed to providing quality education that empowers students to achieve their full potential and become responsible citizens.' }}
                    </p>
                    {{-- Social Links --}}
                    <div class="mt-5 flex gap-3">
                        <a href="{{ $site['social_facebook'] ?? '#' }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-500 hover:text-white" @if(empty($site['social_facebook'])) aria-disabled="true" @endif>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 5.599-.98-.525-1.795-1.218-2.162-2.141-.976.582-1.838 1.012-2.539 1.297-.882-.311-2.032-.766-2.577-1.45-.563-.68-.91-1.469-.91-2.354v-.066c0-1.789 1.452-3.341 3.332-3.694-.314.097-.641.156-1.009.156-1.234 0-2.365-.731-2.961-1.834-.621.937-1.522 1.623-2.49 1.956-.797-.585-1.752-.921-2.75-.906v2.313h2.313v-2.313h2.313v2.313h2.313v-2.313l2.313-.027c.652-.025 1.275-.11 1.855-.336l1.835.049c1.488.475 2.836.748 4.274.748 2.213 0 4.275-.958 5.598-2.551.973-1.175 1.523-2.565 1.625-3.983z"></path></svg>
                        </a>
                        <a href="{{ $site['social_instagram'] ?? '#' }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-600 hover:text-white" @if(empty($site['social_instagram'])) aria-disabled="true" @endif>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg>
                        </a>
                        <a href="{{ $site['social_youtube'] ?? '#' }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-700 hover:text-white" @if(empty($site['social_youtube'])) aria-disabled="true" @endif>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                        </a>
                        <a href="{{ $site['social_linkedin'] ?? '#' }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-all hover:bg-sky-800 hover:text-white" @if(empty($site['social_linkedin'])) aria-disabled="true" @endif>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                        </a>
                    </div>
                </div>
                
                {{-- Quick Links --}}
                <div>
                    <h3 class="mb-4 text-base font-bold text-slate-800">Quick Links</h3>
                    <ul class="space-y-2.5 text-sm">
                        @foreach(($navLinks ?? []) as $item)
                            <li>
                                <a href="{{ $item['url'] }}" class="flex items-center gap-2 text-slate-600 transition-colors hover:text-sky-600 hover:translate-x-1">
                                    <svg class="h-3 w-3 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                
                {{-- Contact Info --}}
                <div>
                    <h3 class="mb-4 text-base font-bold text-slate-800">Contact</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3 text-slate-600">
                            <svg class="h-4 w-4 mt-0.5 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>{{ $site['address'] ?? 'Address not set' }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-600">
                            <svg class="h-4 w-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $site['phone_primary'] ?? 'Phone not set' }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-slate-600">
                            <svg class="h-4 w-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ $site['email'] ?? 'Email not set' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            {{-- Copyright --}}
            <div class="mt-10 border-t border-slate-200 pt-6 text-center">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ $site['name'] ?? config('app.name') }}. All rights reserved. Designed with care for education.</p>
            </div>
        </div>
    </footer>
</body>
</html>
