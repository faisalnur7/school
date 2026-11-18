@php
    $renderForPdf = $renderForPdf ?? false;
    $employeeMode = $employeeMode ?? false;
    $employees = $employees ?? collect();
    $layout = $layout ?? [];
    $cardsPerPage = max(1, min(12, (int) ($layout['cardsPerPage'] ?? 4)));
    $cardsPerRow = max(1, min($cardsPerPage, (int) ($layout['cardsPerRow'] ?? 2)));
    $pageRows = max(1, (int) ($layout['pageRows'] ?? ceil($cardsPerPage / $cardsPerRow)));
    $cardWidthCm = $layout['cardWidthCm'] ?? 5.4;
    $cardHeightCm = $layout['cardHeightCm'] ?? 8.56;
    $gapCm = $layout['gridGapCm'] ?? 0.5;

    $studentPages = ($employeeMode ? $employees : $students)->chunk($cardsPerPage);
    $cardType = $cardType ?? 'id_card';
    $isLibraryCard = $cardType === 'library_card';
    $isAdmitOrSeatCard = in_array($cardType, ['admit_card', 'seat_card'], true);
    $cardSettings = $cardSettings ?? null;
    $cardIsTransparent = (bool) ($cardSettings?->card_is_transparent ?? false);
    $cardColorType = in_array($cardSettings?->card_color_type ?? 'gradient', ['gradient', 'solid'], true)
        ? ($cardSettings?->card_color_type ?? 'gradient')
        : 'gradient';
    $cardGradient1 = $cardSettings?->card_color_gradient_1 ?? '#1e3a5f';
    $cardGradient2 = $cardSettings?->card_color_gradient_2 ?? '#2563eb';
    $cardSolidColor = $cardSettings?->card_solid_color ?? '#1e3a5f';
    $cardThemeAccent = $cardIsTransparent
        ? 'transparent'
        : ($cardColorType === 'solid' ? $cardSolidColor : $cardGradient1);
    $cardThemeBackground = $cardIsTransparent
        ? 'transparent'
        : ($cardColorType === 'solid'
        ? $cardSolidColor
        : "linear-gradient(135deg, {$cardGradient1}, {$cardGradient2})");
    if ($renderForPdf) {
        $cardThemeBackground = $cardIsTransparent ? '#ffffff' : ($cardColorType === 'solid' ? $cardSolidColor : $cardGradient1);
        $cardThemeAccent = $cardThemeBackground;
    }
    $cardSchoolNameColor = $cardSettings?->card_school_name_text_color ?? '#ffffff';
    $cardSchoolDetailColor = $cardSettings?->card_school_detail_text_color ?? '#e5e7eb';
    $cardSloganColor = $cardSettings?->card_slogan_text_color ?? '#e5e7eb';
    $cardBackNoticeColor = $cardSettings?->card_back_notice_text_color ?? '#94a3b8';
    $cardFooterColor = $cardSettings?->card_footer_text_color ?? '#e5e7eb';
    $cardTitleColor = $cardSettings?->card_title_text_color ?? '#ffffff';
    $cardStudentDetailAlignment = in_array($cardSettings?->card_student_detail_alignment ?? 'left', ['left', 'center', 'right'], true) ? $cardSettings?->card_student_detail_alignment : 'left';
    $cardStudentDetailAlignCss = $cardStudentDetailAlignment === 'center'
        ? 'center'
        : ($cardStudentDetailAlignment === 'right' ? 'flex-end' : 'flex-start');
    $cardFrontAlignment = in_array($cardSettings?->card_front_alignment ?? 'center', ['left', 'center', 'right'], true) ? $cardSettings?->card_front_alignment : 'center';
    $cardBackAlignment = in_array($cardSettings?->card_back_alignment ?? 'center', ['left', 'center', 'right'], true) ? $cardSettings?->card_back_alignment : 'center';
    // Padding / photo / logo are entered in the same unit as the card size; CSS below is always cm.
    $layoutUnit = ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm';
    $toCm = fn ($value) => round($layoutUnit === 'px' ? ((float) $value) / 96 * 2.54 : (float) $value, 3);
    $cardFrontPadding = $toCm($cardSettings?->card_front_padding_value ?? 0.8);
    $cardBackPadding = $toCm($cardSettings?->card_back_padding_value ?? 0.8);
    $cardFrontHeaderPadding = $toCm($cardSettings?->card_front_header_padding_value ?? 0.3);
    $cardBackHeaderPadding = $toCm($cardSettings?->card_back_header_padding_value ?? 0.3);
    $cardPhotoWidth = $toCm($cardSettings?->card_photo_width_value ?? 1.8);
    $cardPhotoHeight = $toCm($cardSettings?->card_photo_height_value ?? 2.7);
    $cardLogoSize = $toCm($cardSettings?->card_logo_size_value ?? 0.8);
    // Dragged offsets (mm) saved from the settings preview. Inline left/top keeps them PDF-safe.
    $cardPositions = is_array($cardSettings?->card_element_positions ?? null) ? $cardSettings->card_element_positions : [];
    $pos = function (string $key) use ($cardPositions): string {
        $x = (float) data_get($cardPositions, "$key.x", 0);
        $y = (float) data_get($cardPositions, "$key.y", 0);

        return ($x !== 0.0 || $y !== 0.0) ? "position: relative; left: {$x}mm; top: {$y}mm;" : '';
    };
    $cardNameColor = $cardSettings?->card_name_text_color ?: $cardThemeAccent;
    $cardDetailColor = $cardSettings?->card_student_detail_text_color ?: '#111827';
    $cardDetailFontSize = max(1.0, (float) ($cardSettings?->card_student_detail_font_size ?: 4.6));
    $cardDetailLabelFontSize = round(max(1.0, $cardDetailFontSize * 0.9), 2);
    $cardSchoolNameFontSize = $cardSettings?->card_school_name_font_size ?? 7.2;
    $cardSchoolDetailFontSize = $cardSettings?->card_school_detail_font_size ?? 5.4;
    $cardSloganFontSize = $cardSettings?->card_slogan_font_size ?? 4.8;
    $cardTitleFontSize = $cardSettings?->card_title_font_size ?? 4.7;
    $cardNameFontSize = $cardSettings?->card_name_font_size ?? 7.2;
    $frontTitle = $employeeMode
        ? 'EMPLOYEE ID'
        : match ($cardType) {
            'library_card' => 'LIBRARY CARD',
            'admit_card' => 'ADMIT CARD',
            'seat_card' => 'SEAT CARD',
            default => 'STUDENT ID',
        };
    $backBadge = match ($cardType) {
        'library_card' => 'LIBRARY',
        'admit_card' => 'ADMIT',
        'seat_card' => 'SEAT',
        default => 'BACK',
    };
    $backSectionTitle = $isLibraryCard || $isAdmitOrSeatCard ? 'Student Details' : 'Parent / Guardian';

    $resolveImagePath = function (?string $path) use ($renderForPdf) {
        if (!$path || !file_exists(public_path($path))) {
            return null;
        }

        return $renderForPdf ? public_path($path) : asset($path);
    };

    $fitTextSize = function ($value, float $base, float $min, int $threshold = 22, float $step = 0.08): float {
        $values = is_array($value) ? $value : [$value];
        $longest = 0;

        foreach ($values as $item) {
            $clean = trim(strip_tags((string) $item));
            $longest = max($longest, mb_strlen($clean));
        }

        if ($longest <= $threshold) {
            return $base;
        }

        $reduction = min(($longest - $threshold) * $step, $base - $min);

        return round(max($min, $base - $reduction), 2);
    };

    $principalLabel = $setting?->principal_designation ?: 'Principal';
    $showPrincipalSignature = in_array($cardType, ['id_card', 'library_card'], true);
    $showBackStudentDetails = $cardSettings?->card_show_back_student_details ?? true;
    $showBackSchoolContact = $cardSettings?->card_show_back_school_contact ?? true;
    $showBackQr = $cardSettings?->card_show_back_qr ?? true;
    $showBackSignature = $cardSettings?->card_show_back_signature ?? true;
    $photoFit = in_array($cardSettings?->card_photo_fit ?? 'cover', ['cover', 'contain'], true) ? ($cardSettings?->card_photo_fit ?? 'cover') : 'cover';
    $logoFit = in_array($cardSettings?->card_logo_fit ?? 'contain', ['cover', 'contain'], true) ? ($cardSettings?->card_logo_fit ?? 'contain') : 'contain';
    $frontSchoolNameFontSize = $fitTextSize($setting?->name ?? 'School Name', (float) $cardSchoolNameFontSize, 5.2, 20, 0.08);
    $frontSchoolDetailFontSize = $fitTextSize($setting?->address ?? ($setting?->slogan ?? ''), (float) $cardSchoolDetailFontSize, 4.1, 30, 0.05);
    $frontSloganFontSize = $fitTextSize($setting?->slogan ?? '', (float) $cardSloganFontSize, 3.8, 26, 0.04);
    $frontTitleFontSize = $fitTextSize($frontTitle, (float) $cardTitleFontSize, 4.0, 14, 0.05);
    $backTitleFontSize = $fitTextSize([$backSectionTitle, $backBadge], (float) $cardTitleFontSize, 4.0, 18, 0.04);
    $frontNameFontSize = (float) $cardNameFontSize;
    $backValueFontSize = (float) $cardSchoolDetailFontSize;
    $logoPath = $resolveImagePath($cardSettings?->card_logo ?? null)
        ?? $resolveImagePath($setting?->logo ?? null);
    $principalSignaturePath = $resolveImagePath($cardSettings?->card_principal_signature ?? null);

    if (!$logoPath) {
        $logoPath = null;
    }
