@extends('layouts.master')

@section('contents')
@php
    $employeeMode = $employeeMode ?? false;
    $settingsOnly = $settingsOnly ?? false;
@endphp
<div class="container-fluid">

    @unless($settingsOnly)
    <div class="card no-print id-card-filter-shell">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">{{ $employeeMode ? 'Generate Employee ID Cards' : 'Generate ID Cards' }}</h3>
        </div>
        <div class="card-body pb-2 id-card-filter-body">
            <div class="id-card-filter-panel">
            <form method="GET" action="{{ $employeeMode ? route('hr.employee-id-cards') : route('students.id-cards') }}" id="filterForm">
                @if($employeeMode)
                <div class="row id-card-filter-grid">
                    <div class="col-md-3 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-employee-type" class="font-weight-bold id-card-filter-label">Employee Type</label>
                            <select id="generate-id-cards-index-employee-type" name="employee_type" class="form-control form-control-sm">
                                <option value="all" {{ request('employee_type') === 'all' ? 'selected' : '' }}>All Employees</option>
                                <option value="teacher" {{ request('employee_type') === 'teacher' ? 'selected' : '' }}>Teachers</option>
                                <option value="staff" {{ request('employee_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-employee-search" class="id-card-filter-label">Employee Name or ID</label>
                            <input id="generate-id-cards-index-employee-search" type="text" name="employee_search" class="form-control form-control-sm id-card-filter-input" value="{{ request('employee_search') }}" placeholder="Search employee name or ID" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4 id-card-filter-field d-flex align-items-end">
                        <div class="form-group w-100">
                            <label class="id-card-filter-label">Card Type</label>
                            <input type="text" class="form-control form-control-sm" value="Employee ID Card" readonly>
                        </div>
                    </div>
                </div>
                @else
                <div class="row id-card-filter-grid">
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-session-id" class="font-weight-bold id-card-filter-label">Academic Year <span class="text-danger">*</span></label>
                            <select id="generate-id-cards-index-session-id" name="session_id" class="form-control form-control-sm" required>
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Class <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-control form-control-sm" id="classSelect" required>
                                <option value="">— Select Class —</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" class="form-control form-control-sm" id="sectionSelect" required>
                                <option value="">— Select Section —</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-group-id" class="id-card-filter-label">Group</label>
                            <select id="generate-id-cards-index-group-id" name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-card-type" class="id-card-filter-label">Card Type</label>
                            <select id="generate-id-cards-index-card-type" name="card_type" class="form-control form-control-sm">
                                <option value="id_card" {{ ($cardType ?? 'id_card') === 'id_card' ? 'selected' : '' }}>ID Card</option>
                                <option value="library_card" {{ ($cardType ?? 'id_card') === 'library_card' ? 'selected' : '' }}>Library Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label for="generate-id-cards-index-student-cid" class="id-card-filter-label">Student ID</label>
                            <input id="generate-id-cards-index-student-cid"
                                type="text"
                                name="student_cid"
                                class="form-control form-control-sm id-card-filter-input"
                                value="{{ request('student_cid') }}"
                                placeholder="Or a single Student ID"
                                autocomplete="off">
                        </div>
                    </div>
                </div>
                @endif
                <div class="id-card-filter-actions-row">
                    <div class="d-flex flex-wrap justify-content-end id-card-filter-actions-inner">
                        <a href="{{ route($employeeMode ? 'hr.employee-id-cards.settings.edit' : 'students.id-cards.settings.edit', request()->query()) }}" class="btn btn-outline-primary btn-sm id-card-filter-btn" title="Card settings">
                            <i class="fas fa-sliders-h"></i>
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm id-card-filter-btn" title="Generate">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="{{ $employeeMode ? route('hr.employee-id-cards') : route('students.id-cards') }}" class="btn btn-secondary btn-sm id-card-filter-btn" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @if(($employeeMode ? $employees : $students)->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm id-card-filter-btn" onclick="window.print()" title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route($employeeMode ? 'hr.employee-id-cards.pdf' : 'students.id-cards.pdf', request()->query()) }}" class="btn btn-outline-secondary btn-sm id-card-filter-btn" title="PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    @if($employeeMode && !request('employee_type') && !request('employee_search'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select an employee type or search by employee name/ID to generate cards.</p>
        </div>
    @elseif(!$employeeMode && !($readyToLoad ?? false))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select <strong>Academic Year</strong>, <strong>Class</strong> and <strong>Section</strong> (or enter a single Student ID) to generate cards.</p>
        </div>
    @elseif(($employeeMode ? $employees : $students)->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-primary px-3 py-2" style="font-size:13px">{{ ($employeeMode ? $employees : $students)->count() }} {{ $employeeMode ? 'Employees' : 'Students' }}</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $employeeMode ? 'Front + Back per employee' : (($cardType ?? 'id_card') === 'library_card' ? 'Library card' : 'Front + Back per student') }}
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['cardWidthCm'] ?? 5.4, 2) }}cm × {{ number_format($layout['cardHeightCm'] ?? 8.56, 2) }}cm
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">Landscape print and PDF</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">{{ $layout['cardsPerPage'] ?? 4 }} per page ({{ $layout['cardsPerRow'] ?? 2 }} per row)</span>
        </div>

        @if(!empty($layout['adjusted']))
            <div class="alert alert-warning no-print py-2 px-3 mb-3" style="font-size:13px" role="alert">
                <i class="fas fa-exclamation-triangle mr-1"></i> {{ $layout['adjustedMessage'] }}
                Open <strong>Settings → Layout &amp; Grid</strong> to resize the cards.
            </div>
        @endif

        @include('pages.generate-id-cards._cards', [
            'students' => $students,
            'employees' => $employees ?? collect(),
            'employeeMode' => $employeeMode,
            'setting' => $setting,
            'cardSettings' => $cardSettings ?? null,
            'renderForPdf' => false,
            'cardType' => $cardType ?? 'id_card',
            'layout' => $layout ?? [],
        ])
    @endif
    @endunless
</div>

@if($settingsOnly)
    <div class="id-card-dedicated-settings">
        @include('pages.generate-id-cards._settings-modal', ['employeeMode' => $employeeMode, 'settingsOnly' => true])
    </div>
@endif

@php
    $previewStudentData = ($allStudents ?? collect())->take(300)->mapWithKeys(function ($st) {
        $ai = $st->academicInformations->first();
        $placeholder = $st->gender == \App\Models\Student::FEMALE ? 'assets/img/female-placeholder.png' : 'assets/img/male-placeholder.png';
        $photo = $st->image && file_exists(public_path($st->image)) ? $st->image : $placeholder;

        return [$st->id => [
            'name' => (string) $st->full_name_en,
            'bn' => (string) $st->full_name_bn,
            'cid' => (string) $st->student_cid,
            'class' => trim(($ai?->schoolClass?->name_en ?? '') . ($ai?->section ? ' / ' . $ai->section->name_en : '')),
            'roll' => (string) ($ai?->roll ?? ''),
            'group' => (string) ($ai?->group?->name_en ?? ''),
            'session' => (string) ($ai?->academicSession?->name_en ?? ''),
            'dob' => $st->date_of_birth ? $st->date_of_birth->format('d M Y') : '',
            'blood' => (string) ($st->blood_group ? $st->blood_group_text : ''),
            'photo' => asset($photo),
        ]];
    })->all();
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const cardTypeSelect = document.querySelector('#filterForm select[name="card_type"]');
    const settingsModal = document.getElementById('idCardSettingsModal');
    const settingsForm = settingsModal?.querySelector('form');
    const settingsTypeLabel = document.getElementById('idCardSettingsModalTypeLabel');
    const dirtyBadge = document.getElementById('idCardSettingsDirtyBadge');
    const idCardPreviewLabel = document.getElementById('idCardPreviewLabel');
    const cardThemePreview = document.getElementById('cardThemePreview');
    const cardIsTransparent = document.querySelector('#idCardSettingsModal input[name="card_is_transparent"]');
    const cardStudentDetailAlignment = document.getElementById('cardStudentDetailAlignment');
    const cardColorGradient1 = document.getElementById('cardColorGradient1');
    const cardColorGradient2 = document.getElementById('cardColorGradient2');
    const cardSolidColor = document.getElementById('cardSolidColor');
    const cardSchoolNameColor = document.getElementById('cardSchoolNameColor');
    const cardSchoolDetailColor = document.getElementById('cardSchoolDetailColor');
    const cardSloganColor = document.getElementById('cardSloganColor');
    const cardNameColor = document.getElementById('cardNameColor');
    const cardBackNoticeColor = document.getElementById('cardBackNoticeColor');
    const cardFooterColor = document.getElementById('cardFooterColor');
    const cardTitleColor = document.getElementById('cardTitleColor');
    const cardLogoInput = document.getElementById('cardLogoInput');
    const cardLogoPreview = document.getElementById('cardLogoPreview');
    const cardPrincipalSignatureInput = document.getElementById('cardPrincipalSignatureInput');
    const cardPrincipalSignaturePreview = document.getElementById('cardPrincipalSignaturePreview');
    const cardColorGradient1Preview = document.getElementById('cardColorGradient1Preview');
    const cardColorGradient2Preview = document.getElementById('cardColorGradient2Preview');
    const cardSolidColorPreview = document.getElementById('cardSolidColorPreview');
    const cardSchoolNameColorPreview = document.getElementById('cardSchoolNameColorPreview');
    const cardSchoolDetailColorPreview = document.getElementById('cardSchoolDetailColorPreview');
    const cardSloganColorPreview = document.getElementById('cardSloganColorPreview');
    const cardNameColorPreview = document.getElementById('cardNameColorPreview');
    const cardBackNoticeColorPreview = document.getElementById('cardBackNoticeColorPreview');
    const cardFooterColorPreview = document.getElementById('cardFooterColorPreview');
    const cardTitleColorPreview = document.getElementById('cardTitleColorPreview');
    const idCardLivePreview = document.getElementById('idCardLivePreview');
    const idCardLivePreviewFront = document.getElementById('idCardLivePreviewFront');
    const idCardLivePreviewBack = document.getElementById('idCardLivePreviewBack');
    const idCardLivePreviewLogoFront = document.getElementById('idCardLivePreviewLogoFront');
    const idCardLivePreviewLogoBack = document.getElementById('idCardLivePreviewLogoBack');
    const idCardLivePreviewSignatureBack = document.getElementById('idCardLivePreviewSignatureBack');
    const idCardLivePreviewTitleFront = document.getElementById('idCardLivePreviewTitleFront');
    const idCardLivePreviewTitleBack = document.getElementById('idCardLivePreviewTitleBack');
    const cardPhotoWidth = document.getElementById('cardPhotoWidth');
    const cardPhotoHeight = document.getElementById('cardPhotoHeight');
    const cardPhotoFit = document.getElementById('cardPhotoFit');
    const cardLogoSize = document.getElementById('cardLogoSize');
    const cardLogoFit = document.getElementById('cardLogoFit');
    const cardFrontPadding = document.getElementById('cardFrontPadding');
    const cardBackPadding = document.getElementById('cardBackPadding');
    const cardGridGap = settingsForm?.elements.namedItem('grid_gap_value');
    const cardWidth = settingsForm?.elements.namedItem('card_width_value');
    const cardHeight = settingsForm?.elements.namedItem('card_height_value');
    const cardDimensionUnit = settingsForm?.elements.namedItem('card_dimension_unit');
    const selectedSection = @json(request('section_id'));
    const employeeMode = @json($employeeMode);
    const hasValidationErrors = @json($errors->any());
    const cardSettingsMap = @json($cardSettingsPayload);
    const defaultCardType = @json($cardType ?? 'id_card');
    let activeCardSettings = {};
    let lastDimensionUnit = cardDimensionUnit?.value === 'px' ? 'px' : 'cm';
    let previewLogoUrl = null;
    let previewPrincipalSignatureUrl = null;
    let modalScrollY = 0;
    const initialCardLogoPreviewSrc = cardLogoPreview?.getAttribute('src') || '';
    const initialCardPrincipalSignaturePreviewSrc = cardPrincipalSignaturePreview?.getAttribute('src') || '';
    const defaultThemeSettings = {
        card_is_transparent: false,
        card_color_type: 'gradient',
        card_page_size: 'a4',
        card_page_orientation: 'landscape',
        card_front_alignment: 'center',
        card_back_alignment: 'center',
        card_front_padding_value: 0.8,
        card_back_padding_value: 0.8,
        card_front_header_padding_value: 0.3,
        card_back_header_padding_value: 0.3,
        card_photo_width_value: 1.8,
        card_photo_height_value: 2.7,
        card_photo_fit: 'cover',
        card_logo_size_value: 0.8,
        card_logo_fit: 'contain',
        card_school_name_font_size: 7.2,
        card_school_detail_font_size: 5.4,
        card_slogan_font_size: 4.8,
        card_title_font_size: 4.7,
        card_name_font_size: 7.2,
        card_name_text_color: '#1e3a5f',
        card_student_detail_alignment: 'left',
        card_student_detail_font_size: 4.6,
        card_student_detail_text_color: '#111827',
        card_color_gradient_1: '#1e3a5f',
        card_color_gradient_2: '#2563eb',
        card_solid_color: '#1e3a5f',
        card_school_name_text_color: '#ffffff',
        card_school_detail_text_color: '#e5e7eb',
        card_slogan_text_color: '#e5e7eb',
        card_back_notice_text_color: '#94a3b8',
        card_footer_text_color: '#e5e7eb',
        card_title_text_color: '#ffffff',
        card_show_school_detail_front: false,
        card_show_school_detail_back: true,
        card_show_slogan_front: true,
        card_show_slogan_back: true,
        card_show_title_front: true,
        card_show_title_back: true,
        card_show_logo_front: true,
        card_show_logo_back: true,
        card_show_photo_front: true,
        card_show_footer_front: true,
        card_show_footer_back: true,
        card_show_back_student_details: true,
        card_show_back_school_contact: true,
        card_show_back_qr: true,
        card_show_back_signature: true,
        card_show_back_notice: true,
    };
    const fallbackSchoolLogo = @json($schoolLogoUrl);
    const cardDefaults = @json($cardDefaults ?? \App\Models\AdmitSeatCardSetting::idCardDefaults());
    const previewStudents = @json($previewStudentData);

    function settingKeyFromCardType(cardType) {
        if (employeeMode) return '5';
        return cardType === 'library_card' ? '4' : '3';
    }

    function settingLabelFromCardType(cardType) {
        if (employeeMode) return 'Employee ID Card Settings';
        return cardType === 'library_card' ? 'Library Card Settings' : 'ID Card Settings';
    }

    function previewTitleFromCardType(cardType) {
        if (employeeMode) return 'EMPLOYEE ID';
        return cardType === 'library_card' ? 'LIBRARY CARD' : 'STUDENT ID';
    }

    function syncCardTypeSwitcher(cardType) {
        const normalized = cardType === 'library_card' ? 'library_card' : 'id_card';
        $('.js-card-type-switch').each(function () {
            const $button = $(this);
            const isActive = $button.data('card-type') === normalized;
            $button.toggleClass('active btn-primary', isActive);
            $button.toggleClass('btn-outline-primary', !isActive);
        });

        if (cardTypeSelect) {
            cardTypeSelect.value = normalized;
        }

        // Dedicated settings page: keep the URL and the "Back" links on the card type being edited,
        // so saving (redirect back) and returning to the cards both land on the same preset.
        if (!employeeMode) {
            try {
                const current = new URL(window.location.href);
                current.searchParams.set('card_type', normalized);
                window.history.replaceState(null, '', current.toString());
            } catch (e) { /* non-critical */ }
            document.querySelectorAll('.js-id-card-settings-back').forEach((link) => {
                try {
                    const target = new URL(link.href);
                    target.searchParams.set('card_type', normalized);
                    link.href = target.toString();
                } catch (e) { /* non-critical */ }
            });
        }
    }

    let settingsDirty = false;
    function setDirtyState(isDirty) {
        settingsDirty = !!isDirty;
        dirtyBadge?.classList.toggle('d-none', !isDirty);
        document.getElementById('idCardFooterDirty')?.classList.toggle('d-none', !isDirty);
    }

    function normalizeTooltipText(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function getTooltipTextFromLabel($label) {
        const explicit = normalizeTooltipText($label.attr('data-tooltip-content'));
        if (explicit) {
            return explicit;
        }

        const $hint = $label.nextAll('.id-card-layout-hint, .text-muted.d-block, .small.text-muted, small.text-muted').first();
        const hintText = normalizeTooltipText($hint.text());
        const existingTitle = normalizeTooltipText($label.attr('title'));
        const labelText = normalizeTooltipText($label.text());

        return hintText || existingTitle || labelText;
    }

    function initializeSettingTooltips(context) {
        const root = context ? (context.jquery ? context : $(context)) : $('#idCardSettingsModal');
        const $labels = root.find('label');
        if (!$labels.length || typeof $labels.tooltip !== 'function') return;

        $labels.each(function () {
            const $label = $(this);
            const tooltipText = getTooltipTextFromLabel($label);
            if (!tooltipText) return;

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

    function getSelectedCardColorType() {
        return settingsForm?.querySelector('input[name="card_color_type"]:checked')?.value || 'gradient';
    }

    function normalizeBooleanSetting(value, fallback = false) {
        if (value === undefined || value === null || value === '') {
            return fallback;
        }

        if (typeof value === 'boolean') {
            return value;
        }

        if (typeof value === 'number') {
            return value === 1;
        }

        const normalized = String(value).trim().toLowerCase();
        return ['1', 'true', 'yes', 'on'].includes(normalized);
    }

    function setPreviewElementVisible(selector, isVisible) {
        if (!idCardLivePreview) {
            return;
        }

        idCardLivePreview.querySelectorAll(selector).forEach((element) => {
            element.classList.toggle('d-none', !isVisible);
        });
    }

    // Printable area = page minus the PDF margins (8 mm sides, 10 mm top, 8 mm bottom); mirrors printableArea() in the controller.
    function sheetArea() {
        const letter = settingsForm?.elements.namedItem('card_page_size')?.value === 'letter';
        const landscape = settingsForm?.elements.namedItem('card_page_orientation')?.value !== 'portrait';
        let [width, height] = letter ? [215.9, 279.4] : [210, 297];
        if (landscape) [width, height] = [height, width];
        return { width: width - 16, height: height - 18, label: `${letter ? 'Letter' : 'A4'} ${landscape ? 'landscape' : 'portrait'}` };
    }
    const LENGTH_FIELDS = ['card_width_value', 'card_height_value', 'grid_gap_value', 'card_front_padding_value', 'card_back_padding_value', 'card_front_header_padding_value', 'card_back_header_padding_value', 'card_front_header_padding_value', 'card_back_header_padding_value', 'card_photo_width_value', 'card_photo_height_value', 'card_logo_size_value'];
    const previewFit = (() => {
        try { return JSON.parse(idCardLivePreview?.dataset.fit || '{}'); } catch (e) { return {}; }
    })();

    // Mirrors the server-side fitTextSize() used by the real cards.
    function fitTextSize(value, base, min, threshold = 22, step = 0.08) {
        const values = Array.isArray(value) ? value : [value];
        const longest = values.reduce((max, item) => Math.max(max, String(item ?? '').replace(/<[^>]*>/g, '').trim().length), 0);
        if (longest <= threshold) return base;
        const reduction = Math.min((longest - threshold) * step, base - min);
        return Math.round(Math.max(min, base - reduction) * 100) / 100;
    }

    function syncLengthFieldSteps(unit) {
        LENGTH_FIELDS.forEach((name) => {
            const input = settingsForm?.elements.namedItem(name);
            if (input) input.step = unit === 'px' ? '1' : '0.01';
        });
    }

    // Changing the unit converts the entered numbers so the physical size stays the same.
    function convertLengthFields(fromUnit, toUnit) {
        if (!settingsForm || fromUnit === toUnit) return;
        const factor = toUnit === 'px' ? 96 / 2.54 : 2.54 / 96;
        LENGTH_FIELDS.forEach((name) => {
            const input = settingsForm.elements.namedItem(name);
            const value = parseFloat(input?.value);
            if (!input || Number.isNaN(value)) return;
            input.value = String(Math.round(value * factor * (toUnit === 'px' ? 1 : 100)) / (toUnit === 'px' ? 1 : 100));
        });
        syncLengthFieldSteps(toUnit);
    }

    // Same maths as GenerateIdCardController::buildLayout(): each slot is a front + back card side by side.
    function updateLayoutFitNotice() {
        const notice = document.getElementById('idCardLayoutFitNotice');
        if (!notice || !settingsForm) return;

        const unit = cardDimensionUnit?.value === 'px' ? 'px' : 'cm';
        const toMm = (value) => (unit === 'px' ? value / 96 * 25.4 : value * 10);
        const read = (name, fallback) => {
            const value = parseFloat(settingsForm.elements.namedItem(name)?.value);
            return value > 0 ? value : fallback;
        };
        const widthMm = toMm(read('card_width_value', 5.4));
        const heightMm = toMm(read('card_height_value', 8.56));
        const gapMm = toMm(read('grid_gap_value', 0.5));
        const requestedPerPage = Math.max(1, Math.min(12, Math.round(read('cards_per_page', 4))));
        const requestedPerRow = Math.max(1, Math.min(10, Math.round(read('cards_per_row', 2))));
        const slotWidthMm = widthMm * 2 + gapMm;
        const area = sheetArea();

        let level = 'info';
        let text;
        let diagram = null;
        let tooLarge = false;

        if (slotWidthMm > area.width || heightMm > area.height) {
            level = 'danger';
            tooLarge = true;
            text = `Too large: a front + back pair (${(slotWidthMm / 10).toFixed(1)} × ${(heightMm / 10).toFixed(1)} cm) does not fit the ${area.label} printable area (${(area.width / 10).toFixed(1)} × ${(area.height / 10).toFixed(1)} cm). Reduce the card size or gap — saving will be refused.`;
        } else {
            const maxPerRow = Math.max(1, Math.floor((area.width + gapMm) / (slotWidthMm + gapMm)));
            const maxRows = Math.max(1, Math.floor((area.height + gapMm) / (heightMm + gapMm)));
            const perRow = Math.min(Math.min(requestedPerRow, requestedPerPage), maxPerRow);
            const rows = Math.min(Math.ceil(requestedPerPage / Math.min(requestedPerRow, requestedPerPage)), maxRows);
            const perPage = Math.min(requestedPerPage, Math.max(1, perRow * rows));
            diagram = { perPage, perRow, widthMm, heightMm, gapMm, slotWidthMm };
            if (perPage !== requestedPerPage || perRow !== Math.min(requestedPerRow, requestedPerPage)) {
                level = 'warning';
                text = `Will print ${perPage} per page (${perRow} per row) instead of the requested ${requestedPerPage} / ${requestedPerRow}: only ${maxPerRow} per row × ${maxRows} rows fit on the ${area.label} sheet.`;
            } else {
                text = `Sheet check: ${perPage} per page — ${perRow} per row × ${rows} row${rows > 1 ? 's' : ''}, each with front and back side by side.`;
            }
        }

        notice.className = `alert alert-${level} py-2 px-3 mt-3 mb-0`;
        notice.textContent = text;

        // Inline field validation: the size fields that cause the overflow get the message too.
        const overflowWidth = slotWidthMm > area.width;
        const overflowHeight = heightMm > area.height;
        [['card_width_value', overflowWidth, `Pair is ${(slotWidthMm / 10).toFixed(1)} cm wide; ${area.label} fits ${(area.width / 10).toFixed(1)} cm.`],
         ['card_height_value', overflowHeight, `Taller than the ${(area.height / 10).toFixed(1)} cm printable height.`],
         ['grid_gap_value', overflowWidth && widthMm * 2 <= area.width, 'The gap pushes the pair off the sheet.']].forEach(([name, invalid, message]) => {
            const input = settingsForm.elements.namedItem(name);
            if (!input) return;
            input.classList.toggle('is-invalid', tooLarge && invalid);
            const feedback = settingsForm.querySelector(`[data-feedback-for="${name}"]`);
            if (feedback) feedback.textContent = message;
        });

        renderSheetDiagram(diagram, tooLarge);
        syncSizePreset(widthMm, heightMm);
    }

    // Mini sheet: the paper, its printable area (dashed) and where each front + back pair lands.
    function renderSheetDiagram(layout, tooLarge) {
        const holder = document.getElementById('idCardSheetDiagram');
        if (!holder) return;
        const area = sheetArea();
        const pageW = area.width + 16;
        const pageH = area.height + 18;
        holder.style.aspectRatio = `${pageW} / ${pageH}`;
        holder.classList.toggle('csm-sheet--invalid', !!tooLarge);
        const pct = (value, total) => `${(value / total * 100).toFixed(3)}%`;
        let html = `<div class="csm-sheet__area" style="left:${pct(8, pageW)};top:${pct(10, pageH)};width:${pct(area.width, pageW)};height:${pct(area.height, pageH)}"></div>`;
        if (layout) {
            const { perPage, perRow, widthMm, heightMm, gapMm, slotWidthMm } = layout;
            const rows = Math.ceil(perPage / perRow);
            const usedW = perRow * slotWidthMm + (perRow - 1) * gapMm;
            const left0 = 8 + Math.max(0, (area.width - usedW) / 2);
            for (let i = 0; i < perPage; i++) {
                const col = i % perRow;
                const row = Math.floor(i / perRow);
                const x = left0 + col * (slotWidthMm + gapMm);
                const y = 10 + row * (heightMm + gapMm);
                ['front', 'back'].forEach((side, index) => {
                    html += `<div class="csm-sheet__card csm-sheet__card--${side}" style="left:${pct(x + index * widthMm, pageW)};top:${pct(y, pageH)};width:${pct(widthMm, pageW)};height:${pct(heightMm, pageH)}"></div>`;
                });
            }
            holder.setAttribute('aria-label', `${perPage} per page, ${perRow} per row, ${rows} rows`);
        }
        holder.innerHTML = html + `<span class="csm-sheet__label">${area.label}</span>`;
    }

    const SIZE_PRESETS = { cr80p: [54, 85.6], cr80l: [85.6, 54] };
    const sizePresetSelect = document.getElementById('cardSizePreset');

    // Set a <select> programmatically and refresh the Select2 label (a bare .value= leaves stale text).
    function setSelectValue(select, value) {
        if (!select || select.value === String(value)) return;
        select.value = String(value);
        if (window.jQuery && $(select).data('select2')) $(select).trigger('change.select2');
    }

    function syncSizePreset(widthMm, heightMm) {
        if (!sizePresetSelect) return;
        const match = Object.entries(SIZE_PRESETS).find(([, [w, h]]) => Math.abs(w - widthMm) < 0.3 && Math.abs(h - heightMm) < 0.3);
        setSelectValue(sizePresetSelect, match ? match[0] : 'custom');
    }

    function onSizePresetChange() {
        const preset = SIZE_PRESETS[sizePresetSelect.value];
        if (!preset || !settingsForm) return;
        const px = cardDimensionUnit?.value === 'px';
        const convert = (mm) => (px ? Math.round(mm / 25.4 * 96) : Math.round(mm * 10) / 100);
        cardWidth.value = convert(preset[0]);
        cardHeight.value = convert(preset[1]);
        cardWidth.dispatchEvent(new Event('input', { bubbles: true }));
        setDirtyState(true);
    }

    if (window.jQuery) {
        $(sizePresetSelect).on('change', onSizePresetChange);
    } else {
        sizePresetSelect?.addEventListener('change', onSizePresetChange);
    }

    // ---- Drag to reposition (offsets stored in mm, same as the admit-card preview) ----
    function positionInput(key, axis) {
        return settingsForm?.querySelector(`[data-element-position-key="${key}"][data-element-position-axis="${axis}"]`);
    }

    function applyPreviewPositions() {
        idCardLivePreview?.querySelectorAll('[data-pos-key]').forEach((el) => {
            const x = parseFloat(positionInput(el.dataset.posKey, 'x')?.value) || 0;
            const y = parseFloat(positionInput(el.dataset.posKey, 'y')?.value) || 0;
            el.style.position = 'relative';
            el.style.left = `${x}mm`;
            el.style.top = `${y}mm`;
        });
    }

    function loadPositionInputs(positions) {
        settingsForm?.querySelectorAll('[data-element-position-key]').forEach((input) => {
            const value = positions?.[input.dataset.elementPositionKey]?.[input.dataset.elementPositionAxis];
            input.value = Number.isFinite(parseFloat(value)) ? value : 0;
        });
        applyPreviewPositions();
    }

    function nudgePosition(key, axis, deltaMm) {
        const input = positionInput(key, axis);
        if (!input) return;
        const next = Math.max(-100, Math.min(100, (parseFloat(input.value) || 0) + deltaMm));
        input.value = String(Math.round(next * 100) / 100);
    }

    function applyCardSettings(cardType, copyFromType = null) {
        if (!settingsForm) return;

        const key = settingKeyFromCardType(cardType || defaultCardType);
        const ownSettings = cardSettingsMap[key] || cardSettingsMap['3'] || {};
        // When copying, field values come from the other card type, but uploaded files stay this type's own.
        const settings = copyFromType
            ? (cardSettingsMap[settingKeyFromCardType(copyFromType)] || ownSettings)
            : ownSettings;
        activeCardSettings = ownSettings;
        previewLogoUrl = null;
        previewPrincipalSignatureUrl = null;

        ['cards_per_page', 'cards_per_row', 'card_width_value', 'card_height_value', 'grid_gap_value', 'card_dimension_unit', 'card_page_size', 'card_page_orientation', 'card_front_alignment', 'card_back_alignment', 'card_front_padding_value', 'card_back_padding_value', 'card_front_header_padding_value', 'card_back_header_padding_value', 'card_photo_width_value', 'card_photo_height_value', 'card_photo_fit', 'card_logo_size_value', 'card_logo_fit', 'card_school_name_font_size', 'card_school_detail_font_size', 'card_slogan_font_size', 'card_title_font_size', 'card_name_font_size', 'card_student_detail_alignment', 'card_student_detail_font_size', 'card_student_detail_text_color', 'card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_slogan_text_color', 'card_name_text_color', 'card_back_notice_text_color', 'card_footer_text_color', 'card_title_text_color', 'card_show_school_detail_front', 'card_show_school_detail_back', 'card_show_slogan_front', 'card_show_slogan_back', 'card_show_title_front', 'card_show_title_back', 'card_show_logo_front', 'card_show_logo_back', 'card_show_photo_front', 'card_show_footer_front', 'card_show_footer_back', 'card_show_back_student_details', 'card_show_back_school_contact', 'card_show_back_qr', 'card_show_back_signature', 'card_show_back_notice'].forEach((field) => {
            const input = settingsForm.elements.namedItem(field);
            const rawValue = settings[field] ?? defaultThemeSettings[field];
            const value = field === 'card_is_transparent'
                ? (normalizeBooleanSetting(rawValue, defaultThemeSettings[field]) ? '1' : '0')
                : rawValue;
            if (field === 'card_color_type') {
                settingsForm.querySelectorAll('input[name="card_color_type"]').forEach((radio) => {
                    radio.checked = radio.value === value;
                });
                return;
            }

            if (input && value !== undefined && value !== null) {
                if (input.type === 'checkbox') {
                    input.checked = normalizeBooleanSetting(value);
                } else {
                    input.value = value;
                    if (input.tagName === 'SELECT' && window.jQuery && $(input).data('select2')) {
                        $(input).trigger('change.select2');
                    }
                }
            }
        });

        loadPositionInputs(settings.card_element_positions || {});

        const hiddenType = settingsForm.elements.namedItem('card_type');
        if (hiddenType) hiddenType.value = cardType || defaultCardType;

        if (settingsTypeLabel) {
            settingsTypeLabel.textContent = settingLabelFromCardType(cardType || defaultCardType);
        }

        if (idCardPreviewLabel) {
            idCardPreviewLabel.textContent = cardType === 'library_card'
                ? 'Library Card Preview'
                : 'ID Card Preview';
        }

        if (idCardLivePreviewTitleFront) {
            idCardLivePreviewTitleFront.textContent = previewTitleFromCardType(cardType || defaultCardType);
        }

        if (idCardLivePreviewTitleBack) {
            idCardLivePreviewTitleBack.textContent = 'BACK';
        }

        lastDimensionUnit = cardDimensionUnit?.value === 'px' ? 'px' : 'cm';
        syncLengthFieldSteps(lastDimensionUnit);

        const copySourceLabel = document.getElementById('idCardCopySourceLabel');
        if (copySourceLabel) copySourceLabel.textContent = (cardType || defaultCardType) === 'library_card' ? 'ID card' : 'Library card';

        syncCardTypeSwitcher(cardType || defaultCardType);
        setDirtyState(!!copyFromType);

        refreshCardColorControls();
    }

    function refreshCardColorControls() {
        if (!settingsForm) return;

        const isTransparent = cardIsTransparent?.checked === true;
        const colorType = getSelectedCardColorType();
        const gradient1 = cardColorGradient1?.value || '#1e3a5f';
        const gradient2 = cardColorGradient2?.value || '#2563eb';
        const solid = cardSolidColor?.value || gradient1;
        const theme = isTransparent
            ? 'transparent'
            : (colorType === 'solid'
                ? solid
                : `linear-gradient(135deg, ${gradient1}, ${gradient2})`);

        if (isTransparent) {
            if (cardSchoolNameColor && cardSchoolNameColor.value === '#ffffff') cardSchoolNameColor.value = '#111827';
            if (cardSchoolDetailColor && cardSchoolDetailColor.value === '#e5e7eb') cardSchoolDetailColor.value = '#334155';
            if (cardSloganColor && cardSloganColor.value === '#e5e7eb') cardSloganColor.value = '#334155';
            if (cardBackNoticeColor && cardBackNoticeColor.value === '#94a3b8') cardBackNoticeColor.value = '#64748b';
            if (cardFooterColor && cardFooterColor.value === '#e5e7eb') cardFooterColor.value = '#111827';
            if (cardTitleColor && cardTitleColor.value === '#ffffff') cardTitleColor.value = '#111827';
        }

        if (colorType === 'solid') {
            $('.card-color-gradient-field').addClass('d-none');
            $('.card-color-solid-field').removeClass('d-none');
        } else {
            $('.card-color-gradient-field').removeClass('d-none');
            $('.card-color-solid-field').addClass('d-none');
        }

        if (cardSchoolNameColorPreview) {
            cardSchoolNameColorPreview.style.background = cardSchoolNameColor?.value || '#ffffff';
        }

        if (cardSchoolDetailColorPreview) {
            cardSchoolDetailColorPreview.style.background = cardSchoolDetailColor?.value || '#e5e7eb';
        }

        if (cardSloganColorPreview) {
            cardSloganColorPreview.style.background = cardSloganColor?.value || '#e5e7eb';
        }

        const detailColorInput = settingsForm.elements.namedItem('card_student_detail_text_color');
        const detailColorPreview = document.getElementById('cardStudentDetailColorPreview');
        if (detailColorPreview) {
            detailColorPreview.style.background = detailColorInput?.value || '#111827';
        }

        if (cardNameColorPreview) {
            cardNameColorPreview.style.background = cardNameColor?.value || '#1e3a5f';
        }

        if (cardBackNoticeColorPreview) {
            cardBackNoticeColorPreview.style.background = cardBackNoticeColor?.value || '#94a3b8';
        }

        if (cardFooterColorPreview) {
            cardFooterColorPreview.style.background = cardFooterColor?.value || '#e5e7eb';
        }

        if (cardTitleColorPreview) {
            cardTitleColorPreview.style.background = cardTitleColor?.value || '#ffffff';
        }

        if (cardThemePreview) {
            cardThemePreview.style.background = theme;
            cardThemePreview.style.borderStyle = isTransparent ? 'dashed' : 'solid';
        }

        if (cardColorGradient1Preview) {
            cardColorGradient1Preview.style.background = gradient1;
        }

        if (cardColorGradient2Preview) {
            cardColorGradient2Preview.style.background = gradient2;
        }

        if (cardSolidColorPreview) {
            cardSolidColorPreview.style.background = solid;
        }

        if (cardLogoPreview) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || initialCardLogoPreviewSrc || fallbackSchoolLogo || '';
            cardLogoPreview.src = logoUrl;
            cardLogoPreview.classList.toggle('d-none', !logoUrl);
        }

        if (cardPrincipalSignaturePreview) {
            const signatureUrl = previewPrincipalSignatureUrl || activeCardSettings.card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';
            cardPrincipalSignaturePreview.src = signatureUrl;
            cardPrincipalSignaturePreview.classList.toggle('d-none', !signatureUrl);
        }

        if (idCardLivePreview) {
            const themeAccent = isTransparent
                ? 'transparent'
                : (colorType === 'solid' ? solid : gradient1);
            const studentDetailAlignment = cardStudentDetailAlignment?.value || 'left';
            const studentDetailAlignCss = studentDetailAlignment === 'center'
                ? 'center'
                : (studentDetailAlignment === 'right' ? 'flex-end' : 'flex-start');
            idCardLivePreview.style.setProperty('--preview-bg', theme);
            idCardLivePreview.style.setProperty('--preview-school-name-color', cardSchoolNameColor?.value || '#ffffff');
            idCardLivePreview.style.setProperty('--preview-school-detail-color', cardSchoolDetailColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--preview-slogan-color', cardSloganColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--preview-title-color', cardTitleColor?.value || '#ffffff');
            idCardLivePreview.style.setProperty('--preview-back-notice-color', cardBackNoticeColor?.value || '#94a3b8');
            idCardLivePreview.style.setProperty('--preview-footer-color', cardFooterColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--card-theme-bg', theme);
            idCardLivePreview.style.setProperty('--card-theme-accent', themeAccent);
            idCardLivePreview.style.setProperty('--id-card-school-name-color', cardSchoolNameColor?.value || '#ffffff');
            idCardLivePreview.style.setProperty('--id-card-school-detail-color', cardSchoolDetailColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--id-card-slogan-color', cardSloganColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--id-card-name-color', cardNameColor?.value || '#1e3a5f');
            idCardLivePreview.style.setProperty('--id-card-student-detail-align', studentDetailAlignCss);
            idCardLivePreview.style.setProperty('--id-card-student-detail-text-align', studentDetailAlignment);
            idCardLivePreview.style.setProperty('--id-card-back-notice-color', cardBackNoticeColor?.value || '#94a3b8');
            idCardLivePreview.style.setProperty('--id-card-footer-color', cardFooterColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--id-card-title-color', cardTitleColor?.value || '#ffffff');

            const unit = cardDimensionUnit?.value || 'cm';
            const cardWidthValue = parseFloat(cardWidth?.value || '5.4') || 5.4;
            const cardHeightValue = parseFloat(cardHeight?.value || '8.56') || 8.56;
            const widthValue = parseFloat(cardPhotoWidth?.value || '1.8') || 1.8;
            const heightValue = parseFloat(cardPhotoHeight?.value || '2.7') || 2.7;
            const photoFitValue = cardPhotoFit?.value || 'cover';
            const logoSizeValue = parseFloat(cardLogoSize?.value || '0.8') || 0.8;
            const logoFitValue = cardLogoFit?.value || 'contain';
            const frontPaddingValue = parseFloat(cardFrontPadding?.value || '0.8') || 0.8;
            const backPaddingValue = parseFloat(cardBackPadding?.value || '0.8') || 0.8;
            const gapValue = parseFloat(cardGridGap?.value || '0.5') || 0.5;
            const readPadding = (name, fallback) => {
                const parsed = parseFloat(settingsForm?.elements.namedItem(name)?.value);
                return Number.isFinite(parsed) && parsed >= 0 ? parsed : fallback;
            };
            const frontHeaderPaddingValue = readPadding('card_front_header_padding_value', 0.3);
            const backHeaderPaddingValue = readPadding('card_back_header_padding_value', 0.3);
            const schoolNameFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_school_name_font_size')?.value || '7.2') || 7.2;
            const schoolDetailFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_school_detail_font_size')?.value || '5.4') || 5.4;
            const sloganFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_slogan_font_size')?.value || '4.8') || 4.8;
            const titleFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_title_font_size')?.value || '4.7') || 4.7;
            const nameFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_name_font_size')?.value || '7.2') || 7.2;
            const unitSuffix = unit === 'px' ? 'px' : 'cm';
            idCardLivePreview.style.setProperty('--preview-card-width', `${cardWidthValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-card-height', `${cardHeightValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-card-ratio', `${(cardWidthValue / cardHeightValue).toFixed(4)}`);
            idCardLivePreview.style.setProperty('--preview-photo-width', `${widthValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-photo-height', `${heightValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-logo-size', `${logoSizeValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-front-padding', `${frontPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-back-padding', `${backPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--preview-gap', `${gapValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-width', `${cardWidthValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-height', `${cardHeightValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-gap', `${gapValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-front-padding', `${frontPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-front-header-padding', `${frontHeaderPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-back-header-padding', `${backHeaderPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-back-padding', `${backPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-width', `${widthValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-height', `${heightValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-fit', photoFitValue);
            const previewPhoto = document.getElementById('idCardLivePreviewPhoto');
            if (previewPhoto) previewPhoto.style.objectFit = photoFitValue;
            idCardLivePreview.style.setProperty('--id-card-logo-size', `${logoSizeValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-logo-fit', logoFitValue);
            const detailFontSizeValue = parseFloat(settingsForm?.elements.namedItem('card_student_detail_font_size')?.value || '4.6') || 4.6;
            idCardLivePreview.style.setProperty('--id-card-school-name-font-size', `${fitTextSize(previewFit.schoolName, schoolNameFontSizeValue, 5.2, 20, 0.08)}pt`);
            idCardLivePreview.style.setProperty('--id-card-school-detail-font-size', `${fitTextSize(previewFit.schoolDetail, schoolDetailFontSizeValue, 4.1, 30, 0.05)}pt`);
            idCardLivePreview.style.setProperty('--id-card-slogan-font-size', `${fitTextSize(previewFit.slogan, sloganFontSizeValue, 3.8, 26, 0.04)}pt`);
            idCardLivePreview.style.setProperty('--id-card-title-font-size', `${fitTextSize(previewFit.frontTitle, titleFontSizeValue, 4.0, 14, 0.05)}pt`);
            idCardLivePreview.style.setProperty('--id-card-name-font-size', `${fitTextSize(previewFit.name, nameFontSizeValue, 5.4, 20, 0.08)}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-title-font-size', `${fitTextSize(previewFit.backTitles, titleFontSizeValue, 4.0, 18, 0.04)}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-value-font-size', `${fitTextSize(previewFit.backValues, detailFontSizeValue, 3.6, 26, 0.05)}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-label-font-size', `${Math.round(Math.max(1, fitTextSize(previewFit.backValues, detailFontSizeValue, 3.6, 26, 0.05) * 0.9) * 100) / 100}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-title-color', isTransparent ? (cardTitleColor?.value || '#ffffff') : themeAccent);
            idCardLivePreview.style.setProperty('--id-card-front-align', settingsForm.elements.namedItem('card_front_alignment')?.value || 'center');
            idCardLivePreview.style.setProperty('--id-card-back-align', settingsForm.elements.namedItem('card_back_alignment')?.value || 'center');
            idCardLivePreview.style.setProperty('--id-card-student-detail-color', settingsForm.elements.namedItem('card_student_detail_text_color')?.value || '#111827');
            idCardLivePreview.style.setProperty('--id-card-detail-font-size', `${detailFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-detail-label-font-size', `${Math.round(Math.max(1, detailFontSizeValue * 0.9) * 100) / 100}pt`);

            setPreviewElementVisible('.id-card__header--front [data-pos-key="front_logo"]', !!(settingsForm.elements.namedItem('card_show_logo_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back [data-pos-key="back_logo"]', !!(settingsForm.elements.namedItem('card_show_logo_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__school-detail', !!(settingsForm.elements.namedItem('card_show_school_detail_front')?.checked ?? false));
            setPreviewElementVisible('.js-school-detail-back', !!(settingsForm.elements.namedItem('card_show_school_detail_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--front .id-card__slogan', !!(settingsForm.elements.namedItem('card_show_slogan_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back .id-card__slogan', !!(settingsForm.elements.namedItem('card_show_slogan_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--front .id-card__label-badge', !!(settingsForm.elements.namedItem('card_show_title_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back .id-card__label-badge', !!(settingsForm.elements.namedItem('card_show_title_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__photo-wrap', !!(settingsForm.elements.namedItem('card_show_photo_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__footer--front', !!(settingsForm.elements.namedItem('card_show_footer_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-section--student-details', !!(settingsForm.elements.namedItem('card_show_back_student_details')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-section--school-contact', !!(settingsForm.elements.namedItem('card_show_back_school_contact')?.checked ?? true));
            setPreviewElementVisible('[data-pos-key="back_qr"]', !!(settingsForm.elements.namedItem('card_show_back_qr')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-notice', !!(settingsForm.elements.namedItem('card_show_back_notice')?.checked ?? true));
            setPreviewElementVisible('.id-card__signature', !!(settingsForm.elements.namedItem('card_show_back_signature')?.checked ?? true));
            setPreviewElementVisible('.id-card__footer--back', !!(settingsForm.elements.namedItem('card_show_footer_back')?.checked ?? true));
        }

        updateLayoutFitNotice();
        applyPreviewPositions();

        if (idCardLivePreviewLogoFront) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            idCardLivePreviewLogoFront.src = logoUrl;
            idCardLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
            idCardLivePreviewLogoFront.style.objectFit = cardLogoFit?.value || 'contain';
        }

        if (idCardLivePreviewLogoBack) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            idCardLivePreviewLogoBack.src = logoUrl;
            idCardLivePreviewLogoBack.classList.toggle('d-none', !logoUrl);
            idCardLivePreviewLogoBack.style.objectFit = cardLogoFit?.value || 'contain';
        }

        if (idCardLivePreviewSignatureBack) {
            const signatureUrl = previewPrincipalSignatureUrl || activeCardSettings.card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';
            idCardLivePreviewSignatureBack.src = signatureUrl;
            idCardLivePreviewSignatureBack.classList.toggle('d-none', !signatureUrl);
        }
    }

    function refreshSectionSelect() {
        if (!sectionSelect) return;
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
    }

    function replaceSectionOptions(html) {
        if (!sectionSelect) return;

        const $section = $(sectionSelect);
        if ($section.hasClass('select2-hidden-accessible')) {
            $section.select2('destroy');
        }

        sectionSelect.innerHTML = html;

        if (window.reinitSelect2) {
            window.reinitSelect2(sectionSelect.parentElement);
        } else {
            refreshSectionSelect();
        }
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        replaceSectionOptions('<option value="">Loading...</option>');

        if (!classId) {
            replaceSectionOptions('<option value="">— Select Section —</option>');
            return;
        }

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then((response) => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then((data) => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">— Select Section —</option>';

                sections.forEach((section) => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                replaceSectionOptions(html);
            })
            .catch(() => {
                replaceSectionOptions('<option value="">— Select Section —</option>');
            });
    }

    $(document).on('change', '#classSelect', function () {
        loadSections(this.value);
    });

    $(document).on('click', '#idCardSettingsModal .js-card-type-switch', function () {
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

    // ---- Zoom: "Fit" scales the pair to the panel width; +/- switches to manual steps ----
    const ZOOM_STEPS = [0.5, 0.65, 0.8, 1, 1.25, 1.5, 1.75, 2];
    let manualZoom = null;
    let zoomFrame = null;

    function applyPreviewZoom() {
        const stage = idCardLivePreview?.querySelector('.card-preview-stage--pair');
        const pair = idCardLivePreview?.querySelector('.id-card-pair');
        const label = document.getElementById('idCardZoomLevel');
        if (!stage || !pair) return;
        idCardLivePreview.style.setProperty('--preview-zoom', '1');
        const natural = pair.getBoundingClientRect().width || 1;
        const fit = Math.max(0.4, Math.min(1.4, (stage.clientWidth - 4) / natural));
        const zoom = manualZoom ?? fit;
        idCardLivePreview.style.setProperty('--preview-zoom', String(zoom));
        if (label) {
            label.textContent = manualZoom === null ? `Fit ${Math.round(fit * 100)}%` : `${Math.round(zoom * 100)}%`;
            label.title = manualZoom === null ? 'Fitting to the panel width' : 'Click to fit the panel again';
        }
    }

    function scheduleZoom() {
        if (zoomFrame) cancelAnimationFrame(zoomFrame);
        zoomFrame = requestAnimationFrame(applyPreviewZoom);
    }

    function stepZoom(direction) {
        const current = parseFloat(idCardLivePreview?.style.getPropertyValue('--preview-zoom')) || 1;
        const next = direction > 0
            ? ZOOM_STEPS.find((step) => step > current + 0.01)
            : [...ZOOM_STEPS].reverse().find((step) => step < current - 0.01);
        if (next) {
            manualZoom = next;
            applyPreviewZoom();
        }
    }

    document.getElementById('idCardZoomIn')?.addEventListener('click', () => stepZoom(1));
    document.getElementById('idCardZoomOut')?.addEventListener('click', () => stepZoom(-1));
    document.getElementById('idCardZoomLevel')?.addEventListener('click', function () {
        manualZoom = null;
        applyPreviewZoom();
    });
    if (window.ResizeObserver && idCardLivePreview) {
        new ResizeObserver(scheduleZoom).observe(idCardLivePreview.parentElement || idCardLivePreview);
    }
    window.addEventListener('resize', scheduleZoom);
    settingsForm?.addEventListener('input', scheduleZoom);
    settingsForm?.addEventListener('change', scheduleZoom);
    scheduleZoom();

    // ---- Preview PDF: opens the real mPDF output for one card (uses the last SAVED settings) ----
    document.getElementById('idCardPreviewPdf')?.addEventListener('click', function (event) {
        if (settingsDirty && !window.confirm('You have unsaved changes. The PDF uses the last saved settings. Open it anyway?')) {
            event.preventDefault();
            return;
        }
        const studentId = document.getElementById('idCardPreviewStudent')?.value;
        if (this.dataset.baseHref && !this.dataset.employee) {
            const params = new URLSearchParams({
                preview_student_id: studentId || 'first',
                card_type: settingsForm?.elements.namedItem('card_type')?.value || 'id_card',
                inline: '1',
            });
            this.href = `${this.dataset.baseHref}?${params.toString()}`;
        }
    });

    document.getElementById('idCardResetPositions')?.addEventListener('click', function () {
        loadPositionInputs({});
        setDirtyState(true);
    });

    let dragState = null;
    let suppressDragClick = false;

    idCardLivePreview?.addEventListener('pointerdown', function (event) {
        if (event.button !== 0 || event.target.closest('input, button, a, select, textarea')) return;
        const element = event.target.closest('[data-pos-key]');
        const card = element?.closest('.id-card');
        if (!element || !card || !idCardLivePreview.contains(element)) return;

        const unit = cardDimensionUnit?.value === 'px' ? 'px' : 'cm';
        const widthValue = parseFloat(cardWidth?.value) || 5.4;
        const cardWidthMm = unit === 'px' ? widthValue / 96 * 25.4 : widthValue * 10;

        event.preventDefault();
        dragState = {
            element,
            key: element.dataset.posKey,
            startX: event.clientX,
            startY: event.clientY,
            lastX: event.clientX,
            lastY: event.clientY,
            moved: false,
            mmPerPx: cardWidthMm / Math.max(card.getBoundingClientRect().width, 1),
        };
        element.setPointerCapture?.(event.pointerId);
    });

    idCardLivePreview?.addEventListener('pointermove', function (event) {
        if (!dragState) return;
        if (!dragState.moved) {
            if (Math.abs(event.clientX - dragState.startX) < 3 && Math.abs(event.clientY - dragState.startY) < 3) return;
            dragState.moved = true;
            dragState.element.classList.add('is-dragging');
        }

        event.preventDefault();
        nudgePosition(dragState.key, 'x', (event.clientX - dragState.lastX) * dragState.mmPerPx);
        nudgePosition(dragState.key, 'y', (event.clientY - dragState.lastY) * dragState.mmPerPx);
        dragState.lastX = event.clientX;
        dragState.lastY = event.clientY;
        applyPreviewPositions();
        setDirtyState(true);
    });

    function finishDrag(event) {
        if (!dragState) return;
        suppressDragClick = dragState.moved;
        dragState.element.classList.remove('is-dragging');
        dragState.element.releasePointerCapture?.(event.pointerId);
        dragState = null;
    }

    idCardLivePreview?.addEventListener('pointerup', finishDrag);
    idCardLivePreview?.addEventListener('pointercancel', finishDrag);
    // A drag must not also count as a click (which would jump to that element's setting).
    idCardLivePreview?.addEventListener('click', function (event) {
        if (!suppressDragClick) return;
        event.preventDefault();
        event.stopImmediatePropagation();
        suppressDragClick = false;
    }, true);

    document.getElementById('idCardResetDefaults')?.addEventListener('click', function () {
        if (!settingsForm) return;
        if (!window.confirm('Reset every setting in this form to the standard ID card preset?\nNothing is saved until you press Save Settings.')) return;

        Object.entries(cardDefaults).forEach(([name, value]) => {
            if (name === 'card_color_type') {
                settingsForm.querySelectorAll('input[name="card_color_type"]').forEach((radio) => { radio.checked = radio.value === value; });
                return;
            }
            const input = settingsForm.elements.namedItem(name);
            if (!input || typeof input.type === 'undefined') return;
            if (input.type === 'checkbox') {
                input.checked = !!value;
            } else {
                input.value = value;
            }
        });

        loadPositionInputs({});
        lastDimensionUnit = cardDimensionUnit?.value === 'px' ? 'px' : 'cm';
        syncLengthFieldSteps(lastDimensionUnit);
        refreshCardColorControls();
        setDirtyState(true);
    });

    document.getElementById('idCardCopySettings')?.addEventListener('click', function () {
        const current = settingsForm?.elements.namedItem('card_type')?.value || defaultCardType;
        const other = current === 'library_card' ? 'id_card' : 'library_card';
        if (!window.confirm(`Copy layout, colours and visibility from the ${other === 'library_card' ? 'Library' : 'ID'} card?\nNothing is saved until you press Save Settings.`)) return;
        applyCardSettings(current, other);
    });

    // Preview the card with a real student from the loaded list (front side).
    const previewSampleData = {};
    idCardLivePreview?.querySelectorAll('[data-pv]').forEach((el) => {
        if (!(el.dataset.pv in previewSampleData)) previewSampleData[el.dataset.pv] = el.textContent.trim();
    });
    const samplePhotoSrc = document.getElementById('idCardLivePreviewPhoto')?.getAttribute('src') || '';
    document.getElementById('idCardPreviewStudent')?.addEventListener('change', function () {
        const real = this.value ? previewStudents[this.value] : null;
        const data = real || previewSampleData;
        Object.keys(previewSampleData).forEach((key) => {
            idCardLivePreview?.querySelectorAll(`[data-pv="${key}"]`).forEach((el) => {
                el.textContent = data[key] || '';
                const row = el.closest('.id-card__row') || (key === 'bn' ? el : null);
                row?.classList.toggle('d-none', !data[key]);
            });
        });
        previewFit.name = data.name;
        const photo = document.getElementById('idCardLivePreviewPhoto');
        if (photo) photo.src = real ? real.photo : samplePhotoSrc;
        refreshCardColorControls();
    });

    // A single Student ID is the only way to load cards without Year / Class / Section.
    const studentCidInput = document.querySelector('#filterForm input[name="student_cid"]');
    const requiredFilterSelects = document.querySelectorAll('#filterForm select[name="session_id"], #filterForm select[name="class_id"], #filterForm select[name="section_id"]');
    function syncRequiredFilters() {
        const hasCid = !!studentCidInput?.value.trim();
        requiredFilterSelects.forEach((select) => { select.required = !hasCid; });
    }
    studentCidInput?.addEventListener('input', syncRequiredFilters);
    syncRequiredFilters();

    cardDimensionUnit?.addEventListener('change', function () {
        const nextUnit = cardDimensionUnit.value === 'px' ? 'px' : 'cm';
        convertLengthFields(lastDimensionUnit, nextUnit);
        lastDimensionUnit = nextUnit;
        refreshCardColorControls();
    });

    $(document).on('input change', '#cardPhotoWidth, #cardPhotoHeight, #cardLogoSize, select[name="card_dimension_unit"]', refreshCardColorControls);

    // The app turns every <select> into a Select2, which announces changes through jQuery only; native
    // listeners on the form never hear them. Forward those to the same refresh / dirty / zoom handlers.
    if (settingsForm && window.jQuery) {
        $(settingsForm).on('change', 'select', function () {
            refreshCardColorControls();
            setDirtyState(true);
            scheduleZoom();
        });
    }

    if (settingsForm) {
        settingsForm.addEventListener('input', refreshCardColorControls);
        settingsForm.addEventListener('change', refreshCardColorControls);
        settingsForm.addEventListener('input', function () {
            setDirtyState(true);
        });
        settingsForm.addEventListener('change', function () {
            setDirtyState(true);
        });
        settingsForm.addEventListener('submit', function () {
            setDirtyState(false);
        });
    }

    if (settingsForm) {
        if (cardIsTransparent) {
            cardIsTransparent.addEventListener('change', refreshCardColorControls);
        }
        settingsForm.addEventListener('input', function (event) {
            if (['card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_slogan_text_color', 'card_back_notice_text_color', 'card_footer_text_color', 'card_title_text_color'].includes(event.target.name)) {
                refreshCardColorControls();
            }
        });
        settingsForm.addEventListener('change', function (event) {
            if (['card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_slogan_text_color', 'card_back_notice_text_color', 'card_footer_text_color', 'card_title_text_color'].includes(event.target.name)) {
                refreshCardColorControls();
            }
        });
    }

    if (cardLogoInput && cardLogoPreview) {
        cardLogoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                previewLogoUrl = null;
                const logoUrl = activeCardSettings.card_logo_url || initialCardLogoPreviewSrc || fallbackSchoolLogo || '';
                cardLogoPreview.src = logoUrl;
                cardLogoPreview.classList.toggle('d-none', !logoUrl);
                if (idCardLivePreviewLogoFront) {
                    idCardLivePreviewLogoFront.src = logoUrl;
                    idCardLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
                }
                refreshCardColorControls();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                previewLogoUrl = event.target.result;
                cardLogoPreview.src = event.target.result;
                cardLogoPreview.classList.remove('d-none');
                if (idCardLivePreviewLogoFront) {
                    idCardLivePreviewLogoFront.src = event.target.result;
                    idCardLivePreviewLogoFront.classList.remove('d-none');
                }
                refreshCardColorControls();
            };
            reader.readAsDataURL(file);
        });
    }

    if (cardPrincipalSignatureInput && cardPrincipalSignaturePreview) {
        cardPrincipalSignatureInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                previewPrincipalSignatureUrl = null;
                const signatureUrl = activeCardSettings.card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';
                cardPrincipalSignaturePreview.src = signatureUrl;
                cardPrincipalSignaturePreview.classList.toggle('d-none', !signatureUrl);
                if (idCardLivePreviewSignatureBack) {
                    idCardLivePreviewSignatureBack.src = signatureUrl;
                    idCardLivePreviewSignatureBack.classList.toggle('d-none', !signatureUrl);
                }
                refreshCardColorControls();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                previewPrincipalSignatureUrl = event.target.result;
                cardPrincipalSignaturePreview.src = event.target.result;
                cardPrincipalSignaturePreview.classList.remove('d-none');
                if (idCardLivePreviewSignatureBack) {
                    idCardLivePreviewSignatureBack.src = event.target.result;
                    idCardLivePreviewSignatureBack.classList.remove('d-none');
                }
                refreshCardColorControls();
            };
            reader.readAsDataURL(file);
        });
    }

    refreshCardColorControls();

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSection);
    }

    $('#idCardSettingsModal').on('show.bs.modal', function () {
        if (!hasValidationErrors) {
            applyCardSettings(cardTypeSelect?.value || defaultCardType);
        } else {
            refreshCardColorControls();
        }
        lockPageScroll();
        setDirtyState(false);
    });

    $('#idCardSettingsModal').on('shown.bs.modal', function () {
        initializeSettingTooltips(this);
        refreshCardColorControls();
    });

    $('#idCardSettingsModal').on('hidden.bs.modal', function () {
        $(this).find('label').tooltip('dispose');
        unlockPageScroll();
    });

    @if($settingsOnly)
        // Dedicated page: there is no modal show/shown event, so initialise immediately.
        if (!hasValidationErrors) {
            applyCardSettings(defaultCardType);
        }
        initializeSettingTooltips($('#idCardSettingsModal'));
        refreshCardColorControls();
        setDirtyState(false);
    @elseif($errors->any())
        $('#idCardSettingsModal').modal('show');
    @endif
});
</script>

<style>
@include('pages.generate-id-cards._styles', ['setting' => $setting, 'pageCss' => $layout['pageCss'] ?? 'A4 landscape'])

.id-card-filter-shell {
    background: #fff;
    border: 1px solid rgba(148, 163, 184, 0.2);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    border-radius: 1rem;
    overflow: hidden;
}

.id-card-filter-body {
    padding-top: 1rem;
    background: transparent !important;
}

.id-card-filter-panel {
    padding: 1rem;
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 0.95rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
}

.id-card-filter-grid {
    row-gap: 1rem;
}

.id-card-filter-label {
    display: inline-flex;
    margin-bottom: 0.35rem;
    color: #334155;
    font-size: 0.84rem;
}

.id-card-filter-input {
    background: #fff;
    color: #0f172a;
}

.id-card-filter-btn {
    min-width: 2.7rem;
    min-height: 2.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.id-card-filter-actions-inner {
    gap: 6px;
}

.id-card-filter-actions-row {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.75rem;
}

/* The page sits inside a bootstrap .row (flex), so without this it shrinks to its content width. */
.id-card-dedicated-settings {
    flex: 0 0 100%;
    width: 100%;
    max-width: 100%;
    min-width: 0;
}

.id-card-dedicated-settings .modal {
    position: static;
    display: block !important;
    opacity: 1 !important;
    overflow: visible;
    padding-right: 0 !important;
}

.id-card-dedicated-settings .modal-dialog {
    width: 100%;
    max-width: none;
    min-height: 0;
    margin: 0 auto;
    transform: none !important;
    display: block;
}

.id-card-dedicated-settings .id-card-settings-modal-content {
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
}

/* Preview on top, settings underneath using the full page width (same as the admit-card settings page). */
.id-card-dedicated-settings .admit-seat-cards-modal-layout {
    display: flex;
    flex-direction: column;
    margin-left: 0;
    margin-right: 0;
}

.id-card-dedicated-settings .admit-seat-cards-modal-preview,
.id-card-dedicated-settings .admit-seat-cards-modal-settings {
    width: 100%;
    max-width: 100%;
    flex: 0 0 100%;
    padding-left: 0;
    padding-right: 0;
}

.id-card-dedicated-settings .admit-seat-cards-modal-preview {
    margin-bottom: 1rem !important;
}

.id-card-dedicated-settings .csm-preview-sticky {
    position: static;
    max-width: 760px;
    margin: 0 auto;
}

.id-card-dedicated-settings .card-preview-shell {
    position: static;
}

/* Preview toolbar: one tidy row above the live preview */
.csm-preview-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
}

.csm-preview-toolbar__student {
    flex: 1 1 100%;
    min-width: 0;
    max-width: 100%;
}

.csm-preview-toolbar #idCardResetPositions {
    margin-left: auto;
}

.csm-preview-toolbar .btn {
    white-space: nowrap;
}

.csm-zoom-level {
    min-width: 4.6rem;
    font-variant-numeric: tabular-nums;
}

.csm-preview-toolbar .btn-group {
    display: inline-flex;
    flex-direction: row;
    flex-wrap: nowrap;
    width: auto;
}

.csm-preview-toolbar .btn-group > .btn {
    width: auto;
    flex: 0 0 auto;
}

@media (max-width: 575.98px) {
    .csm-preview-toolbar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }

    .csm-preview-toolbar__student {
        grid-column: 1 / -1;
    }

    .csm-preview-toolbar .btn-group {
        grid-column: 1 / -1;
        display: flex;
        width: 100%;
    }

    .csm-preview-toolbar .btn-group > .btn {
        flex: 1 1 0;
    }

    .csm-preview-toolbar .btn-group > .csm-zoom-level {
        flex: 2 1 0;
    }

    .csm-preview-toolbar #idCardResetPositions,
    .csm-preview-toolbar #idCardPreviewPdf {
        grid-column: span 1;
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }

}

/* Layout tab groups */
.csm-group {
    margin-bottom: 1.1rem;
    padding-bottom: 1rem;
    border-bottom: 1px dashed rgba(148, 163, 184, 0.45);
}

.csm-group .invalid-feedback {
    display: none;
}

.csm-group .is-invalid ~ .invalid-feedback {
    display: block;
}

.csm-group.mb-0 {
    padding-bottom: 0;
    border-bottom: 0;
}

.csm-group__title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 0.6rem;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: inherit;
}

.csm-group__title i {
    color: #2563eb;
}

.csm-group__title small {
    font-size: 0.74rem;
    font-weight: 500;
    letter-spacing: 0;
    text-transform: none;
    color: inherit;
    opacity: 0.6;
}

/* Mini sheet diagram */
.csm-sheet {
    position: relative;
    width: 100%;
    max-width: 260px;
    margin: 0 auto;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.csm-sheet--invalid {
    border-color: #dc3545;
}

.csm-sheet__area {
    position: absolute;
    border: 1px dashed #cbd5e1;
}

.csm-sheet__card {
    position: absolute;
    box-sizing: border-box;
    border-radius: 2px;
    border: 1px solid #1e3a5f;
}

.csm-sheet__card--front {
    background: rgba(37, 99, 235, 0.28);
}

.csm-sheet__card--back {
    background: rgba(37, 99, 235, 0.10);
}

.csm-sheet__label {
    position: absolute;
    right: 6px;
    bottom: 4px;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #94a3b8;
}

/* Sticky action bar */
.id-card-dedicated-settings .id-card-settings-modal-content {
    overflow: visible;
}

.id-card-dedicated-settings .id-card-settings-modal-footer {
    position: sticky;
    bottom: 0;
    z-index: 20;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(6px);
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 -8px 24px rgba(15, 23, 42, 0.06);
}

.csm-dirty {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    color: #b45309;
}

.csm-dirty i {
    font-size: 0.45rem;
}

/* Wide screens: preview stays in view on the left while you tweak settings on the right */
@media (min-width: 1500px) {
    .id-card-dedicated-settings .admit-seat-cards-modal-layout {
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: flex-start;
    }

    .id-card-dedicated-settings .admit-seat-cards-modal-preview {
        flex: 0 0 540px;
        max-width: 540px;
        padding-right: 1.25rem;
        margin-bottom: 0 !important;
    }

    .id-card-dedicated-settings .admit-seat-cards-modal-settings {
        flex: 1 1 0;
        min-width: 0;
        max-width: none;
    }

    .id-card-dedicated-settings .csm-preview-sticky {
        position: sticky;
        top: 72px;
        max-width: none;
        margin: 0;
    }
}

@media (prefers-reduced-motion: reduce) {
    .csm-preview-toolbar .btn { transition: none; }
}

.id-card-settings-modal-dialog {
    max-width: 1140px;
    margin: 1.75rem auto;
}

.id-card-settings-modal-content {
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
}

.id-card-settings-modal-header {
    padding: 0.8rem 1rem;
    border-bottom: 1px solid #eaeef4;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    align-items: flex-start;
}

.id-card-settings-modal-header > div:first-child {
    flex: 1 1 260px;
    min-width: 0;
}

.id-card-settings-type-switcher {
    flex: 0 0 auto;
    flex-wrap: nowrap;
    display: inline-flex;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.02);
}

.id-card-settings-type-switcher .btn {
    white-space: nowrap;
    min-width: 8.5rem;
    padding: 0.52rem 0.9rem;
}

.id-card-settings-type-switcher .btn + .btn {
    margin-left: -1px;
}

.id-card-settings-modal-close {
    opacity: 0.7;
    font-size: 1.8rem;
    line-height: 1;
    text-shadow: none;
}

.id-card-settings-modal-close:hover {
    opacity: 1;
}

.id-card-settings-modal-body {
    padding: 0.9rem;
    background: #fff;
}

.id-card-settings-modal-body .form-control,
.id-card-settings-modal-body .custom-select,
.id-card-settings-modal-body .custom-file-input,
.id-card-settings-modal-body .custom-file-label {
    min-height: 32px;
    padding-top: 0.22rem;
    padding-bottom: 0.22rem;
    font-size: 0.85rem;
}

.id-card-settings-modal-body .form-group {
    margin-bottom: 0.7rem;
}

.id-card-settings-field {
    padding: 0.72rem;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
}

.id-card-settings-help {
    margin-top: 0.75rem;
    padding: 0.85rem 1rem;
    border-radius: 14px;
    background: #f8fafc;
    color: #64748b;
    font-size: 0.88rem;
    border: 1px solid #e5e7eb;
}

.id-card-settings-tabs,
.id-card-settings-modal-content .csm-section-tabs {
    display: flex;
    flex-wrap: nowrap;
    gap: 0.35rem;
    padding: 0.25rem;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.88);
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
}

.id-card-settings-tabs .nav-item,
.id-card-settings-modal-content .csm-section-tabs .nav-item {
    margin-bottom: 0;
    flex: 0 0 auto;
}

.id-card-settings-tabs .nav-link,
.id-card-settings-modal-content .csm-section-tabs .nav-link {
    border: 0;
    border-radius: 12px;
    padding: 0.44rem 0.72rem;
    font-size: 0.84rem;
    font-weight: 700;
    color: #475569;
    background: transparent;
    white-space: nowrap;
    transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.id-card-settings-tabs .nav-link:hover,
.id-card-settings-modal-content .csm-section-tabs .nav-link:hover {
    color: #0f172a;
    background: rgba(226, 232, 240, 0.6);
}

.id-card-settings-tabs .nav-link.active,
.id-card-settings-modal-content .csm-section-tabs .nav-link.active {
    color: #fff;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
}

.id-card-settings-panel > .tab-pane > .card,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card {
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}

.id-card-settings-panel > .tab-pane > .card > .card-header,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-header {
    padding: 0.85rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
}

.id-card-settings-panel > .tab-pane > .card > .card-body,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-body {
    padding: 1rem;
}

.id-card-settings-section-header .badge {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

.id-card-settings-section-header h6 {
    letter-spacing: -0.01em;
}

.id-card-settings-field {
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

.id-card-settings-field:hover {
    border-color: #cbd5e1;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
}

.id-card-settings-label {
    margin: 0;
    font-size: 0.76rem;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.id-card-settings-label[data-toggle="tooltip"] {
    cursor: help;
}

.id-card-settings-control {
    width: 100%;
    min-height: 42px;
    border-radius: 12px;
    border-color: #cbd5e1;
    box-shadow: none;
}

.id-card-settings-control:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.16);
}

.id-card-settings-color-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: nowrap;
}

.id-card-settings-color-native {
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

.id-card-settings-color-native::-webkit-color-swatch-wrapper {
    padding: 0;
}

.id-card-settings-color-native::-webkit-color-swatch {
    border: 0;
    border-radius: 8px;
}

.id-card-settings-color-preview {
    width: 32px;
    height: 32px;
    flex: 0 0 32px;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
}

.id-card-settings-note {
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

.id-card-settings-modal-content .csm-bg-toggle {
    display: flex;
    gap: 0.35rem;
    padding: 0.35rem;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.85);
    overflow: hidden;
}

.id-card-settings-modal-content .csm-bg-toggle .btn {
    border: 0;
    border-radius: 12px;
    padding: 0.68rem 0.95rem;
    font-weight: 700;
    color: #475569;
    background: transparent;
    box-shadow: none;
    transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
}

.id-card-settings-modal-content .csm-bg-toggle .btn:hover {
    color: #0f172a;
    background: rgba(226, 232, 240, 0.6);
}

.id-card-settings-modal-content .csm-bg-toggle .btn.active {
    color: #fff;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18);
}

.id-card-upload-box {
    padding: 0.95rem 1rem;
    border: 1px dashed #cbd5e1;
    border-radius: 16px;
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.94) 100%);
}

.id-card-upload-preview {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    background: #ffffff;
}

.id-card-settings-modal-footer {
    padding: 0.85rem 1rem 1rem;
    border-top: 1px solid #eaeef4;
    background: #fff;
}

.card-header {
    background: linear-gradient(135deg, #1f2a44, #111827);
}

.card-title {
    font-weight: 700;
}

html[data-theme='dark'] .id-card-filter-shell {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.97) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 12px 28px rgba(2, 6, 23, 0.34);
}

html[data-theme='dark'] .id-card-filter-shell .card-header,
html[data-theme='dark'] .id-card-filter-shell .card-body {
    background: rgba(15, 23, 42, 0.97) !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-body {
    background: transparent;
}

html[data-theme='dark'] .id-card-filter-panel {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 10px 24px rgba(2, 6, 23, 0.28);
}

html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--multiple {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single .select2-selection__clear,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    background: transparent !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .select2-container--open .select2-dropdown {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
    box-shadow: 0 16px 32px rgba(2, 6, 23, 0.34) !important;
}

html[data-theme='dark'] .select2-container--open .select2-results__option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .select2-container--open .select2-results__option--highlighted {
    background: #2563eb !important;
    color: #fff !important;
}

html[data-theme='dark'] .id-card-filter-label {
    color: #cbd5e1;
}

html[data-theme='dark'] .id-card-filter-input {
    background: rgba(15, 23, 42, 0.96);
    color: #e2e8f0;
    border-color: rgba(148, 163, 184, 0.22);
}

html[data-theme='dark'] .id-card-filter-input::placeholder {
    color: #94a3b8;
}

html[data-theme='dark'] .id-card-filter-shell .form-control,
html[data-theme='dark'] .id-card-filter-shell .select2-container,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single {
    background-color: #0f172a !important;
    color: #e2e8f0 !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default.select2-container--focus .select2-selection--single,
html[data-theme='dark'] .id-card-filter-shell .form-control:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.18) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__placeholder {
    background: transparent !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #94a3b8 transparent transparent transparent !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-dropdown {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-results__option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-results__option--highlighted {
    background: #2563eb !important;
    color: #fff !important;
}

html[data-theme='dark'] .id-card-filter-shell .btn-secondary {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.22);
    color: #e2e8f0;
}

html[data-theme='dark'] .id-card-filter-shell .btn-secondary:hover,
html[data-theme='dark'] .id-card-filter-shell .btn-secondary:focus {
    background: #1e293b;
    border-color: rgba(148, 163, 184, 0.32);
    color: #f8fafc;
}

html[data-theme='dark'] .id-card-filter-shell .btn-primary {
    background: #2563eb;
    border-color: #2563eb;
}

html[data-theme='dark'] .id-card-filter-shell .btn-success {
    background: #16a34a;
    border-color: #16a34a;
}

@media (max-width: 767.98px) {
    .id-card-filter-actions-inner {
        width: 100%;
        justify-content: flex-end;
    }

    .id-card-filter-actions-row {
        justify-content: flex-start;
    }

    .id-card-settings-modal-dialog {
        margin: 0.5rem;
        max-width: calc(100vw - 1rem);
    }

    .id-card-settings-modal-content {
        border-radius: 14px;
    }

    .id-card-settings-modal-header {
        padding: 0.75rem;
        flex-direction: column;
        align-items: stretch;
        gap: 0.55rem;
    }

    .id-card-settings-modal-header > div:first-child {
        flex-basis: 100%;
    }

    .id-card-settings-type-switcher {
        width: 100%;
        display: flex;
    }

    .id-card-settings-type-switcher .btn {
        flex: 1 1 0;
        min-width: 0;
        padding: 0.45rem 0.6rem;
    }

    .id-card-settings-modal-close {
        align-self: flex-end;
        margin-top: -0.1rem;
    }

    .id-card-settings-modal-body {
        padding: 0.75rem;
    }

    .id-card-settings-modal-body .form-control,
    .id-card-settings-modal-body .custom-select,
    .id-card-settings-modal-body .custom-file-input,
    .id-card-settings-modal-body .custom-file-label {
        min-height: 38px;
        font-size: 0.9rem;
    }

    .id-card-settings-modal-content .csm-section-tabs {
        gap: 0.25rem;
        padding: 0.2rem;
        border-radius: 14px;
    }

    .id-card-settings-modal-content .csm-section-tabs .nav-link {
        padding: 0.38rem 0.55rem;
        font-size: 0.78rem;
    }

    .id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-header {
        padding: 0.78rem 0.85rem;
    }

    .id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-body {
        padding: 0.85rem;
    }

    .id-card-settings-field,
    .id-card-upload-box,
    .id-card-upload-preview {
        padding: 0.7rem;
        border-radius: 14px;
    }

    .id-card-settings-help {
        padding: 0.75rem 0.85rem;
        font-size: 0.84rem;
    }

    .csm-preview-sticky {
        position: static;
    }

    .admit-seat-cards-modal-preview {
        margin-bottom: 0.75rem;
    }
}

html[data-theme='dark'] .id-card-settings-modal-content {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.97) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 24px 60px rgba(2, 6, 23, 0.34);
}

html[data-theme='dark'] .id-card-settings-modal-header,
html[data-theme='dark'] .id-card-settings-modal-body,
html[data-theme='dark'] .id-card-settings-modal-footer {
    background: rgba(15, 23, 42, 0.97) !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-settings-field {
    background: rgba(15, 23, 42, 0.96);
    border-color: rgba(148, 163, 184, 0.22);
}

html[data-theme='dark'] .id-card-settings-help {
    background: rgba(15, 23, 42, 0.96);
    border-color: rgba(148, 163, 184, 0.22);
    color: #cbd5e1;
}

html[data-theme='dark'] .id-card-filter-shell .text-muted,
html[data-theme='dark'] .id-card-filter-shell .no-print p {
    color: #cbd5e1 !important;
}

@media print {
    @page {
        size: 297mm 210mm;
        margin: 1cm 0.8cm 0.8cm 0.8cm;
    }

    html,
    body {
        width: 297mm !important;
        height: 210mm !important;
        overflow: visible !important;
    }

    .card {
        border: none;
        box-shadow: none;
    }

    .id-card-pages {
        padding: 0;
    }
}

@include('pages.admit-seat-cards._styles')

@media print {
    @page {
        size: 297mm 210mm;
        margin: 1cm 0.8cm 0.8cm 0.8cm;
    }

    html,
    body {
        width: 297mm !important;
        height: 210mm !important;
        overflow: visible !important;
    }
}

/* Dark mode: settings page / modal */
html[data-theme='dark'] .id-card-settings-modal-content,
html[data-theme='dark'] .id-card-settings-modal-body,
html[data-theme='dark'] .id-card-settings-modal-footer,
html[data-theme='dark'] .id-card-dedicated-settings .id-card-settings-modal-footer {
    background: #0f172a;
    border-color: #1e293b;
    color: #e2e8f0;
}
html[data-theme='dark'] .id-card-settings-modal-header {
    background: #111c33;
    border-bottom-color: #1e293b;
    color: #e2e8f0;
}
html[data-theme='dark'] .id-card-settings-modal-header h5,
html[data-theme='dark'] .id-card-settings-modal-header h6,
html[data-theme='dark'] .id-card-settings-modal-body h6,
html[data-theme='dark'] .id-card-settings-modal-body .card-title,
html[data-theme='dark'] .id-card-settings-modal-content .csm-group__title {
    color: #f1f5f9;
}
html[data-theme='dark'] .id-card-settings-modal-content .text-muted,
html[data-theme='dark'] .id-card-settings-modal-content small {
    color: #94a3b8 !important;
}
html[data-theme='dark'] .id-card-settings-modal-content .text-dark {
    color: #e2e8f0 !important;
}
html[data-theme='dark'] .id-card-settings-tabs,
html[data-theme='dark'] .id-card-settings-modal-content .csm-section-tabs,
html[data-theme='dark'] .id-card-settings-modal-content .csm-bg-toggle {
    background: #111c33;
    border-color: #1e293b;
}
html[data-theme='dark'] .id-card-settings-tabs .nav-link,
html[data-theme='dark'] .id-card-settings-modal-content .csm-section-tabs .nav-link,
html[data-theme='dark'] .id-card-settings-modal-content .csm-bg-toggle .btn {
    color: #94a3b8;
}
html[data-theme='dark'] .id-card-settings-tabs .nav-link:hover,
html[data-theme='dark'] .id-card-settings-modal-content .csm-section-tabs .nav-link:hover,
html[data-theme='dark'] .id-card-settings-modal-content .csm-bg-toggle .btn:hover {
    color: #f1f5f9;
    background: rgba(51, 65, 85, 0.7);
}
html[data-theme='dark'] .id-card-settings-tabs .nav-link.active,
html[data-theme='dark'] .id-card-settings-modal-content .csm-section-tabs .nav-link.active,
html[data-theme='dark'] .id-card-settings-modal-content .csm-bg-toggle .btn.active {
    color: #fff;
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}
html[data-theme='dark'] .id-card-settings-panel > .tab-pane > .card,
html[data-theme='dark'] .id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card {
    background: #0f172a;
    border-color: #1e293b;
    box-shadow: none;
}
html[data-theme='dark'] .id-card-settings-panel > .tab-pane > .card > .card-header,
html[data-theme='dark'] .id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-header {
    background: #111c33 !important;
    border-bottom-color: #1e293b;
    color: #f1f5f9;
}
html[data-theme='dark'] .id-card-settings-field,
html[data-theme='dark'] .id-card-upload-box {
    background: #111c33;
    border-color: #1e293b;
    box-shadow: none;
}
html[data-theme='dark'] .id-card-settings-field:hover {
    border-color: #334155;
    box-shadow: none;
}
html[data-theme='dark'] .id-card-upload-preview {
    background: #0b1220;
    border-color: #1e293b;
}
html[data-theme='dark'] .id-card-settings-label,
html[data-theme='dark'] .id-card-settings-modal-content label {
    color: #cbd5e1;
}
html[data-theme='dark'] .id-card-settings-modal-content .form-control,
html[data-theme='dark'] .id-card-settings-modal-content .custom-select,
html[data-theme='dark'] .id-card-settings-modal-content .custom-file-label,
html[data-theme='dark'] .id-card-settings-control {
    background-color: #0b1220;
    border-color: #334155;
    color: #e2e8f0;
}
html[data-theme='dark'] .id-card-settings-modal-content .form-control:focus,
html[data-theme='dark'] .id-card-settings-modal-content .custom-select:focus,
html[data-theme='dark'] .id-card-settings-control:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
}
html[data-theme='dark'] .id-card-settings-modal-content .form-control:disabled,
html[data-theme='dark'] .id-card-settings-modal-content .form-control[readonly] {
    background-color: #111c33;
    color: #94a3b8;
}
html[data-theme='dark'] .id-card-settings-color-preview {
    border-color: #475569;
}
html[data-theme='dark'] .id-card-settings-note {
    background: #111c33;
    border-color: #1e293b;
    color: #cbd5e1;
    box-shadow: none;
}
html[data-theme='dark'] .id-card-settings-modal-content .csm-group {
    border-bottom-color: rgba(148, 163, 184, 0.25);
}
html[data-theme='dark'] .csm-preview-toolbar .btn,
html[data-theme='dark'] .id-card-settings-modal-content .btn-outline-secondary,
html[data-theme='dark'] .id-card-settings-modal-content .btn-light {
    background: #111c33;
    border-color: #334155;
    color: #e2e8f0 !important;
}
html[data-theme='dark'] .csm-preview-toolbar .btn:hover {
    background: #1e293b;
}
html[data-theme='dark'] .csm-preview-toolbar .btn-primary,
html[data-theme='dark'] .id-card-settings-modal-content .btn-primary {
    background: #2563eb;
    border-color: #2563eb;
    color: #fff !important;
}
html[data-theme='dark'] .csm-zoom-level {
    color: #e2e8f0;
}
html[data-theme='dark'] .id-card-settings-modal-content .select2-container--default .select2-selection--single {
    background: #0b1220;
    border-color: #334155;
}
html[data-theme='dark'] .id-card-settings-modal-content .select2-selection__rendered {
    color: #e2e8f0 !important;
}
html[data-theme='dark'] .id-card-settings-modal-content .close {
    color: #e2e8f0;
    text-shadow: none;
}

/* Photo & Logo pane */
.id-card-settings-unit {
    margin-left: 0.25rem;
    font-weight: 600;
    letter-spacing: 0;
    text-transform: none;
    opacity: 0.6;
}
.id-card-upload-box input[type="file"] {
    padding: 0.3rem;
    cursor: pointer;
}
.id-card-upload-box input[type="file"]::file-selector-button {
    margin-right: 0.7rem;
    padding: 0.25rem 0.8rem;
    border: 0;
    border-radius: 8px;
    background: #2563eb;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}
.id-card-upload-box input[type="file"]::file-selector-button:hover {
    background: #1d4ed8;
}
.id-card-upload-preview {
    display: flex;
    flex-direction: column;
}
.id-card-upload-preview__stage {
    flex: 1 1 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 120px;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    background:
        repeating-conic-gradient(#f1f5f9 0% 25%, #ffffff 0% 50%) 50% / 16px 16px;
}
html[data-theme='dark'] .id-card-upload-preview__stage {
    border-color: #334155;
    background:
        repeating-conic-gradient(#111c33 0% 25%, #0b1220 0% 50%) 50% / 16px 16px;
}
html[data-theme='dark'] .id-card-upload-box {
    border-color: #334155;
}

.id-card-settings-field--compact {
    padding: 0.6rem 0.75rem;
    gap: 0.35rem;
    border-radius: 12px;
}
.id-card-settings-field--compact .id-card-settings-control {
    min-height: 34px;
}
.id-card-upload-box--compact {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.7rem 0.85rem;
    border-radius: 12px;
}
.id-card-upload-box__main {
    flex: 1 1 auto;
    min-width: 0;
}
.id-card-upload-preview__stage--thumb {
    flex: 0 0 96px;
    width: 96px;
    height: 84px;
    min-height: 0;
    padding: 4px;
}

/* Compact density for every settings tab */
.id-card-settings-modal-body { padding: 0.6rem; }
.id-card-settings-tabs,
.id-card-settings-modal-content .csm-section-tabs { padding: 0.18rem; margin-bottom: 0.5rem !important; border-radius: 12px; }
.id-card-settings-tabs .nav-link,
.id-card-settings-modal-content .csm-section-tabs .nav-link { padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 9px; }
.id-card-settings-panel > .tab-pane > .card,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card { border-radius: 12px; margin-bottom: 0 !important; }
.id-card-settings-panel > .tab-pane > .card > .card-header,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-header { padding: 0.45rem 0.75rem; }
.id-card-settings-panel > .tab-pane > .card > .card-body,
.id-card-settings-modal-content .admit-seat-cards-settings-panel > .tab-pane > .card > .card-body { padding: 0.65rem 0.75rem; }
.id-card-settings-section-header .badge { width: 22px; height: 22px; font-size: 0.7rem; border-radius: 7px; }
.admit-seat-cards-settings-panel .card-body .row { margin-left: -0.35rem; margin-right: -0.35rem; }
.admit-seat-cards-settings-panel .card-body .row > [class*="col-"] { padding-left: 0.35rem; padding-right: 0.35rem; }
.admit-seat-cards-settings-panel .card-body .row > .mb-2,
.admit-seat-cards-settings-panel .card-body .row > .mb-3 { margin-bottom: 0.45rem !important; }
.id-card-settings-field { padding: 0.45rem 0.6rem; gap: 0.25rem; border-radius: 10px; }
.id-card-settings-field--compact { padding: 0.45rem 0.6rem; gap: 0.25rem; border-radius: 10px; }
.id-card-settings-label { font-size: 0.68rem; }
.id-card-settings-control,
.id-card-settings-field--compact .id-card-settings-control,
.id-card-settings-modal-body .form-control,
.id-card-settings-modal-body .custom-select { min-height: 30px; padding-top: 0.15rem; padding-bottom: 0.15rem; font-size: 0.8rem; border-radius: 8px; }
.admit-seat-cards-settings-panel .csm-group { margin-bottom: 0.55rem; padding-bottom: 0.5rem; }
.admit-seat-cards-settings-panel .csm-group__title { margin-bottom: 0.4rem; font-size: 0.72rem; }
.admit-seat-cards-settings-panel .card-body .mb-3 { margin-bottom: 0.5rem !important; }
.id-card-settings-note { margin-bottom: 0.5rem; padding: 0.35rem 0.6rem; }
.id-card-upload-box--compact { padding: 0.45rem 0.65rem; gap: 0.65rem; border-radius: 10px; }
.id-card-upload-preview__stage--thumb { flex-basis: 72px; width: 72px; height: 60px; }
.id-card-upload-preview__stage--thumb img { max-height: 52px !important; }
.id-card-settings-color-native { width: 40px !important; min-width: 40px !important; max-width: 40px !important; height: 30px !important; flex-basis: 40px !important; border-radius: 8px; }
.id-card-settings-color-preview { width: 26px; height: 26px; flex-basis: 26px; border-radius: 8px; }
.id-card-settings-modal-footer { padding: 0.5rem 0.75rem; }
</style>
@endsection
