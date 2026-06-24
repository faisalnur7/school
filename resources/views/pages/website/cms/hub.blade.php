@extends('layouts.master')
@section('title', 'Website Management')
@section('contents')
<div class="col-12">
    @php
        $systemPageCards = collect($systemPageDefinitions)->map(function ($card, $type) use ($systemPages) {
            $systemPage = $systemPages[$type] ?? null;
            $status = $systemPage ? ucfirst($systemPage->status) : 'Not set up';

            return [
                'route' => 'website.cms.page.edit',
                'params' => [$type],
                'icon' => $card['icon'],
                'title' => $card['label'],
                'subtitle' => $status,
                'badge' => $status,
                'from' => match ($type) {
                    'home' => '#0891b2',
                    'about' => '#7c3aed',
                    'academics' => '#4f46e5',
                    'admission' => '#059669',
                    'notices' => '#d97706',
                    'news-events' => '#dc2626',
                    'calendar' => '#0f766e',
                    'results' => '#0f766e',
                    'gallery' => '#2563eb',
                    'teachers-staff' => '#475569',
                    'contact' => '#059669',
                    'downloads' => '#1f2937',
                    'facilities' => '#0ea5e9',
                    'policies' => '#b45309',
                    default => '#64748b',
                },
                'to' => match ($type) {
                    'home' => '#0e7490',
                    'about' => '#6d28d9',
                    'academics' => '#3730a3',
                    'admission' => '#047857',
                    'notices' => '#b45309',
                    'news-events' => '#b91c1c',
                    'calendar' => '#0d9488',
                    'results' => '#115e59',
                    'gallery' => '#1d4ed8',
                    'teachers-staff' => '#334155',
                    'contact' => '#047857',
                    'downloads' => '#111827',
                    'facilities' => '#0284c7',
                    'policies' => '#92400e',
                    default => '#475569',
                },
            ];
        })->values();
    @endphp

    {{-- Hero --}}
    <div class="bg-gradient-to-br from-slate-800 to-slate-600 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-globe text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Website Management</h3>
            <p class="text-slate-300 text-sm mt-1 mb-0">Manage every page, section, photo, and slider of your public website</p>
        </div>
    </div>

    {{-- System Pages --}}
    <div class="card card-outline card-primary mb-4">
        <div class="card-header"><h3 class="card-title font-bold">System Pages</h3></div>
        <div class="card-body">
            <x-hub-card-browser :cards="$systemPageCards" storage-key="website-cms-system-pages" default-view="medium" />
        </div>
    </div>

    {{-- Banners & Custom Pages --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card card-outline card-warning">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Home Slider / Banners</h3>
                    <span class="badge badge-warning">{{ $bannerCount }} slides</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Manage the hero image slider shown on the home page. Upload photos, set titles, subtitles and call-to-action buttons.</p>
                    <a href="{{ route('website.cms.banners') }}" class="btn btn-warning btn-block">
                        <i class="fas fa-images mr-1"></i> Manage Slider
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-danger">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Event Board</h3>
                    <span class="badge badge-danger">{{ $eventCount }} events</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Create school events, activities, celebrations, and announcements that show on the public events page.</p>
                    <a href="{{ route('events.index') }}" class="btn btn-danger btn-block">
                        <i class="fas fa-calendar-alt mr-1"></i> Manage Events
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Academic Calendar</h3>
                    <span class="badge badge-success">{{ $calendarCount }} items</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Publish term dates, holidays, exams, and important academic events on the public calendar page.</p>
                    <a href="{{ route('website.academic-calendar.index') }}" class="btn btn-success btn-block">
                        <i class="fas fa-calendar-check mr-1"></i> Manage Calendar
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-primary">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Gallery</h3>
                    <span class="badge badge-primary">{{ $galleryCount }} photos</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Upload campus photos, event moments, achievements and school activities.</p>
                    <a href="{{ route('website.gallery.index') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-images mr-1"></i> Manage Gallery
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-info">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-bold">Custom Pages</h3>
                    <span class="badge badge-info">{{ $customPages->count() }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-sm">Create additional pages like Gallery, Downloads, Achievements etc.</p>
                    <a href="{{ route('website.pages.index') }}" class="btn btn-info btn-block">
                        <i class="fas fa-file-alt mr-1"></i> Manage Custom Pages
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-outline card-secondary">
                <div class="card-header"><h3 class="card-title font-bold">Website Settings</h3></div>
                <div class="card-body">
                    <p class="text-muted text-sm">Update school name, tagline, contact info and other global website settings.</p>
                    <a href="{{ route('website.settings.edit') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-cog mr-1"></i> Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
