@php
    $renderForPdf = $renderForPdf ?? false;
    $layout = $layout ?? [];
    $cardsPerPage = max(1, min(12, (int) ($layout['cardsPerPage'] ?? 8)));
    $cardsPerRow = max(1, min($cardsPerPage, (int) ($layout['cardsPerRow'] ?? 2)));
    $pageRows = max(1, (int) ($layout['pageRows'] ?? ceil($cardsPerPage / $cardsPerRow)));
    $cardWidthMm = $layout['cardWidthMm'] ?? 80;
    $cardHeightMm = $layout['cardHeightMm'] ?? 46;
    $gapXmm = $layout['gapXmm'] ?? 4;
    $gapYmm = $layout['gapYmm'] ?? 4;
    $marginMm = $layout['marginMm'] ?? 8;

    $studentPages = $students->chunk($cardsPerPage);
    $cardTitle = $cardType ?? 'admit_card';
    $isSeatCard = $cardTitle === 'seat_card';
    $cardLabel = $isSeatCard ? 'SEAT CARD' : 'ADMIT CARD';
    $schoolAddress = $setting?->address;
    $examTypeLabel = $examType ? (strtolower($examType) === 'term' ? 'Terminal Exam' : 'Tutorial Exam') : null;
    $examName = $selectedExam?->name;
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
    $cardSchoolNameColor = $cardSettings?->card_school_name_text_color ?? '#ffffff';
    $cardSchoolDetailColor = $cardSettings?->card_school_detail_text_color ?? '#e5e7eb';
    $cardSloganColor = $cardSettings?->card_slogan_text_color ?? '#e5e7eb';
    $cardBorderColors = $cardSettings?->card_border_colors ?? [];
    $cardBorderTransparent = $cardSettings?->card_border_transparent ?? [];
    $cardBorderDefaults = ['school_name' => '#ffffff', 'school_detail' => '#ffffff', 'slogan' => '#ffffff', 'title' => '#ffffff', 'exam_type' => '#ffffff', 'exam_name' => '#fff200', 'vertical_label' => '#16a085'];
    $cardBorderColors = collect($cardBorderDefaults)
        ->mapWithKeys(fn (string $default, string $key) => [$key => data_get($cardBorderTransparent, $key, false)
            ? 'transparent'
            : data_get($cardBorderColors, $key, $default)])
        ->all();
    $cardTitleColor = $cardSettings?->card_title_text_color ?? '#ffffff';
    $cardVerticalLabelColor = $cardSettings?->card_vertical_label_text_color ?? '#16a085';
    $cardPrincipalLabelColor = $cardSettings?->card_principal_label_text_color ?? '#3f3f46';
    $cardNameColor = $cardSettings?->card_name_text_color ?? '#111827';
    $cardExamTypeColor = $cardSettings?->card_exam_type_text_color ?? '#ffffff';
    $cardExamNameColor = $cardSettings?->card_exam_name_text_color ?? '#e5e7eb';
    $cardFooterColor = $cardSettings?->card_footer_text_color ?? '#e5e7eb';
    $cardStudentDetailAlignment = in_array($cardSettings?->card_student_detail_alignment ?? 'left', ['left', 'center', 'right'], true) ? $cardSettings?->card_student_detail_alignment : 'left';
    $cardFrontAlignment = in_array($cardSettings?->card_front_alignment ?? 'center', ['left', 'center', 'right'], true) ? $cardSettings?->card_front_alignment : 'center';
    $cardExamAlignment = match ($cardFrontAlignment) {
        'left' => 'flex-start',
        'right' => 'flex-end',
        default => 'center',
    };
    $cardFrontPadding = $cardSettings?->card_front_padding_value ?? 0.8;
    $cardPhotoWidth = $cardSettings?->card_photo_width_value ?? 1.8;
    $cardPhotoHeight = $cardSettings?->card_photo_height_value ?? 2.7;
    $cardPhotoFit = in_array($cardSettings?->card_photo_fit ?? 'cover', ['cover', 'contain'], true) ? ($cardSettings?->card_photo_fit ?? 'cover') : 'cover';
    $cardLogoSize = $cardSettings?->card_logo_size_value ?? 0.8;
    $cardSignatureSize = $cardSettings?->card_signature_size_value ?? 1.4;
    $cardSchoolNameFontSize = $cardSettings?->card_school_name_font_size ?? 7.2;
    $cardSchoolDetailFontSize = $cardSettings?->card_school_detail_font_size ?? 5.4;
    $cardTitleFontSize = $cardSettings?->card_title_font_size ?? 4.7;
    $cardVerticalLabelFontSize = $cardSettings?->card_vertical_label_font_size ?? 5.2;
    $cardPrincipalLabelFontSize = $cardSettings?->card_principal_label_font_size ?? 5.2;
    $cardNameFontSize = $cardSettings?->card_name_font_size ?? 7.2;
    $cardExamTypeFontSize = $cardSettings?->card_exam_type_font_size ?? 7.4;
    $cardExamNameFontSize = $cardSettings?->card_exam_name_font_size ?? 6.8;
    $cardFooterFontSize = $cardSettings?->card_footer_font_size ?? 4.5;
    $cardStudentDetailFontSize = $cardSettings?->card_student_detail_font_size ?? 8.5;
    $cardStudentDetailColor = $cardSettings?->card_student_detail_text_color ?? '#111827';
    $cardTextPadding = $cardSettings?->card_text_padding_value ?? 0;
    $cardTextMargin = $cardSettings?->card_text_margin_value ?? 0;
    $cardTypographySpacing = $cardSettings?->card_typography_spacing ?? [];
    $cardElementPositions = $cardSettings?->card_element_positions ?? [];
    $cardElementSizes = $cardSettings?->card_element_sizes ?? [];
    $elementPositionVars = static function () use ($cardElementPositions): string {
        $keys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type', 'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'];

        return collect($keys)->map(function (string $key) use ($cardElementPositions): string {
            $position = data_get($cardElementPositions, $key, []);
            $x = (float) data_get($position, 'x', 0);
            $y = (float) data_get($position, 'y', 0);

            return "--admit-card-position-{$key}-x: {$x}mm; --admit-card-position-{$key}-y: {$y}mm;";
        })->implode(' ');
    };
    $textSpacing = static function (string $key) use ($cardTypographySpacing, $cardTextPadding, $cardTextMargin): string {
        $padding = data_get($cardTypographySpacing, "$key.padding", $cardTextPadding);
        $margin = data_get($cardTypographySpacing, "$key.margin", $cardTextMargin);
        $padding = is_array($padding) ? $padding : array_fill_keys(['top', 'right', 'bottom', 'left'], $padding);
        $margin = is_array($margin) ? $margin : array_fill_keys(['top', 'right', 'bottom', 'left'], $margin);

        return collect(['top', 'right', 'bottom', 'left'])->map(function (string $side) use ($key, $padding, $margin): string {
            return "--admit-card-{$key}-padding-{$side}: " . ((float) ($padding[$side] ?? 0)) . "mm; --admit-card-{$key}-margin-{$side}: " . ((float) ($margin[$side] ?? 0)) . "mm;";
        })->implode(' ');
    };

    $resolveImagePath = function (?string $path) use ($renderForPdf) {
        if (!$path || !file_exists(public_path($path))) {
            return null;
        }

        return $renderForPdf ? public_path($path) : asset($path);
    };

    $principalLabel = $setting?->principal_designation ?: 'Principal';
    $principalSignaturePath = $resolveImagePath($cardSettings?->card_principal_signature ?? null);

    $logoPath = $resolveImagePath($cardSettings?->card_logo ?? null)
        ?? $resolveImagePath($setting?->logo ?? null);
