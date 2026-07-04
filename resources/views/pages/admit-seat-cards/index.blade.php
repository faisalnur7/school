@extends('layouts.master')

@section('styles')
    <style>
        @include('pages.admit-seat-cards._styles')
        .admit-seat-cards-page .admit-seat-typography-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.96) 0%, rgba(255, 255, 255, 0.98) 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-typography-header {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e5e7eb;
            padding: 0.9rem 1rem;
        }

        .admit-seat-cards-page .admit-seat-typography-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .admit-seat-typography-row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-bottom: 0.8rem;
            padding: 0.9rem 0.95rem;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.9) 100%);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            align-items: center;
        }

        .admit-seat-cards-page .admit-seat-typography-row:last-child {
            margin-bottom: 0;
        }

        .admit-seat-cards-page .admit-seat-typography-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page .admit-seat-layout-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 0.96) 0%, rgba(255, 255, 255, 0.98) 100%);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-layout-header {
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .admit-seat-cards-page .admit-seat-layout-header .badge {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .admit-seat-cards-page .admit-seat-layout-header h6 {
            letter-spacing: -0.01em;
        }

        .admit-seat-cards-page .admit-seat-layout-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .admit-seat-layout-note {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 0.85rem;
            padding: 0.55rem 0.75rem;
            border: 1px solid #dbe4ee;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.8);
            color: #475569;
            font-size: 0.78rem;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
        }

        .admit-seat-cards-page .admit-seat-layout-field {
            height: 100%;
            padding: 0.95rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.94) 100%);
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
        }

        .admit-seat-cards-page .admit-seat-layout-field:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page .admit-seat-layout-label {
            margin: 0;
            font-size: 0.76rem;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .admit-seat-cards-page .card-settings-modal-content label[data-toggle="tooltip"] {
            cursor: help;
        }

        .admit-seat-cards-page .admit-seat-layout-control {
            width: 100%;
            min-height: 42px;
        }

        .admit-seat-cards-page .csm-color-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: nowrap;
        }

        .admit-seat-cards-page .csm-typography-control {
            min-height: 38px;
            border-radius: 12px;
            border-color: #cbd5e1;
            box-shadow: none;
        }

        .admit-seat-cards-page .csm-typography-control:focus {
            border-color: #94a3b8;
            box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.16);
        }

        .admit-seat-cards-page .csm-color-native {
            width: 54px !important;
            min-width: 54px !important;
            max-width: 54px !important;
            height: 38px !important;
            padding: 0.18rem !important;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            cursor: pointer;
            flex: 0 0 54px;
        }

        .admit-seat-cards-page .csm-color-native::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .admit-seat-cards-page .csm-color-native::-webkit-color-swatch {
            border: 0;
            border-radius: 8px;
        }

        .admit-seat-cards-page .csm-color-preview {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
        }

        .admit-seat-cards-page .card-settings-modal-content {
            border: 1px solid #dbe4ee;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 42%);
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.18);
        }

        .admit-seat-cards-page .card-settings-modal-header {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .admit-seat-cards-page .card-settings-modal-body {
            padding: 1rem 1.2rem 1.2rem;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 18%);
        }

        .admit-seat-cards-page .card-settings-modal-footer {
            padding: 1rem 1.2rem;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .admit-seat-cards-page .admit-seat-cards-modal-settings .admit-seat-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.35rem;
            padding: 0.28rem;
            border: 1px solid #dbe4ee;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
        }

        .admit-seat-cards-page .admit-seat-cards-modal-settings .admit-seat-tabs .nav-item {
            margin-bottom: 0;
            flex: 1 1 0;
            min-width: 0;
        }

        .admit-seat-cards-page .admit-seat-cards-modal-settings .admit-seat-tabs .nav-link {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 999px;
            padding: 0.42rem 0.78rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #475569;
            background: transparent !important;
            white-space: nowrap;
            text-align: center;
            line-height: 1.1;
            transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        }

        .admit-seat-cards-page .admit-seat-cards-modal-settings .admit-seat-tabs .nav-link:hover {
            color: #0f172a;
            background: rgba(226, 232, 240, 0.55);
        }

        .admit-seat-cards-page .admit-seat-cards-modal-settings .admit-seat-tabs .nav-link.active {
            color: #fff;
            background: #2563eb !important;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel > .tab-pane > .card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel > .tab-pane > .card > .card-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel > .tab-pane > .card > .card-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .card-settings-modal-content .form-control:not([type="color"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .admit-seat-cards-page .card-settings-modal-content .custom-select,
        .admit-seat-cards-page .card-settings-modal-content select.form-control,
        .admit-seat-cards-page .card-settings-modal-content textarea.form-control {
            min-height: 42px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background-color: #fff;
            color: #0f172a;
            box-shadow: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .admit-seat-cards-page .card-settings-modal-content .form-control:not([type="color"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        .admit-seat-cards-page .card-settings-modal-content .custom-select:focus,
        .admit-seat-cards-page .card-settings-modal-content select.form-control:focus,
        .admit-seat-cards-page .card-settings-modal-content textarea.form-control:focus {
            border-color: #94a3b8;
            box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.16);
        }

        .admit-seat-cards-page .card-settings-modal-content input[type="number"] {
            font-variant-numeric: tabular-nums;
        }

        .admit-seat-cards-page .card-settings-modal-content .btn-group-toggle .btn {
            border-radius: 12px;
            border-color: #cbd5e1;
            background: #fff;
            color: #475569;
            font-weight: 700;
            box-shadow: none;
        }

        .admit-seat-cards-page .card-settings-modal-content .btn-group-toggle .btn.active {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-color: #1d4ed8;
            color: #fff;
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18);
        }

        .admit-seat-cards-page .card-settings-modal-content .text-muted.d-block,
        .admit-seat-cards-page .card-settings-modal-content .small.text-muted.d-block {
            font-size: 0.72rem;
            line-height: 1.25;
            color: #94a3b8 !important;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-panel {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-header {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #eef2f7;
            padding: 0.95rem 1rem;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.75rem;
            align-items: end;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-control {
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-control:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page .admit-seat-cards-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            grid-column: 1 / -1;
        }

        .admit-seat-cards-page .result-filter-icon-btn {
            min-width: 46px;
            min-height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1rem;
        }

        @media (max-width: 1199.98px) {
            .admit-seat-cards-page .admit-seat-cards-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .admit-seat-cards-page .admit-seat-cards-filter-grid {
                grid-template-columns: 1fr;
            }

            .admit-seat-cards-page .admit-seat-cards-filter-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .admit-seat-cards-page .admit-seat-cards-filter-actions > * {
                width: 100%;
                justify-content: center;
            }
        }

        @media print {
            .card {
                border: none;
                box-shadow: none;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
<div class="container-fluid admit-seat-cards-page">
    <div class="card card-outline no-print result-filter-panel admit-seat-cards-filter-panel">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 admit-seat-cards-filter-header">
            <div class="flex flex-col">
                <h3 class="card-title mb-0 text-dark">
                    <i class="fas fa-filter mr-2 text-info"></i>Filter Options
                </h3>
                <small class="text-muted">Generate admit or seat cards by year, class, exam, and layout.</small>
            </div>
        </div>
        <div class="card-body admit-seat-cards-filter-body">
            <form method="GET" action="{{ route('results.admit-seat-cards.index') }}" id="filterForm" class="admit-seat-cards-filter-form">
                <div class="admit-seat-cards-filter-grid">
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">— Select Year —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Class</label>
                        <select name="class_id" id="classSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Group</label>
                        <select name="group_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Groups</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Type</label>
                        <select name="exam_type" class="form-control form-control-sm admit-seat-cards-filter-control" id="examTypeSelect">
                            <option value="">All Types</option>
                            <option value="tutorial" {{ ($examType ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                            <option value="term" {{ ($examType ?? '') === 'term' ? 'selected' : '' }}>Terminal Exam</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Name</label>
                        <select name="exam_id" class="form-control form-control-sm admit-seat-cards-filter-control" id="examSelect">
                            <option value="">All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} ({{ $exam->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Card Type</label>
                        <select name="card_type" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="admit_card" {{ ($cardType ?? 'admit_card') === 'admit_card' ? 'selected' : '' }}>Admit Card</option>
                            <option value="seat_card" {{ ($cardType ?? 'admit_card') === 'seat_card' ? 'selected' : '' }}>Seat Card</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Student ID</label>
                        <input
                            type="text"
                            name="student_cid"
                            class="form-control form-control-sm admit-seat-cards-filter-control"
                            value="{{ request('student_cid') }}"
                            placeholder="Enter Student ID"
                            autocomplete="off">
                    </div>
                    <div class="admit-seat-cards-filter-actions">
                        <button type="button" class="btn btn-outline-primary btn-sm result-filter-icon-btn" data-toggle="modal" data-target="#cardSettingsModal" title="Card settings" aria-label="Card settings">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <button type="submit" class="btn btn-dark btn-sm result-filter-icon-btn" title="Generate" aria-label="Generate">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="{{ route('results.admit-seat-cards.index') }}" class="btn btn-outline-secondary btn-sm result-filter-icon-btn" title="Reset" aria-label="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @if($students->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm result-filter-icon-btn" onclick="window.print()" title="Print" aria-label="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route('results.admit-seat-cards.pdf', request()->query()) }}" class="btn btn-danger btn-sm result-filter-icon-btn" target="_blank" title="PDF" aria-label="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('session_id') && !request('student_cid'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year or enter a Student ID to generate admit or seat cards.</p>
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        @php
            $requestedCardsPerPage = (int) ($layout['requestedCardsPerPage'] ?? $layout['cardsPerPage'] ?? 8);
            $effectiveCardsPerPage = (int) ($layout['cardsPerPage'] ?? 8);
            $maxCardsPerPage = (int) ($layout['maxCardsPerPage'] ?? $effectiveCardsPerPage);
            $layoutIsClamped = $requestedCardsPerPage > $effectiveCardsPerPage;
        @endphp

        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">{{ $students->count() }} Students</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $effectiveCardsPerPage }} cards/page
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $layout['cardsPerRow'] ?? 2 }} cards/row
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['cardWidthMm'] ?? 0, 1) }}mm × {{ number_format($layout['cardHeightMm'] ?? 0, 1) }}mm
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['gridGapMm'] ?? 0, 1) }}mm gap
            </span>
        </div>

        @if($layoutIsClamped)
            <div class="alert alert-warning no-print py-2 px-3 mb-3">
                Requested {{ $requestedCardsPerPage }} cards/page, but only {{ $effectiveCardsPerPage }} fit on A4 with the current card size, row count, and gap.
                Reduce the card height or gap if you need more on one page.
            </div>
        @endif

        @include('pages.admit-seat-cards._cards', [
            'students' => $students,
            'setting' => $setting,
            'cardSettings' => $cardSettings ?? null,
            'renderForPdf' => false,
            'cardType' => $cardType ?? 'admit_card',
            'examType' => $examType ?? null,
            'selectedExam' => $selectedExam ?? null,
            'layout' => $layout ?? [],
        ])
    @endif
</div>

<div class="modal fade" id="cardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="cardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl card-settings-modal-dialog" role="document">
        <div class="modal-content card-settings-modal-content">
            <form method="POST" action="{{ route('results.admit-seat-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="cardSettingsModalLabel">{{ (old('card_type', $cardType ?? 'admit_card') === 'seat_card') ? 'Seat Card Settings' : 'Admit Card Settings' }}</h5>
                        <small class="text-muted d-block" id="cardSettingsModalTypeLabel">Switch between admit and seat card presets.</small>
                        <small class="text-muted d-block">Save a single layout profile for search, print, and PDF output.</small>
                    </div>
                    <div class="btn-group btn-group-sm csm-type-switcher" role="group" aria-label="Card type selector">
                        <button type="button" class="btn btn-outline-primary js-card-type-switch {{ (old('card_type', $cardType ?? 'admit_card') === 'admit_card') ? 'active' : '' }}" data-card-type="admit_card" data-card-label="Admit Card Settings">Admit Card</button>
                        <button type="button" class="btn btn-outline-primary js-card-type-switch {{ (old('card_type', $cardType ?? 'admit_card') === 'seat_card') ? 'active' : '' }}" data-card-type="seat_card" data-card-label="Seat Card Settings">Seat Card</button>
                    </div>
                    <span id="cardSettingsDirtyBadge" class="badge badge-warning align-self-center d-none">Unsaved changes</span>
                    <button type="button" class="close card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ old('card_type', $cardType ?? 'admit_card') }}">
                    <input type="hidden" name="card_show_school_detail_back" value="{{ old('card_show_school_detail_back', $cardSettings?->card_show_school_detail_back ?? true) ? 1 : 0 }}">
                    <input type="hidden" name="card_show_slogan_back" value="{{ old('card_show_slogan_back', $cardSettings?->card_show_slogan_back ?? true) ? 1 : 0 }}">
                    <input type="hidden" name="card_show_title_back" value="{{ old('card_show_title_back', $cardSettings?->card_show_title_back ?? true) ? 1 : 0 }}">
                    <input type="hidden" name="card_show_back_notice" value="{{ old('card_show_back_notice', $cardSettings?->card_show_back_notice ?? true) ? 1 : 0 }}">
                    @php
                        $selectedColorType = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
                        $selectedTransparent = old('card_is_transparent', $cardSettings?->card_is_transparent ?? false);
                        $resolveLogoUrl = function (?string $path) {
                            if (!$path) {
                                return null;
                            }

                            return file_exists(public_path($path)) ? asset($path) : null;
                        };
                        $schoolLogoUrl = $resolveLogoUrl($setting?->logo ?? null);
                        $currentCardLogoUrl = $resolveLogoUrl($cardSettings?->card_logo ?? null) ?: $schoolLogoUrl;
                    @endphp
                    <div class="row align-items-stretch admit-seat-cards-modal-layout">
                        <div class="col-12 col-lg-5 mb-3 admit-seat-cards-modal-preview">
                            <div class="csm-preview-sticky">
                                @include('pages.card-settings._live-preview', [
                                    'prefix' => 'admitSeat',
                                    'previewType' => 'admit',
                                    'showBack' => false,
                                    'previewLabel' => $cardType === 'seat_card' ? 'Seat Card Preview' : 'Admit Card Preview',
                                    'schoolName' => $setting?->name ?? 'School Name',
                                    'schoolDetailLine' => $setting?->address ?? '',
                                    'slogan' => $setting?->slogan ?? 'Stay Green, Be Bright',
                                    'cardLabel' => $cardType === 'seat_card' ? 'SEAT CARD' : 'ADMIT CARD',
                                    'backTitle' => 'BACK',
                                    'backNotice' => 'If found, please return to the school.',
                                    'examTypeLabel' => $examType ? (strtolower($examType) === 'term' ? 'Terminal Exam' : 'Tutorial Exam') : null,
                                    'examName' => $selectedExam?->name,
                                    'footerLines' => array_values(array_filter([
                                        $setting?->contact_number_1,
                                        $setting?->whatsapp_number,
                                    ])),
                                    'logoUrl' => $currentCardLogoUrl,
                                    'showSchoolDetailFront' => $cardSettings?->card_show_school_detail_front ?? true,
                                    'showSloganFront' => $cardSettings?->card_show_slogan_front ?? true,
                                    'showTitleFront' => $cardSettings?->card_show_title_front ?? true,
                                    'showLogoFront' => $cardSettings?->card_show_logo_front ?? true,
                                    'showPhotoFront' => $cardSettings?->card_show_photo_front ?? true,
                                    'showExamTypeFront' => $cardSettings?->card_show_exam_type_front ?? true,
                                    'showExamNameFront' => $cardSettings?->card_show_exam_name_front ?? true,
                                    'showFooterFront' => $cardSettings?->card_show_footer_front ?? true,
                                    'previewCardWidthValue' => $cardSettings?->card_width_value ?? 9.4,
                                    'previewCardHeightValue' => $cardSettings?->card_height_value ?? 6.6,
                                    'previewCardDimensionUnit' => $cardSettings?->card_dimension_unit ?? 'cm',
                                    'focusTargets' => [
                                        'logo' => 'admitSeatCardLogoInput',
                                        'school_name' => 'admitSeatSchoolNameColor',
                                        'school_detail' => 'admitSeatSchoolDetailColor',
                                        'title' => 'admitSeatTitleColor',
                                        'name' => 'admitSeatNameColor',
                                        'student_detail_alignment' => 'admitSeatStudentDetailAlignment',
                                        'student_detail_font_size' => 'admitSeatStudentDetailFontSize',
                                        'student_detail_color' => 'admitSeatStudentDetailColor',
                                        'exam_type' => 'admitSeatExamTypeColor',
                                        'exam_name' => 'admitSeatExamNameColor',
                                        'footer' => 'admitSeatExamNameColor',
                                    ],
                                ])
                            </div>
                        </div>

                        <div class="col-12 col-lg-7 admit-seat-cards-modal-settings">
                            <ul class="nav admit-seat-tabs csm-section-tabs mb-2" id="admitSeatSettingsTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="admitSeatLayoutTab" data-toggle="tab" href="#admitSeatLayoutPane" role="tab" aria-controls="admitSeatLayoutPane" aria-selected="true">Layout &amp; Grid</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="admitSeatPhotoTab" data-toggle="tab" href="#admitSeatPhotoPane" role="tab" aria-controls="admitSeatPhotoPane" aria-selected="false">Photo &amp; Logo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="admitSeatTypographyTab" data-toggle="tab" href="#admitSeatTypographyPane" role="tab" aria-controls="admitSeatTypographyPane" aria-selected="false">Typography &amp; Colors</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="admitSeatBackgroundTab" data-toggle="tab" href="#admitSeatBackgroundPane" role="tab" aria-controls="admitSeatBackgroundPane" aria-selected="false">Background</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="admitSeatVisibilityTab" data-toggle="tab" href="#admitSeatVisibilityPane" role="tab" aria-controls="admitSeatVisibilityPane" aria-selected="false">Visibility</a>
                                </li>
                            </ul>

                            <div class="tab-content admit-seat-cards-settings-panel">
                                <div class="tab-pane fade show active" id="admitSeatLayoutPane" role="tabpanel" aria-labelledby="admitSeatLayoutTab">
                                    <div class="card mb-2 shadow-sm admit-seat-layout-card">
                                        <div class="card-header admit-seat-layout-header">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 0.75rem;">
                                                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                                    <span class="badge badge-primary"><i class="fas fa-vector-square"></i></span>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold">Layout &amp; Grid</h6>
                                                        <small class="text-muted d-block">Tune the card grid, spacing, and page alignment.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body admit-seat-layout-body">
                                            @if(($layoutIsClamped ?? false))
                                                <div class="alert alert-warning py-2 px-3 mb-3 small rounded-lg border-0" style="background:#fff7ed;color:#9a3412;">
                                                    Only {{ $maxCardsPerPage }} cards fit on A4 with the current layout.
                                                </div>
                                            @endif

                                            <div class="row">
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatCardsPerPage" data-toggle="tooltip" data-placement="top" title="Total cards rendered on a page." aria-label="Total cards rendered on a page.">Cards / Page</label>
                                                    <input type="number" name="cards_per_page" id="admitSeatCardsPerPage" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="1" max="12" value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 8) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatCardsPerRow" data-toggle="tooltip" data-placement="top" title="How many cards sit side by side." aria-label="How many cards sit side by side.">Cards / Row</label>
                                                    <input type="number" name="cards_per_row" id="admitSeatCardsPerRow" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="1" max="10" value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatGridGap" data-toggle="tooltip" data-placement="top" title="Spacing between cards on the sheet." aria-label="Spacing between cards on the sheet.">Grid Gap</label>
                                                    <input type="number" name="grid_gap_value" id="admitSeatGridGap" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="0.1" step="0.1" value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.85) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatCardWidth" data-toggle="tooltip" data-placement="top" title="Width of each rendered card." aria-label="Width of each rendered card.">Card Width</label>
                                                    <input type="number" name="card_width_value" id="admitSeatCardWidth" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="0.1" step="0.1" value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 9.4) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatCardHeight" data-toggle="tooltip" data-placement="top" title="Height of each rendered card." aria-label="Height of each rendered card.">Card Height</label>
                                                    <input type="number" name="card_height_value" id="admitSeatCardHeight" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="0.1" step="0.1" value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 6.6) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatDimensionUnit" data-toggle="tooltip" data-placement="top" title="Controls all layout measurements." aria-label="Controls all layout measurements.">Unit</label>
                                                    <select name="card_dimension_unit" id="admitSeatDimensionUnit" class="csm-input csm-select form-control form-control-sm admit-seat-layout-control">
                                                        <option value="cm" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                                                        <option value="px" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'px' ? 'selected' : '' }}>Pixel (px)</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatFrontAlignment" data-toggle="tooltip" data-placement="top" title="Controls the front card content alignment." aria-label="Controls the front card content alignment.">Front Alignment</label>
                                                    <select name="card_front_alignment" id="admitSeatFrontAlignment" class="csm-input csm-select form-control form-control-sm admit-seat-layout-control">
                                                        <option value="left" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                        <option value="center" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                        <option value="right" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatBackAlignment" data-toggle="tooltip" data-placement="top" title="Controls the back card content alignment." aria-label="Controls the back card content alignment.">Back Alignment</label>
                                                    <select name="card_back_alignment" id="admitSeatBackAlignment" class="csm-input csm-select form-control form-control-sm admit-seat-layout-control">
                                                        <option value="left" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                        <option value="center" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                        <option value="right" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                    </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatFrontPadding" data-toggle="tooltip" data-placement="top" title="Inner spacing inside the front card." aria-label="Inner spacing inside the front card.">Front Padding</label>
                                                    <input type="number" name="card_front_padding_value" id="admitSeatFrontPadding" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="0" step="0.1" value="{{ old('card_front_padding_value', $cardSettings?->card_front_padding_value ?? 0.8) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1">
                                                    <div class="admit-seat-layout-field">
                                                    <label class="admit-seat-layout-label" for="admitSeatBackPadding" data-toggle="tooltip" data-placement="top" title="Inner spacing inside the back card." aria-label="Inner spacing inside the back card.">Back Padding</label>
                                                    <input type="number" name="card_back_padding_value" id="admitSeatBackPadding" class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control" min="0" step="0.1" value="{{ old('card_back_padding_value', $cardSettings?->card_back_padding_value ?? 0.8) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admitSeatPhotoPane" role="tabpanel" aria-labelledby="admitSeatPhotoTab">
                                    <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i class="fas fa-image"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Photo &amp; Logo</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Photo Width</label>
                                                            <input type="number" name="card_photo_width_value" id="admitSeatPhotoWidth" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_photo_width_value', $cardSettings?->card_photo_width_value ?? 1.8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Photo Height</label>
                                                            <input type="number" name="card_photo_height_value" id="admitSeatPhotoHeight" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Logo Size</label>
                                                            <input type="number" name="card_logo_size_value" id="admitSeatLogoSize" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr class="my-2">

                                            <div class="row align-items-end">
                                                    <div class="col-12 col-md-9 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Card Logo</label>
                                                            <div
                                                                id="admitSeatCardLogoDropzone"
                                                                class="dropzone rounded border bg-white p-2"
                                                                data-existing-image-url="{{ $currentCardLogoUrl ?: '' }}"
                                                                data-existing-image-name="{{ basename($cardSettings?->card_logo ?? $setting?->logo ?? 'card-logo.png') }}"
                                                                style="min-height: 110px;"
                                                            >
                                                                <input type="file" name="card_logo" id="admitSeatCardLogoInput" class="d-none" accept="image">
                                                                <div class="dz-message needsclick text-center py-3">
                                                                    <div class="font-weight-bold">Drop logo here or click to browse</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admitSeatTypographyPane" role="tabpanel" aria-labelledby="admitSeatTypographyTab">
                                    <div class="card mb-2 shadow-sm admit-seat-typography-card">
                                            <div class="card-header py-2 d-flex align-items-center justify-content-between admit-seat-typography-header">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;"><i class="fas fa-font"></i></span>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold" style="letter-spacing:-0.01em;">Typography &amp; Colors</h6>
                                                        <small class="text-muted">Fine-tune font scale and color tone for the card face.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body admit-seat-typography-body">
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">School Name</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_school_name_font_size" id="admitSeatSchoolNameFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_school_name_font_size', $cardSettings?->card_school_name_font_size ?? 7.2) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_school_name_text_color" id="admitSeatSchoolNameColor" class="csm-color-native" value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatSchoolNameColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">School Details</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_school_detail_font_size" id="admitSeatSchoolDetailFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_school_detail_font_size', $cardSettings?->card_school_detail_font_size ?? 5.4) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_school_detail_text_color" id="admitSeatSchoolDetailColor" class="csm-color-native" value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatSchoolDetailColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Slogan</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_slogan_font_size" id="admitSeatSloganFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_slogan_font_size', $cardSettings?->card_slogan_font_size ?? 4.8) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_slogan_text_color" id="admitSeatSloganColor" class="csm-color-native" value="{{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatSloganColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Card Title</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_title_font_size" id="admitSeatTitleFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_title_font_size', $cardSettings?->card_title_font_size ?? 4.7) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_title_text_color" id="admitSeatTitleColor" class="csm-color-native" value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatTitleColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Student Name</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_name_font_size" id="admitSeatNameFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_name_font_size', $cardSettings?->card_name_font_size ?? 7.2) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_name_text_color" id="admitSeatNameColor" class="csm-color-native" value="{{ old('card_name_text_color', $cardSettings?->card_name_text_color ?? '#111827') }}">
                                                            <span id="admitSeatNameColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Exam Type</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_exam_type_font_size" id="admitSeatExamTypeFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_exam_type_font_size', $cardSettings?->card_exam_type_font_size ?? 7.4) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_exam_type_text_color" id="admitSeatExamTypeColor" class="csm-color-native" value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatExamTypeColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Exam Name</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_exam_name_font_size" id="admitSeatExamNameFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_exam_name_font_size', $cardSettings?->card_exam_name_font_size ?? 6.8) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_exam_name_text_color" id="admitSeatExamNameColor" class="csm-color-native" value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatExamNameColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center admit-seat-typography-row mb-0">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Student Detail</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_student_detail_font_size" id="admitSeatStudentDetailFontSize" class="csm-input csm-typography-control form-control form-control-sm" min="1" step="0.1" value="{{ old('card_student_detail_font_size', $cardSettings?->card_student_detail_font_size ?? 8.5) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="csm-color-row">
                                                            <input type="color" name="card_student_detail_text_color" id="admitSeatStudentDetailColor" class="csm-color-native" value="{{ old('card_student_detail_text_color', $cardSettings?->card_student_detail_text_color ?? '#111827') }}">
                                                            <span id="admitSeatStudentDetailColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Student Detail Alignment</label>
                                                            <select name="card_student_detail_alignment" id="admitSeatStudentDetailAlignment" class="csm-input csm-select form-control form-control-sm">
                                                                <option value="left" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                                                                <option value="center" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'center' ? 'selected' : '' }}>Center</option>
                                                                <option value="right" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'right' ? 'selected' : '' }}>Right</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admitSeatBackgroundPane" role="tabpanel" aria-labelledby="admitSeatBackgroundTab">
                                    <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i class="fas fa-fill-drip"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Background</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-group mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" name="card_is_transparent" id="admitSeatCardIsTransparent" value="1" {{ $selectedTransparent ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatCardIsTransparent">Transparent Background</label>
                                                    </div>
                                                </div>

                                                <div class="admit-seat-card-color-controls {{ $selectedTransparent ? 'd-none' : '' }}">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Background Type</label>
                                                            <div class="btn-group btn-group-toggle csm-bg-toggle d-flex" data-toggle="buttons">
                                                                <label class="btn btn-outline-primary btn-sm flex-fill {{ $selectedColorType === 'gradient' ? 'active' : '' }}">
                                                                    <input type="radio" name="card_color_type" value="gradient" {{ $selectedColorType === 'gradient' ? 'checked' : '' }}> Gradient
                                                                </label>
                                                                <label class="btn btn-outline-primary btn-sm flex-fill {{ $selectedColorType === 'solid' ? 'active' : '' }}">
                                                                    <input type="radio" name="card_color_type" value="solid" {{ $selectedColorType === 'solid' ? 'checked' : '' }}> Solid
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Gradient Start</label>
                                                            <span class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                <input type="color" name="card_color_gradient_1" id="admitSeatCardColorGradient1" class="csm-color-native" value="{{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }}">
                                                                <span id="admitSeatCardColorGradient1Preview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Gradient End</label>
                                                            <span class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                <input type="color" name="card_color_gradient_2" id="admitSeatCardColorGradient2" class="csm-color-native" value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                                                <span id="admitSeatCardColorGradient2Preview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2 admit-seat-card-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Solid Color</label>
                                                            <span class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                <input type="color" name="card_solid_color" id="admitSeatCardSolidColor" class="csm-color-native" value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                                                <span id="admitSeatCardSolidColorPreview" class="d-inline-block rounded ml-2" style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-0 mt-2">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Preview</label>
                                                    <div id="admitSeatCardThemePreview" class="border rounded" style="height: 36px;"></div>
                                                </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admitSeatVisibilityPane" role="tabpanel" aria-labelledby="admitSeatVisibilityTab">
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-eye"></i></span>
                                                <h6 class="mb-0 font-weight-bold">Visibility</h6>
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_logo_front" id="admitSeatShowLogoFront" {{ old('card_show_logo_front', $cardSettings?->card_show_logo_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowLogoFront">Front Logo</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_logo_back" id="admitSeatShowLogoBack" {{ old('card_show_logo_back', $cardSettings?->card_show_logo_back ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowLogoBack">Back Logo</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_photo_front" id="admitSeatShowPhotoFront" {{ old('card_show_photo_front', $cardSettings?->card_show_photo_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowPhotoFront">Photo</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_school_detail_front" id="admitSeatShowSchoolDetailFront" {{ old('card_show_school_detail_front', $cardSettings?->card_show_school_detail_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowSchoolDetailFront">School Detail</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_slogan_front" id="admitSeatShowSloganFront" {{ old('card_show_slogan_front', $cardSettings?->card_show_slogan_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowSloganFront">Slogan</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_title_front" id="admitSeatShowTitleFront" {{ old('card_show_title_front', $cardSettings?->card_show_title_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowTitleFront">Front Title</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_footer_front" id="admitSeatShowFooterFront" {{ old('card_show_footer_front', $cardSettings?->card_show_footer_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowFooterFront">Front Footer</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_footer_back" id="admitSeatShowFooterBack" {{ old('card_show_footer_back', $cardSettings?->card_show_footer_back ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowFooterBack">Back Footer</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_exam_type_front" id="admitSeatShowExamTypeFront" {{ old('card_show_exam_type_front', $cardSettings?->card_show_exam_type_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowExamTypeFront">Exam Type</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="card_show_exam_name_front" id="admitSeatShowExamNameFront" {{ old('card_show_exam_name_front', $cardSettings?->card_show_exam_name_front ?? true) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="admitSeatShowExamNameFront">Exam Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="csm-footnote">These settings are saved once and used by search and PDF output.</p>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer card-settings-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $cardSettingsPayload = $cardSettingsMap->mapWithKeys(function ($setting) {
        return [
            (string) $setting->card_type => [
                'cards_per_page' => $setting->cards_per_page,
                'cards_per_row' => $setting->cards_per_row,
                'card_width_value' => $setting->card_width_value,
                'card_height_value' => $setting->card_height_value,
                'grid_gap_value' => $setting->grid_gap_value,
                'card_dimension_unit' => $setting->card_dimension_unit,
                'card_front_alignment' => $setting->card_front_alignment,
                'card_back_alignment' => $setting->card_back_alignment,
                'card_front_padding_value' => $setting->card_front_padding_value,
                'card_back_padding_value' => $setting->card_back_padding_value,
                'card_photo_width_value' => $setting->card_photo_width_value,
                'card_photo_height_value' => $setting->card_photo_height_value,
                'card_logo_size_value' => $setting->card_logo_size_value,
                'card_school_name_font_size' => $setting->card_school_name_font_size,
                'card_school_detail_font_size' => $setting->card_school_detail_font_size,
                'card_slogan_font_size' => $setting->card_slogan_font_size,
                'card_slogan_text_color' => $setting->card_slogan_text_color,
                'card_title_font_size' => $setting->card_title_font_size,
                'card_name_font_size' => $setting->card_name_font_size,
                'card_name_text_color' => $setting->card_name_text_color,
                'card_exam_type_font_size' => $setting->card_exam_type_font_size,
                'card_exam_name_font_size' => $setting->card_exam_name_font_size,
                'card_student_detail_alignment' => $setting->card_student_detail_alignment,
                'card_student_detail_font_size' => $setting->card_student_detail_font_size,
                'card_student_detail_text_color' => $setting->card_student_detail_text_color,
                'card_is_transparent' => $setting->card_is_transparent,
                'card_color_type' => $setting->card_color_type,
                'card_color_gradient_1' => $setting->card_color_gradient_1,
                'card_color_gradient_2' => $setting->card_color_gradient_2,
                'card_solid_color' => $setting->card_solid_color,
                'card_school_name_text_color' => $setting->card_school_name_text_color,
                'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                'card_title_text_color' => $setting->card_title_text_color,
                'card_exam_type_text_color' => $setting->card_exam_type_text_color,
                'card_exam_name_text_color' => $setting->card_exam_name_text_color,
                'card_show_school_detail_front' => $setting->card_show_school_detail_front,
                'card_show_school_detail_back' => $setting->card_show_school_detail_back,
                'card_show_slogan_front' => $setting->card_show_slogan_front,
                'card_show_slogan_back' => $setting->card_show_slogan_back,
                'card_show_title_front' => $setting->card_show_title_front,
                'card_show_title_back' => $setting->card_show_title_back,
                'card_show_logo_front' => $setting->card_show_logo_front,
                'card_show_logo_back' => $setting->card_show_logo_back,
                'card_show_photo_front' => $setting->card_show_photo_front,
                'card_show_footer_front' => $setting->card_show_footer_front,
                'card_show_footer_back' => $setting->card_show_footer_back,
                'card_show_exam_type_front' => $setting->card_show_exam_type_front,
                'card_show_exam_name_front' => $setting->card_show_exam_name_front,
                'card_show_back_notice' => $setting->card_show_back_notice,
                'card_logo_url' => ($setting->card_logo && file_exists(public_path($setting->card_logo))) ? asset($setting->card_logo) : null,
            ],
        ];
    })->toArray();
@endphp

<script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
<script>
if (typeof Dropzone !== 'undefined') {
    // Disable auto-discovery before DOMContentLoaded so it cannot attach
    // to this .dropzone element before our custom initializer runs.
    Dropzone.autoDiscover = false;
}

document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const cardTypeSelect = document.querySelector('#filterForm select[name="card_type"]');
    const cardSettingsModal = document.getElementById('cardSettingsModal');
    const cardSettingsForm = cardSettingsModal?.querySelector('form');
    const cardSettingsModalTitle = document.getElementById('cardSettingsModalLabel');
    const cardSettingsTypeLabel = document.getElementById('cardSettingsModalTypeLabel');
    const dirtyBadge = document.getElementById('cardSettingsDirtyBadge');
    const admitSeatCardIsTransparent = document.getElementById('admitSeatCardIsTransparent');
    const admitSeatCardThemePreview = document.getElementById('admitSeatCardThemePreview');
    const admitSeatPreviewLabel = document.getElementById('admitSeatPreviewLabel');
    const admitSeatLivePreviewTitleFront = document.getElementById('admitSeatLivePreviewTitleFront');
    const admitSeatCardColorGradient1 = document.getElementById('admitSeatCardColorGradient1');
    const admitSeatCardColorGradient2 = document.getElementById('admitSeatCardColorGradient2');
    const admitSeatCardSolidColor = document.getElementById('admitSeatCardSolidColor');
    const admitSeatSchoolNameColor = document.getElementById('admitSeatSchoolNameColor');
    const admitSeatSchoolDetailColor = document.getElementById('admitSeatSchoolDetailColor');
    const admitSeatTitleColor = document.getElementById('admitSeatTitleColor');
    const admitSeatExamTypeColor = document.getElementById('admitSeatExamTypeColor');
    const admitSeatExamNameColor = document.getElementById('admitSeatExamNameColor');
    const admitSeatSchoolNameFontSize = document.getElementById('admitSeatSchoolNameFontSize');
    const admitSeatSchoolDetailFontSize = document.getElementById('admitSeatSchoolDetailFontSize');
    const admitSeatSloganFontSize = document.getElementById('admitSeatSloganFontSize');
    const admitSeatSloganColor = document.getElementById('admitSeatSloganColor');
    const admitSeatTitleFontSize = document.getElementById('admitSeatTitleFontSize');
    const admitSeatNameFontSize = document.getElementById('admitSeatNameFontSize');
    const admitSeatNameColor = document.getElementById('admitSeatNameColor');
    const admitSeatExamTypeFontSize = document.getElementById('admitSeatExamTypeFontSize');
    const admitSeatExamNameFontSize = document.getElementById('admitSeatExamNameFontSize');
    const admitSeatStudentDetailAlignment = document.getElementById('admitSeatStudentDetailAlignment');
    const admitSeatStudentDetailFontSize = document.getElementById('admitSeatStudentDetailFontSize');
    const admitSeatStudentDetailColor = document.getElementById('admitSeatStudentDetailColor');
    const admitSeatCardColorGradient1Preview = document.getElementById('admitSeatCardColorGradient1Preview');
    const admitSeatCardColorGradient2Preview = document.getElementById('admitSeatCardColorGradient2Preview');
    const admitSeatCardSolidColorPreview = document.getElementById('admitSeatCardSolidColorPreview');
    let modalScrollY = 0;
    const admitSeatSchoolNameColorPreview = document.getElementById('admitSeatSchoolNameColorPreview');
    const admitSeatSchoolDetailColorPreview = document.getElementById('admitSeatSchoolDetailColorPreview');
    const admitSeatSloganColorPreview = document.getElementById('admitSeatSloganColorPreview');
    const admitSeatTitleColorPreview = document.getElementById('admitSeatTitleColorPreview');
    const admitSeatNameColorPreview = document.getElementById('admitSeatNameColorPreview');
    const admitSeatExamTypeColorPreview = document.getElementById('admitSeatExamTypeColorPreview');
    const admitSeatExamNameColorPreview = document.getElementById('admitSeatExamNameColorPreview');
    const admitSeatStudentDetailColorPreview = document.getElementById('admitSeatStudentDetailColorPreview');
    const admitSeatShowLogoFront = document.getElementById('admitSeatShowLogoFront');
    const admitSeatShowSchoolDetailFront = document.getElementById('admitSeatShowSchoolDetailFront');
    const admitSeatShowSloganFront = document.getElementById('admitSeatShowSloganFront');
    const admitSeatShowTitleFront = document.getElementById('admitSeatShowTitleFront');
    const admitSeatShowPhotoFront = document.getElementById('admitSeatShowPhotoFront');
    const admitSeatShowFooterFront = document.getElementById('admitSeatShowFooterFront');
    const admitSeatShowExamTypeFront = document.getElementById('admitSeatShowExamTypeFront');
    const admitSeatShowExamNameFront = document.getElementById('admitSeatShowExamNameFront');
    const admitSeatCardLogoDropzone = document.getElementById('admitSeatCardLogoDropzone');
    const admitSeatCardLogoInput = document.getElementById('admitSeatCardLogoInput');
    const admitSeatLivePreview = document.getElementById('admitSeatLivePreview');
    const admitSeatLivePreviewLogoFront = document.getElementById('admitSeatLivePreviewLogoFront');
    const admitSeatCardWidth = cardSettingsForm?.elements.namedItem('card_width_value');
    const admitSeatCardHeight = cardSettingsForm?.elements.namedItem('card_height_value');
    const admitSeatCardDimensionUnit = cardSettingsForm?.elements.namedItem('card_dimension_unit');
    const admitSeatFrontAlignment = cardSettingsForm?.elements.namedItem('card_front_alignment');
    const admitSeatPhotoWidth = document.getElementById('admitSeatPhotoWidth');
    const admitSeatPhotoHeight = document.getElementById('admitSeatPhotoHeight');
    const admitSeatLogoSize = document.getElementById('admitSeatLogoSize');
    const selectedSection = @json(request('section_id'));
    const hasValidationErrors = @json($errors->any());
    const cardSettingsMap = @json($cardSettingsPayload);
    const defaultCardType = @json($cardType ?? 'admit_card');
    const fallbackSchoolLogo = @json($schoolLogoUrl);
    let activeCardSettings = {};
    let previewLogoUrl = null;
    let admitSeatCardLogoDropzoneInstance = null;
    const defaultThemeSettings = {
        card_is_transparent: false,
        card_color_type: 'gradient',
        card_front_alignment: 'center',
        card_back_alignment: 'center',
        card_front_padding_value: 0.8,
        card_back_padding_value: 0.8,
        card_photo_width_value: 1.8,
        card_photo_height_value: 2.7,
        card_logo_size_value: 0.8,
        card_school_name_font_size: 7.2,
        card_school_detail_font_size: 5.4,
        card_slogan_font_size: 4.8,
        card_slogan_text_color: '#e5e7eb',
        card_title_font_size: 4.7,
        card_name_font_size: 7.2,
        card_name_text_color: '#111827',
        card_exam_type_font_size: 7.4,
        card_exam_name_font_size: 6.8,
        card_student_detail_alignment: 'left',
        card_student_detail_font_size: 8.5,
        card_student_detail_text_color: '#111827',
        card_show_school_detail_front: true,
        card_show_school_detail_back: true,
        card_show_slogan_front: true,
        card_show_slogan_back: true,
        card_show_title_front: true,
        card_show_title_back: true,
        card_color_gradient_1: '#1e3a5f',
        card_color_gradient_2: '#2563eb',
        card_solid_color: '#1e3a5f',
        card_school_name_text_color: '#ffffff',
        card_school_detail_text_color: '#e5e7eb',
        card_title_text_color: '#ffffff',
        card_exam_type_text_color: '#ffffff',
        card_exam_name_text_color: '#e5e7eb',
        card_show_logo_front: true,
        card_show_logo_back: true,
        card_show_photo_front: true,
        card_show_footer_front: true,
        card_show_footer_back: true,
        card_show_exam_type_front: true,
        card_show_exam_name_front: true,
        card_show_back_notice: true,
    };

    function settingKeyFromCardType(cardType) {
        return cardType === 'seat_card' ? '2' : '1';
    }

    function settingLabelFromCardType(cardType) {
        return cardType === 'seat_card' ? 'Seat Card Settings' : 'Admit Card Settings';
    }

    function previewLabelFromCardType(cardType) {
        return cardType === 'seat_card' ? 'Seat Card Preview' : 'Admit Card Preview';
    }

    function getSelectedCardColorType() {
        return cardSettingsForm?.querySelector('input[name="card_color_type"]:checked')?.value || 'gradient';
    }

    function setSelectedCardColorType(value) {
        if (!cardSettingsForm) return;

        const normalized = value === 'solid' ? 'solid' : 'gradient';
        cardSettingsForm.querySelectorAll('input[name="card_color_type"]').forEach((radio) => {
            radio.checked = radio.value === normalized;
            radio.closest('label')?.classList.toggle('active', radio.checked);
        });
    }

    function syncCardTypeSwitcher(cardType) {
        const normalized = cardType === 'seat_card' ? 'seat_card' : 'admit_card';
        $('.js-card-type-switch').each(function () {
            const $button = $(this);
            const isActive = $button.data('card-type') === normalized;
            $button.toggleClass('active btn-primary', isActive);
            $button.toggleClass('btn-outline-primary', !isActive);
        });

        if (cardTypeSelect) {
            cardTypeSelect.value = normalized;
        }
    }

    function setDirtyState(isDirty) {
        if (!dirtyBadge) return;
        dirtyBadge.classList.toggle('d-none', !isDirty);
    }

    function setPreviewElementVisible(selector, isVisible) {
        if (!admitSeatLivePreview) return;
        admitSeatLivePreview.querySelectorAll(selector).forEach((element) => {
            element.classList.toggle('d-none', !isVisible);
        });
    }

    function lockPageScroll() {
        modalScrollY = window.scrollY || window.pageYOffset || 0;
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${modalScrollY}px`;
        document.body.style.width = '100%';
    }

    function unlockPageScroll() {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        window.scrollTo(0, modalScrollY || 0);
    }

    function normalizeTooltipText(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function getTooltipTextFromLabel($label) {
        const explicit = normalizeTooltipText($label.attr('data-tooltip-content'));
        if (explicit) {
            return explicit;
        }

        const $hint = $label.nextAll('.admit-seat-layout-hint, .text-muted.d-block, .small.text-muted, small.text-muted').first();
        const hintText = normalizeTooltipText($hint.text());
        const existingTitle = normalizeTooltipText($label.attr('title'));
        const labelText = normalizeTooltipText($label.text());

        return hintText || existingTitle || labelText;
    }

    function initializeLayoutTooltips(context) {
        const root = context ? (context.jquery ? context : $(context)) : $('#cardSettingsModal');
        const $labels = root.find('label');
        if (!$labels.length || typeof $labels.tooltip !== 'function') return;

        $labels.each(function () {
            const $label = $(this);
            const tooltipText = getTooltipTextFromLabel($label);

            if (!tooltipText) {
                return;
            }

            $label.attr('title', tooltipText);

            $label.attr('data-toggle', 'tooltip');
            $label.attr('data-placement', $label.attr('data-placement') || 'top');
        });

        $labels.tooltip('dispose');
        $labels.tooltip({
            container: 'body',
            trigger: 'hover focus',
        });
    }

    function syncLogoFileInput(file) {
        if (!admitSeatCardLogoInput) {
            return;
        }

        if (!file) {
            admitSeatCardLogoInput.value = '';
            return;
        }

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        admitSeatCardLogoInput.files = dataTransfer.files;
    }

    function updateLogoPreview(url) {
        previewLogoUrl = url || null;

        if (admitSeatLivePreviewLogoFront) {
            admitSeatLivePreviewLogoFront.src = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            admitSeatLivePreviewLogoFront.classList.toggle('d-none', !admitSeatLivePreviewLogoFront.src);
        }
    }

    function resetLogoDropzonePreview() {
        if (!admitSeatCardLogoDropzoneInstance) {
            updateLogoPreview(null);
            syncLogoFileInput(null);
            return;
        }

        admitSeatCardLogoDropzoneInstance.removeAllFiles(true);
        const currentUrl = activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
        if (currentUrl) {
            addExistingLogoPreview(currentUrl, basenameFromPath(activeCardSettings.card_logo_url ? activeCardSettings.card_logo_url : (fallbackSchoolLogo || 'card-logo.png')));
        } else {
            updateLogoPreview(null);
        }
        syncLogoFileInput(null);
    }

    function basenameFromPath(path) {
        if (!path) {
            return 'card-logo.png';
        }

        return String(path).split('/').pop() || 'card-logo.png';
    }

    function addExistingLogoPreview(url, name) {
        if (!admitSeatCardLogoDropzoneInstance || !url) {
            updateLogoPreview(url || null);
            return;
        }

        const mockFile = {
            name: name || 'card-logo.png',
            size: 0,
            accepted: true,
            isExisting: true,
            previewUrl: url,
        };

        admitSeatCardLogoDropzoneInstance.emit('addedfile', mockFile);
        admitSeatCardLogoDropzoneInstance.emit('thumbnail', mockFile, url);
        admitSeatCardLogoDropzoneInstance.emit('complete', mockFile);
        admitSeatCardLogoDropzoneInstance.files.push(mockFile);
        updateLogoPreview(url);
    }

    function initializeLogoDropzone() {
        if (!admitSeatCardLogoDropzone || typeof Dropzone === 'undefined' || admitSeatCardLogoDropzoneInstance) {
            return;
        }

        const existingImageUrl = admitSeatCardLogoDropzone.dataset.existingImageUrl || '';
        const existingImageName = admitSeatCardLogoDropzone.dataset.existingImageName || 'card-logo.png';

        admitSeatCardLogoDropzoneInstance = new Dropzone(admitSeatCardLogoDropzone, {
            url: window.location.href,
            method: 'post',
            autoProcessQueue: false,
            clickable: true,
            maxFiles: 1,
            acceptedFiles: 'image/*',
            previewsContainer: admitSeatCardLogoDropzone,
            addRemoveLinks: true,
            dictDefaultMessage: '',
            previewTemplate: `
                <div class="dz-preview dz-file-preview mb-2 w-100">
                    <div class="d-flex align-items-center border rounded bg-light p-2">
                        <div class="mr-2 flex-shrink-0" style="width:48px;height:48px;">
                            <img data-dz-thumbnail class="rounded border bg-white" style="width:48px;height:48px;object-fit:contain;">
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="text-truncate font-weight-bold small" data-dz-name></div>
                            <div class="small text-muted" data-dz-size></div>
                        </div>
                        <a class="btn btn-sm btn-outline-danger ml-2" data-dz-remove href="javascript:void(0)">Remove</a>
                    </div>
                </div>`,
            init: function () {
                if (existingImageUrl) {
                    addExistingLogoPreview(existingImageUrl, existingImageName);
                }

                this.on('addedfile', function (file) {
                    if (!file || file.accepted === false) {
                        return;
                    }

                    if (file.isExisting) {
                        updateLogoPreview(file.previewUrl || existingImageUrl);
                        return;
                    }

                    const existingFiles = this.files.slice(0, -1);
                    existingFiles.forEach((existingFile) => {
                        if (existingFile !== file) {
                            this.removeFile(existingFile);
                        }
                    });

                    syncLogoFileInput(file);

                    const reader = new FileReader();
                    reader.onload = function (event) {
                        updateLogoPreview(event.target.result);
                    };
                    reader.readAsDataURL(file);
                });

                this.on('removedfile', function () {
                    if (this.files.length === 0) {
                        syncLogoFileInput(null);
                        updateLogoPreview(null);
                    }
                });
            },
        });
    }

    function applyCardSettings(cardType) {
        if (!cardSettingsForm) return;

        const normalizedCardType = cardType || defaultCardType;
        const key = settingKeyFromCardType(normalizedCardType);
        const settings = cardSettingsMap[key] || cardSettingsMap['1'] || {};
        activeCardSettings = settings;
        previewLogoUrl = null;

        const fields = [
            'cards_per_page',
            'cards_per_row',
            'card_width_value',
            'card_height_value',
            'grid_gap_value',
            'card_dimension_unit',
            'card_front_alignment',
            'card_back_alignment',
            'card_front_padding_value',
            'card_back_padding_value',
            'card_photo_width_value',
            'card_photo_height_value',
            'card_logo_size_value',
            'card_school_name_font_size',
            'card_school_detail_font_size',
            'card_slogan_font_size',
            'card_slogan_text_color',
            'card_title_font_size',
            'card_name_font_size',
            'card_name_text_color',
            'card_exam_type_font_size',
            'card_exam_name_font_size',
            'card_student_detail_alignment',
            'card_student_detail_font_size',
            'card_student_detail_text_color',
            'card_slogan_text_color',
            'card_is_transparent',
            'card_color_gradient_1',
            'card_color_gradient_2',
            'card_solid_color',
            'card_school_name_text_color',
            'card_school_detail_text_color',
            'card_slogan_text_color',
            'card_title_text_color',
            'card_exam_type_text_color',
            'card_exam_name_text_color',
            'card_show_school_detail_front',
            'card_show_school_detail_back',
            'card_show_slogan_front',
            'card_show_slogan_back',
            'card_show_title_front',
            'card_show_title_back',
            'card_show_logo_front',
            'card_show_logo_back',
            'card_show_photo_front',
            'card_show_footer_front',
            'card_show_footer_back',
            'card_show_exam_type_front',
            'card_show_exam_name_front',
            'card_show_back_notice',
        ];
        fields.forEach((field) => {
            const input = cardSettingsForm.elements.namedItem(field);
            const value = field === 'card_is_transparent'
                ? ((settings[field] ?? defaultThemeSettings[field]) ? '1' : '0')
                : (settings[field] ?? defaultThemeSettings[field]);
            if (input && value !== undefined && value !== null) {
                if (input.type === 'checkbox') {
                    input.checked = !!value && value !== '0' && value !== 'false';
                } else {
                    input.value = value;
                }
            }
        });

        setSelectedCardColorType(settings.card_color_type ?? defaultThemeSettings.card_color_type);

        const cardTypeInput = cardSettingsForm.elements.namedItem('card_type');
        if (cardTypeInput) {
            cardTypeInput.value = normalizedCardType;
        }

        if (cardSettingsModalTitle) {
            cardSettingsModalTitle.textContent = settingLabelFromCardType(normalizedCardType);
        }

        if (admitSeatPreviewLabel) {
            admitSeatPreviewLabel.textContent = previewLabelFromCardType(normalizedCardType);
        }

        if (admitSeatLivePreviewTitleFront) {
            admitSeatLivePreviewTitleFront.textContent = normalizedCardType === 'seat_card' ? 'SEAT CARD' : 'ADMIT CARD';
        }

        syncCardTypeSwitcher(normalizedCardType);
        resetLogoDropzonePreview();
        setDirtyState(false);

        refreshCardThemeControls();
    }

    function refreshCardThemeControls() {
        if (!cardSettingsForm) return;

        const isTransparent = admitSeatCardIsTransparent?.checked === true;
        const colorType = getSelectedCardColorType();
        const gradient1 = admitSeatCardColorGradient1?.value || '#1e3a5f';
        const gradient2 = admitSeatCardColorGradient2?.value || '#2563eb';
        const solid = admitSeatCardSolidColor?.value || gradient1;
        const theme = isTransparent
            ? 'transparent'
            : (colorType === 'solid'
                ? solid
                : `linear-gradient(135deg, ${gradient1}, ${gradient2})`);

        if (isTransparent) {
            if (admitSeatSchoolNameColor && admitSeatSchoolNameColor.value === '#ffffff') admitSeatSchoolNameColor.value = '#111827';
            if (admitSeatSchoolDetailColor && admitSeatSchoolDetailColor.value === '#e5e7eb') admitSeatSchoolDetailColor.value = '#334155';
            if (admitSeatTitleColor && admitSeatTitleColor.value === '#ffffff') admitSeatTitleColor.value = '#111827';
            if (admitSeatExamTypeColor && admitSeatExamTypeColor.value === '#ffffff') admitSeatExamTypeColor.value = '#111827';
            if (admitSeatExamNameColor && admitSeatExamNameColor.value === '#e5e7eb') admitSeatExamNameColor.value = '#334155';
        }

        $('.admit-seat-card-color-controls').toggleClass('d-none', isTransparent);

        if (!isTransparent && colorType === 'solid') {
            $('.admit-seat-card-gradient-field').addClass('d-none');
            $('.admit-seat-card-solid-field').removeClass('d-none');
        } else if (!isTransparent) {
            $('.admit-seat-card-gradient-field').removeClass('d-none');
            $('.admit-seat-card-solid-field').addClass('d-none');
        } else {
            $('.admit-seat-card-gradient-field').addClass('d-none');
            $('.admit-seat-card-solid-field').addClass('d-none');
        }

        if (admitSeatCardThemePreview) {
            admitSeatCardThemePreview.style.background = theme;
            admitSeatCardThemePreview.style.borderStyle = isTransparent ? 'dashed' : 'solid';
        }

        if (admitSeatSchoolNameColorPreview) {
            admitSeatSchoolNameColorPreview.style.background = admitSeatSchoolNameColor?.value || '#ffffff';
        }

        if (admitSeatSchoolDetailColorPreview) {
            admitSeatSchoolDetailColorPreview.style.background = admitSeatSchoolDetailColor?.value || '#e5e7eb';
        }

        if (admitSeatSloganFontSize) {
            admitSeatLivePreview.style.setProperty('--admit-card-slogan-font-size', `${parseFloat(admitSeatSloganFontSize.value || '4.8') || 4.8}pt`);
        }

        if (admitSeatSloganColorPreview) {
            admitSeatSloganColorPreview.style.background = admitSeatSloganColor?.value || '#e5e7eb';
        }

        if (admitSeatTitleColorPreview) {
            admitSeatTitleColorPreview.style.background = admitSeatTitleColor?.value || '#ffffff';
        }

        if (admitSeatNameColorPreview) {
            admitSeatNameColorPreview.style.background = admitSeatNameColor?.value || '#111827';
        }

        if (admitSeatExamTypeColorPreview) {
            admitSeatExamTypeColorPreview.style.background = admitSeatExamTypeColor?.value || '#ffffff';
        }

        if (admitSeatExamNameColorPreview) {
            admitSeatExamNameColorPreview.style.background = admitSeatExamNameColor?.value || '#e5e7eb';
        }

        if (admitSeatStudentDetailColorPreview) {
            admitSeatStudentDetailColorPreview.style.background = admitSeatStudentDetailColor?.value || '#111827';
        }

        if (admitSeatCardColorGradient1Preview) {
            admitSeatCardColorGradient1Preview.style.background = gradient1;
        }

        if (admitSeatCardColorGradient2Preview) {
            admitSeatCardColorGradient2Preview.style.background = gradient2;
        }

        if (admitSeatCardSolidColorPreview) {
            admitSeatCardSolidColorPreview.style.background = solid;
        }

        if (admitSeatLivePreview) {
            admitSeatLivePreview.style.setProperty('--preview-bg', theme);
            admitSeatLivePreview.style.setProperty('--preview-school-name-color', admitSeatSchoolNameColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--preview-school-detail-color', admitSeatSchoolDetailColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--preview-slogan-color', admitSeatSloganColor?.value || admitSeatSchoolDetailColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--preview-title-color', admitSeatTitleColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--preview-exam-type-color', admitSeatExamTypeColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--preview-exam-name-color', admitSeatExamNameColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--preview-back-notice-color', admitSeatExamNameColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--preview-footer-color', admitSeatExamNameColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--preview-student-detail-align', admitSeatStudentDetailAlignment?.value || 'left');
            admitSeatLivePreview.style.setProperty('--preview-student-detail-font-size', `${admitSeatStudentDetailFontSize?.value || 8.5}px`);
            admitSeatLivePreview.style.setProperty('--preview-student-detail-color', admitSeatStudentDetailColor?.value || '#111827');
            admitSeatLivePreview.style.setProperty('--admit-card-theme-bg', theme);
            admitSeatLivePreview.style.setProperty('--admit-card-theme-accent', isTransparent ? 'transparent' : (colorType === 'solid' ? solid : gradient1));
            admitSeatLivePreview.style.setProperty('--admit-card-school-name-color', admitSeatSchoolNameColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--admit-card-school-detail-color', admitSeatSchoolDetailColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--admit-card-slogan-color', admitSeatSloganColor?.value || admitSeatSchoolDetailColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--admit-card-title-color', admitSeatTitleColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--admit-card-name-color', admitSeatNameColor?.value || '#111827');
            admitSeatLivePreview.style.setProperty('--admit-card-exam-type-color', admitSeatExamTypeColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--admit-card-exam-name-color', admitSeatExamNameColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-align', admitSeatStudentDetailAlignment?.value || 'left');
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-font-size', `${admitSeatStudentDetailFontSize?.value || 8.5}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-color', admitSeatStudentDetailColor?.value || '#111827');
            admitSeatLivePreview.style.setProperty('--admit-card-front-align', admitSeatFrontAlignment?.value || 'center');

            const unit = admitSeatCardDimensionUnit?.value || 'cm';
            const cardWidthValue = parseFloat(admitSeatCardWidth?.value || '9.4') || 9.4;
            const cardHeightValue = parseFloat(admitSeatCardHeight?.value || '6.6') || 6.6;
            const widthValue = parseFloat(admitSeatPhotoWidth?.value || '1.8') || 1.8;
            const heightValue = parseFloat(admitSeatPhotoHeight?.value || '2.7') || 2.7;
            const logoSizeValue = parseFloat(admitSeatLogoSize?.value || '0.8') || 0.8;
            const frontPaddingRaw = parseFloat(cardSettingsForm?.elements.namedItem('card_front_padding_value')?.value || '0.8');
            const frontPaddingMm = Number.isFinite(frontPaddingRaw) ? frontPaddingRaw : 0.8;
            const cardWidthMm = unit === 'px' ? (cardWidthValue / 96) * 25.4 : cardWidthValue * 10;
            const cardHeightMm = unit === 'px' ? (cardHeightValue / 96) * 25.4 : cardHeightValue * 10;
            admitSeatLivePreview.style.setProperty('--admit-card-preview-width', `${cardWidthMm}mm`);
            admitSeatLivePreview.style.setProperty('--admit-card-preview-height', `${cardHeightMm}mm`);
            admitSeatLivePreview.style.setProperty('--preview-card-ratio', `${(cardWidthMm / cardHeightMm).toFixed(4)}`);
            admitSeatLivePreview.style.setProperty('--admit-card-front-padding', `${frontPaddingMm}mm`);
            admitSeatLivePreview.style.setProperty('--admit-card-photo-width', `${widthValue}cm`);
            admitSeatLivePreview.style.setProperty('--admit-card-photo-height', `${heightValue}cm`);
            admitSeatLivePreview.style.setProperty('--admit-card-logo-size', `${logoSizeValue}cm`);
            admitSeatLivePreview.style.setProperty('--admit-card-school-name-font-size', `${parseFloat(admitSeatSchoolNameFontSize?.value || '7.2') || 7.2}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-school-detail-font-size', `${parseFloat(admitSeatSchoolDetailFontSize?.value || '5.4') || 5.4}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-slogan-font-size', `${parseFloat(admitSeatSloganFontSize?.value || '4.8') || 4.8}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-title-font-size', `${parseFloat(admitSeatTitleFontSize?.value || '4.7') || 4.7}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-name-font-size', `${parseFloat(admitSeatNameFontSize?.value || '7.2') || 7.2}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-exam-type-font-size', `${parseFloat(admitSeatExamTypeFontSize?.value || '7.4') || 7.4}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-exam-name-font-size', `${parseFloat(admitSeatExamNameFontSize?.value || '6.8') || 6.8}pt`);

            setPreviewElementVisible('.admit-card__logo, #admitSeatLivePreviewLogoFront', !!(admitSeatShowLogoFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__address', !!(admitSeatShowSchoolDetailFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__slogan', !!(admitSeatShowSloganFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__exam-label', !!(admitSeatShowTitleFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__exam-type', !!(admitSeatShowExamTypeFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__exam-name', !!(admitSeatShowExamNameFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__photo-wrap', !!(admitSeatShowPhotoFront?.checked ?? true));
            setPreviewElementVisible('.admit-card__footer', !!(admitSeatShowFooterFront?.checked ?? true));
        }

        if (admitSeatLivePreviewLogoFront) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            admitSeatLivePreviewLogoFront.src = logoUrl;
            admitSeatLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
        }
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        if (!classId) {
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then(data => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach(section => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                sectionSelect.innerHTML = html;
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            });
    }

    $(document).on('change', '#classSelect', function () {
        loadSections(this.value);
    });

    $(document).on('click', '#cardSettingsModal .js-card-type-switch', function () {
        const nextCardType = $(this).data('card-type');
        applyCardSettings(nextCardType);
    });

    $(document).on('click', '.card-settings-modal-body [data-preview-focus-target]', function (event) {
        event.preventDefault();
        const targetId = $(this).data('preview-focus-target');
        if (!targetId) return;

        const $input = $(`#${targetId}`);
        if (!$input.length) return;

        $('.card-preview-clickable').removeClass('is-focused');
        $(this).addClass('is-focused');

        $input.trigger('focus');

        if ($input.is('input, textarea')) {
            if ($input.is('[type="color"]') || $input.is('[type="file"]')) {
                $input.trigger('click');
            } else if (typeof $input[0].select === 'function') {
                $input[0].select();
            }
        }
    });

    function setPreviewSide(target, side) {
        const normalized = side === 'back' ? 'back' : 'front';
        const $preview = $(`#${target}LivePreview`);
        if (!$preview.length) return;

        $preview.find('.js-card-preview-side').removeClass('active btn-secondary').addClass('btn-outline-secondary');
        $preview.find(`.js-card-preview-side[data-preview-side="${normalized}"]`).addClass('active btn-secondary').removeClass('btn-outline-secondary');

        const $front = $(`#${target}LivePreviewFront`);
        const $back = $(`#${target}LivePreviewBack`);
        if (!$front.length || !$back.length) return;

        if (normalized === 'back') {
            $front.addClass('d-none');
            $back.removeClass('d-none');
        } else {
            $front.removeClass('d-none');
            $back.addClass('d-none');
        }
    }

    $(document).on('click', '.js-card-preview-side', function () {
        setPreviewSide($(this).data('preview-target'), $(this).data('preview-side'));
    });

    $(document).on('input change', '#admitSeatPhotoWidth, #admitSeatPhotoHeight, #admitSeatLogoSize, select[name="card_dimension_unit"]', refreshCardThemeControls);

    $(document).on('change', '#admitSeatCardIsTransparent, input[name="card_color_type"], #admitSeatCardColorGradient1, #admitSeatCardColorGradient2, #admitSeatCardSolidColor, #admitSeatSchoolNameColor, #admitSeatSchoolDetailColor, #admitSeatTitleColor, #admitSeatNameColor, #admitSeatExamTypeColor, #admitSeatExamNameColor, #admitSeatStudentDetailAlignment, #admitSeatStudentDetailFontSize, #admitSeatStudentDetailColor', function () {
        if (this && this.name === 'card_color_type') {
            $(this).closest('.btn-group-toggle').find('label').removeClass('active');
            $(this).closest('label').addClass('active');
        }
        refreshCardThemeControls();
    });

    if (cardSettingsForm) {
        cardSettingsForm.addEventListener('input', refreshCardThemeControls);
        cardSettingsForm.addEventListener('change', refreshCardThemeControls);
        cardSettingsForm.addEventListener('input', function () {
            setDirtyState(true);
        });
        cardSettingsForm.addEventListener('change', function () {
            setDirtyState(true);
        });
        cardSettingsForm.addEventListener('submit', function () {
            setDirtyState(false);
        });
    }

    initializeLogoDropzone();

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSection);
    }

    $('#cardSettingsModal').on('show.bs.modal', function () {
        if (!hasValidationErrors) {
            applyCardSettings(cardTypeSelect?.value || defaultCardType);
        }
        lockPageScroll();
        setDirtyState(false);
    });

    $('#cardSettingsModal').on('shown.bs.modal', function () {
        initializeLayoutTooltips(this);
        refreshCardThemeControls();
    });

    $('#cardSettingsModal').on('hidden.bs.modal', function () {
        $(this).find('label').tooltip('dispose');
        unlockPageScroll();
    });

    @if($errors->any())
        $('#cardSettingsModal').modal('show');
    @endif
});
</script>

<script>
const form = document.getElementById('filterForm');
document.getElementById('examTypeSelect')?.addEventListener('change', function () {
    const examSelect = document.getElementById('examSelect');
    if (examSelect) {
        examSelect.value = '';
    }
    form?.submit();
});
document.getElementById('examSelect')?.addEventListener('change', function () {
    form?.submit();
});
</script>

@endsection
