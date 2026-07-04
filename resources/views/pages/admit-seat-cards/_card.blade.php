@php
    $cardWidthStyle = $cardWidthStyle ?? (($cardWidthMm ?? 80) . 'mm');
    $cardHeightStyle = $cardHeightStyle ?? (($cardHeightMm ?? 46) . 'mm');
    $cardLabel = $cardLabel ?? 'ADMIT CARD';
    $schoolName = $schoolName ?? 'School Name';
    $schoolAddress = $schoolAddress ?? null;
    $slogan = $slogan ?? null;
    $examTypeLabel = $examTypeLabel ?? null;
    $examName = $examName ?? null;
    $studentName = $studentName ?? 'Student Name';
    $studentCid = $studentCid ?? '0001';
    $studentRoll = $studentRoll ?? null;
    $studentClass = $studentClass ?? 'One';
    $studentSection = $studentSection ?? 'A';
    $studentSession = $studentSession ?? '2025-2026';
    $cardNameColor = $cardNameColor ?? '#111827';
    $photoPath = $photoPath ?? null;
    $photoAlt = $photoAlt ?? $studentName;
    $logoPath = $logoPath ?? null;
    $principalLabel = $principalLabel ?? 'Principal';
    $principalSignaturePath = $principalSignaturePath ?? null;
    $principalSignatureId = $principalSignatureId ?? null;
    $footerLines = array_values(array_filter($footerLines ?? [], function ($line) {
        return filled($line);
    }));
    $focusTargets = $focusTargets ?? [];
    $isPreview = !empty($focusTargets);
    $focusFor = static function (string $key) use ($focusTargets) {
        return $focusTargets[$key] ?? null;
    };
    $previewAttr = static function (?string $target) use ($isPreview) {
        if (!$isPreview || !$target) {
            return '';
        }

        return ' data-preview-focus-target="' . e($target) . '"';
    };
    $previewClass = $isPreview ? ' card-preview-clickable' : '';
@endphp

<div class="admit-card" style="width: {{ $cardWidthStyle }}; height: {{ $cardHeightStyle }};">
    @if($logoPath)
        <div class="admit-card__watermark">
            <img src="{{ $logoPath }}" alt="" class="admit-card__watermark-logo">
        </div>
    @endif

    <div class="admit-card__header">
        <div class="admit-card__brand">
            @if(($showLogoFront ?? true) && $logoPath)
                <div{!! $isPreview ? ' class="card-preview-clickable"' . $previewAttr($focusFor('logo')) : '' !!}>
                    <img @if(!empty($logoId)) id="{{ $logoId }}" @endif src="{{ $logoPath }}" alt="Logo" class="admit-card__logo">
                </div>
            @endif

            <div class="admit-card__brand-text">
                <div class="admit-card__school{{ $previewClass }}"{!! $previewAttr($focusFor('school_name')) !!}>{{ $schoolName }}</div>
                @if(($showSchoolDetailFront ?? true) && $schoolAddress)
                    <div class="admit-card__address{{ $previewClass }}"{!! $previewAttr($focusFor('school_detail')) !!}>{{ $schoolAddress }}</div>
                @endif
                @if(($showSloganFront ?? true) && $slogan)
                    <div class="admit-card__slogan{{ $previewClass }}"{!! $previewAttr($focusFor('slogan')) !!}>{{ $slogan }}</div>
                @endif
            </div>
        </div>

        <div class="admit-card__exam">
            @if($showTitleFront ?? true)
                <div @if(!empty($frontTitleId)) id="{{ $frontTitleId }}" @endif class="admit-card__exam-label{{ $previewClass }}"{!! $previewAttr($focusFor('title')) !!}>{{ $cardLabel }}</div>
            @endif
            @if(($showExamTypeFront ?? true) && $examTypeLabel)
                <div class="admit-card__exam-type{{ $previewClass }}"{!! $previewAttr($focusFor('exam_type')) !!}>{{ $examTypeLabel }}</div>
            @endif
            @if(($showExamNameFront ?? true) && $examName)
                <div class="admit-card__exam-name{{ $previewClass }}"{!! $previewAttr($focusFor('exam_name')) !!}>{{ $examName }}</div>
            @endif
        </div>
    </div>

    <div class="admit-card__body">
        <div class="admit-card__info">
            <div class="admit-card__name{{ $previewClass }}"{!! $previewAttr($focusFor('name')) !!}>{{ $studentName }}</div>

            <div class="admit-card__rows{{ $previewClass }}"{!! $previewAttr($focusFor('student_detail_color')) !!}>
                <div class="admit-card__row">
                    <span class="admit-card__lbl">ID</span>
                    <span class="admit-card__val">{{ $studentCid }}</span>
                </div>
                @if($studentRoll)
                    <div class="admit-card__row">
                        <span class="admit-card__lbl">Roll</span>
                        <span class="admit-card__val">{{ $studentRoll }}</span>
                    </div>
                @endif
                <div class="admit-card__row">
                    <span class="admit-card__lbl">Class</span>
                    <span class="admit-card__val">{{ $studentClass }}</span>
                </div>
                <div class="admit-card__row">
                    <span class="admit-card__lbl">Section</span>
                    <span class="admit-card__val">{{ $studentSection }}</span>
                </div>
                <div class="admit-card__row">
                    <span class="admit-card__lbl">Session</span>
                    <span class="admit-card__val">{{ $studentSession }}</span>
                </div>
            </div>
        </div>

        <div class="admit-card__photo-wrap{{ $previewClass }}"{!! $previewAttr($focusFor('logo')) !!}>
            @if($showPhotoFront ?? true)
                <img src="{{ $photoPath }}" class="admit-card__photo" alt="{{ $photoAlt }}">
            @endif
        </div>

        <div class="admit-card__signature">
            @if($principalSignaturePath)
                <img @if(!empty($principalSignatureId)) id="{{ $principalSignatureId }}" @endif src="{{ $principalSignaturePath }}" alt="Principal signature" class="admit-card__signature-image">
            @endif
            <div class="admit-card__signature-line"></div>
            <div class="admit-card__signature-label">{{ $principalLabel }}</div>
        </div>
    </div>

    @if(($showFooterFront ?? true) && !empty($footerLines))
        <div class="admit-card__footer{{ $previewClass }}"{!! $previewAttr($focusFor('footer')) !!}>
            @foreach($footerLines as $footerLine)
                <span>{{ $footerLine }}</span>
            @endforeach
        </div>
    @endif
</div>
