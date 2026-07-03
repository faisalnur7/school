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

<div class="modal fade" id="idCardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="idCardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered id-card-settings-modal-dialog" role="document">
        <div class="modal-content id-card-settings-modal-content">
            <form method="POST" action="{{ route('students.id-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header id-card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="idCardSettingsModalLabel">Card Settings</h5>
                        <small class="text-muted d-block" id="idCardSettingsModalTypeLabel">{{ (old('card_type', $cardType ?? 'id_card') === 'library_card') ? 'Library Card Settings' : 'ID Card Settings' }}</small>
                        <small class="text-muted d-block">Save a single layout profile for search, print, and PDF output.</small>
                    </div>
                    <div class="btn-group btn-group-sm id-card-settings-type-switcher" role="group" aria-label="Card type selector">
                        <button type="button" class="btn btn-outline-primary js-card-type-switch {{ (old('card_type', $cardType ?? 'id_card') === 'id_card') ? 'active' : '' }}" data-card-type="id_card" data-card-label="ID Card Settings">ID Card</button>
                        <button type="button" class="btn btn-outline-primary js-card-type-switch {{ (old('card_type', $cardType ?? 'id_card') === 'library_card') ? 'active' : '' }}" data-card-type="library_card" data-card-label="Library Card Settings">Library Card</button>
                    </div>
                    <span id="idCardSettingsDirtyBadge" class="badge badge-warning align-self-center d-none">Unsaved changes</span>
                    <button type="button" class="close id-card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body id-card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ old('card_type', $cardType ?? 'id_card') }}">
                    @php
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
                    <div class="row align-items-stretch">
                        <div class="col-12 col-lg-5 mb-3">
                            @include('pages.card-settings._live-preview', [
                                'prefix' => 'idCard',
                                'previewType' => 'id',
                                'previewLabel' => ($cardType ?? 'id_card') === 'library_card' ? 'Library Card Preview' : 'ID Card Preview',
                                'schoolName' => $setting?->name ?? 'School Name',
                                'schoolDetailLine' => $setting?->address ?? '',
                                'schoolContactLine1' => $setting?->contact_number_1 ?? null,
                                'schoolContactLine2' => $setting?->contact_number_2 ?? null,
                                'schoolWhatsapp' => $setting?->whatsapp_number ?? null,
                                'schoolEmail' => $setting?->email ?? null,
                                'schoolWebsite' => $setting?->website ?? null,
                                'schoolQrUrl' => (!empty($setting?->whatsapp_qr) && file_exists(public_path($setting->whatsapp_qr))) ? asset($setting->whatsapp_qr) : null,
                                'slogan' => $setting?->slogan ?? 'Stay Green, Be Bright',
                                'frontTitle' => 'STUDENT ID',
                                'backTitle' => 'BACK',
                                'backNotice' => 'If found, please return to the school.',
                                'footerLine' => $setting?->whatsapp_number ?: ($setting?->contact_number_1 ?? '+880 1886-780641'),
                                'logoUrl' => $currentCardLogoUrl,
                                'showSchoolDetailFront' => $cardSettings?->card_show_school_detail_front ?? true,
                                'showSchoolDetailBack' => $cardSettings?->card_show_school_detail_back ?? true,
                                'showSloganFront' => $cardSettings?->card_show_slogan_front ?? true,
                                'showSloganBack' => $cardSettings?->card_show_slogan_back ?? true,
                                'showTitleFront' => $cardSettings?->card_show_title_front ?? true,
                                'showTitleBack' => $cardSettings?->card_show_title_back ?? true,
                                'showLogoFront' => $cardSettings?->card_show_logo_front ?? true,
                                'showLogoBack' => $cardSettings?->card_show_logo_back ?? true,
                                'showPhotoFront' => $cardSettings?->card_show_photo_front ?? true,
                                'showBackNotice' => $cardSettings?->card_show_back_notice ?? true,
                                'showFooterFront' => $cardSettings?->card_show_footer_front ?? true,
                                'showFooterBack' => $cardSettings?->card_show_footer_back ?? true,
                                'previewCardWidthValue' => $cardSettings?->card_width_value ?? 5.4,
                                'previewCardHeightValue' => $cardSettings?->card_height_value ?? 8.4,
                                'previewCardDimensionUnit' => $cardSettings?->card_dimension_unit ?? 'cm',
                                'focusTargets' => [
                                    'logo' => 'cardLogoInput',
                                    'school_name' => 'cardSchoolNameColor',
                                    'school_detail' => 'cardSchoolDetailColor',
                                    'slogan' => 'cardSloganColor',
                                    'title' => 'cardTitleColor',
                                    'back_notice' => 'cardBackNoticeColor',
                                    'footer' => 'cardFooterColor',
                                ],
                            ])
                        </div>
                        <div class="col-12 col-lg-7">
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
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Front Alignment</label>
                                        <select name="card_front_alignment" id="cardFrontAlignment" class="form-control form-control-sm id-card-filter-input">
                                            <option value="left" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                            <option value="center" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                            <option value="right" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Back Alignment</label>
                                        <select name="card_back_alignment" id="cardBackAlignment" class="form-control form-control-sm id-card-filter-input">
                                            <option value="left" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                            <option value="center" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                            <option value="right" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Front Padding</label>
                                        <input type="number" name="card_front_padding_value" id="cardFrontPadding" class="form-control form-control-sm id-card-filter-input" min="0" step="0.1" value="{{ old('card_front_padding_value', $cardSettings?->card_front_padding_value ?? 0.8) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Back Padding</label>
                                        <input type="number" name="card_back_padding_value" id="cardBackPadding" class="form-control form-control-sm id-card-filter-input" min="0" step="0.1" value="{{ old('card_back_padding_value', $cardSettings?->card_back_padding_value ?? 0.8) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Photo Width</label>
                                        <input type="number" name="card_photo_width_value" id="cardPhotoWidth" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('card_photo_width_value', $cardSettings?->card_photo_width_value ?? 1.8) }}">
                                        <small class="text-muted d-block mt-1">Uses the selected unit above.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Photo Height</label>
                                        <input type="number" name="card_photo_height_value" id="cardPhotoHeight" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                        <small class="text-muted d-block mt-1">Uses the selected unit above.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Logo Size</label>
                                        <input type="number" name="card_logo_size_value" id="cardLogoSize" class="form-control form-control-sm id-card-filter-input" min="0.1" step="0.1" value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                        <small class="text-muted d-block mt-1">Uses the selected unit above.</small>
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
                                        <label class="font-weight-bold id-card-filter-label">Slogan Color</label>
                                        <div class="d-flex align-items-center" style="gap:10px">
                                            <input type="color" name="card_slogan_text_color" id="cardSloganColor"
                                                class="form-control form-control-color p-1"
                                                style="width:48px;height:38px;cursor:pointer"
                                                value="{{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }}">
                                            <div id="cardSloganColorPreview" class="rounded"
                                                style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Back Notice Color</label>
                                        <div class="d-flex align-items-center" style="gap:10px">
                                            <input type="color" name="card_back_notice_text_color" id="cardBackNoticeColor"
                                                class="form-control form-control-color p-1"
                                                style="width:48px;height:38px;cursor:pointer"
                                                value="{{ old('card_back_notice_text_color', $cardSettings?->card_back_notice_text_color ?? '#94a3b8') }}">
                                            <div id="cardBackNoticeColorPreview" class="rounded"
                                                style="width:32px;height:32px;border:1px solid #ddd;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Footer Color</label>
                                        <div class="d-flex align-items-center" style="gap:10px">
                                            <input type="color" name="card_footer_text_color" id="cardFooterColor"
                                                class="form-control form-control-color p-1"
                                                style="width:48px;height:38px;cursor:pointer"
                                                value="{{ old('card_footer_text_color', $cardSettings?->card_footer_text_color ?? '#e5e7eb') }}">
                                            <div id="cardFooterColorPreview" class="rounded"
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
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">School Font Size</label>
                                        <input type="number" name="card_school_name_font_size" id="cardSchoolNameFontSize" class="form-control form-control-sm id-card-filter-input" min="1" step="0.1" value="{{ old('card_school_name_font_size', $cardSettings?->card_school_name_font_size ?? 7.2) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Detail Font Size</label>
                                        <input type="number" name="card_school_detail_font_size" id="cardSchoolDetailFontSize" class="form-control form-control-sm id-card-filter-input" min="1" step="0.1" value="{{ old('card_school_detail_font_size', $cardSettings?->card_school_detail_font_size ?? 5.4) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Slogan Font Size</label>
                                        <input type="number" name="card_slogan_font_size" id="cardSloganFontSize" class="form-control form-control-sm id-card-filter-input" min="1" step="0.1" value="{{ old('card_slogan_font_size', $cardSettings?->card_slogan_font_size ?? 4.8) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Title Font Size</label>
                                        <input type="number" name="card_title_font_size" id="cardTitleFontSize" class="form-control form-control-sm id-card-filter-input" min="1" step="0.1" value="{{ old('card_title_font_size', $cardSettings?->card_title_font_size ?? 4.7) }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Name Font Size</label>
                                        <input type="number" name="card_name_font_size" id="cardNameFontSize" class="form-control form-control-sm id-card-filter-input" min="1" step="0.1" value="{{ old('card_name_font_size', $cardSettings?->card_name_font_size ?? 7.2) }}">
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="id-card-settings-field mb-0">
                                        <label class="font-weight-bold id-card-filter-label">Visibility</label>
                                        <div class="d-flex flex-wrap" style="gap:12px">
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_photo_front" id="cardShowPhotoFront" {{ old('card_show_photo_front', $cardSettings?->card_show_photo_front ?? true) ? 'checked' : '' }}> Photo
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_logo_front" id="cardShowLogoFront" {{ old('card_show_logo_front', $cardSettings?->card_show_logo_front ?? true) ? 'checked' : '' }}> Front Logo
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_logo_back" id="cardShowLogoBack" {{ old('card_show_logo_back', $cardSettings?->card_show_logo_back ?? true) ? 'checked' : '' }}> Back Logo
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_school_detail_front" id="cardShowSchoolDetailFront" {{ old('card_show_school_detail_front', $cardSettings?->card_show_school_detail_front ?? true) ? 'checked' : '' }}> Front Details
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_school_detail_back" id="cardShowSchoolDetailBack" {{ old('card_show_school_detail_back', $cardSettings?->card_show_school_detail_back ?? true) ? 'checked' : '' }}> Back Details
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_slogan_front" id="cardShowSloganFront" {{ old('card_show_slogan_front', $cardSettings?->card_show_slogan_front ?? true) ? 'checked' : '' }}> Front Slogan
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_slogan_back" id="cardShowSloganBack" {{ old('card_show_slogan_back', $cardSettings?->card_show_slogan_back ?? true) ? 'checked' : '' }}> Back Slogan
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_title_front" id="cardShowTitleFront" {{ old('card_show_title_front', $cardSettings?->card_show_title_front ?? true) ? 'checked' : '' }}> Front Title
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_title_back" id="cardShowTitleBack" {{ old('card_show_title_back', $cardSettings?->card_show_title_back ?? true) ? 'checked' : '' }}> Back Title
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_footer_front" id="cardShowFooterFront" {{ old('card_show_footer_front', $cardSettings?->card_show_footer_front ?? true) ? 'checked' : '' }}> Front Footer
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_footer_back" id="cardShowFooterBack" {{ old('card_show_footer_back', $cardSettings?->card_show_footer_back ?? true) ? 'checked' : '' }}> Back Footer
                                            </label>
                                            <label class="d-inline-flex align-items-center" style="gap:6px">
                                                <input type="checkbox" name="card_show_back_notice" id="cardShowBackNotice" {{ old('card_show_back_notice', $cardSettings?->card_show_back_notice ?? true) ? 'checked' : '' }}> Back Notice
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="id-card-background-settings mt-2">
                                <label class="font-weight-bold id-card-filter-label mb-2">Background Settings</label>
                                <div class="row">
                                    <div class="col-12 col-md-4 mb-3">
                                        <div class="id-card-settings-field mb-0">
                                            <label class="font-weight-bold id-card-filter-label">Background Type</label>
                                            @php
                                                $cardColorTypeValue = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
                                            @endphp
                                            <div class="d-flex flex-column" style="gap:8px">
                                                <label class="d-inline-flex align-items-center" style="gap:8px;margin-bottom:0;">
                                                    <input type="radio" name="card_color_type" value="gradient" {{ $cardColorTypeValue === 'gradient' ? 'checked' : '' }}>
                                                    <span>Gradient</span>
                                                </label>
                                                <label class="d-inline-flex align-items-center" style="gap:8px;margin-bottom:0;">
                                                    <input type="radio" name="card_color_type" value="solid" {{ $cardColorTypeValue === 'solid' ? 'checked' : '' }}>
                                                    <span>Solid</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4 mb-3 card-color-gradient-field">
                                        <div class="id-card-settings-field mb-0">
                                            <label class="font-weight-bold id-card-filter-label">Background Gradient 1</label>
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
                                    <div class="col-12 col-md-4 mb-3 card-color-gradient-field">
                                        <div class="id-card-settings-field mb-0">
                                            <label class="font-weight-bold id-card-filter-label">Background Gradient 2</label>
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
                                    <div class="col-12 col-md-4 mb-3 card-color-solid-field">
                                        <div class="id-card-settings-field mb-0">
                                            <label class="font-weight-bold id-card-filter-label">Background Solid Color</label>
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
                                            <label class="font-weight-bold id-card-filter-label">Background Preview</label>
                                            <div id="cardThemePreview" class="rounded" style="height:44px;border:1px solid #dbe4ee;"></div>
                                        </div>
                                    </div>
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
                'card_title_font_size' => $setting->card_title_font_size,
                'card_name_font_size' => $setting->card_name_font_size,
                'card_is_transparent' => $setting->card_is_transparent,
                'card_color_type' => $setting->card_color_type,
                'card_color_gradient_1' => $setting->card_color_gradient_1,
                'card_color_gradient_2' => $setting->card_color_gradient_2,
                'card_solid_color' => $setting->card_solid_color,
                'card_school_name_text_color' => $setting->card_school_name_text_color,
                'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                'card_slogan_text_color' => $setting->card_slogan_text_color,
                'card_back_notice_text_color' => $setting->card_back_notice_text_color,
                'card_footer_text_color' => $setting->card_footer_text_color,
                'card_title_text_color' => $setting->card_title_text_color,
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
                'card_show_back_notice' => $setting->card_show_back_notice,
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
    const dirtyBadge = document.getElementById('idCardSettingsDirtyBadge');
    const cardThemePreview = document.getElementById('cardThemePreview');
    const cardIsTransparent = document.getElementById('cardIsTransparent');
    const cardColorGradient1 = document.getElementById('cardColorGradient1');
    const cardColorGradient2 = document.getElementById('cardColorGradient2');
    const cardSolidColor = document.getElementById('cardSolidColor');
    const cardSchoolNameColor = document.getElementById('cardSchoolNameColor');
    const cardSchoolDetailColor = document.getElementById('cardSchoolDetailColor');
    const cardSloganColor = document.getElementById('cardSloganColor');
    const cardBackNoticeColor = document.getElementById('cardBackNoticeColor');
    const cardFooterColor = document.getElementById('cardFooterColor');
    const cardTitleColor = document.getElementById('cardTitleColor');
    const cardLogoInput = document.getElementById('cardLogoInput');
    const cardLogoPreview = document.getElementById('cardLogoPreview');
    const cardColorGradient1Preview = document.getElementById('cardColorGradient1Preview');
    const cardColorGradient2Preview = document.getElementById('cardColorGradient2Preview');
    const cardSolidColorPreview = document.getElementById('cardSolidColorPreview');
    const cardSchoolNameColorPreview = document.getElementById('cardSchoolNameColorPreview');
    const cardSchoolDetailColorPreview = document.getElementById('cardSchoolDetailColorPreview');
    const cardSloganColorPreview = document.getElementById('cardSloganColorPreview');
    const cardBackNoticeColorPreview = document.getElementById('cardBackNoticeColorPreview');
    const cardFooterColorPreview = document.getElementById('cardFooterColorPreview');
    const cardTitleColorPreview = document.getElementById('cardTitleColorPreview');
    const idCardLivePreview = document.getElementById('idCardLivePreview');
    const idCardLivePreviewFront = document.getElementById('idCardLivePreviewFront');
    const idCardLivePreviewBack = document.getElementById('idCardLivePreviewBack');
    const idCardLivePreviewLogoFront = document.getElementById('idCardLivePreviewLogoFront');
    const idCardLivePreviewLogoBack = document.getElementById('idCardLivePreviewLogoBack');
    const idCardLivePreviewTitleFront = document.getElementById('idCardLivePreviewTitleFront');
    const idCardLivePreviewTitleBack = document.getElementById('idCardLivePreviewTitleBack');
    const cardPhotoWidth = document.getElementById('cardPhotoWidth');
    const cardPhotoHeight = document.getElementById('cardPhotoHeight');
    const cardLogoSize = document.getElementById('cardLogoSize');
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
        card_title_font_size: 4.7,
        card_name_font_size: 7.2,
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

    function getSelectedCardColorType() {
        return settingsForm?.querySelector('input[name="card_color_type"]:checked')?.value || 'gradient';
    }

    function applyCardSettings(cardType) {
        if (!settingsForm) return;

        const key = settingKeyFromCardType(cardType || defaultCardType);
        const settings = cardSettingsMap[key] || cardSettingsMap['3'] || {};
        activeCardSettings = settings;
        previewLogoUrl = null;

        ['cards_per_page', 'cards_per_row', 'card_width_value', 'card_height_value', 'grid_gap_value', 'card_dimension_unit', 'card_front_alignment', 'card_back_alignment', 'card_front_padding_value', 'card_back_padding_value', 'card_photo_width_value', 'card_photo_height_value', 'card_logo_size_value', 'card_school_name_font_size', 'card_school_detail_font_size', 'card_slogan_font_size', 'card_title_font_size', 'card_name_font_size', 'card_is_transparent', 'card_color_type', 'card_color_gradient_1', 'card_color_gradient_2', 'card_solid_color', 'card_school_name_text_color', 'card_school_detail_text_color', 'card_slogan_text_color', 'card_back_notice_text_color', 'card_footer_text_color', 'card_title_text_color', 'card_show_school_detail_front', 'card_show_school_detail_back', 'card_show_slogan_front', 'card_show_slogan_back', 'card_show_title_front', 'card_show_title_back', 'card_show_logo_front', 'card_show_logo_back', 'card_show_photo_front', 'card_show_footer_front', 'card_show_footer_back', 'card_show_back_notice'].forEach((field) => {
            const input = settingsForm.elements.namedItem(field);
            const value = field === 'card_is_transparent'
                ? ((settings[field] ?? defaultThemeSettings[field]) ? '1' : '0')
                : (settings[field] ?? defaultThemeSettings[field]);
            if (field === 'card_color_type') {
                settingsForm.querySelectorAll('input[name="card_color_type"]').forEach((radio) => {
                    radio.checked = radio.value === value;
                });
                return;
            }

            if (input && value !== undefined && value !== null) {
                if (input.type === 'checkbox') {
                    input.checked = !!value && value !== '0' && value !== 'false';
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

        const isTransparent = cardIsTransparent?.value === '1' || cardIsTransparent?.value === 'true' || cardIsTransparent?.checked === true;
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

        $('.id-card-background-settings').show();
        if (colorType === 'solid') {
            $('.card-color-gradient-field').hide();
            $('.card-color-solid-field').show();
        } else {
            $('.card-color-gradient-field').show();
            $('.card-color-solid-field').hide();
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
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            cardLogoPreview.src = logoUrl;
            cardLogoPreview.classList.toggle('d-none', !logoUrl);
        }

        if (idCardLivePreview) {
            const themeAccent = isTransparent
                ? 'transparent'
                : (colorType === 'solid' ? solid : gradient1);
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
            idCardLivePreview.style.setProperty('--id-card-back-notice-color', cardBackNoticeColor?.value || '#94a3b8');
            idCardLivePreview.style.setProperty('--id-card-footer-color', cardFooterColor?.value || '#e5e7eb');
            idCardLivePreview.style.setProperty('--id-card-title-color', cardTitleColor?.value || '#ffffff');

            const unit = cardDimensionUnit?.value || 'cm';
            const cardWidthValue = parseFloat(cardWidth?.value || '5.4') || 5.4;
            const cardHeightValue = parseFloat(cardHeight?.value || '8.4') || 8.4;
            const widthValue = parseFloat(cardPhotoWidth?.value || '1.8') || 1.8;
            const heightValue = parseFloat(cardPhotoHeight?.value || '2.7') || 2.7;
            const logoSizeValue = parseFloat(cardLogoSize?.value || '0.8') || 0.8;
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
            idCardLivePreview.style.setProperty('--id-card-logo-size', `${logoSizeValue}${unitSuffix}`);
            idCardLivePreview.style.setProperty('--id-card-school-name-font-size', `${schoolNameFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-school-detail-font-size', `${schoolDetailFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-slogan-font-size', `${sloganFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-title-font-size', `${titleFontSizeValue}pt`);
            idCardLivePreview.style.setProperty('--id-card-name-font-size', `${nameFontSizeValue}pt`);
        }

        if (idCardLivePreviewLogoFront) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            idCardLivePreviewLogoFront.src = logoUrl;
            idCardLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
        }

        if (idCardLivePreviewLogoBack) {
            const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
            idCardLivePreviewLogoBack.src = logoUrl;
            idCardLivePreviewLogoBack.classList.toggle('d-none', !logoUrl);
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
                const logoUrl = activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
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
        setDirtyState(false);
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
    max-width: none;
    width: calc(100vw - 16px);
    margin: 8px auto;
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
