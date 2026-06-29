@php
    $renderForPdf = $renderForPdf ?? false;
    $layout = $layout ?? [];
    $cardsPerPage = max(1, min(12, (int) ($layout['cardsPerPage'] ?? 4)));
    $cardsPerRow = max(1, min($cardsPerPage, (int) ($layout['cardsPerRow'] ?? 2)));
    $pageRows = max(1, (int) ($layout['pageRows'] ?? ceil($cardsPerPage / $cardsPerRow)));
    $cardWidthMm = $layout['cardWidthMm'] ?? 54;
    $cardHeightMm = $layout['cardHeightMm'] ?? 84;
    $gapMm = $layout['gridGapMm'] ?? 5;

    $studentPages = $students->chunk($cardsPerPage);
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
    $cardSchoolNameColor = $cardSettings?->card_school_name_text_color ?? '#ffffff';
    $cardSchoolDetailColor = $cardSettings?->card_school_detail_text_color ?? '#e5e7eb';
    $cardSloganColor = $cardSettings?->card_slogan_text_color ?? '#e5e7eb';
    $cardBackNoticeColor = $cardSettings?->card_back_notice_text_color ?? '#94a3b8';
    $cardFooterColor = $cardSettings?->card_footer_text_color ?? '#e5e7eb';
    $cardTitleColor = $cardSettings?->card_title_text_color ?? '#ffffff';
    $frontTitle = match ($cardType) {
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

    $logoPath = $resolveImagePath($cardSettings?->card_logo ?? null)
        ?? $resolveImagePath($setting?->logo ?? null);

    if (!$logoPath) {
        $logoPath = null;
    }
@endphp

<div class="id-card-pages" style="--id-card-width: {{ $cardWidthMm }}mm; --id-card-height: {{ $cardHeightMm }}mm; --id-card-gap: {{ $gapMm }}mm; --card-theme-bg: {{ $cardThemeBackground }}; --card-theme-accent: {{ $cardThemeAccent }}; --id-card-school-name-color: {{ $cardSchoolNameColor }}; --id-card-school-detail-color: {{ $cardSchoolDetailColor }}; --id-card-slogan-color: {{ $cardSloganColor }}; --id-card-back-notice-color: {{ $cardBackNoticeColor }}; --id-card-footer-color: {{ $cardFooterColor }}; --id-card-title-color: {{ $cardTitleColor }};">
    @foreach($studentPages as $pageIndex => $pageStudents)
        <div class="id-card-page" style="grid-template-columns: repeat({{ $cardsPerRow }}, max-content); gap: {{ $gapMm }}mm {{ $gapMm }}mm;">
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

                <div class="id-card-pair" style="gap: {{ $gapMm }}mm;">
                    <div class="id-card" style="width: {{ $cardWidthMm }}mm; height: {{ $cardHeightMm }}mm;">
                        <div class="id-card__header id-card__header--front">
                            @if($logoPath)
                                <img src="{{ $logoPath }}" class="id-card__logo" alt="Logo">
                            @endif
                            <div class="id-card__school-name">{{ $setting?->name ?? 'School Name' }}</div>
                            @if($setting?->slogan)
                                <div class="id-card__slogan">{{ $setting->slogan }}</div>
                            @endif
                            <div class="id-card__label-badge">{{ $frontTitle }}</div>
                        </div>

                        <div class="id-card__front-body">
                            <img src="{{ $photoPath }}" class="id-card__photo" alt="{{ $student->full_name_en }}">

                            <div class="id-card__info">
                                <div class="id-card__name">{{ $student->full_name_en }}</div>
                                @if($student->full_name_bn)
                                    <div class="id-card__name-bn">{{ $student->full_name_bn }}</div>
                                @endif

                                <div class="id-card__divider"></div>

                                <div class="id-card__rows">
                                    <div class="id-card__row">
                                        <span class="id-card__lbl">ID</span>
                                        <span class="id-card__val">{{ $student->student_cid }}</span>
                                    </div>
                                    @if($ai)
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
                                    @if($student->date_of_birth)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">DOB</span>
                                            <span class="id-card__val">{{ $student->date_of_birth->format('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if($student->blood_group)
                                        <div class="id-card__row">
                                            <span class="id-card__lbl">Blood</span>
                                            <span class="id-card__val id-card__blood">{{ $student->blood_group_text }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="id-card__footer id-card__footer--front">
                            @if($setting?->contact_number_1)
                                <span>📞 {{ $setting->contact_number_1 }}</span>
                            @endif
                            @if($setting?->contact_number_2)
                                <span>📞 {{ $setting->contact_number_2 }}</span>
                            @endif
                            @if($setting?->website)
                                <span>🌐 {{ $setting->website }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="id-card id-card--back" style="width: {{ $cardWidthMm }}mm; height: {{ $cardHeightMm }}mm;">
                        <div class="id-card__header id-card__header--back">
                            <div class="id-card__school-name">{{ $setting?->name ?? 'School Name' }}</div>
                            @if($setting?->slogan)
                                <div class="id-card__slogan">{{ $setting->slogan }}</div>
                            @endif
                            <div class="id-card__label-badge">{{ $backBadge }}</div>
                        </div>

                        <div class="id-card__back-body">
                            <div class="id-card__back-section">
                                <div class="id-card__back-title">{{ $backSectionTitle }}</div>
                                @if($isLibraryCard || $isAdmitOrSeatCard)
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

                            <div class="id-card__back-section">
                                <div class="id-card__back-title">{{ $isLibraryCard ? 'Library / School Contact' : ($isAdmitOrSeatCard ? 'School / Exam Contact' : 'School Contact') }}</div>
                                @if($setting?->address)
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

                            @if($setting?->whatsapp_qr && file_exists(public_path($setting->whatsapp_qr)))
                                <div style="display:flex;justify-content:center;">
                                    <img src="{{ $renderForPdf ? public_path($setting->whatsapp_qr) : asset($setting->whatsapp_qr) }}" class="id-card__qr" alt="WhatsApp QR">
                                </div>
                            @endif

                            <div class="id-card__back-notice">If found, please return to the school.</div>
                        </div>

                        <div class="id-card__footer id-card__footer--back">
                            @if($setting?->whatsapp_number)
                                <span>📱 {{ $setting->whatsapp_number }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
