@php
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
    $previewLabel = $isLibraryCard ? 'Library Card Preview' : 'ID Card Preview';
    $frontTitle = $isLibraryCard ? 'LIBRARY CARD' : 'STUDENT ID';
    $cardTypeLabel = $isLibraryCard ? 'Library Card Settings' : 'ID Card Settings';
    $visibilityRows = [
        ['name' => 'card_show_logo_front', 'id' => 'idCardShowLogoFront', 'label' => 'Front Logo'],
        ['name' => 'card_show_logo_back', 'id' => 'idCardShowLogoBack', 'label' => 'Back Logo'],
        ['name' => 'card_show_photo_front', 'id' => 'idCardShowPhotoFront', 'label' => 'Photo'],
        ['name' => 'card_show_school_detail_front', 'id' => 'idCardShowSchoolDetailFront', 'label' => 'Front School Detail'],
        ['name' => 'card_show_school_detail_back', 'id' => 'idCardShowSchoolDetailBack', 'label' => 'Back School Detail'],
        ['name' => 'card_show_slogan_front', 'id' => 'idCardShowSloganFront', 'label' => 'Front Slogan'],
        ['name' => 'card_show_slogan_back', 'id' => 'idCardShowSloganBack', 'label' => 'Back Slogan'],
        ['name' => 'card_show_title_front', 'id' => 'idCardShowTitleFront', 'label' => 'Front Title'],
        ['name' => 'card_show_title_back', 'id' => 'idCardShowTitleBack', 'label' => 'Back Title'],
        ['name' => 'card_show_footer_front', 'id' => 'idCardShowFooterFront', 'label' => 'Front Footer'],
        ['name' => 'card_show_footer_back', 'id' => 'idCardShowFooterBack', 'label' => 'Back Footer'],
        ['name' => 'card_show_back_student_details', 'id' => 'idCardShowBackStudentDetails', 'label' => 'Back Student Details'],
        ['name' => 'card_show_back_school_contact', 'id' => 'idCardShowBackSchoolContact', 'label' => 'Back School Contact'],
        ['name' => 'card_show_back_qr', 'id' => 'idCardShowBackQr', 'label' => 'Back QR'],
        ['name' => 'card_show_back_signature', 'id' => 'idCardShowBackSignature', 'label' => 'Back Signature'],
        ['name' => 'card_show_back_notice', 'id' => 'idCardShowBackNotice', 'label' => 'Back Notice'],
    ];
@endphp

