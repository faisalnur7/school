@php
    $employeeMode = $employeeMode ?? false;
    $settingsOnly = $settingsOnly ?? false;
    $settingsBackUrl = route($employeeMode ? 'hr.employee-id-cards' : 'students.id-cards', request()->query());
    $selectedCardType = old('card_type', $cardType ?? 'id_card');
    $selectedColorType = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
    $selectedTransparent = old('card_is_transparent', $cardSettings?->card_is_transparent ?? false);
    $isLibraryCard = $selectedCardType === 'library_card';

    $resolveLogoUrl = function (?string $path): ?string {
        if (!$path) {
            return null;
        }

        return file_exists(public_path($path)) ? asset($path) : null;
    };

    $schoolLogoUrl = $resolveLogoUrl($setting?->logo ?? null);
    $currentCardLogoUrl = $resolveLogoUrl($cardSettings?->card_logo ?? null) ?: $schoolLogoUrl;
    $currentCardPrincipalSignatureUrl = $resolveLogoUrl($cardSettings?->card_principal_signature ?? null);
    $currentCardPhotoFit = old('card_photo_fit', $cardSettings?->card_photo_fit ?? 'cover');
    $currentCardLogoFit = old('card_logo_fit', $cardSettings?->card_logo_fit ?? 'contain');
    $previewLabel = $employeeMode ? 'Employee ID Card Preview' : ($isLibraryCard ? 'Library Card Preview' : 'ID Card Preview');
    $frontTitle = $employeeMode ? 'EMPLOYEE ID' : ($isLibraryCard ? 'LIBRARY CARD' : 'STUDENT ID');
    $cardTypeLabel = $employeeMode ? 'Employee ID Card Settings' : ($isLibraryCard ? 'Library Card Settings' : 'ID Card Settings');
    // 'side' groups the switches into the Front / Back sections; 'default' is used when nothing is saved yet.
    $visibilityRows = [
        ['side' => 'front', 'name' => 'card_show_logo_front', 'id' => 'idCardShowLogoFront', 'label' => 'Logo'],
        ['side' => 'front', 'name' => 'card_show_photo_front', 'id' => 'idCardShowPhotoFront', 'label' => 'Photo'],
        ['side' => 'front', 'name' => 'card_show_school_detail_front', 'id' => 'idCardShowSchoolDetailFront', 'label' => 'School Detail (address)', 'default' => false],
        ['side' => 'front', 'name' => 'card_show_slogan_front', 'id' => 'idCardShowSloganFront', 'label' => 'Slogan'],
        ['side' => 'front', 'name' => 'card_show_title_front', 'id' => 'idCardShowTitleFront', 'label' => 'Title'],
        ['side' => 'front', 'name' => 'card_show_footer_front', 'id' => 'idCardShowFooterFront', 'label' => 'Footer'],
        ['side' => 'back', 'name' => 'card_show_logo_back', 'id' => 'idCardShowLogoBack', 'label' => 'Logo'],
        ['side' => 'back', 'name' => 'card_show_school_detail_back', 'id' => 'idCardShowSchoolDetailBack', 'label' => 'School Detail (address)'],
        ['side' => 'back', 'name' => 'card_show_slogan_back', 'id' => 'idCardShowSloganBack', 'label' => 'Slogan'],
        ['side' => 'back', 'name' => 'card_show_title_back', 'id' => 'idCardShowTitleBack', 'label' => 'Title'],
        ['side' => 'back', 'name' => 'card_show_back_student_details', 'id' => 'idCardShowBackStudentDetails', 'label' => $employeeMode ? 'Employee Details' : 'Student Details'],
        ['side' => 'back', 'name' => 'card_show_back_school_contact', 'id' => 'idCardShowBackSchoolContact', 'label' => 'School Contact'],
        ['side' => 'back', 'name' => 'card_show_back_qr', 'id' => 'idCardShowBackQr', 'label' => 'QR Code'],
        ['side' => 'back', 'name' => 'card_show_back_signature', 'id' => 'idCardShowBackSignature', 'label' => 'Signature'],
        ['side' => 'back', 'name' => 'card_show_back_notice', 'id' => 'idCardShowBackNotice', 'label' => 'Notice'],
        ['side' => 'back', 'name' => 'card_show_footer_back', 'id' => 'idCardShowFooterBack', 'label' => 'Footer'],
    ];
@endphp

