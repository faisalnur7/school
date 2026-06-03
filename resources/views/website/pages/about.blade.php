@extends('website.layouts.app')
@section('content')

{{-- Page Header --}}
<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14"
     style="background: {{ !empty($page?->cover_image) ? 'url('.asset($page->cover_image).') center/cover no-repeat' : 'linear-gradient(135deg, #0284c7, #4f46e5, #7c3aed)' }}">
    {{-- Background Decorations --}}
    <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-24 right-10 h-72 w-72 rounded-full bg-purple-500/20 blur-3xl"></div>
    
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            About Us
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Who We Are' }}</h1>
        <p class="mt-2 text-base text-sky-100 lg:text-lg">{{ $page?->excerpt ?? 'Discover our mission, vision, and values' }}</p>
    </div>
</div>

{{-- Main Content Grid --}}
<div class="grid gap-8 lg:grid-cols-3">

{{-- Main Article --}}
<div class="lg:col-span-2">
    @if($page)
        <article class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg transition-all duration-300 hover:shadow-xl lg:p-8">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 transition-all duration-300 group-hover:from-indigo-500/5 group-hover:to-purple-500/5"></div>
            
            {{-- Section Badge --}}
            <div class="relative mb-4 inline-flex rounded-xl bg-gradient-to-br from-sky-500 to-indigo-600 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white shadow-md">
                {{ $page->title ?? 'About Us' }}
            </div>
            
            <div class="relative prose prose-slate prose-lg max-w-none text-slate-600">
                {!! nl2br(e($page->content)) !!}
            </div>
        </article>
        
        {{-- Page Sections with Photo Support --}}
        @if($page->sections->isNotEmpty())
            <div class="mt-8 space-y-6">
                @foreach($page->sections->where('is_active', true) as $section)
                @php $hasImage = !empty($section->image); $pos = $section->image_position ?? 'right'; @endphp

                @if($hasImage && $pos === 'background')
                {{-- Background image section --}}
                <div class="relative overflow-hidden rounded-2xl shadow-md" style="min-height:180px;">
                    <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-black/55"></div>
                    <div class="relative z-10 p-6">
                        <h2 class="text-xl font-bold text-white">{{ $section->title }}</h2>
                        @if($section->content)<p class="mt-2 text-sm text-white/85 leading-relaxed">{!! nl2br(e($section->content)) !!}</p>@endif
                    </div>
                </div>

                @elseif($hasImage && $pos === 'top')
                {{-- Image on top --}}
                <div class="group rounded-2xl border border-slate-100 bg-white overflow-hidden shadow-md">
                    <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="w-full object-cover" style="max-height:220px;">
                    <div class="p-5">
                        <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                        @if($section->content)<p class="mt-2 text-sm text-slate-600 leading-relaxed">{!! nl2br(e($section->content)) !!}</p>@endif
                    </div>
                </div>

                @elseif($hasImage)
                {{-- Image left or right --}}
                <div class="group rounded-2xl border border-slate-100 bg-white shadow-md overflow-hidden">
                    <div class="flex flex-col {{ $pos === 'left' ? 'md:flex-row' : 'md:flex-row-reverse' }} gap-0">
                        <div class="md:w-2/5 flex-shrink-0">
                            <img src="{{ asset($section->image) }}" alt="{{ $section->title }}" class="h-full w-full object-cover" style="min-height:180px;max-height:260px;">
                        </div>
                        <div class="flex-1 p-5 flex flex-col justify-center">
                            <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                            @if($section->content)<p class="mt-2 text-sm text-slate-600 leading-relaxed">{!! nl2br(e($section->content)) !!}</p>@endif
                        </div>
                    </div>
                </div>

                @else
                {{-- Text only --}}
                <div class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $section->title }}</h2>
                    @if($section->content)<p class="mt-2 text-sm text-slate-600 leading-relaxed">{!! nl2br(e($section->content)) !!}</p>@endif
                </div>
                @endif

                @endforeach
            </div>
        @endif
    @else
        <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-slate-100">
                <svg class="h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <p class="text-lg font-medium text-slate-600">About content is not published yet.</p>
            <p class="text-sm text-slate-500">Please check back later for updates!</p>
        </div>
    @endif
</div>