@endphp

    <div class="admit-card-pages" style="--admit-card-theme-bg: {{ $cardThemeBackground }}; --admit-card-theme-accent: {{ $cardThemeAccent }}; --admit-card-school-name-color: {{ $cardSchoolNameColor }}; --admit-card-school-detail-color: {{ $cardSchoolDetailColor }}; --admit-card-slogan-color: {{ $cardSloganColor }}; --admit-card-title-color: {{ $cardTitleColor }}; --admit-card-vertical-label-color: {{ $cardVerticalLabelColor }}; --admit-card-principal-label-color: {{ $cardPrincipalLabelColor }}; --admit-card-name-color: {{ $cardNameColor }}; --admit-card-exam-type-color: {{ $cardExamTypeColor }}; --admit-card-exam-name-color: {{ $cardExamNameColor }}; --admit-card-footer-color: {{ $cardFooterColor }}; --admit-card-school-name-border-color: {{ data_get($cardBorderColors, 'school_name', '#ffffff') }}; --admit-card-school-detail-border-color: {{ data_get($cardBorderColors, 'school_detail', '#ffffff') }}; --admit-card-slogan-border-color: {{ data_get($cardBorderColors, 'slogan', '#ffffff') }}; --admit-card-title-border-color: {{ data_get($cardBorderColors, 'title', '#ffffff') }}; --admit-card-exam-type-border-color: {{ data_get($cardBorderColors, 'exam_type', '#ffffff') }}; --admit-card-exam-name-border-color: {{ data_get($cardBorderColors, 'exam_name', '#fff200') }}; --admit-card-vertical-label-border-color: {{ data_get($cardBorderColors, 'vertical_label', '#16a085') }}; --admit-card-text-padding: {{ $cardTextPadding }}mm; --admit-card-text-margin: {{ $cardTextMargin }}mm; --admit-card-element-exam-name-width: {{ filled(data_get($cardElementSizes, 'exam_name.width')) ? ((float) data_get($cardElementSizes, 'exam_name.width')) . 'mm' : 'auto' }}; --admit-card-element-exam-name-height: {{ filled(data_get($cardElementSizes, 'exam_name.height')) ? ((float) data_get($cardElementSizes, 'exam_name.height')) . 'mm' : 'auto' }}; {!! $elementPositionVars() !!} {!! $textSpacing('school_name') !!} {!! $textSpacing('school_detail') !!} {!! $textSpacing('slogan') !!} {!! $textSpacing('title') !!} {!! $textSpacing('name') !!} {!! $textSpacing('exam_type') !!} {!! $textSpacing('exam_name') !!} {!! $textSpacing('student_detail') !!} {!! $textSpacing('footer') !!} {!! $textSpacing('logo') !!} {!! $textSpacing('photo') !!} {!! $textSpacing('signature') !!} {!! $textSpacing('vertical_label') !!} --admit-card-student-detail-align: {{ $cardStudentDetailAlignment }}; --admit-card-student-detail-font-size: {{ $cardStudentDetailFontSize }}pt; --admit-card-student-detail-color: {{ $cardStudentDetailColor }}; --admit-card-front-align: {{ $cardFrontAlignment }}; --admit-card-exam-align: {{ $cardExamAlignment }}; --admit-card-front-padding: {{ $cardFrontPadding }}mm; --admit-card-photo-width: {{ $cardPhotoWidth }}cm; --admit-card-photo-height: {{ $cardPhotoHeight }}cm; --admit-card-photo-fit: {{ $cardPhotoFit }}; --admit-card-logo-size: {{ $cardLogoSize }}cm; --admit-card-school-name-font-size: {{ $cardSchoolNameFontSize }}pt; --admit-card-school-detail-font-size: {{ $cardSchoolDetailFontSize }}pt; --admit-card-title-font-size: {{ $cardTitleFontSize }}pt; --admit-card-vertical-label-font-size: {{ $cardVerticalLabelFontSize }}pt; --admit-card-principal-label-font-size: {{ $cardPrincipalLabelFontSize }}pt; --admit-card-name-font-size: {{ $cardNameFontSize }}pt; --admit-card-exam-type-font-size: {{ $cardExamTypeFontSize }}pt; --admit-card-exam-name-font-size: {{ $cardExamNameFontSize }}pt; --admit-card-footer-font-size: {{ $cardFooterFontSize }}pt;">
    @foreach($studentPages as $pageStudents)
        <div
            class="admit-card-page"
            style="grid-template-columns: repeat({{ $cardsPerRow }}, {{ $cardWidthMm }}mm); gap: {{ $gapYmm }}mm {{ $gapXmm }}mm;"
        >
            <div class="admit-card-page__header">
                <span class="admit-card-page__header-label">Card Type</span>
                <span class="admit-card-page__header-value">{{ $cardLabel }}</span>
            </div>

            @foreach($pageStudents as $student)
                @php
                    $ai = $student->academicInformations->first();

                    $placeholder = $student->gender == \App\Models\Student::FEMALE
                        ? 'assets/img/female-placeholder.png'
                        : 'assets/img/male-placeholder.png';

                    $photoPath = null;
                    if ($student->image && file_exists(public_path($student->image))) {
                        $photoPath = $renderForPdf ? public_path($student->image) : asset($student->image);
                    } else {
                        $photoPath = $renderForPdf ? public_path($placeholder) : asset($placeholder);
                    }
                @endphp

                @include('pages.admit-seat-cards._card', [
                    'cardWidthMm' => $cardWidthMm,
                    'cardHeightMm' => $cardHeightMm,
                    'cardWidthStyle' => $cardWidthMm . 'mm',
                    'cardHeightStyle' => $cardHeightMm . 'mm',
                    'cardLabel' => $cardLabel,
                    'schoolName' => $setting?->name ?? 'School Name',
                    'schoolAddress' => $schoolAddress,
                    'slogan' => $setting?->slogan,
                    'examTypeLabel' => $examTypeLabel,
                    'examName' => $examName,
                    'studentName' => $student->full_name_en,
                    'studentFatherName' => $student->father_name,
                    'studentMotherName' => $student->mother_name,
                    'studentCid' => $student->student_cid,
                    'studentRoll' => $ai?->roll,
                    'studentClass' => $ai?->schoolClass?->name_en ?? '—',
                    'studentSection' => $ai?->section?->name_en ?? '—',
                    'studentSession' => $ai?->academicSession?->name_en ?? '—',
                    'logoPath' => $logoPath,
                    'photoPath' => $photoPath,
                    'photoAlt' => $student->full_name_en,
                    'principalLabel' => $principalLabel,
                    'principalSignaturePath' => $principalSignaturePath,
                    'signatureSize' => $cardSignatureSize,
                    'principalLabelFontSize' => $cardPrincipalLabelFontSize,
                    'principalLabelColor' => $cardPrincipalLabelColor,
                    'showLogoFront' => $cardSettings?->card_show_logo_front ?? true,
                    'showSchoolDetailFront' => $cardSettings?->card_show_school_detail_front ?? true,
                    'showSloganFront' => $cardSettings?->card_show_slogan_front ?? true,
                    'showTitleFront' => $cardSettings?->card_show_title_front ?? true,
                    'showPhotoFront' => $cardSettings?->card_show_photo_front ?? true,
                    'showFatherNameFront' => $cardSettings?->card_show_father_name_front ?? false,
                    'showMotherNameFront' => $cardSettings?->card_show_mother_name_front ?? false,
                    'showStudentNameLabelFront' => $cardSettings?->card_show_student_name_label_front ?? false,
                    'showRollFront' => $cardSettings?->card_show_roll_front ?? true,
                    'showClassFront' => $cardSettings?->card_show_class_front ?? true,
                    'showSectionFront' => $cardSettings?->card_show_section_front ?? true,
                    'showSessionFront' => $cardSettings?->card_show_session_front ?? true,
                    'showVerticalLabelFront' => $cardSettings?->card_show_vertical_label_front ?? false,
                    'examNameBadgeFront' => $cardSettings?->card_exam_name_badge_front ?? false,
                    'showExamTypeFront' => $cardSettings?->card_show_exam_type_front ?? true,
                    'showExamNameFront' => $cardSettings?->card_show_exam_name_front ?? true,
                    'showFooterFront' => $cardSettings?->card_show_footer_front ?? true,
                    'studentFieldOrder' => $cardSettings?->card_student_field_order ?? [],
                    'cardElementPositions' => $cardSettings?->card_element_positions ?? [],
                    'cardElementSizes' => $cardSettings?->card_element_sizes ?? [],
                    'footerLines' => array_values(array_unique(array_filter([
                        $setting?->contact_number_1,
                        $setting?->whatsapp_number,
                    ]))),
                ])
            @endforeach
        </div>
    @endforeach
</div>
