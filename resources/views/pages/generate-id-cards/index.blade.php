@extends('layouts.master')

@section('contents')
<div class="container-fluid">

    <div class="card no-print id-card-filter-shell">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Generate ID Cards</h3>
        </div>
        <div class="card-body pb-2 id-card-filter-body">
            <div class="id-card-filter-panel">
            <form method="GET" action="{{ route('students.id-cards') }}" id="filterForm">
                <div class="row id-card-filter-grid">
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="font-weight-bold id-card-filter-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Class</label>
                            <select name="class_id" class="form-control form-control-sm" id="classSelect">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Section</label>
                            <select name="section_id" class="form-control form-control-sm" id="sectionSelect">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Group</label>
                            <select name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Card Type</label>
                            <select name="card_type" class="form-control form-control-sm">
                                <option value="id_card" {{ ($cardType ?? 'id_card') === 'id_card' ? 'selected' : '' }}>ID Card</option>
                                <option value="library_card" {{ ($cardType ?? 'id_card') === 'library_card' ? 'selected' : '' }}>Library Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Student ID</label>
                            <input
                                type="text"
                                name="student_cid"
                                class="form-control form-control-sm id-card-filter-input"
                                value="{{ request('student_cid') }}"
                                placeholder="Enter Student ID"
                                autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="id-card-filter-actions-row">
                    <div class="d-flex flex-wrap justify-content-end id-card-filter-actions-inner">
                        <button type="button" class="btn btn-outline-primary btn-sm id-card-filter-btn" data-toggle="modal" data-target="#idCardSettingsModal" title="Card settings">
                            <i class="fas fa-sliders-h"></i>
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm id-card-filter-btn" title="Generate">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="{{ route('students.id-cards') }}" class="btn btn-secondary btn-sm id-card-filter-btn" title="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @if($students->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm id-card-filter-btn" onclick="window.print()" title="Print">
                                <i class="fas fa-print"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    @if(!request('session_id') && !request('student_cid'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year or enter a Student ID to generate a card.</p>
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-primary px-3 py-2" style="font-size:13px">{{ $students->count() }} Students</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ ($cardType ?? 'id_card') === 'library_card' ? 'Library card' : 'Front + Back per student' }}
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ number_format($layout['cardWidthCm'] ?? 5.4, 1) }}cm × {{ number_format($layout['cardHeightCm'] ?? 8.4, 1) }}cm
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">Landscape print and PDF</span>
        </div>

        @include('pages.generate-id-cards._cards', [
            'students' => $students,
            'setting' => $setting,
            'cardSettings' => $cardSettings ?? null,
            'renderForPdf' => false,
            'cardType' => $cardType ?? 'id_card',
            'layout' => $layout ?? [],
        ])
    @endif
</div>

@include('pages.generate-id-cards._settings-modal')

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
    const hasValidationErrors = @json($errors->any());
    const cardSettingsMap = @json($cardSettingsPayload);
    const defaultCardType = @json($cardType ?? 'id_card');
    let activeCardSettings = {};
    let previewLogoUrl = null;
    let previewPrincipalSignatureUrl = null;
    let modalScrollY = 0;
    const initialCardLogoPreviewSrc = cardLogoPreview?.getAttribute('src') || '';
    const initialCardPrincipalSignaturePreviewSrc = cardPrincipalSignaturePreview?.getAttribute('src') || '';
    const defaultThemeSettings = {
        card_is_transparent: false,
        card_color_type: 'gradient',
        card_front_alignment: 'center',
        card_back_alignment: 'center',
        card_front_padding_value: 0.8,
        card_back_padding_value: 0.8,
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
        card_color_gradient_1: '#1e3a5f',
        card_color_gradient_2: '#2563eb',
        card_solid_color: '#1e3a5f',
        card_school_name_text_color: '#ffffff',
        card_school_detail_text_color: '#e5e7eb',
        card_slogan_text_color: '#e5e7eb',
        card_back_notice_text_color: '#94a3b8',
        card_footer_text_color: '#e5e7eb',
        card_title_text_color: '#ffffff',
        card_show_school_detail_front: true,
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

    function settingKeyFromCardType(cardType) {
        return cardType === 'library_card' ? '4' : '3';
    }

    function settingLabelFromCardType(cardType) {
        return cardType === 'library_card' ? 'Library Card Settings' : 'ID Card Settings';
    }

    function previewTitleFromCardType(cardType) {
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
    }

    function setDirtyState(isDirty) {
        if (!dirtyBadge) return;
        dirtyBadge.classList.toggle('d-none', !isDirty);
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

    function applyCardSettings(cardType) {
        if (!settingsForm) return;

        const key = settingKeyFromCardType(cardType || defaultCardType);
        const settings = cardSettingsMap[key] || cardSettingsMap['3'] || {};
        activeCardSettings = settings;
        previewLogoUrl = null;
        previewPrincipalSignatureUrl = null;

        ['cards_per_page', 'cards_per_row', 'card_width_value', 'card_height_value', 'grid_gap_value', 'card_dimension_unit', 'card_front_alignment', 'card_back_alignment', 'card_front_padding_value', 'card_back_padding_value', 'card_photo_width_value', 'card_photo_height_value', 'card_photo_fit', 'card_logo_size_value', 'card_logo_fit', 'card_school_name_font_size', 'card_school_detail_font_size', 'card_slogan_font_size', 'card_title_font_size', 'card_name_font_size', 'card_student_detail_alignment', 'card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_slogan_text_color', 'card_name_text_color', 'card_back_notice_text_color', 'card_footer_text_color', 'card_title_text_color', 'card_show_school_detail_front', 'card_show_school_detail_back', 'card_show_slogan_front', 'card_show_slogan_back', 'card_show_title_front', 'card_show_title_back', 'card_show_logo_front', 'card_show_logo_back', 'card_show_photo_front', 'card_show_footer_front', 'card_show_footer_back', 'card_show_back_student_details', 'card_show_back_school_contact', 'card_show_back_qr', 'card_show_back_signature', 'card_show_back_notice'].forEach((field) => {
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
                }
            }
        });

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

        syncCardTypeSwitcher(cardType || defaultCardType);
        setDirtyState(false);

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
            const cardHeightValue = parseFloat(cardHeight?.value || '8.4') || 8.4;
            const widthValue = parseFloat(cardPhotoWidth?.value || '1.8') || 1.8;
            const heightValue = parseFloat(cardPhotoHeight?.value || '2.7') || 2.7;
            const photoFitValue = cardPhotoFit?.value || 'cover';
            const logoSizeValue = parseFloat(cardLogoSize?.value || '0.8') || 0.8;
            const logoFitValue = cardLogoFit?.value || 'contain';
            const frontPaddingValue = parseFloat(cardFrontPadding?.value || '0.8') || 0.8;
            const backPaddingValue = parseFloat(cardBackPadding?.value || '0.8') || 0.8;
            const gapValue = parseFloat(cardGridGap?.value || '0.5') || 0.5;
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
            idCardLivePreview.style.setProperty('--id-card-back-padding', `${backPaddingValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-width', `${widthValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-height', `${heightValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-photo-fit', photoFitValue);
            idCardLivePreview.style.setProperty('--id-card-logo-size', `${logoSizeValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-logo-fit', logoFitValue);
            idCardLivePreview.style.setProperty('--id-card-school-name-font-size', `${schoolNameFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-school-detail-font-size', `${schoolDetailFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-slogan-font-size', `${sloganFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-title-font-size', `${titleFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-name-font-size', `${nameFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-title-font-size', `${titleFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-back-value-font-size', `${Math.max(3.6, schoolDetailFontSizeValue * 0.9)}pt`);

            setPreviewElementVisible('.id-card__header--front .id-card__logo', !!(settingsForm.elements.namedItem('card_show_logo_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back .id-card__logo', !!(settingsForm.elements.namedItem('card_show_logo_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--front .id-card__slogan', !!(settingsForm.elements.namedItem('card_show_slogan_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back .id-card__slogan', !!(settingsForm.elements.namedItem('card_show_slogan_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--front .id-card__label-badge', !!(settingsForm.elements.namedItem('card_show_title_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__header--back .id-card__label-badge', !!(settingsForm.elements.namedItem('card_show_title_back')?.checked ?? true));
            setPreviewElementVisible('.id-card__photo', !!(settingsForm.elements.namedItem('card_show_photo_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__footer--front', !!(settingsForm.elements.namedItem('card_show_footer_front')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-section--student-details', !!(settingsForm.elements.namedItem('card_show_back_student_details')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-section--school-contact', !!(settingsForm.elements.namedItem('card_show_back_school_contact')?.checked ?? true));
            setPreviewElementVisible('.id-card__qr', !!(settingsForm.elements.namedItem('card_show_back_qr')?.checked ?? true));
            setPreviewElementVisible('.id-card__back-notice', !!(settingsForm.elements.namedItem('card_show_back_notice')?.checked ?? true));
            setPreviewElementVisible('.id-card__signature', !!(settingsForm.elements.namedItem('card_show_back_signature')?.checked ?? true));
            setPreviewElementVisible('.id-card__footer--back', !!(settingsForm.elements.namedItem('card_show_footer_back')?.checked ?? true));
        }

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
            replaceSectionOptions('<option value="">All Sections</option>');
            return;
        }

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then((response) => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then((data) => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach((section) => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                replaceSectionOptions(html);
            })
            .catch(() => {
                replaceSectionOptions('<option value="">All Sections</option>');
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

    $(document).on('input change', '#cardPhotoWidth, #cardPhotoHeight, #cardLogoSize, select[name="card_dimension_unit"]', refreshCardColorControls);

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
        setPreviewSide('idCard', 'front');
        refreshCardColorControls();
    });

    $('#idCardSettingsModal').on('hidden.bs.modal', function () {
        $(this).find('label').tooltip('dispose');
        unlockPageScroll();
    });

    @if($errors->any())
        $('#idCardSettingsModal').modal('show');
    @endif
});
</script>

<style>
@include('pages.generate-id-cards._styles', ['setting' => $setting])

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
    .card {
        border: none;
        box-shadow: none;
    }

    .id-card-pages {
        padding: 0;
    }
}

@include('pages.admit-seat-cards._styles')
</style>
@endsection
