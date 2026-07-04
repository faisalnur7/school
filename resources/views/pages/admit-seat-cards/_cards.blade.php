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
    $cardTitleColor = $cardSettings?->card_title_text_color ?? '#ffffff';
    $cardNameColor = $cardSettings?->card_name_text_color ?? '#111827';
    $cardExamTypeColor = $cardSettings?->card_exam_type_text_color ?? '#ffffff';
    $cardExamNameColor = $cardSettings?->card_exam_name_text_color ?? '#e5e7eb';
    $cardStudentDetailAlignment = in_array($cardSettings?->card_student_detail_alignment ?? 'left', ['left', 'center', 'right'], true) ? $cardSettings?->card_student_detail_alignment : 'left';
    $cardFrontAlignment = in_array($cardSettings?->card_front_alignment ?? 'center', ['left', 'center', 'right'], true) ? $cardSettings?->card_front_alignment : 'center';
    $cardFrontPadding = $cardSettings?->card_front_padding_value ?? 0.8;
    $cardPhotoWidth = $cardSettings?->card_photo_width_value ?? 1.8;
    $cardPhotoHeight = $cardSettings?->card_photo_height_value ?? 2.7;
    $cardLogoSize = $cardSettings?->card_logo_size_value ?? 0.8;
    $cardSchoolNameFontSize = $cardSettings?->card_school_name_font_size ?? 7.2;
    $cardSchoolDetailFontSize = $cardSettings?->card_school_detail_font_size ?? 5.4;
    $cardTitleFontSize = $cardSettings?->card_title_font_size ?? 4.7;
    $cardNameFontSize = $cardSettings?->card_name_font_size ?? 7.2;
    $cardExamTypeFontSize = $cardSettings?->card_exam_type_font_size ?? 7.4;
    $cardExamNameFontSize = $cardSettings?->card_exam_name_font_size ?? 6.8;
    $cardStudentDetailFontSize = $cardSettings?->card_student_detail_font_size ?? 8.5;
    $cardStudentDetailColor = $cardSettings?->card_student_detail_text_color ?? '#111827';
    $principalLabel = $setting?->principal_designation ?: 'Principal';

    $resolveImagePath = function (?string $path) use ($renderForPdf) {
        if (!$path || !file_exists(public_path($path))) {
            return null;
        }

        return $renderForPdf ? public_path($path) : asset($path);
    };

    $logoPath = $resolveImagePath($cardSettings?->card_logo ?? null)
        ?? $resolveImagePath($setting?->logo ?? null);
@endphp

<div class="admit-card-pages" style="--admit-card-theme-bg: {{ $cardThemeBackground }}; --admit-card-theme-accent: {{ $cardThemeAccent }}; --admit-card-school-name-color: {{ $cardSchoolNameColor }}; --admit-card-school-detail-color: {{ $cardSchoolDetailColor }}; --admit-card-title-color: {{ $cardTitleColor }}; --admit-card-name-color: {{ $cardNameColor }}; --admit-card-exam-type-color: {{ $cardExamTypeColor }}; --admit-card-exam-name-color: {{ $cardExamNameColor }}; --admit-card-student-detail-align: {{ $cardStudentDetailAlignment }}; --admit-card-student-detail-font-size: {{ $cardStudentDetailFontSize }}pt; --admit-card-student-detail-color: {{ $cardStudentDetailColor }}; --admit-card-front-align: {{ $cardFrontAlignment }}; --admit-card-front-padding: {{ $cardFrontPadding }}mm; --admit-card-photo-width: {{ $cardPhotoWidth }}cm; --admit-card-photo-height: {{ $cardPhotoHeight }}cm; --admit-card-logo-size: {{ $cardLogoSize }}cm; --admit-card-school-name-font-size: {{ $cardSchoolNameFontSize }}pt; --admit-card-school-detail-font-size: {{ $cardSchoolDetailFontSize }}pt; --admit-card-title-font-size: {{ $cardTitleFontSize }}pt; --admit-card-name-font-size: {{ $cardNameFontSize }}pt; --admit-card-exam-type-font-size: {{ $cardExamTypeFontSize }}pt; --admit-card-exam-name-font-size: {{ $cardExamNameFontSize }}pt;">
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
                    'studentCid' => $student->student_cid,
                    'studentRoll' => $ai?->roll,
                    'studentClass' => $ai?->schoolClass?->name_en ?? '—',
                    'studentSection' => $ai?->section?->name_en ?? '—',
                    'studentSession' => $ai?->academicSession?->name_en ?? '—',
                    'logoPath' => $logoPath,
                    'photoPath' => $photoPath,
                    'photoAlt' => $student->full_name_en,
                    'principalLabel' => $principalLabel,
                    'showLogoFront' => $cardSettings?->card_show_logo_front ?? true,
                    'showSchoolDetailFront' => $cardSettings?->card_show_school_detail_front ?? true,
                    'showSloganFront' => $cardSettings?->card_show_slogan_front ?? true,
                    'showTitleFront' => $cardSettings?->card_show_title_front ?? true,
                    'showPhotoFront' => $cardSettings?->card_show_photo_front ?? true,
                    'showExamTypeFront' => $cardSettings?->card_show_exam_type_front ?? true,
                    'showExamNameFront' => $cardSettings?->card_show_exam_name_front ?? true,
                    'showFooterFront' => $cardSettings?->card_show_footer_front ?? true,
                    'footerLines' => array_values(array_filter([
                        $setting?->contact_number_1,
                        $setting?->whatsapp_number,
                    ])),
                ])
            @endforeach
        </div>
    @endforeach
</div>
