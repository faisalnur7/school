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
    $studentFatherName = $studentFatherName ?? null;
    $studentMotherName = $studentMotherName ?? null;
    $showStudentNameLabelFront = $showStudentNameLabelFront ?? false;
    $showRollFront = $showRollFront ?? true;
    $showClassFront = $showClassFront ?? true;
    $showSectionFront = $showSectionFront ?? true;
    $showSessionFront = $showSessionFront ?? true;
    $showVerticalLabelFront = $showVerticalLabelFront ?? false;
    $examNameBadgeFront = $examNameBadgeFront ?? false;
    $studentFieldOrder = $studentFieldOrder ?? [];
    $defaultStudentFieldOrder = ['student_name', 'student_id', 'father_name', 'mother_name', 'roll', 'class', 'section', 'session'];
    $studentFieldOrder = array_values(array_unique(array_filter(is_array($studentFieldOrder) ? $studentFieldOrder : [])));
    foreach ($defaultStudentFieldOrder as $defaultStudentField) {
        if (!in_array($defaultStudentField, $studentFieldOrder, true)) {
            $studentFieldOrder[] = $defaultStudentField;
        }
    }
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
    $signatureSize = $signatureSize ?? 1.4;
    $principalSignatureId = $principalSignatureId ?? null;
    $footerLines = array_values(array_filter($footerLines ?? [], function ($line) {
        return filled($line);
    }));
    $focusTargets = $focusTargets ?? [];
    $cardElementPositions = $cardElementPositions ?? [];
    $cardElementSizes = $cardElementSizes ?? [];
    $examNameSize = data_get($cardElementSizes, 'exam_name', []);
    $headerSize = data_get($cardElementSizes, 'header', []);
    $elementPositionVars = static function () use ($cardElementPositions): string {
        $keys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type', 'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'];

        return collect($keys)->map(function (string $key) use ($cardElementPositions): string {
            $position = data_get($cardElementPositions, $key, []);

            return "--admit-card-position-{$key}-x: " . (float) data_get($position, 'x', 0) . "mm; --admit-card-position-{$key}-y: " . (float) data_get($position, 'y', 0) . "mm;";
        })->implode(' ');
    };
    $isPreview = !empty($focusTargets);
    $focusFor = static function (string $key) use ($focusTargets) {
        return $focusTargets[$key] ?? null;
    };
    $previewAttr = static function (?string $target, ?string $explicitSpacingKey = null) use ($isPreview, $focusTargets) {
        if (!$isPreview || !$target) {
            return '';
        }

        $spacingKeyByFocus = [
            'school_name' => 'school_name',
            'school_detail' => 'school_detail',
            'slogan' => 'slogan',
            'title' => 'title',
            'name' => 'name',
            'student_detail_color' => 'student_detail',
            'exam_type' => 'exam_type',
            'exam_name' => 'exam_name',
            'footer' => 'footer',
            'logo' => 'logo',
            'photo' => 'photo',
            'principal_signature' => 'signature',
            'vertical_label' => 'vertical_label',
        ];
        $spacingKey = $explicitSpacingKey;
        $tooltipLabels = [
            'school_name' => 'School Name',
            'school_detail' => 'School Address',
            'slogan' => 'Slogan',
            'title' => 'Front Title',
            'name' => 'Student Name',
            'student_detail_color' => 'Student Fields',
            'student_detail' => 'Student Fields',
            'exam_type' => 'Exam Type',
            'exam_name' => 'Exam Name / Badge',
            'footer' => 'Front Footer',
            'logo' => 'Logo',
            'photo' => 'Student Photo',
            'principal_signature' => 'Principal Signature',
            'signature' => 'Principal Signature',
            'vertical_label' => 'Vertical Admit Label',
        ];
        $matchedFocusKey = null;
        foreach ($spacingKeyByFocus as $focusKey => $candidateSpacingKey) {
            if (!$spacingKey && ($focusTargets[$focusKey] ?? null) === $target) {
                $spacingKey = $candidateSpacingKey;
                $matchedFocusKey = $focusKey;
                break;
            }
        }

        $tooltipLabel = $tooltipLabels[$matchedFocusKey] ?? ($spacingKey ? ($tooltipLabels[$spacingKey] ?? str($spacingKey)->replace('_', ' ')->title()) : null);

        return ' data-preview-focus-target="' . e($target) . '"' .
            ($spacingKey ? ' data-preview-spacing-key="' . e($spacingKey) . '"' : '') .
            ($tooltipLabel ? ' title="Change ' . e($tooltipLabel) . ' settings"' : '');
    };
    $previewClass = $isPreview ? ' card-preview-clickable' : '';