<div class="modal fade {{ $settingsOnly ? 'show' : '' }}" id="idCardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="idCardSettingsModalLabel" aria-hidden="{{ $settingsOnly ? 'false' : 'true' }}">
    <div class="modal-dialog modal-dialog-centered modal-xl id-card-settings-modal-dialog card-settings-modal-dialog" role="document">
        <div class="modal-content id-card-settings-modal-content card-settings-modal-content">
            <form method="POST" action="{{ $employeeMode ? route('hr.employee-id-cards.settings') : route('students.id-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header id-card-settings-modal-header card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="idCardSettingsModalLabel">{{ $cardTypeLabel }}</h5>
                        <small class="text-muted d-block" id="idCardSettingsModalTypeLabel">{{ $employeeMode ? 'Customize the employee card layout and output.' : 'Switch between ID and library card presets.' }}</small>
                        <small class="text-muted d-block">Save one layout profile for search, print, and PDF output.</small>
                    </div>
                    <div class="btn-group btn-group-sm id-card-settings-type-switcher card-settings-type-switcher {{ $employeeMode ? 'd-none' : '' }}" role="group" aria-label="Card type selector">
                        <button type="button" class="btn {{ !$isLibraryCard ? 'btn-primary active' : 'btn-outline-primary' }} js-card-type-switch" data-card-type="id_card">ID Card</button>
                        <button type="button" class="btn {{ $isLibraryCard ? 'btn-primary active' : 'btn-outline-primary' }} js-card-type-switch" data-card-type="library_card">Library Card</button>
                    </div>
                    <span id="idCardSettingsDirtyBadge" class="badge badge-warning align-self-center d-none">Unsaved changes</span>
                    @if($settingsOnly)
                        <a href="{{ $settingsBackUrl }}" class="btn btn-outline-secondary btn-sm align-self-center js-id-card-settings-back">
                            <i class="fas fa-arrow-left mr-1"></i>Back to cards
                        </a>
                    @else
                        <button type="button" class="close id-card-settings-modal-close card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    @endif
                </div>

                <div class="modal-body id-card-settings-modal-body card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ $selectedCardType }}">
                    <div class="d-none" aria-hidden="true">
                        @foreach(\App\Models\AdmitSeatCardSetting::ID_CARD_POSITION_KEYS as $positionKey)
                            @foreach(['x', 'y'] as $axis)
                                <input type="hidden" name="card_element_positions[{{ $positionKey }}][{{ $axis }}]" data-element-position-key="{{ $positionKey }}" data-element-position-axis="{{ $axis }}" value="{{ old("card_element_positions.$positionKey.$axis", data_get($cardSettings?->card_element_positions, "$positionKey.$axis", 0)) }}">
                            @endforeach
                        @endforeach
                    </div>
                    <input type="hidden" name="card_exam_type_font_size" value="{{ old('card_exam_type_font_size', $cardSettings?->card_exam_type_font_size ?? 7.4) }}">
                    <input type="hidden" name="card_exam_name_font_size" value="{{ old('card_exam_name_font_size', $cardSettings?->card_exam_name_font_size ?? 6.8) }}">
                    <input type="hidden" name="card_exam_type_text_color" value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                    <input type="hidden" name="card_exam_name_text_color" value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                    <input type="hidden" name="card_show_exam_type_front" value="{{ old('card_show_exam_type_front', $cardSettings?->card_show_exam_type_front ?? true) ? 1 : 0 }}">
                    <input type="hidden" name="card_show_exam_name_front" value="{{ old('card_show_exam_name_front', $cardSettings?->card_show_exam_name_front ?? true) ? 1 : 0 }}">

                    <div class="row align-items-stretch admit-seat-cards-modal-layout">
                        <div class="col-12 col-lg-3 mb-3 admit-seat-cards-modal-preview">
                            <div class="csm-preview-sticky">
                                <div class="csm-preview-toolbar" role="toolbar" aria-label="Preview tools">
                                    @if(!$employeeMode && ($allStudents ?? collect())->isNotEmpty())
                                        <select id="idCardPreviewStudent" class="custom-select custom-select-sm csm-preview-toolbar__student" aria-label="Preview with a real student">
                                            <option value="">Sample student</option>
                                            @foreach($allStudents->take(300) as $pvStudent)
                                                <option value="{{ $pvStudent->id }}">{{ $pvStudent->academicInformations->first()?->roll ?: '—' }} · {{ $pvStudent->full_name_en }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="csm-preview-toolbar__student text-muted small">Sample data</span>
                                    @endif
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Zoom">
                                        <button type="button" class="btn btn-outline-secondary" id="idCardZoomOut" title="Zoom out" aria-label="Zoom out"><i class="fas fa-search-minus"></i></button>
                                        <button type="button" class="btn btn-outline-secondary csm-zoom-level" id="idCardZoomLevel" title="Fit to panel" aria-label="Fit to panel">Fit</button>
                                        <button type="button" class="btn btn-outline-secondary" id="idCardZoomIn" title="Zoom in" aria-label="Zoom in"><i class="fas fa-search-plus"></i></button>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="idCardResetPositions" title="Put every dragged element back to its default place">
                                        <i class="fas fa-arrows-alt mr-1"></i>Reset positions
                                    </button>
                                    <a href="{{ $employeeMode ? route('hr.employee-id-cards.pdf', ['preview' => 1, 'inline' => 1]) : route('students.id-cards.pdf', ['preview_student_id' => 'first', 'card_type' => $selectedCardType, 'inline' => 1]) }}"
                                       class="btn btn-outline-primary btn-sm" id="idCardPreviewPdf" @if($employeeMode) data-employee="1" @endif target="_blank" rel="noopener"
                                       data-base-href="{{ $employeeMode ? route('hr.employee-id-cards.pdf') : route('students.id-cards.pdf') }}"
                                       title="Open the real PDF for one card, using the last saved settings">
                                        <i class="fas fa-file-pdf mr-1"></i>Preview PDF
                                    </a>
                                </div>
                                @include('pages.card-settings._live-preview', [
                                    'prefix' => 'idCard',
                                    'employeeMode' => $employeeMode,
                                    'previewType' => 'id',
                                    'cardType' => $selectedCardType,
                                    'showBack' => true,
                                    'previewLabel' => $previewLabel,
                                    'schoolName' => $setting?->name ?? 'School Name',
                                    'schoolDetailLine' => $setting?->address ?? '',
                                    'slogan' => $setting?->slogan ?? 'Stay Green, Be Bright',
                                    'frontTitle' => $frontTitle,
                                    'backTitle' => 'BACK',
                                    'backNotice' => 'If found, please return to the school.',
                                    'footerLine' => $setting?->whatsapp_number ?? '',
                                    'previewSettings' => $cardSettings?->toArray() ?? [],
                                    'logoUrl' => $currentCardLogoUrl,
                                    'cardPhotoFit' => $currentCardPhotoFit,
                                    'cardLogoFit' => $currentCardLogoFit,
                                    'principalSignatureUrl' => $currentCardPrincipalSignatureUrl,
                                    'schoolContactLine1' => $setting?->contact_number_1 ?? null,
                                    'schoolContactLine2' => $setting?->contact_number_2 ?? null,
                                    'schoolWhatsapp' => $setting?->whatsapp_number ?? null,
                                    'schoolEmail' => $setting?->email ?? null,
                                    'schoolWebsite' => $setting?->website ?? null,
                                    'schoolQrUrl' => $setting?->whatsapp_qr ? (file_exists(public_path($setting->whatsapp_qr)) ? asset($setting->whatsapp_qr) : null) : null,
                                    'showSchoolDetailFront' => $cardSettings?->card_show_school_detail_front ?? false,
                                    'showSchoolDetailBack' => $cardSettings?->card_show_school_detail_back ?? true,
                                    'showSloganFront' => $cardSettings?->card_show_slogan_front ?? true,
                                    'showSloganBack' => $cardSettings?->card_show_slogan_back ?? true,
                                    'showTitleFront' => $cardSettings?->card_show_title_front ?? true,
                                    'showTitleBack' => $cardSettings?->card_show_title_back ?? true,
                                    'showLogoFront' => $cardSettings?->card_show_logo_front ?? true,
                                    'showLogoBack' => $cardSettings?->card_show_logo_back ?? true,
                                    'showPhotoFront' => $cardSettings?->card_show_photo_front ?? true,
                                    'showBackNotice' => $cardSettings?->card_show_back_notice ?? true,
                                    'showBackStudentDetails' => $cardSettings?->card_show_back_student_details ?? true,
                                    'showBackSchoolContact' => $cardSettings?->card_show_back_school_contact ?? true,
                                    'showBackQr' => $cardSettings?->card_show_back_qr ?? true,
                                    'showBackSignature' => $cardSettings?->card_show_back_signature ?? true,
                                    'showFooterFront' => $cardSettings?->card_show_footer_front ?? true,
                                    'showFooterBack' => $cardSettings?->card_show_footer_back ?? true,
                                    'previewCardWidthValue' => $cardSettings?->card_width_value ?? 5.4,
                                    'previewCardHeightValue' => $cardSettings?->card_height_value ?? 8.56,
                                    'previewCardDimensionUnit' => $cardSettings?->card_dimension_unit ?? 'cm',
                                    'focusTargets' => [
                                        'logo' => 'cardLogoInput',
                                        'school_name' => 'cardSchoolNameColor',
                                        'school_detail' => 'cardSchoolDetailColor',
                                        'slogan' => 'cardSloganColor',
                                        'title' => 'cardTitleColor',
                                        'name' => 'cardNameColor',
                                        'student_detail_alignment' => 'cardStudentDetailAlignment',
                                        'student_detail_font_size' => 'cardStudentDetailFontSize',
                                        'student_detail_color' => 'cardStudentDetailColor',
                                        'back_notice' => 'cardBackNoticeColor',
                                        'footer' => 'cardFooterColor',
                                        'photo' => 'cardPhotoWidth',
                                        'principal_signature' => 'cardPrincipalSignatureInput',
                                        'photo_fit' => 'cardPhotoFit',
                                        'logo_fit' => 'cardLogoFit',
                                    ],
                                ])
                            </div>
                        </div>

                        <div class="col-12 col-lg-9 admit-seat-cards-modal-settings">
                            <ul class="nav nav-tabs csm-section-tabs mb-2" id="idCardSettingsTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="idCardLayoutTab" data-toggle="tab" href="#idCardLayoutPane" role="tab" aria-controls="idCardLayoutPane" aria-selected="true">Layout &amp; Grid</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="idCardPhotoTab" data-toggle="tab" href="#idCardPhotoPane" role="tab" aria-controls="idCardPhotoPane" aria-selected="false">Photo &amp; Logo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="idCardTypographyTab" data-toggle="tab" href="#idCardTypographyPane" role="tab" aria-controls="idCardTypographyPane" aria-selected="false">Typography &amp; Colors</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="idCardBackgroundTab" data-toggle="tab" href="#idCardBackgroundPane" role="tab" aria-controls="idCardBackgroundPane" aria-selected="false">Background</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="idCardVisibilityTab" data-toggle="tab" href="#idCardVisibilityPane" role="tab" aria-controls="idCardVisibilityPane" aria-selected="false">Visibility</a>
                                </li>
                            </ul>

                            <div class="tab-content admit-seat-cards-settings-panel">
                                <div class="tab-pane fade show active" id="idCardLayoutPane" role="tabpanel" aria-labelledby="idCardLayoutTab">
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header id-card-settings-section-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-vector-square"></i></span>
                                                <div>
                                                        <h6 class="mb-0 font-weight-bold" style="font-size:12px;">Layout &amp; Grid</h6>
                                                    <small class="text-muted d-block">Tune the card grid, spacing, and page alignment.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $lv = fn ($name, $default) => old($name, $cardSettings?->{$name} ?? $default);
                                                $num = function (string $name, string $id, string $label, string $tip, $default, array $attrs = [], string $col = 'col-6 col-xl-3') use ($lv) {
                                                    $extra = collect($attrs)->map(fn ($v, $k) => $k . '="' . e($v) . '"')->implode(' ');
                                                    return '<div class="' . $col . ' mb-2"><div class="id-card-settings-field">'
                                                        . '<label class="id-card-settings-label" for="' . $id . '" data-toggle="tooltip" data-placement="top" title="' . e($tip) . '">' . e($label) . '</label>'
                                                        . '<input type="number" name="' . $name . '" id="' . $id . '" class="form-control form-control-sm id-card-settings-control csm-input" ' . $extra . ' value="' . e($lv($name, $default)) . '">'
                                                        . '<div class="invalid-feedback" data-feedback-for="' . $name . '"></div>'
                                                        . '</div></div>';
                                                };
                                                $sel = function (string $name, string $id, string $label, string $tip, string $default, array $options, string $col = 'col-6 col-xl-3') use ($lv) {
                                                    $current = $lv($name, $default);
                                                    $opts = collect($options)->map(fn ($text, $value) => '<option value="' . $value . '"' . ($current === (string) $value ? ' selected' : '') . '>' . e($text) . '</option>')->implode('');
                                                    return '<div class="' . $col . ' mb-2"><div class="id-card-settings-field">'
                                                        . '<label class="id-card-settings-label" for="' . $id . '" data-toggle="tooltip" data-placement="top" title="' . e($tip) . '">' . e($label) . '</label>'
                                                        . '<select name="' . $name . '" id="' . $id . '" class="form-control form-control-sm id-card-settings-control csm-select">' . $opts . '</select>'
                                                        . '</div></div>';
                                                };
                                                $align = ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'];
                                            @endphp

                                            <div class="csm-group">
                                                <div class="csm-group__title"><i class="fas fa-file-alt"></i>Sheet <small>paper and how many cards print on it</small></div>
                                                <div class="row align-items-start">
                                                    <div class="col-12 col-lg-8">
                                                        <div class="row">
                                                            {!! $sel('card_page_size', 'cardPageSize', 'Paper Size', 'Paper used for print and PDF.', 'a4', ['a4' => 'A4', 'letter' => 'Letter'], 'col-6 col-md-4') !!}
                                                            {!! $sel('card_page_orientation', 'cardPageOrientation', 'Orientation', 'Sheet orientation for print and PDF.', 'landscape', ['landscape' => 'Landscape', 'portrait' => 'Portrait'], 'col-6 col-md-4') !!}
                                                            {!! $num('cards_per_page', 'cardCardsPerPage', 'Cards / Page', 'Total cards (front + back pairs) on a page.', 4, ['min' => 1, 'max' => 12], 'col-6 col-md-4') !!}
                                                            {!! $num('cards_per_row', 'cardCardsPerRow', 'Cards / Row', 'How many front + back pairs sit side by side.', 2, ['min' => 1, 'max' => 10], 'col-6 col-md-4') !!}
                                                            {!! $num('grid_gap_value', 'cardGridGap', 'Grid Gap', 'Spacing between cards on the sheet.', 0.5, ['min' => 0.1, 'step' => 0.01], 'col-6 col-md-4') !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-4 mb-2">
                                                        <div class="csm-sheet" id="idCardSheetDiagram" role="img" aria-label="Sheet layout preview"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="csm-group">
                                                <div class="csm-group__title"><i class="fas fa-id-card"></i>Card size <small>one card, front and back are identical</small></div>
                                                <div class="row">
                                                    <div class="col-6 col-xl-3 mb-2">
                                                        <div class="id-card-settings-field">
                                                            <label class="id-card-settings-label" for="cardSizePreset" data-toggle="tooltip" data-placement="top" title="Standard card sizes. Picking one fills width and height.">Size Preset</label>
                                                            <select id="cardSizePreset" class="form-control form-control-sm id-card-settings-control csm-select">
                                                                <option value="cr80p">CR80 portrait · 54 × 85.6 mm</option>
                                                                <option value="cr80l">CR80 landscape · 85.6 × 54 mm</option>
                                                                <option value="custom">Custom</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    {!! $num('card_width_value', 'cardCardWidth', 'Card Width', 'Width of each rendered card.', 5.4, ['min' => 0.1, 'step' => 0.01]) !!}
                                                    {!! $num('card_height_value', 'cardCardHeight', 'Card Height', 'Height of each rendered card.', 8.56, ['min' => 0.1, 'step' => 0.01]) !!}
                                                    {!! $sel('card_dimension_unit', 'cardDimensionUnit', 'Unit', 'Unit for every measurement. Changing it converts the numbers.', 'cm', ['cm' => 'Centimeter (cm)', 'px' => 'Pixel (px)']) !!}
                                                </div>
                                            </div>

                                            <div class="csm-group mb-0">
                                                <div class="csm-group__title"><i class="fas fa-align-center"></i>Alignment &amp; spacing</div>
                                                <div class="row">
                                                    {!! $sel('card_front_alignment', 'cardFrontAlignment', 'Front Alignment', 'Controls the front card content alignment.', 'center', $align) !!}
                                                    {!! $sel('card_back_alignment', 'cardBackAlignment', 'Back Alignment', 'Controls the back card content alignment.', 'center', $align) !!}
                                                    {!! $num('card_front_padding_value', 'cardFrontPadding', 'Front Body Padding', 'Inner spacing around the front card content (photo, name, details).', 0.8, ['min' => 0, 'step' => 0.01]) !!}
                                                    {!! $num('card_back_padding_value', 'cardBackPadding', 'Back Body Padding', 'Inner spacing around the back card content.', 0.8, ['min' => 0, 'step' => 0.01]) !!}
                                                    {!! $num('card_front_header_padding_value', 'cardFrontHeaderPadding', 'Front Header Padding', 'Spacing inside the coloured header on the front (logo, school name, title).', 0.3, ['min' => 0, 'max' => 5, 'step' => 0.01]) !!}
                                                    {!! $num('card_back_header_padding_value', 'cardBackHeaderPadding', 'Back Header Padding', 'Spacing inside the coloured header on the back.', 0.3, ['min' => 0, 'max' => 5, 'step' => 0.01]) !!}
                                                </div>
                                            </div>

                                            <div id="idCardLayoutFitNotice" class="alert alert-info py-2 px-3 mt-3 mb-0" style="font-size:12px" role="status" aria-live="polite"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="idCardPhotoPane" role="tabpanel" aria-labelledby="idCardPhotoTab">
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header id-card-settings-section-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-image"></i></span>
                                                <h6 class="mb-0 font-weight-bold">Photo &amp; Logo</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="csm-group">
                                                <div class="csm-group__title"><i class="fas fa-image"></i>Sizes &amp; fit <small>photo and logo dimensions</small></div>
                                                <div class="row">
                                                    <div class="col-6 col-md-4 col-xl mb-2">
                                                        <div class="id-card-settings-field id-card-settings-field--compact">
                                                            <label class="id-card-settings-label" for="cardPhotoWidth">Photo W <span class="id-card-settings-unit">cm</span></label>
                                                            <input type="number" name="card_photo_width_value" id="cardPhotoWidth" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.01" value="{{ old('card_photo_width_value', $cardSettings?->card_photo_width_value ?? 1.8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4 col-xl mb-2">
                                                        <div class="id-card-settings-field id-card-settings-field--compact">
                                                            <label class="id-card-settings-label" for="cardPhotoHeight">Photo H <span class="id-card-settings-unit">cm</span></label>
                                                            <input type="number" name="card_photo_height_value" id="cardPhotoHeight" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.01" value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4 col-xl mb-2">
                                                        <div class="id-card-settings-field id-card-settings-field--compact">
                                                            <label class="id-card-settings-label" for="cardPhotoFit">Photo fit</label>
                                                            <select name="card_photo_fit" id="cardPhotoFit" class="form-control form-control-sm id-card-settings-control csm-select">
                                                                <option value="cover" {{ $currentCardPhotoFit === 'cover' ? 'selected' : '' }}>Cover</option>
                                                                <option value="contain" {{ $currentCardPhotoFit === 'contain' ? 'selected' : '' }}>Contain</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4 col-xl mb-2">
                                                        <div class="id-card-settings-field id-card-settings-field--compact">
                                                            <label class="id-card-settings-label" for="cardLogoSize">Logo size <span class="id-card-settings-unit">cm</span></label>
                                                            <input type="number" name="card_logo_size_value" id="cardLogoSize" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.01" value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-4 col-xl mb-2">
                                                        <div class="id-card-settings-field id-card-settings-field--compact">
                                                            <label class="id-card-settings-label" for="cardLogoFit">Logo fit</label>
                                                            <select name="card_logo_fit" id="cardLogoFit" class="form-control form-control-sm id-card-settings-control csm-select">
                                                                <option value="contain" {{ $currentCardLogoFit === 'contain' ? 'selected' : '' }}>Contain</option>
                                                                <option value="cover" {{ $currentCardLogoFit === 'cover' ? 'selected' : '' }}>Cover</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="csm-group mb-0">
                                                <div class="csm-group__title"><i class="fas fa-upload"></i>Images <small>uploads replace the current file for this card preset</small></div>
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-2">
                                                        <div class="id-card-upload-box id-card-upload-box--compact h-100">
                                                            <div class="id-card-upload-box__main">
                                                                <label class="id-card-settings-label d-block mb-1" for="cardLogoInput"><i class="fas fa-upload mr-1"></i>School logo</label>
                                                                <input type="file" name="card_logo" id="cardLogoInput" class="form-control form-control-sm" accept="image/*">
                                                                <small class="text-muted d-block mt-1">Logo shown in the card header.</small>
                                                            </div>
                                                            <div class="id-card-upload-preview__stage id-card-upload-preview__stage--thumb">
                                                                <img id="cardLogoPreview" src="{{ $currentCardLogoUrl ?? '' }}" alt="Logo preview" class="{{ $currentCardLogoUrl ? '' : 'd-none' }}" style="max-width:100%; max-height:72px; object-fit: contain;">
                                                                <div class="small text-muted text-center {{ $currentCardLogoUrl ? 'd-none' : '' }}">No logo</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 mb-2">
                                                        <div class="id-card-upload-box id-card-upload-box--compact h-100">
                                                            <div class="id-card-upload-box__main">
                                                                <label class="id-card-settings-label d-block mb-1" for="cardPrincipalSignatureInput"><i class="fas fa-upload mr-1"></i>Principal signature</label>
                                                                <input type="file" name="card_principal_signature" id="cardPrincipalSignatureInput" class="form-control form-control-sm" accept="image/png,image/jpeg">
                                                                <small class="text-muted d-block mt-1">PNG, JPG or JPEG.</small>
                                                            </div>
                                                            <div class="id-card-upload-preview__stage id-card-upload-preview__stage--thumb">
                                                                <img id="cardPrincipalSignaturePreview" src="{{ $currentCardPrincipalSignatureUrl ?? '' }}" alt="Principal signature preview" class="{{ $currentCardPrincipalSignatureUrl ? '' : 'd-none' }}" style="max-width:100%; max-height:72px; object-fit: contain;">
                                                                <div class="small text-muted text-center {{ $currentCardPrincipalSignatureUrl ? 'd-none' : '' }}">No signature</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="idCardTypographyPane" role="tabpanel" aria-labelledby="idCardTypographyTab">
                                    @php
                                        $typo = fn (string $name, $default) => old($name, $cardSettings?->{$name} ?? $default);
                                        $who = $employeeMode ? 'Employee' : 'Student';
                                        // [label, where it applies, size field/id/default, colour field/id/default]
                                        $frontTypography = [
                                            ['School Name', 'front + back header', ['card_school_name_font_size', 'cardSchoolNameFontSize', 7.2], ['card_school_name_text_color', 'cardSchoolNameColor', '#ffffff']],
                                            ['School Detail (address)', 'front header · size also sets the back notice', ['card_school_detail_font_size', 'cardSchoolDetailFontSize', 5.4], ['card_school_detail_text_color', 'cardSchoolDetailColor', '#e5e7eb']],
                                            ['Slogan', 'front + back header', ['card_slogan_font_size', 'cardSloganFontSize', 4.8], ['card_slogan_text_color', 'cardSloganColor', '#e5e7eb']],
                                            ['Card Title', 'front + back badge', ['card_title_font_size', 'cardTitleFontSize', 4.7], ['card_title_text_color', 'cardTitleColor', '#ffffff']],
                                            [$who . ' Name', 'front', ['card_name_font_size', 'cardNameFontSize', 7.2], ['card_name_text_color', 'cardNameColor', '#1e3a5f']],
                                            [$who . ' Detail', 'front rows + back details', ['card_student_detail_font_size', 'cardStudentDetailFontSize', 4.6], ['card_student_detail_text_color', 'cardStudentDetailColor', '#111827']],
                                        ];
                                        $backColours = [
                                            ['Back Notice', 'back', 'card_back_notice_text_color', 'cardBackNoticeColor', '#94a3b8'],
                                            ['Footer', 'front + back footer', 'card_footer_text_color', 'cardFooterColor', '#e5e7eb'],
                                        ];
                                    @endphp
                                    <div class="row">
                                        <div class="col-12 col-xl-7 mb-2">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header id-card-settings-section-header">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-primary mr-2"><i class="fas fa-id-card"></i></span>
                                                        <div>
                                                            <h6 class="mb-0 font-weight-bold">Typography &amp; Colors — Front side</h6>
                                                            <small class="text-muted d-block">Font size (pt) and colour. Header texts and detail rows are shared with the back.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($frontTypography as [$label, $where, $size, $colour])
                                                        <div class="row align-items-center mb-3">
                                                            <div class="col-12 col-md-5 mb-1 mb-md-0">
                                                                <strong class="d-block">{{ $label }}</strong>
                                                                <small class="text-muted">{{ $where }}</small>
                                                            </div>
                                                            <div class="col-6 col-md-3">
                                                                <input type="number" name="{{ $size[0] }}" id="{{ $size[1] }}" class="form-control form-control-sm id-card-settings-control csm-input" min="1" max="24" step="0.1" value="{{ $typo($size[0], $size[2]) }}">
                                                            </div>
                                                            <div class="col-6 col-md-4">
                                                                <div class="id-card-settings-color-row">
                                                                    <input type="color" name="{{ $colour[0] }}" id="{{ $colour[1] }}" class="id-card-settings-color-native csm-color-native" value="{{ $typo($colour[0], $colour[2]) }}">
                                                                    <span id="{{ $colour[1] }}Preview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ $typo($colour[0], $colour[2]) }};"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    <div class="row align-items-center mb-0">
                                                        <div class="col-12 col-md-5 mb-1 mb-md-0">
                                                            <strong class="d-block">{{ $who }} Detail Alignment</strong>
                                                            <small class="text-muted">front rows + back details</small>
                                                        </div>
                                                        <div class="col-12 col-md-7">
                                                            <select name="card_student_detail_alignment" id="cardStudentDetailAlignment" class="form-control form-control-sm id-card-settings-control csm-select">
                                                                @foreach(['left' => 'Left', 'center' => 'Center', 'right' => 'Right'] as $value => $text)
                                                                    <option value="{{ $value }}" {{ $typo('card_student_detail_alignment', 'left') === $value ? 'selected' : '' }}>{{ $text }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-xl-5 mb-2">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header id-card-settings-section-header">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge badge-primary mr-2"><i class="fas fa-clone"></i></span>
                                                        <div>
                                                            <h6 class="mb-0 font-weight-bold">Typography &amp; Colors — Back side</h6>
                                                            <small class="text-muted d-block">Colours that only the back (or both footers) use.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($backColours as [$label, $where, $name, $id, $default])
                                                        <div class="row align-items-center mb-3">
                                                            <div class="col-12 col-md-5 mb-1 mb-md-0">
                                                                <strong class="d-block">{{ $label }}</strong>
                                                                <small class="text-muted">{{ $where }}</small>
                                                            </div>
                                                            <div class="col-12 col-md-7">
                                                                <div class="id-card-settings-color-row">
                                                                    <input type="color" name="{{ $name }}" id="{{ $id }}" class="id-card-settings-color-native csm-color-native" value="{{ $typo($name, $default) }}">
                                                                    <span id="{{ $id }}Preview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ $typo($name, $default) }};"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <p class="id-card-settings-note mb-0">
                                                        The back's section titles use the card colour (Background tab). Its header, title badge and
                                                        detail rows use the sizes and colours set on the Front side.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="idCardBackgroundPane" role="tabpanel" aria-labelledby="idCardBackgroundTab">
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header id-card-settings-section-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-fill-drip"></i></span>
                                                <h6 class="mb-0 font-weight-bold">Background</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="card_is_transparent" id="cardIsTransparent" value="1" {{ $selectedTransparent ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="cardIsTransparent">Transparent Background</label>
                                                </div>
                                            </div>

                                            <div class="id-card-background-settings">
                                                <div class="row">
                                                    <div class="col-12 col-md-4 mb-2">
                                                        <div class="id-card-settings-field">
                                                            <label class="id-card-settings-label">Background Type</label>
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
                                                    <div class="col-12 col-md-4 mb-2 card-color-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                        <div class="id-card-settings-field">
                                                            <label class="id-card-settings-label" for="cardColorGradient1">Gradient Start</label>
                                                            <div class="id-card-settings-color-row">
                                                                <input type="color" name="card_color_gradient_1" id="cardColorGradient1" class="id-card-settings-color-native csm-color-native" value="{{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }}">
                                                                <span id="cardColorGradient1Preview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }};"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2 card-color-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                        <div class="id-card-settings-field">
                                                            <label class="id-card-settings-label" for="cardColorGradient2">Gradient End</label>
                                                            <div class="id-card-settings-color-row">
                                                                <input type="color" name="card_color_gradient_2" id="cardColorGradient2" class="id-card-settings-color-native csm-color-native" value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                                                <span id="cardColorGradient2Preview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }};"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4 mb-2 card-color-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                                                        <div class="id-card-settings-field">
                                                            <label class="id-card-settings-label" for="cardSolidColor">Solid Color</label>
                                                            <div class="id-card-settings-color-row">
                                                                <input type="color" name="card_solid_color" id="cardSolidColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                                                <span id="cardSolidColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }};"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group mb-0 mt-2">
                                                    <label class="d-block mb-1 small font-weight-bold text-dark">Preview</label>
                                                    <div id="cardThemePreview" class="border rounded" style="height: 36px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="idCardVisibilityPane" role="tabpanel" aria-labelledby="idCardVisibilityTab">
                                    @foreach(['front' => ['Front side', 'fa-id-card'], 'back' => ['Back side', 'fa-clone']] as $side => [$sideTitle, $sideIcon])
                                        <div class="card mb-2 shadow-sm">
                                            <div class="card-header id-card-settings-section-header">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i class="fas {{ $sideIcon }}"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Visibility — {{ $sideTitle }}</h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach(array_filter($visibilityRows, fn ($row) => $row['side'] === $side) as $row)
                                                        <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                            <div class="custom-control custom-switch">
                                                                <input
                                                                    type="checkbox"
                                                                    class="custom-control-input"
                                                                    name="{{ $row['name'] }}"
                                                                    id="{{ $row['id'] }}"
                                                                    {{ old($row['name'], $cardSettings?->{$row['name']} ?? ($row['default'] ?? true)) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="{{ $row['id'] }}">{{ $row['label'] }}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                <p class="id-card-settings-note">These settings are saved once and used by search and PDF output.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer id-card-settings-modal-footer card-settings-modal-footer">
                    <div class="mr-auto d-flex flex-wrap align-items-center" style="gap:8px">
                        <button type="button" class="btn btn-outline-warning" id="idCardResetDefaults" title="Restore the standard ID card preset (then press Save Settings)">
                            <i class="fas fa-undo mr-1"></i>Reset to defaults
                        </button>
                        @unless($employeeMode)
                            <button type="button" class="btn btn-outline-secondary" id="idCardCopySettings" title="Copy layout, colours and visibility from the other card type (then press Save Settings)">
                                <i class="fas fa-copy mr-1"></i>Copy from <span id="idCardCopySourceLabel">{{ $isLibraryCard ? 'ID card' : 'Library card' }}</span>
                            </button>
                        @endunless
                    </div>
                    <span id="idCardFooterDirty" class="csm-dirty d-none" role="status"><i class="fas fa-circle"></i>Unsaved changes</span>
                    @if($settingsOnly)
                        <a href="{{ $settingsBackUrl }}" class="btn btn-outline-secondary js-id-card-settings-back">Back to cards</a>
                    @else
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    @endif
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