{{-- Sidebar --}}
<div class="space-y-6">
    {{-- Quick Contact Card --}}
    <div class="rounded-2xl border border-slate-100 bg-gradient-to-br from-sky-50 to-indigo-50 p-5 shadow-lg">
        <h3 class="text-lg font-bold text-slate-900">Get In Touch</h3>
        <p class="mt-2 text-sm text-slate-600">Have questions? We'd love to hear from you.</p>
        <a href="{{ route('website.contact') }}" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 px-4 py-2.5 text-sm font-bold text-white transition-all hover:shadow-lg hover:shadow-sky-500/25">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Contact Us
        </a>
    </div>

    {{-- Key Information Cards --}}
    @php
    $infoCards = [
        ['icon' => 'M12 4.354v4.812-4.812a4 4 0 00-3.584-5.253 4 4 0 015.253 3.584zM7 4v4a4 4 0 004 4h4a4 4 0 004-4V4a4 4 0 00-4-4H7zM4 12v4a4 4 0 004 4h4a4 4 0 004-4v-4a4 4 0 00-4-4H8a4 4 0 00-4 4zM20 12v4a4 4 0 01-4 4h-4a4 4 0 01-4-4v-4a4 4 0 014-4h4a4 4 0 014 4z', 'title' => 'Mission', 'text' => 'To provide quality education for all students.'],
        ['icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3 3 0 1013.878 6H12v-.386a2 2 0 00-.658-1.532l-.548-.547a2 2 0 10-.658 1.532V5', 'title' => 'Vision', 'text' => 'To be a center of educational excellence.'],
        ['icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title' => 'Values', 'text' => 'Integrity, respect, and lifelong learning.'],
    ];
    @endphp
    
    @foreach($infoCards as $card)
        <div class="group rounded-2xl border border-slate-100 bg-white p-5 shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">{{ $card['title'] }}</h4>
                    <p class="text-xs text-slate-500">{{ $card['text'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

</div>

{{-- Teachers Section --}}
@if($teachers->isNotEmpty())
    <section class="mt-12">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Our Teachers</h2>
            <p class="text-sm text-slate-500">Meet our dedicated teaching staff</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($teachers as $teacher)
                @php
                    $deptObj = $teacher->relationLoaded('department') && $teacher->department ? $teacher->department : null;
                    $deptName = $deptObj && is_object($deptObj) ? $deptObj->name : null;
                    $desigObj = $teacher->relationLoaded('designation') && $teacher->designation ? $teacher->designation : null;
                    $desigName = $desigObj && is_object($desigObj) ? $desigObj->name : 'Teacher';
                @endphp
                <a href="{{ route('website.teacher.show', $teacher->id) }}" class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-indigo-200 hover:shadow-xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-500/0 to-indigo-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
                    
                    <div class="relative">
                        <div class="relative mx-auto mb-4 inline-block">
                            @if($teacher->photo_url)
                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="h-20 w-20 rounded-full border-4 border-white object-cover shadow-md transition-transform duration-300 group-hover:scale-110">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-sky-500 to-indigo-600 text-white shadow-md">
                                    <span class="text-2xl font-bold">{{ substr($teacher->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-green-600 text-white">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-900 transition-colors group-hover:text-indigo-700">{{ $teacher->name }}</h3>
                        <p class="mt-1 text-xs font-semibold text-indigo-600">{{ $desigName }}</p>
                        @if($deptName)
                            <p class="mt-1 text-xs text-slate-500">{{ $deptName }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- Staff Section --}}
@if($staff->isNotEmpty())
    <section class="mt-10">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-slate-900">Our Staff</h2>
            <p class="text-sm text-slate-500">Our administrative and support team</p>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($staff as $member)
                @php
                    $deptObj = $member->relationLoaded('department') && $member->department ? $member->department : null;
                    $deptName = $deptObj && is_object($deptObj) ? $deptObj->name : null;
                    $desigObj = $member->relationLoaded('designation') && $member->designation ? $member->designation : null;
                    $desigName = $desigObj && is_object($desigObj) ? $desigObj->name : 'Staff';
                @endphp
                <a href="{{ route('website.staff.show', $member->id) }}" class="group relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 text-center shadow-md transition-all duration-300 hover:-translate-y-1 hover:border-amber-200 hover:shadow-xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-amber-500/0 to-orange-500/0 opacity-0 transition-all duration-300 group-hover:opacity-100"></div>
                    
                    <div class="relative">
                        <div class="relative mx-auto mb-4 inline-block">
                            @if($member->photo_url)
                                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="h-20 w-20 rounded-full border-4 border-white object-cover shadow-md transition-transform duration-300 group-hover:scale-110">
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md">
                                    <span class="text-2xl font-bold">{{ substr($member->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-green-600 text-white">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                        <h3 class="font-bold text-slate-900 transition-colors group-hover:text-amber-700">{{ $member->name }}</h3>
                        <p class="mt-1 text-xs font-semibold text-amber-600">{{ $desigName }}</p>
                        @if($deptName)
                            <p class="mt-1 text-xs text-slate-500">{{ $deptName }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection