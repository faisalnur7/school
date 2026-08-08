<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 10px; color: #222; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .header-table td { border: 0; padding: 0; vertical-align: middle; }
    .logo-cell { width: 80px; }
    .logo { width: 64px; height: 64px; object-fit: contain; }
    .school-name { font-size: 18px; font-weight: 700; margin-bottom: 2px; text-align: center; }
    .school-meta { text-align: center; font-size: 10px; color: #444; line-height: 1.45; }
    h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
    p.sub { text-align: center; margin: 0 0 10px; font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th { background: #2d3748; color: #fff; padding: 6px; text-align: left; }
    td { padding: 5px 6px; border-bottom: 1px solid #ddd; vertical-align: top; }
    .text-center { text-align: center; }
    .small { font-size: 9px; color: #555; }
    .group-row td {
        background: #f8fafc;
        color: #1f2937;
        font-weight: 700;
        border-bottom: 1px solid #cbd5e1;
        padding: 6px 8px;
    }
    .group-row--section td {
        background: #eef6ff;
    }
    .group-pill {
        display: inline-block;
        padding: 2px 6px;
        margin-right: 6px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .group-pill--section {
        background: #e0f2fe;
        color: #0369a1;
    }
    .group-meta {
        margin-left: 6px;
        color: #64748b;
        font-size: 9px;
        font-weight: 600;
    }
</style>
</head>
<body>
@php
    $logoPath = !empty($setting?->logo) ? public_path($setting->logo) : null;
@endphp

@if($setting)
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" class="logo" alt="School Logo">
            @endif
        </td>
        <td>
            <div class="school-name">{{ $setting->name }}</div>
            <div class="school-meta">
                @if($setting->address)
                    <div>{{ $setting->address }}</div>
                @endif
                @if($setting->email || $setting->contact_number_1 || $setting->contact_number_2)
                    <div>
                        @if($setting->email) Email: {{ $setting->email }} @endif
                        @if($setting->contact_number_1) | Phone: {{ $setting->contact_number_1 }} @endif
                        @if($setting->contact_number_2) , {{ $setting->contact_number_2 }} @endif
                    </div>
                @endif
                @if($setting->slogan)
                    <div>{{ $setting->slogan }}</div>
                @endif
            </div>
        </td>
        <td class="logo-cell"></td>
    </tr>
</table>
@endif

<h2>{{ __('Student List') }}</h2>
@if(!empty($filterHeading['class']) || !empty($filterHeading['section']) || !empty($filterHeading['group']) || !empty($filterHeading['session']))
<p class="sub" style="margin-bottom: 4px;">
    @if(!empty($filterHeading['session']))
        {{ __('Session') }}: {{ $filterHeading['session'] }}
    @endif
    @if(!empty($filterHeading['class']))
        &nbsp;|&nbsp; {{ __('Class') }}: {{ $filterHeading['class'] }}
    @endif
    @if(!empty($filterHeading['section']))
        &nbsp;|&nbsp; {{ __('Section') }}: {{ $filterHeading['section'] }}
    @endif
    @if(!empty($filterHeading['group']))
        &nbsp;|&nbsp; {{ __('Group') }}: {{ $filterHeading['group'] }}
    @endif
</p>
@endif
<p class="sub">
    {{ __('Total Students') }}: {{ $students->count() }}
    &nbsp;|&nbsp; {{ __('Generated') }}: {{ now()->format('d M Y, h:i A') }}
</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            @foreach($selectedColumns as $column)
                <th>{{ $pdfColumnOptions[$column] ?? ucfirst(str_replace('_', ' ', $column)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