@endphp

<div class="id-card-pages" data-render-for-pdf="{{ $renderForPdf ? '1' : '0' }}" style="--id-card-width: {{ $cardWidthCm }}cm; --id-card-height: {{ $cardHeightCm }}cm; --id-card-gap: {{ $gapCm }}cm; --card-theme-bg: {{ $cardThemeBackground }}; --card-theme-accent: {{ $cardThemeAccent }}; --id-card-school-name-color: {{ $cardSchoolNameColor }}; --id-card-school-detail-color: {{ $cardSchoolDetailColor }}; --id-card-slogan-color: {{ $cardSloganColor }}; --id-card-back-notice-color: {{ $cardBackNoticeColor }}; --id-card-footer-color: {{ $cardFooterColor }}; --id-card-title-color: {{ $cardTitleColor }}; --id-card-student-detail-align: {{ $cardStudentDetailAlignCss }}; --id-card-student-detail-text-align: {{ $cardStudentDetailAlignment }}; --id-card-front-align: {{ $cardFrontAlignment }}; --id-card-back-align: {{ $cardBackAlignment }}; --id-card-front-padding: {{ $cardFrontPadding }}cm; --id-card-front-header-padding: {{ $cardFrontHeaderPadding }}cm; --id-card-back-header-padding: {{ $cardBackHeaderPadding }}cm; --id-card-back-padding: {{ $cardBackPadding }}cm; --id-card-photo-width: {{ $cardPhotoWidth }}cm; --id-card-photo-height: {{ $cardPhotoHeight }}cm; --id-card-photo-fit: {{ $photoFit }}; --id-card-logo-size: {{ $cardLogoSize }}cm; --id-card-logo-fit: {{ $logoFit }}; --id-card-school-name-font-size: {{ $frontSchoolNameFontSize }}pt; --id-card-school-detail-font-size: {{ $frontSchoolDetailFontSize }}pt; --id-card-slogan-font-size: {{ $frontSloganFontSize }}pt; --id-card-title-font-size: {{ $frontTitleFontSize }}pt; --id-card-back-title-font-size: {{ $backTitleFontSize }}pt; --id-card-back-value-font-size: {{ $backValueFontSize }}pt; --id-card-back-title-color: {{ $cardIsTransparent ? $cardTitleColor : $cardThemeAccent }}; --id-card-name-font-size: {{ $frontNameFontSize }}pt; --id-card-name-color: {{ $cardNameColor }}; --id-card-student-detail-color: {{ $cardDetailColor }}; --id-card-detail-font-size: {{ $cardDetailFontSize }}pt; --id-card-detail-label-font-size: {{ $cardDetailLabelFontSize }}pt;">
    @foreach($studentPages as $pageIndex => $pageStudents)
        <div class="id-card-page" style="grid-template-columns: repeat({{ $cardsPerRow }}, max-content); gap: {{ $gapCm }}cm {{ $gapCm }}cm;">
            @foreach($pageStudents as $student)
                @php
                    $ai = $employeeMode ? null : $student->academicInformations->first();
                    $displayName = $employeeMode ? $student->name : $student->full_name_en;
                    $displayId = $employeeMode ? $student->employee_id : $student->student_cid;

                    $placeholder = !$employeeMode && $student->gender == \App\Models\Student::FEMALE
                        ? 'assets/img/female-placeholder.png'
                        : 'assets/img/male-placeholder.png';

                    $photoPath = null;
                    $photoSource = $employeeMode ? $student->photo : $student->image;
                    if ($photoSource && file_exists(public_path($photoSource))) {
                        $photoPath = $renderForPdf ? public_path($photoSource) : asset($photoSource);
                    } else {
                        $photoPath = $renderForPdf ? public_path($placeholder) : asset($placeholder);
                    }

                    $frontNameFontSize = $fitTextSize($displayName ?? 'Employee Name', (float) $cardNameFontSize, 5.4, 20, 0.08);
                    $frontSchoolNameFontSize = $fitTextSize($setting?->name ?? 'School Name', (float) $cardSchoolNameFontSize, 5.2, 20, 0.08);
                    $frontSchoolDetailFontSize = $fitTextSize($setting?->address ?? ($setting?->slogan ?? ''), (float) $cardSchoolDetailFontSize, 4.1, 30, 0.05);
                    $frontSloganFontSize = $fitTextSize($setting?->slogan ?? '', (float) $cardSloganFontSize, 3.8, 26, 0.04);
                    $frontTitleFontSize = $fitTextSize($frontTitle, (float) $cardTitleFontSize, 4.0, 14, 0.05);
                    $backTitleFontSize = $fitTextSize([$backSectionTitle, $backBadge], (float) $cardTitleFontSize, 4.0, 18, 0.04);

                    $backValues = [];
                    if ($showBackStudentDetails) {
                        $backValues = array_merge($backValues, [
                            $displayName,
                            $displayId,
                            $ai?->schoolClass?->name_en ?? '',
                            $ai?->section?->name_en ?? '',
                            $ai?->roll ?? '',
                            $ai?->academicSession?->name_en ?? '',
                            $student->present_address ?? '',
                        ]);
                    }

                    if ($showBackSchoolContact) {
                        $backValues = array_merge($backValues, [
                            $setting?->address ?? '',
                            implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])),
                            $setting?->whatsapp_number ?? '',
                            $setting?->email ?? '',
                            $setting?->website ?? '',
                        ]);
                    }

                    // Body text on the back follows the Student Detail size (same rule as the preview and the PDF).
                    $backValueFontSize = $fitTextSize($backValues, (float) $cardDetailFontSize, 3.6, 26, 0.05);
                    $backLabelFontSize = round(max(1.0, $backValueFontSize * 0.9), 2);
                @endphp

                <div class="id-card-pair" style="gap: {{ $gapCm }}cm; --id-card-school-name-font-size: {{ $frontSchoolNameFontSize }}pt; --id-card-school-detail-font-size: {{ $frontSchoolDetailFontSize }}pt; --id-card-slogan-font-size: {{ $frontSloganFontSize }}pt; --id-card-title-font-size: {{ $frontTitleFontSize }}pt; --id-card-back-title-font-size: {{ $backTitleFontSize }}pt; --id-card-back-value-font-size: {{ $backValueFontSize }}pt; --id-card-back-label-font-size: {{ $backLabelFontSize }}pt; --id-card-name-font-size: {{ $frontNameFontSize }}pt;">
                    <div class="id-card" style="width: {{ $cardWidthCm }}cm; height: {{ $cardHeightCm }}cm;">
                        <div class="id-card__header id-card__header--front">
                            @if(($cardSettings?->card_show_logo_front ?? true) && $logoPath)
                                <img src="{{ $logoPath }}" class="id-card__logo" alt="Logo" style="width: {{ $cardLogoSize }}cm; height: {{ $cardLogoSize }}cm; {{ $pos('front_logo') }}">
                            @endif
                            <div class="id-card__school-name" style="{{ $pos('front_school_name') }}">{{ $setting?->name ?? 'School Name' }}</div>
                            @if(($cardSettings?->card_show_school_detail_front ?? false) && $setting?->address)
                                <div class="id-card__school-detail" style="{{ $pos('front_school_detail') }}">{{ $setting->address }}</div>
                            @endif
                            @if(($cardSettings?->card_show_slogan_front ?? true) && $setting?->slogan)
                                <div class="id-card__slogan" style="{{ $pos('front_slogan') }}">{{ $setting->slogan }}</div>
                            @endif
                            @if($cardSettings?->card_show_title_front ?? true)
                                <div class="id-card__label-badge" style="{{ $pos('front_title') }}">{{ $frontTitle }}</div>
                            @endif
                        </div>

                        <div class="id-card__front-body">
                            @if($cardSettings?->card_show_photo_front ?? true)
                                <img src="{{ $photoPath }}" class="id-card__photo" alt="{{ $displayName }}" style="{{ $pos('front_photo') }}">
                            @endif

                            <div class="id-card__info">
                                <div class="id-card__name" style="{{ $pos('front_name') }}">{{ $displayName }}</div>
                                @if(!$employeeMode && $student->full_name_bn)
                                    <div class="id-card__name-bn">{{ $student->full_name_bn }}</div>
                                @endif

                                <div class="id-card__divider"></div>

                                <div class="id-card__rows" style="{{ $pos('front_details') }}">
                                    <div class="id-card__row">
                                        <span class="id-card__lbl">ID</span>
                                        <span class="id-card__val">{{ $displayId }}</span>
                                    </div>
                                    @if($employeeMode)
                                        <div class="id-card__row"><span class="id-card__lbl">Role</span><span class="id-card__val">{{ $student->designation?->name ?? '—' }}</span></div>
                                        <div class="id-card__row"><span class="id-card__lbl">Dept.</span><span class="id-card__val">{{ is_string($student->department) ? $student->department : ($student->department?->name ?? '—') }}</span></div>
                                        <div class="id-card__row"><span class="id-card__lbl">Type</span><span class="id-card__val">{{ ucfirst($student->employee_type) }}</span></div>
                                        @if($student->joining_date)<div class="id-card__row"><span class="id-card__lbl">Joined</span><span class="id-card__val">{{ $student->joining_date->format('d M Y') }}</span></div>@endif
                                        @if($student->phone)<div class="id-card__row"><span class="id-card__lbl">Phone</span><span class="id-card__val">{{ $student->phone }}</span></div>@endif
                                    @elseif($ai)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Class</span>
                                            <span class="id-card__val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span>
                                        </div>
                                        @if($ai->roll)
                                            <div class="id-card__row">
                                                <span class="id-card__lbl">Roll</span>
                                                <span class="id-card__val">{{ $ai->roll }}</span>
                                            </div>
                                        @endif
                                        @if($ai->group)
                                            <div class="id-card__row">
                                                <span class="id-card__lbl">Group</span>
                                                <span class="id-card__val">{{ $ai->group->name_en }}</span>
                                            </div>
                                        @endif
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Session</span>
                                            <span class="id-card__val">{{ $ai->academicSession?->name_en ?? '—' }}</span>
                                        </div>
                                    @endif
                                    @if(!$employeeMode && $student->date_of_birth)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">DOB</span>
                                            <span class="id-card__val">{{ $student->date_of_birth->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if(!$employeeMode && $student->blood_group)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Blood</span>
                                            <span class="id-card__val id-card__blood">{{ $student->blood_group_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($cardSettings?->card_show_footer_front ?? true)
                            <div class="id-card__footer id-card__footer--front" style="{{ $pos('front_footer') }}">
                            @if($setting?->contact_number_1)
                                <span>{{ $renderForPdf ? 'Tel:' : '📞' }} {{ $setting->contact_number_1 }}</span>
                            @endif
                            @if($setting?->contact_number_2)
                                <span>{{ $renderForPdf ? 'Tel:' : '📞' }} {{ $setting->contact_number_2 }}</span>
                            @endif
                            @if($setting?->website)
                                <span>{{ $renderForPdf ? 'Web:' : '🌐' }} {{ $setting->website }}</span>
                            @endif
                        </div>
                            @endif
                    </div>

                    <div class="id-card id-card--back" style="width: {{ $cardWidthCm }}cm; height: {{ $cardHeightCm }}cm;">
                        <div class="id-card__header id-card__header--back">
                            @if(($cardSettings?->card_show_logo_back ?? true) && $logoPath)
                                <img src="{{ $logoPath }}" class="id-card__logo" alt="Logo" style="width: {{ $cardLogoSize }}cm; height: {{ $cardLogoSize }}cm; {{ $pos('back_logo') }}">
                            @endif
                            <div class="id-card__school-name" style="{{ $pos('back_school_name') }}">{{ $setting?->name ?? 'School Name' }}</div>
                            @if(($cardSettings?->card_show_slogan_back ?? true) && $setting?->slogan)
                                <div class="id-card__slogan" style="{{ $pos('back_slogan') }}">{{ $setting->slogan }}</div>
                            @endif
                            @if($cardSettings?->card_show_title_back ?? true)
                                <div class="id-card__label-badge" style="{{ $pos('back_title') }}">{{ $backBadge }}</div>
                            @endif
                        </div>

                        <div class="id-card__back-body">
                            @if($showBackStudentDetails)
                            <div class="id-card__back-section id-card__back-section--student-details" style="{{ $pos('back_details') }}">
                                <div class="id-card__back-title">{{ $backSectionTitle }}</div>
                                @if($employeeMode)
                                    <div class="id-card__back-row"><span class="id-card__lbl">Name</span><span class="id-card__val">{{ $displayName }}</span></div>
                                    <div class="id-card__back-row"><span class="id-card__lbl">ID</span><span class="id-card__val">{{ $displayId }}</span></div>
                                    @if($student->designation)<div class="id-card__back-row"><span class="id-card__lbl">Role</span><span class="id-card__val">{{ $student->designation->name }}</span></div>@endif
                                    @if($student->department)<div class="id-card__back-row"><span class="id-card__lbl">Dept.</span><span class="id-card__val">{{ is_string($student->department) ? $student->department : $student->department->name }}</span></div>@endif
                                    <div class="id-card__back-row"><span class="id-card__lbl">Type</span><span class="id-card__val">{{ ucfirst($student->employee_type) }}</span></div>
                                    @if($student->joining_date)<div class="id-card__back-row"><span class="id-card__lbl">Joined</span><span class="id-card__val">{{ $student->joining_date->format('d M Y') }}</span></div>@endif
                                    @if($student->phone)<div class="id-card__back-row"><span class="id-card__lbl">Phone</span><span class="id-card__val">{{ $student->phone }}</span></div>@endif
                                @elseif($isLibraryCard || $isAdmitOrSeatCard)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Name</span>
                                        <span class="id-card__val">{{ $student->full_name_en }}</span>
                                    </div>
                                    @if($student->student_cid)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">ID</span>
                                            <span class="id-card__val">{{ $student->student_cid }}</span>
                                        </div>
                                    @endif
                                    @if($ai)
                                        @if($ai->schoolClass)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Class</span>
                                                <span class="id-card__val">{{ $ai->schoolClass?->name_en ?? '—' }}@if($ai->section) / {{ $ai->section->name_en }}@endif</span>
                                            </div>
                                        @endif
                                        @if($ai->section)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Section</span>
                                                <span class="id-card__val">{{ $ai->section->name_en }}</span>
                                            </div>
                                        @endif
                                        @if($ai->roll)
                                            <div class="id-card__back-row">
                                                <span class="id-card__lbl">Roll</span>
                                                <span class="id-card__val">{{ $ai->roll }}</span>
                                            </div>
                                        @endif
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Session</span>
                                            <span class="id-card__val">{{ $ai->academicSession?->name_en ?? '—' }}</span>
                                        </div>
                                    @endif
                                @else
                                    @if($student->father_name)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Father</span>
                                            <span class="id-card__val">{{ $student->father_name }}</span>
                                        </div>
                                    @endif
                                    @if($student->mother_name)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Mother</span>
                                            <span class="id-card__val">{{ $student->mother_name }}</span>
                                        </div>
                                    @endif
                                    @if($student->father_phone || $student->mother_phone)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Contact</span>
                                            <span class="id-card__val">{{ implode(', ', array_filter([$student->father_phone, $student->mother_phone])) }}</span>
                                        </div>
                                    @endif
                                    @if($student->present_address)
                                        <div class="id-card__back-row">
                                            <span class="id-card__lbl">Address</span>
                                            <span class="id-card__val">{{ Str::limit($student->present_address, 46) }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            @endif

                            @if($showBackSchoolContact)
                            <div class="id-card__back-section id-card__back-section--school-contact" style="{{ $pos('back_contact') }}">
                                <div class="id-card__back-title">{{ $isLibraryCard ? 'Library / School Contact' : ($isAdmitOrSeatCard ? 'School / Exam Contact' : 'School Contact') }}</div>
                                @if(($cardSettings?->card_show_school_detail_back ?? true) && $setting?->address)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Address</span>
                                        <span class="id-card__val">{{ Str::limit($setting->address, 52) }}</span>
                                    </div>
                                @endif
                                @if($setting?->contact_number_1 || $setting?->contact_number_2)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Contact</span>
                                        <span class="id-card__val">{{ implode(', ', array_filter([$setting?->contact_number_1, $setting?->contact_number_2])) }}</span>
                                    </div>
                                @endif
                                @if($setting?->whatsapp_number)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">WhatsApp</span>
                                        <span class="id-card__val">{{ $setting->whatsapp_number }}</span>
                                    </div>
                                @endif
                                @if($setting?->email)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Email</span>
                                        <span class="id-card__val">{{ $setting->email }}</span>
                                    </div>
                                @endif
                                @if($setting?->website)
                                    <div class="id-card__back-row">
                                        <span class="id-card__lbl">Web</span>
                                        <span class="id-card__val">{{ $setting->website }}</span>
                                    </div>
                                @endif
                            </div>
                            @endif

                            @if($showBackQr && $setting?->whatsapp_qr && file_exists(public_path($setting->whatsapp_qr)))
                                <div style="display:flex;justify-content:center; {{ $pos('back_qr') }}">
                                    <img src="{{ $renderForPdf ? public_path($setting->whatsapp_qr) : asset($setting->whatsapp_qr) }}" class="id-card__qr" alt="WhatsApp QR">
                                </div>
                            @endif

                            @if($cardSettings?->card_show_back_notice ?? true)
                                <div class="id-card__back-notice" style="{{ $pos('back_notice') }}">If found, please return to the school.</div>
                            @endif

                            @if($showBackSignature && $showPrincipalSignature)
                                <div class="id-card__signature{{ $principalSignaturePath ? ' id-card__signature--image' : '' }}" style="{{ $pos('back_signature') }}">
                                    @if($principalSignaturePath)
                                        <img src="{{ $principalSignaturePath }}" alt="Principal signature" class="id-card__signature-image">
                                    @endif
                                    <div class="id-card__signature-line"></div>
                                    <div class="id-card__signature-label">{{ $principalLabel }}</div>
                                </div>
                            @endif
                        </div>

                        @if($cardSettings?->card_show_footer_back ?? true)
                            <div class="id-card__footer id-card__footer--back" style="{{ $pos('back_footer') }}">
                            @if($setting?->whatsapp_number)
                                <span>{{ $renderForPdf ? 'WhatsApp:' : '📱' }} {{ $setting->whatsapp_number }}</span>
                            @endif
                        </div>
                            @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
