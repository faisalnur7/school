@extends('layouts.master')

@section('styles')
    <style>
        @include('pages.admit-seat-cards._styles')

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
                        <h5 class="modal-title mb-1" id="cardSettingsModalLabel">Card Settings</h5>
                        <small class="text-muted d-block" id="cardSettingsModalTypeLabel">{{ (old('card_type', $cardType ?? 'admit_card') === 'seat_card') ? 'Seat Card Settings' : 'Admit Card Settings' }}</small>
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
                                    'frontTitle' => $cardType === 'seat_card' ? 'SEAT CARD' : 'ADMIT CARD',
                                    'backTitle' => 'BACK',
                                    'backNotice' => 'If found, please return to the school.',
                                    'footerLine' => $setting?->whatsapp_number ?: ($setting?->contact_number_1 ?? '+880 1886-780641'),
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
                            <ul class="nav nav-tabs csm-section-tabs mb-2" id="admitSeatSettingsTabs" role="tablist">
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
                                    <div class="card mb-2 shadow-sm">
                                    <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary mr-2"><i class="fas fa-vector-square"></i></span>
                                            <h6 class="mb-0 font-weight-bold">Layout &amp; Grid</h6>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        @if(($layoutIsClamped ?? false))
                                            <div class="alert alert-warning py-1 px-2 mb-2 small">Only {{ $maxCardsPerPage }} cards fit on A4 with the current layout.</div>
                                        @endif
                                        <div class="row no-gutters">
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Cards / Page</label>
                                                    <input type="number" name="cards_per_page" class="csm-input form-control form-control-sm" min="1" max="12" value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 8) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Cards / Row</label>
                                                    <input type="number" name="cards_per_row" class="csm-input form-control form-control-sm" min="1" max="10" value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Grid Gap</label>
                                                    <input type="number" name="grid_gap_value" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.85) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Card Width</label>
                                                    <input type="number" name="card_width_value" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 9.4) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Card Height</label>
                                                    <input type="number" name="card_height_value" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 6.6) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-4 col-xl-2 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Unit</label>
                                                    <select name="card_dimension_unit" class="csm-input csm-select form-control form-control-sm">
                                                        <option value="cm" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                                                        <option value="px" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'px' ? 'selected' : '' }}>Pixel (px)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-2">

                                        <div class="row">
                                            <div class="col-12 col-md-6 col-xl-3 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Front Alignment</label>
                                                    <select name="card_front_alignment" id="admitSeatFrontAlignment" class="csm-input csm-select form-control form-control-sm">
                                                        <option value="left" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                        <option value="center" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                        <option value="right" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-xl-3 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Back Alignment</label>
                                                    <select name="card_back_alignment" id="admitSeatBackAlignment" class="csm-input csm-select form-control form-control-sm">
                                                        <option value="left" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                        <option value="center" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                        <option value="right" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-xl-3 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Front Padding</label>
                                                    <input type="number" name="card_front_padding_value" id="admitSeatFrontPadding" class="csm-input form-control form-control-sm" min="0" step="0.1" value="{{ old('card_front_padding_value', $cardSettings?->card_front_padding_value ?? 0.8) }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 col-xl-3 mb-2">
                                                <div class="form-group mb-0">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Back Padding</label>
                                                    <input type="number" name="card_back_padding_value" id="admitSeatBackPadding" class="csm-input form-control form-control-sm" min="0" step="0.1" value="{{ old('card_back_padding_value', $cardSettings?->card_back_padding_value ?? 0.8) }}">
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
                                                            <span class="small text-muted d-block mt-1">In selected unit</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Photo Height</label>
                                                            <input type="number" name="card_photo_height_value" id="admitSeatPhotoHeight" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                                            <span class="small text-muted d-block mt-1">In selected unit</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Logo Size</label>
                                                            <input type="number" name="card_logo_size_value" id="admitSeatLogoSize" class="csm-input form-control form-control-sm" min="0.1" step="0.1" value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                                            <span class="small text-muted d-block mt-1">In selected unit</span>
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
                                                                    <small class="text-muted">PNG, JPG, SVG supported</small>
                                                                </div>
                                                            </div>
                                                            <span class="small text-muted d-block mt-1">Leave blank to use the school logo</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="admitSeatTypographyPane" role="tabpanel" aria-labelledby="admitSeatTypographyTab">
                                    <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i class="fas fa-font"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Typography &amp; Colors</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">School Name</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_school_name_font_size" id="admitSeatSchoolNameFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_school_name_font_size', $cardSettings?->card_school_name_font_size ?? 7.2) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_school_name_text_color" id="admitSeatSchoolNameColor" class="csm-color-native" value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatSchoolNameColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">School Details</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_school_detail_font_size" id="admitSeatSchoolDetailFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_school_detail_font_size', $cardSettings?->card_school_detail_font_size ?? 5.4) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_school_detail_text_color" id="admitSeatSchoolDetailColor" class="csm-color-native" value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatSchoolDetailColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Slogan</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_slogan_font_size" id="admitSeatSloganFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_slogan_font_size', $cardSettings?->card_slogan_font_size ?? 4.8) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_slogan_text_color" id="admitSeatSloganColor" class="csm-color-native" value="{{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatSloganColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Card Title</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_title_font_size" id="admitSeatTitleFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_title_font_size', $cardSettings?->card_title_font_size ?? 4.7) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_title_text_color" id="admitSeatTitleColor" class="csm-color-native" value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatTitleColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Exam Type</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_exam_type_font_size" id="admitSeatExamTypeFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_exam_type_font_size', $cardSettings?->card_exam_type_font_size ?? 7.4) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_exam_type_text_color" id="admitSeatExamTypeColor" class="csm-color-native" value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                                                            <span id="admitSeatExamTypeColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Exam Name</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_exam_name_font_size" id="admitSeatExamNameFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_exam_name_font_size', $cardSettings?->card_exam_name_font_size ?? 6.8) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_exam_name_text_color" id="admitSeatExamNameColor" class="csm-color-native" value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                                                            <span id="admitSeatExamNameColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center">
                                                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                        <strong class="csm-tc-name d-block">Student Detail</strong>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                        <input type="number" name="card_student_detail_font_size" id="admitSeatStudentDetailFontSize" class="csm-input form-control form-control-sm" min="1" step="0.1" value="{{ old('card_student_detail_font_size', $cardSettings?->card_student_detail_font_size ?? 8.5) }}">
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="d-flex align-items-center">
                                                            <input type="color" name="card_student_detail_text_color" id="admitSeatStudentDetailColor" class="csm-color-native" value="{{ old('card_student_detail_text_color', $cardSettings?->card_student_detail_text_color ?? '#111827') }}">
                                                            <span id="admitSeatStudentDetailColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
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
                                                                <span id="admitSeatCardColorGradient1Preview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Gradient End</label>
                                                            <span class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                <input type="color" name="card_color_gradient_2" id="admitSeatCardColorGradient2" class="csm-color-native" value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                                                <span id="admitSeatCardColorGradient2Preview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2 admit-seat-card-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Solid Color</label>
                                                            <span class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                <input type="color" name="card_solid_color" id="admitSeatCardSolidColor" class="csm-color-native" value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                                                <span id="admitSeatCardSolidColorPreview" class="d-inline-block ml-2" style="width:18px;height:18px;border-radius:50%;border:1px solid #d1d5db;vertical-align:middle;"></span>
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
                'card_show_slogan_front' => $setting->card_show_slogan_front,
                'card_show_title_front' => $setting->card_show_title_front,
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
    const admitSeatExamTypeFontSize = document.getElementById('admitSeatExamTypeFontSize');
    const admitSeatExamNameFontSize = document.getElementById('admitSeatExamNameFontSize');
    const admitSeatStudentDetailAlignment = document.getElementById('admitSeatStudentDetailAlignment');
    const admitSeatStudentDetailFontSize = document.getElementById('admitSeatStudentDetailFontSize');
    const admitSeatStudentDetailColor = document.getElementById('admitSeatStudentDetailColor');
    const admitSeatCardColorGradient1Preview = document.getElementById('admitSeatCardColorGradient1Preview');
    const admitSeatCardColorGradient2Preview = document.getElementById('admitSeatCardColorGradient2Preview');
    const admitSeatCardSolidColorPreview = document.getElementById('admitSeatCardSolidColorPreview');
    const admitSeatSchoolNameColorPreview = document.getElementById('admitSeatSchoolNameColorPreview');
    const admitSeatSchoolDetailColorPreview = document.getElementById('admitSeatSchoolDetailColorPreview');
    const admitSeatSloganColorPreview = document.getElementById('admitSeatSloganColorPreview');
    const admitSeatTitleColorPreview = document.getElementById('admitSeatTitleColorPreview');
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
        card_exam_type_font_size: 7.4,
        card_exam_name_font_size: 6.8,
        card_student_detail_alignment: 'left',
        card_student_detail_font_size: 8.5,
        card_student_detail_text_color: '#111827',
        card_show_school_detail_front: true,
        card_show_slogan_front: true,
        card_show_title_front: true,
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
            'card_show_slogan_front',
            'card_show_title_front',
            'card_show_logo_front',
            'card_show_logo_back',
            'card_show_photo_front',
            'card_show_footer_front',
            'card_show_footer_back',
            'card_show_exam_type_front',
            'card_show_exam_name_front',
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

        if (cardSettingsTypeLabel) {
            cardSettingsTypeLabel.textContent = settingLabelFromCardType(normalizedCardType);
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
            admitSeatLivePreview.style.setProperty('--admit-card-exam-type-color', admitSeatExamTypeColor?.value || '#ffffff');
            admitSeatLivePreview.style.setProperty('--admit-card-exam-name-color', admitSeatExamNameColor?.value || '#e5e7eb');
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-align', admitSeatStudentDetailAlignment?.value || 'left');
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-font-size', `${admitSeatStudentDetailFontSize?.value || 8.5}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-student-detail-color', admitSeatStudentDetailColor?.value || '#111827');
            admitSeatLivePreview.style.setProperty('--admit-card-front-align', admitSeatStudentDetailAlignment?.value || 'left');

            const unit = admitSeatCardDimensionUnit?.value || 'cm';
            const cardWidthValue = parseFloat(admitSeatCardWidth?.value || '9.4') || 9.4;
            const cardHeightValue = parseFloat(admitSeatCardHeight?.value || '6.6') || 6.6;
            const widthValue = parseFloat(admitSeatPhotoWidth?.value || '1.8') || 1.8;
            const heightValue = parseFloat(admitSeatPhotoHeight?.value || '2.7') || 2.7;
            const logoSizeValue = parseFloat(admitSeatLogoSize?.value || '0.8') || 0.8;
            const pxPerUnit = unit === 'px' ? 1 : 37.7952755906;
            const cardWidthPx = Math.max(180, Math.round(cardWidthValue * pxPerUnit));
            const cardHeightPx = Math.max(180, Math.round(cardHeightValue * pxPerUnit));
            admitSeatLivePreview.style.setProperty('--preview-card-width', `${cardWidthPx}px`);
            admitSeatLivePreview.style.setProperty('--preview-card-height', `${cardHeightPx}px`);
            admitSeatLivePreview.style.setProperty('--preview-card-ratio', `${(cardWidthPx / cardHeightPx).toFixed(4)}`);
            admitSeatLivePreview.style.setProperty('--preview-photo-width', `${Math.max(36, Math.round(widthValue * pxPerUnit))}px`);
            admitSeatLivePreview.style.setProperty('--preview-photo-height', `${Math.max(44, Math.round(heightValue * pxPerUnit))}px`);
            admitSeatLivePreview.style.setProperty('--preview-logo-size', `${Math.max(28, Math.round(logoSizeValue * pxPerUnit))}px`);
            admitSeatLivePreview.style.setProperty('--admit-card-front-padding', `${parseFloat(cardSettingsForm?.elements.namedItem('card_front_padding_value')?.value || '0.8') || 0.8}${unit === 'px' ? 'px' : 'cm'}`);
            admitSeatLivePreview.style.setProperty('--admit-card-photo-width', `${widthValue}${unit === 'px' ? 'px' : 'cm'}`);
            admitSeatLivePreview.style.setProperty('--admit-card-photo-height', `${heightValue}${unit === 'px' ? 'px' : 'cm'}`);
            admitSeatLivePreview.style.setProperty('--admit-card-logo-size', `${logoSizeValue}${unit === 'px' ? 'px' : 'cm'}`);
            admitSeatLivePreview.style.setProperty('--admit-card-school-name-font-size', `${parseFloat(admitSeatSchoolNameFontSize?.value || '7.2') || 7.2}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-school-detail-font-size', `${parseFloat(admitSeatSchoolDetailFontSize?.value || '5.4') || 5.4}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-slogan-font-size', `${parseFloat(admitSeatSloganFontSize?.value || '4.8') || 4.8}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-title-font-size', `${parseFloat(admitSeatTitleFontSize?.value || '4.7') || 4.7}pt`);
            admitSeatLivePreview.style.setProperty('--admit-card-name-font-size', `${parseFloat(admitSeatStudentDetailFontSize?.value || '12') || 12}pt`);
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

    $(document).on('click', '.js-card-preview-side', function () {
        const target = $(this).data('preview-target');
        const side = $(this).data('preview-side');
        const $preview = $(`#${target}LivePreview`);
        if (!$preview.length) return;

        $preview.find('.js-card-preview-side').removeClass('active btn-secondary').addClass('btn-outline-secondary');
        $(this).addClass('active btn-secondary').removeClass('btn-outline-secondary');

        const $front = $(`#${target}LivePreviewFront`);
        const $back = $(`#${target}LivePreviewBack`);
        if (side === 'front') {
            $front.show();
            $back.hide();
        } else if (side === 'back') {
            $front.hide();
            $back.show();
        } else {
            $front.show();
            $back.show();
        }
    });

    $(document).on('input change', '#admitSeatPhotoWidth, #admitSeatPhotoHeight, #admitSeatLogoSize, select[name="card_dimension_unit"]', refreshCardThemeControls);

    $(document).on('change', '#admitSeatCardIsTransparent, input[name="card_color_type"], #admitSeatCardColorGradient1, #admitSeatCardColorGradient2, #admitSeatCardSolidColor, #admitSeatSchoolNameColor, #admitSeatSchoolDetailColor, #admitSeatTitleColor, #admitSeatExamTypeColor, #admitSeatExamNameColor, #admitSeatStudentDetailAlignment, #admitSeatStudentDetailFontSize, #admitSeatStudentDetailColor', function () {
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
        setDirtyState(false);
    });

    $('#cardSettingsModal').on('shown.bs.modal', function () {
        refreshCardThemeControls();
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