@endphp

<div class="admit-card" style="width: {{ $cardWidthStyle }}; height: {{ $cardHeightStyle }}; --admit-card-vertical-label-color: {{ $cardVerticalLabelColor ?? '#16a085' }}; --admit-card-vertical-label-font-size: {{ $cardVerticalLabelFontSize ?? 5.2 }}pt; --admit-card-element-exam-name-width: {{ filled(data_get($examNameSize, 'width')) ? ((float) data_get($examNameSize, 'width')) . 'mm' : 'auto' }}; --admit-card-element-exam-name-height: {{ filled(data_get($examNameSize, 'height')) ? ((float) data_get($examNameSize, 'height')) . 'mm' : 'auto' }}; {!! $elementPositionVars() !!}">
    @if($logoPath)
        <div class="admit-card__watermark">
            <img src="{{ $logoPath }}" alt="" class="admit-card__watermark-logo">
        </div>
    @endif

    <div class="admit-card__header" @if($isPreview) data-preview-resize-key="header" @endif style="--admit-card-header-height: {{ filled(data_get($headerSize, 'height')) ? ((float) data_get($headerSize, 'height')) . 'mm' : 'auto' }};">
        <div class="admit-card__brand">
            @if((($showLogoFront ?? true) || $isPreview) && $logoPath)
                <div class="admit-card__logo-wrap{{ $previewClass }}"{!! $previewAttr($focusFor('logo')) !!}>
                    <img @if(!empty($logoId)) id="{{ $logoId }}" @endif src="{{ $logoPath }}" alt="Logo" class="admit-card__logo">
                </div>
            @endif

            <div class="admit-card__brand-text">
                <div class="admit-card__school{{ $previewClass }}"{!! $previewAttr($focusFor('school_name')) !!}>{{ $schoolName }}</div>
                @if((($showSchoolDetailFront ?? true) || $isPreview) && $schoolAddress)
                    <div class="admit-card__address{{ $previewClass }}"{!! $previewAttr($focusFor('school_detail')) !!}>{{ $schoolAddress }}</div>
                @endif
                @if((($showSloganFront ?? true) || $isPreview) && $slogan)
                    <div class="admit-card__slogan{{ $previewClass }}"{!! $previewAttr($focusFor('slogan')) !!}>{{ $slogan }}</div>
                @endif
            </div>
        </div>

        <div class="admit-card__exam">
            @if(($showTitleFront ?? true) || $isPreview)
                <div @if(!empty($frontTitleId)) id="{{ $frontTitleId }}" @endif class="admit-card__exam-label{{ $previewClass }}"{!! $previewAttr($focusFor('title')) !!}>{{ $cardLabel }}</div>
            @endif
            @if((($showExamTypeFront ?? true) || $isPreview) && $examTypeLabel)
                <div class="admit-card__exam-type{{ $previewClass }}"{!! $previewAttr($focusFor('exam_type')) !!}>{{ $examTypeLabel }}</div>
            @endif
            @if(((($showExamNameFront ?? true) && ($examNameBadgeFront ?? false)) || $isPreview) && $examName)
                <div class="admit-card__exam-name{{ $examNameBadgeFront ? ' admit-card__exam-name--badge' : '' }}{{ $previewClass }}"{!! $previewAttr($focusFor('exam_name')) !!} @if($isPreview) data-preview-resize-key="exam_name" @endif>
                    {{ $examName }}
                </div>
            @endif
        </div>
        @if($isPreview)
            <span class="admit-card__element-resize-handle admit-card__header-resize-handle" data-preview-resize-handle role="button" tabindex="0"
                aria-label="Resize card header" title="Drag to resize header"></span>
        @endif
    </div>

    <div class="admit-card__body{{ $showVerticalLabelFront ? ' admit-card__body--with-vertical-label' : '' }}">
        @if($showVerticalLabelFront)
            <div class="admit-card__vertical-label{{ $previewClass }}"{!! $previewAttr($focusFor('vertical_label'), 'vertical_label') !!}>ADMIT CARD</div>
        @endif
        <div class="admit-card__content">
            <div class="admit-card__info">
                <div class="admit-card__rows{{ $previewClass }}"{!! $previewAttr($focusFor('student_detail_color')) !!}>
                    @foreach ($studentFieldOrder as $studentField)
                        @if ($studentField === 'student_name')
                            <div class="admit-card__student-field" data-preview-field-order="student_name">
                                @if($showStudentNameLabelFront || $isPreview)
                                    <div class="admit-card__row admit-card__student-name-row{{ $showStudentNameLabelFront ? '' : ' d-none' }}"
                                        data-preview-visibility="student_name_label">
                                        <span class="admit-card__lbl">Student Name</span>
                                        <span class="admit-card__colon" aria-hidden="true">:</span>
                                        <span class="admit-card__val">{{ $studentName }}</span>
                                    </div>
                                @endif
                                @if(!$showStudentNameLabelFront || $isPreview)
                                    <div class="admit-card__name{{ $previewClass }}{{ $showStudentNameLabelFront ? ' d-none' : '' }}"
                                        data-preview-visibility="student_name_value"{!! $previewAttr($focusFor('name')) !!}>{{ $studentName }}</div>
                                @endif
                            </div>
                        @elseif ($studentField === 'student_id')
                            <div class="admit-card__student-field" data-preview-field-order="student_id">
                                <div class="admit-card__row" data-preview-visibility="student_id">
                                    <span class="admit-card__lbl">ID</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentCid }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'father_name' && ((($showFatherNameFront ?? false) || $isPreview) && filled($studentFatherName)))
                            <div class="admit-card__student-field" data-preview-field-order="father_name">
                                <div class="admit-card__row" data-preview-visibility="father_name">
                                    <span class="admit-card__lbl">Father's Name</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentFatherName }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'mother_name' && ((($showMotherNameFront ?? false) || $isPreview) && filled($studentMotherName)))
                            <div class="admit-card__student-field" data-preview-field-order="mother_name">
                                <div class="admit-card__row" data-preview-visibility="mother_name">
                                    <span class="admit-card__lbl">Mother's Name</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentMotherName }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'roll' && (($showRollFront || $isPreview) && $studentRoll))
                            <div class="admit-card__student-field" data-preview-field-order="roll">
                                <div class="admit-card__row" data-preview-visibility="roll">
                                    <span class="admit-card__lbl">Roll</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentRoll }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'class' && ($showClassFront || $isPreview))
                            <div class="admit-card__student-field" data-preview-field-order="class">
                                <div class="admit-card__row" data-preview-visibility="class">
                                    <span class="admit-card__lbl">Class</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentClass }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'section' && ($showSectionFront || $isPreview))
                            <div class="admit-card__student-field" data-preview-field-order="section">
                                <div class="admit-card__row" data-preview-visibility="section">
                                    <span class="admit-card__lbl">Section</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentSection }}</span>
                                </div>
                            </div>
                        @elseif ($studentField === 'session' && ($showSessionFront || $isPreview))
                            <div class="admit-card__student-field" data-preview-field-order="session">
                                <div class="admit-card__row" data-preview-visibility="session">
                                    <span class="admit-card__lbl">Session</span>
                                    <span class="admit-card__colon" aria-hidden="true">:</span>
                                    <span class="admit-card__val">{{ $studentSession }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="admit-card__photo-wrap{{ $previewClass }}"{!! $previewAttr($focusFor('photo')) !!}>
                @if(($showPhotoFront ?? true) || $isPreview)
                    <img src="{{ $photoPath }}" class="admit-card__photo" alt="{{ $photoAlt }}">
                @endif
            </div>
        </div>

        <div class="admit-card__signature{{ $previewClass }}" style="--admit-card-signature-size: {{ $signatureSize }}cm;"{!! $previewAttr($focusFor('principal_signature')) !!}>
            @if($principalSignaturePath)
                <img @if(!empty($principalSignatureId)) id="{{ $principalSignatureId }}" @endif src="{{ $principalSignaturePath }}" alt="Principal signature" class="admit-card__signature-image">
            @endif
            <div class="admit-card__signature-label">{{ $principalLabel }}</div>
        </div>
    </div>

    @if((($showFooterFront ?? true) || $isPreview) && !empty($footerLines))
        <div class="admit-card__footer{{ $previewClass }}"{!! $previewAttr($focusFor('footer')) !!}>
            @foreach($footerLines as $footerLine)
                <span>{{ $footerLine }}</span>
            @endforeach
        </div>
    @endif

    @if($isPreview)
        <span class="admit-card__resize-handle" role="button" tabindex="0"
            aria-label="Resize card preview" title="Drag to resize card"></span>
    @endif
</div>