<div class="modal fade" id="idCardSettingsModal" tabindex="-1" role="dialog" aria-labelledby="idCardSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl id-card-settings-modal-dialog card-settings-modal-dialog" role="document">
        <div class="modal-content id-card-settings-modal-content card-settings-modal-content">
            <form method="POST" action="{{ route('students.id-cards.settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header id-card-settings-modal-header card-settings-modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="idCardSettingsModalLabel">{{ $cardTypeLabel }}</h5>
                        <small class="text-muted d-block" id="idCardSettingsModalTypeLabel">Switch between ID and library card presets.</small>
                        <small class="text-muted d-block">Save one layout profile for search, print, and PDF output.</small>
                    </div>
                    <div class="btn-group btn-group-sm id-card-settings-type-switcher card-settings-type-switcher" role="group" aria-label="Card type selector">
                        <button type="button" class="btn {{ !$isLibraryCard ? 'btn-primary active' : 'btn-outline-primary' }} js-card-type-switch" data-card-type="id_card">ID Card</button>
                        <button type="button" class="btn {{ $isLibraryCard ? 'btn-primary active' : 'btn-outline-primary' }} js-card-type-switch" data-card-type="library_card">Library Card</button>
                    </div>
                    <span id="idCardSettingsDirtyBadge" class="badge badge-warning align-self-center d-none">Unsaved changes</span>
                    <button type="button" class="close id-card-settings-modal-close card-settings-modal-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body id-card-settings-modal-body card-settings-modal-body">
                    <input type="hidden" name="card_type" value="{{ $selectedCardType }}">
                    <input type="hidden" name="card_exam_type_font_size" value="{{ old('card_exam_type_font_size', $cardSettings?->card_exam_type_font_size ?? 7.4) }}">
                    <input type="hidden" name="card_exam_name_font_size" value="{{ old('card_exam_name_font_size', $cardSettings?->card_exam_name_font_size ?? 6.8) }}">
                    <input type="hidden" name="card_exam_type_text_color" value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                    <input type="hidden" name="card_exam_name_text_color" value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                    <input type="hidden" name="card_show_exam_type_front" value="{{ old('card_show_exam_type_front', $cardSettings?->card_show_exam_type_front ?? true) ? 1 : 0 }}">
                    <input type="hidden" name="card_show_exam_name_front" value="{{ old('card_show_exam_name_front', $cardSettings?->card_show_exam_name_front ?? true) ? 1 : 0 }}">

                    <div class="row align-items-stretch admit-seat-cards-modal-layout">
                        <div class="col-12 col-lg-3 mb-3 admit-seat-cards-modal-preview">
                            <div class="csm-preview-sticky">
                                @include('pages.card-settings._live-preview', [
                                    'prefix' => 'idCard',
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
                                    'footerLine' => $setting?->whatsapp_number ?? $setting?->contact_number_1 ?? 'Contact',
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
                                    'showBackStudentDetails' => $cardSettings?->card_show_back_student_details ?? true,
                                    'showBackSchoolContact' => $cardSettings?->card_show_back_school_contact ?? true,
                                    'showBackQr' => $cardSettings?->card_show_back_qr ?? true,
                                    'showBackSignature' => $cardSettings?->card_show_back_signature ?? true,
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
                                            <div class="row">
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardCardsPerPage" data-toggle="tooltip" data-placement="top" title="Total cards rendered on a page.">Cards / Page</label>
                                                        <input type="number" name="cards_per_page" id="cardCardsPerPage" class="form-control form-control-sm id-card-settings-control csm-input" min="1" max="12" value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 4) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardCardsPerRow" data-toggle="tooltip" data-placement="top" title="How many cards sit side by side.">Cards / Row</label>
                                                        <input type="number" name="cards_per_row" id="cardCardsPerRow" class="form-control form-control-sm id-card-settings-control csm-input" min="1" max="10" value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardGridGap" data-toggle="tooltip" data-placement="top" title="Spacing between cards on the sheet.">Grid Gap</label>
                                                        <input type="number" name="grid_gap_value" id="cardGridGap" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.5) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardCardWidth" data-toggle="tooltip" data-placement="top" title="Width of each rendered card.">Card Width</label>
                                                        <input type="number" name="card_width_value" id="cardCardWidth" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 5.4) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardCardHeight" data-toggle="tooltip" data-placement="top" title="Height of each rendered card.">Card Height</label>
                                                        <input type="number" name="card_height_value" id="cardCardHeight" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 8.4) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardDimensionUnit" data-toggle="tooltip" data-placement="top" title="Controls all layout measurements.">Unit</label>
                                                        <select name="card_dimension_unit" id="cardDimensionUnit" class="form-control form-control-sm id-card-settings-control csm-select">
                                                            <option value="cm" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'cm' ? 'selected' : '' }}>Centimeter (cm)</option>
                                                            <option value="px" {{ old('card_dimension_unit', $cardSettings?->card_dimension_unit ?? 'cm') === 'px' ? 'selected' : '' }}>Pixel (px)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardFrontAlignment" data-toggle="tooltip" data-placement="top" title="Controls the front card content alignment.">Front Alignment</label>
                                                        <select name="card_front_alignment" id="cardFrontAlignment" class="form-control form-control-sm id-card-settings-control csm-select">
                                                            <option value="left" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                            <option value="center" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                            <option value="right" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardBackAlignment" data-toggle="tooltip" data-placement="top" title="Controls the back card content alignment.">Back Alignment</label>
                                                        <select name="card_back_alignment" id="cardBackAlignment" class="form-control form-control-sm id-card-settings-control csm-select">
                                                            <option value="left" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                            <option value="center" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                            <option value="right" {{ old('card_back_alignment', $cardSettings?->card_back_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardFrontPadding" data-toggle="tooltip" data-placement="top" title="Inner spacing inside the front card.">Front Padding</label>
                                                        <input type="number" name="card_front_padding_value" id="cardFrontPadding" class="form-control form-control-sm id-card-settings-control csm-input" min="0" step="0.1" value="{{ old('card_front_padding_value', $cardSettings?->card_front_padding_value ?? 0.8) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardBackPadding" data-toggle="tooltip" data-placement="top" title="Inner spacing inside the back card.">Back Padding</label>
                                                        <input type="number" name="card_back_padding_value" id="cardBackPadding" class="form-control form-control-sm id-card-settings-control csm-input" min="0" step="0.1" value="{{ old('card_back_padding_value', $cardSettings?->card_back_padding_value ?? 0.8) }}">
                                                    </div>
                                                </div>
                                            </div>
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
                                            <div class="row">
                                                <div class="col-12 col-md-4 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardPhotoWidth">Photo Width</label>
                                                        <input type="number" name="card_photo_width_value" id="cardPhotoWidth" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('card_photo_width_value', $cardSettings?->card_photo_width_value ?? 1.8) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardPhotoHeight">Photo Height</label>
                                                        <input type="number" name="card_photo_height_value" id="cardPhotoHeight" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardLogoSize">Logo Size</label>
                                                        <input type="number" name="card_logo_size_value" id="cardLogoSize" class="form-control form-control-sm id-card-settings-control csm-input" min="0.1" step="0.1" value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="id-card-upload-box">
                                                        <label class="id-card-settings-label d-block mb-2" for="cardLogoInput">School Logo</label>
                                                        <input type="file" name="card_logo" id="cardLogoInput" class="form-control form-control-sm">
                                                        <small class="text-muted d-block mt-2">Uploads replace the current logo for this card preset.</small>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="id-card-upload-preview text-center">
                                                        <label class="id-card-settings-label d-block mb-2">Logo Preview</label>
                                                        <img id="cardLogoPreview" src="{{ $currentCardLogoUrl ?? '' }}" alt="Logo preview" class="img-fluid {{ $currentCardLogoUrl ? '' : 'd-none' }}" style="max-height: 96px; object-fit: contain;">
                                                        <div class="small text-muted {{ $currentCardLogoUrl ? 'd-none' : '' }}">No custom logo uploaded.</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="id-card-upload-box">
                                                        <label class="id-card-settings-label d-block mb-2" for="cardPrincipalSignatureInput">Principal Signature</label>
                                                        <input type="file" name="card_principal_signature" id="cardPrincipalSignatureInput" class="form-control form-control-sm" accept="image/png,image/jpeg">
                                                        <small class="text-muted d-block mt-2">Upload PNG, JPG, or JPEG for the principal signature image.</small>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="id-card-upload-preview text-center">
                                                        <label class="id-card-settings-label d-block mb-2">Signature Preview</label>
                                                        <img id="cardPrincipalSignaturePreview" src="{{ $currentCardPrincipalSignatureUrl ?? '' }}" alt="Principal signature preview" class="img-fluid {{ $currentCardPrincipalSignatureUrl ? '' : 'd-none' }}" style="max-height: 96px; object-fit: contain;">
                                                        <div class="small text-muted {{ $currentCardPrincipalSignatureUrl ? 'd-none' : '' }}">No principal signature uploaded.</div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardPhotoFit">Photo Fit</label>
                                                        <select name="card_photo_fit" id="cardPhotoFit" class="form-control form-control-sm id-card-settings-control csm-select">
                                                            <option value="cover" {{ $currentCardPhotoFit === 'cover' ? 'selected' : '' }}>Cover</option>
                                                            <option value="contain" {{ $currentCardPhotoFit === 'contain' ? 'selected' : '' }}>Contain</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4 mb-2">
                                                    <div class="id-card-settings-field">
                                                        <label class="id-card-settings-label" for="cardLogoFit">Logo Fit</label>
                                                        <select name="card_logo_fit" id="cardLogoFit" class="form-control form-control-sm id-card-settings-control csm-select">
                                                            <option value="contain" {{ $currentCardLogoFit === 'contain' ? 'selected' : '' }}>Contain</option>
                                                            <option value="cover" {{ $currentCardLogoFit === 'cover' ? 'selected' : '' }}>Cover</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="idCardTypographyPane" role="tabpanel" aria-labelledby="idCardTypographyTab">
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header id-card-settings-section-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2" style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;"><i class="fas fa-font"></i></span>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold" style="letter-spacing:-0.01em;">Typography &amp; Colors</h6>
                                                    <small class="text-muted d-block">Fine-tune font scale and color tone for the card face.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">School Name</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_school_name_font_size" id="cardSchoolNameFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_school_name_font_size', $cardSettings?->card_school_name_font_size ?? 7.2) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_school_name_text_color" id="cardSchoolNameColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                                        <span id="cardSchoolNameColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Student Details</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_school_detail_font_size" id="cardSchoolDetailFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_school_detail_font_size', $cardSettings?->card_school_detail_font_size ?? 5.4) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_school_detail_text_color" id="cardSchoolDetailColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                                        <span id="cardSchoolDetailColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Slogan</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_slogan_font_size" id="cardSloganFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_slogan_font_size', $cardSettings?->card_slogan_font_size ?? 4.8) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_slogan_text_color" id="cardSloganColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }}">
                                                        <span id="cardSloganColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Card Title</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_title_font_size" id="cardTitleFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_title_font_size', $cardSettings?->card_title_font_size ?? 4.7) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_title_text_color" id="cardTitleColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                                        <span id="cardTitleColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Student Name</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_name_font_size" id="cardNameFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_name_font_size', $cardSettings?->card_name_font_size ?? 7.2) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_name_text_color" id="cardNameColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_name_text_color', $cardSettings?->card_name_text_color ?? '#1e3a5f') }}">
                                                        <span id="cardNameColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_name_text_color', $cardSettings?->card_name_text_color ?? '#1e3a5f') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Student Detail</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <input type="number" name="card_student_detail_font_size" id="cardStudentDetailFontSize" class="form-control form-control-sm id-card-settings-control csm-input" min="1" step="0.1" value="{{ old('card_student_detail_font_size', $cardSettings?->card_student_detail_font_size ?? 8.5) }}">
                                                </div>
                                                <div class="col-12 col-md-5">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_student_detail_text_color" id="cardStudentDetailColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_student_detail_text_color', $cardSettings?->card_student_detail_text_color ?? '#111827') }}">
                                                        <span id="cardStudentDetailColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_student_detail_text_color', $cardSettings?->card_student_detail_text_color ?? '#111827') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Student Detail Alignment</strong>
                                                </div>
                                                <div class="col-12 col-md-3 mb-1 mb-md-0">
                                                    <select name="card_student_detail_alignment" id="cardStudentDetailAlignment" class="form-control form-control-sm id-card-settings-control csm-select">
                                                        <option value="left" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                                                        <option value="center" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'center' ? 'selected' : '' }}>Center</option>
                                                        <option value="right" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'right' ? 'selected' : '' }}>Right</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <hr class="my-3">

                                            <div class="row align-items-center mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Back Notice</strong>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_back_notice_text_color" id="cardBackNoticeColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_back_notice_text_color', $cardSettings?->card_back_notice_text_color ?? '#94a3b8') }}">
                                                        <span id="cardBackNoticeColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_back_notice_text_color', $cardSettings?->card_back_notice_text_color ?? '#94a3b8') }};"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center mt-3 mb-0">
                                                <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                    <strong class="d-block">Footer</strong>
                                                </div>
                                                <div class="col-12 col-md-8">
                                                    <div class="id-card-settings-color-row">
                                                        <input type="color" name="card_footer_text_color" id="cardFooterColor" class="id-card-settings-color-native csm-color-native" value="{{ old('card_footer_text_color', $cardSettings?->card_footer_text_color ?? '#e5e7eb') }}">
                                                        <span id="cardFooterColorPreview" class="id-card-settings-color-preview d-inline-block rounded ml-2" style="background: {{ old('card_footer_text_color', $cardSettings?->card_footer_text_color ?? '#e5e7eb') }};"></span>
                                                    </div>
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
                                    <div class="card mb-2 shadow-sm">
                                        <div class="card-header id-card-settings-section-header">
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-eye"></i></span>
                                                <h6 class="mb-0 font-weight-bold">Visibility</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($visibilityRows as $row)
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input
                                                                type="checkbox"
                                                                class="custom-control-input"
                                                                name="{{ $row['name'] }}"
                                                                id="{{ $row['id'] }}"
                                                                {{ old($row['name'], $cardSettings?->{$row['name']} ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="{{ $row['id'] }}">{{ $row['label'] }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="id-card-settings-note">These settings are saved once and used by search and PDF output.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer id-card-settings-modal-footer card-settings-modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
