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
                {{ number_format($layout['cardWidthMm'] ?? 54, 1) }}mm × {{ number_format($layout['cardHeightMm'] ?? 84, 1) }}mm
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

<div class="modal fade" id="idCardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="idCardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered id-card-settings-modal-dialog" role="document">
        <div class="modal-content id-card-settings-modal-content">
            <form method="POST" action="{{ route('students.id-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header id-card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="idCardSettingsModalLabel">Card Settings</h5>
                        <small class="text-muted d-block" id="idCardSettingsModalTypeLabel">{{ (old('card_type', $cardType ?? 'id_card') === 'library_card') ? 'Library Card Settings' : 'ID Card Settings' }}</small>
                        <small class="text-muted d-block">Save a single layout profile for search, print, and PDF output.</small>
                    </div>
                    <button type="button" class="close id-card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body id-card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ old('card_type', $cardType ?? 'id_card') }}">
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
                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Cards / Page</label>
                                <input type="number" name="cards_per_page" class="form-control form-control-sm id-card-filter-input" min="1" max="12" value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 4) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Cards / Row</label>
                                <input type="number" name="cards_per_row" class="form-control form-control-sm id-card-filter-input" min="1" max="10" value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Card Width</label>
                                <input type="number" name="card_width_value" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 5.4) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Card Height</label>
                                <input type="number" name="card_height_value" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 8.4) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Unit</label>
                                <select name="card_dimension_unit" class="form-control form-control-sm id-card-filter-input">
                                    <option value="cm" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                                    <option value="px" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'px' ? 'selected' : '' }}>Pixel (px)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Grid Gap</label>
                                <input type="number" name="grid_gap_value" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.5) }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Card Logo</label>
                                <input type="file" name="card_logo" id="cardLogoInput" class="form-control form-control-sm id-card-filter-input" accept="image/*">
                                <small class="text-muted d-block mt-2">Leave blank to use the school logo.</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Logo Preview</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <img
                                        id="cardLogoPreview"
                                        src="{{ $currentCardLogoUrl ?: '' }}"
                                        alt="Card logo preview"
                                        class="rounded"
                                        style="width:52px;height:52px;object-fit:contain;border:1px solid #dbe4ee;background:#fff;padding:4px;">
                                    <span class="text-muted small">Current logo used by this card type.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="id-card-settings-help">These settings are saved once and used by search and PDF output.</div>
                    <div class="row mt-2">
                        <div class="col-12 col-md-3 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Transparent</label>
                                <select name="card_is_transparent" id="cardIsTransparent" class="form-control form-control-sm id-card-filter-input">
                                    <option value="0" {{ !$selectedTransparent ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $selectedTransparent ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">School Name Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_school_name_text_color" id="cardSchoolNameColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                    <div id="cardSchoolNameColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">School Details Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_school_detail_text_color" id="cardSchoolDetailColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                    <div id="cardSchoolDetailColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Card Title Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_title_text_color" id="cardTitleColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                    <div id="cardTitleColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2 id-card-theme-color-fields">
                        <div class="col-12 col-md-4 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Color Type</label>
                                <select name="card_color_type" id="cardColorType" class="form-control form-control-sm id-card-filter-input">
                                    <option value="gradient" {{ old('card_color_type', $cardSettings?->card_color_type ?? 'gradient') === 'gradient' ? 'selected' : '' }}>Gradient</option>
                                    <option value="solid" {{ old('card_color_type', $cardSettings?->card_color_type ?? 'gradient') === 'solid' ? 'selected' : '' }}>Solid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 card-color-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Gradient Color 1</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_color_gradient_1" id="cardColorGradient1"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }}">
                                    <div id="cardColorGradient1Preview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 card-color-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Gradient Color 2</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_color_gradient_2" id="cardColorGradient2"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                    <div id="cardColorGradient2Preview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 mb-3 card-color-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Solid Color</label>
                                <div class="d-flex align-items-center" style="gap:10px">
                                    <input type="color" name="card_solid_color" id="cardSolidColor"
                                        class="form-control form-control-color p-1"
                                        style="width:48px;height:38px;cursor:pointer"
                                        value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                    <div id="cardSolidColorPreview" class="rounded"
                                        style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8 mb-3">
                            <div class="id-card-settings-field mb-0">
                                <label class="font-weight-bold id-card-filter-label">Theme Preview</label>
                                <div id="cardThemePreview" class="rounded" style="height:44px;border:1px solid #dbe4ee;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer id-card-settings-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    $cardSettingsPayload = $cardSettingsMap->mapWithKeys(function ($setting) use ($resolveLogoUrl, $schoolLogoUrl) {
        return [
            (string) $setting->card_type => [
                'cards_per_page' => $setting->cards_per_page,
                'cards_per_row' => $setting->cards_per_row,
                'card_width_value' => $setting->card_width_value,
                'card_height_value' => $setting->card_height_value,
                'grid_gap_value' => $setting->grid_gap_value,
                'card_dimension_unit' => $setting->card_dimension_unit,
                'card_is_transparent' => $setting->card_is_transparent,
                'card_color_type' => $setting->card_color_type,
                'card_color_gradient_1' => $setting->card_color_gradient_1,
                'card_color_gradient_2' => $setting->card_color_gradient_2,
                'card_solid_color' => $setting->card_solid_color,
                'card_school_name_text_color' => $setting->card_school_name_text_color,
                'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                'card_title_text_color' => $setting->card_title_text_color,
                'card_logo_url' => $resolveLogoUrl($setting->card_logo ?? null) ?: $schoolLogoUrl,
            ],
        ];
    })->toArray();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const cardTypeSelect = document.querySelector('#filterForm select[name="card_type"]');
    const settingsModal = document.getElementById('idCardSettingsModal');
    const settingsForm = settingsModal?.querySelector('form');
    const settingsTypeLabel = document.getElementById('idCardSettingsModalTypeLabel');
    const cardThemePreview = document.getElementById('cardThemePreview');
    const cardColorType = document.getElementById('cardColorType');
    const cardIsTransparent = document.getElementById('cardIsTransparent');
    const cardColorGradient1 = document.getElementById('cardColorGradient1');
    const cardColorGradient2 = document.getElementById('cardColorGradient2');
    const cardSolidColor = document.getElementById('cardSolidColor');
    const cardSchoolNameColor = document.getElementById('cardSchoolNameColor');
    const cardSchoolDetailColor = document.getElementById('cardSchoolDetailColor');
    const cardTitleColor = document.getElementById('cardTitleColor');
    const cardLogoInput = document.getElementById('cardLogoInput');
    const cardLogoPreview = document.getElementById('cardLogoPreview');
    const cardColorGradient1Preview = document.getElementById('cardColorGradient1Preview');
    const cardColorGradient2Preview = document.getElementById('cardColorGradient2Preview');
    const cardSolidColorPreview = document.getElementById('cardSolidColorPreview');
    const cardSchoolNameColorPreview = document.getElementById('cardSchoolNameColorPreview');
    const cardSchoolDetailColorPreview = document.getElementById('cardSchoolDetailColorPreview');
    const cardTitleColorPreview = document.getElementById('cardTitleColorPreview');
    const selectedSection = @json(request('section_id'));
    const hasValidationErrors = @json($errors->any());
    const cardSettingsMap = @json($cardSettingsPayload);
    const defaultCardType = @json($cardType ?? 'id_card');
    const defaultThemeSettings = {
        card_is_transparent: false,
        card_color_type: 'gradient',
        card_color_gradient_1: '#1e3a5f',
        card_color_gradient_2: '#2563eb',
        card_solid_color: '#1e3a5f',
        card_school_name_text_color: '#ffffff',
        card_school_detail_text_color: '#e5e7eb',
        card_title_text_color: '#ffffff',
    };
    const fallbackSchoolLogo = @json($schoolLogoUrl);

    function settingKeyFromCardType(cardType) {
        return cardType === 'library_card' ? '4' : '3';
    }

    function settingLabelFromCardType(cardType) {
        return cardType === 'library_card' ? 'Library Card Settings' : 'ID Card Settings';
    }

    function applyCardSettings(cardType) {
        if (!settingsForm) return;

        const key = settingKeyFromCardType(cardType || defaultCardType);
        const settings = cardSettingsMap[key] || cardSettingsMap['3'] || {};

        ['cards_per_page', 'cards_per_row', 'card_width_value', 'card_height_value', 'grid_gap_value', 'card_dimension_unit', 'card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_title_text_color'].forEach((field) => {
            const input = settingsForm.elements.namedItem(field);
            const value = field === 'card_is_transparent'
                ? ((settings[field] ?? defaultThemeSettings[field]) ? '1' : '0')
                : (settings[field] ?? defaultThemeSettings[field]);
            if (input && value !== undefined && value !== null) {
                input.value = value;
            }
        });

        const hiddenType = settingsForm.elements.namedItem('card_type');
        if (hiddenType) hiddenType.value = cardType || defaultCardType;

        if (settingsTypeLabel) {
            settingsTypeLabel.textContent = settingLabelFromCardType(cardType || defaultCardType);
        }

        refreshCardColorControls();
    }

    function refreshCardColorControls() {
        if (!settingsForm) return;

        const isTransparent = cardIsTransparent?.value === '1' || cardIsTransparent?.value === 'true' || cardIsTransparent?.checked === true;
        const colorType = cardColorType?.value || 'gradient';
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
            if (cardTitleColor && cardTitleColor.value === '#ffffff') cardTitleColor.value = '#111827';
        }

        if (isTransparent) {
            $('.id-card-theme-color-fields').hide();
        } else {
            $('.id-card-theme-color-fields').show();
            if (colorType === 'solid') {
                $('.card-color-gradient-field').hide();
                $('.card-color-solid-field').show();
            } else {
                $('.card-color-gradient-field').show();
                $('.card-color-solid-field').hide();
            }
        }

        if (cardSchoolNameColorPreview) {
            cardSchoolNameColorPreview.style.background = cardSchoolNameColor?.value || '#ffffff';
        }

        if (cardSchoolDetailColorPreview) {
            cardSchoolDetailColorPreview.style.background = cardSchoolDetailColor?.value || '#e5e7eb';
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
            cardLogoPreview.src = settings.card_logo_url || fallbackSchoolLogo || '';
            cardLogoPreview.classList.toggle('d-none', !(settings.card_logo_url || fallbackSchoolLogo));
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

    if (settingsForm) {
        if (cardIsTransparent) {
            cardIsTransparent.addEventListener('change', refreshCardColorControls);
        }
        if (cardColorType) {
            cardColorType.addEventListener('change', refreshCardColorControls);
        }
        settingsForm.addEventListener('input', function (event) {
            if (['card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_title_text_color'].includes(event.target.name)) {
                refreshCardColorControls();
            }
        });
        settingsForm.addEventListener('change', function (event) {
            if (['card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_title_text_color'].includes(event.target.name)) {
                refreshCardColorControls();
            }
        });
    }

    if (cardLogoInput && cardLogoPreview) {
        cardLogoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                cardLogoPreview.src = fallbackSchoolLogo || '';
                cardLogoPreview.classList.toggle('d-none', !fallbackSchoolLogo);
                return;
            }

            const reader = new FileReader();
            reader.onload = function (event) {
                cardLogoPreview.src = event.target.result;
                cardLogoPreview.classList.remove('d-none');
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
    });

    $('#idCardSettingsModal').on('shown.bs.modal', function () {
        refreshCardColorControls();
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
    max-width: 860px;
}

.id-card-settings-modal-content {
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.16);
}

.id-card-settings-modal-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eaeef4;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    align-items: flex-start;
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
    padding: 1.25rem;
    background: #fff;
}

.id-card-settings-field {
    padding: 0.95rem;
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

.id-card-settings-modal-footer {
    padding: 1rem 1.25rem 1.2rem;
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
</style>
@endsection
