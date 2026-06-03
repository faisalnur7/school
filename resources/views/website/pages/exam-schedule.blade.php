@extends('website.layouts.app')
@section('content')

<div class="relative mb-10 overflow-hidden rounded-2xl px-6 py-10 text-white shadow-2xl lg:px-10 lg:py-14" style="background: linear-gradient(135deg,#4f46e5,#7c3aed,#db2777);">
    <div class="absolute inset-0 bg-black/25"></div>
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
            Exam Schedule
        </div>
        <h1 class="mt-4 text-3xl font-bold lg:text-4xl">{{ $page?->title ?? 'Exam Schedule' }}</h1>
        <p class="mt-2 text-base text-purple-100 lg:text-lg">{{ $page?->excerpt ?? 'Published exam dates and session details.' }}</p>
    </div>
</div>

<div class="space-y-4">
    @forelse($exams as $exam)
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-md">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $exam->academicSession?->name_en ?? $exam->academicSession?->name_bn ?? 'Session' }}</p>
                    <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $exam->name }}</h2>
                    <p class="mt-1 text-sm text-slate-600">{{ $exam->type_label }} · {{ ucfirst($exam->status) }}</p>
                </div>
                <div class="flex flex-wrap gap-2 text-sm">
                    @if($exam->start_date)
                        <span class="rounded-full bg-indigo-50 px-3 py-1.5 font-semibold text-indigo-700">Start {{ $exam->start_date->format('d M Y') }}</span>
                    @endif
                    @if($exam->end_date)
                        <span class="rounded-full bg-purple-50 px-3 py-1.5 font-semibold text-purple-700">End {{ $exam->end_date->format('d M Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-2xl border border-slate-100 bg-white px-6 py-16 text-center shadow-lg">
            <p class="text-lg font-medium text-slate-600">No published exam schedule found.</p>
        </div>
    @endforelse
</div>

@if($exams->hasPages())
    <div class="mt-10 rounded-2xl border border-slate-100 bg-white px-6 py-4 shadow-md">
        {{ $exams->links() }}
    </div>
@endif
@endsection
