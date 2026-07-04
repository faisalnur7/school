@php
    $previewType = $previewType ?? 'id';
    $prefix = $prefix ?? 'card';
    $schoolName = $schoolName ?? 'School Name';
    $slogan = $slogan ?? 'Stay Green, Be Bright';
    $frontTitle = $frontTitle ?? 'STUDENT ID';
    $backTitle = $backTitle ?? 'BACK';
    $schoolDetailLine = $schoolDetailLine ?? 'Address, Phone, Email';
    $schoolContactLine1 = $schoolContactLine1 ?? null;
    $schoolContactLine2 = $schoolContactLine2 ?? null;
    $schoolWhatsapp = $schoolWhatsapp ?? null;
    $schoolEmail = $schoolEmail ?? null;
    $schoolWebsite = $schoolWebsite ?? null;
    $schoolQrUrl = $schoolQrUrl ?? null;
    $backNotice = $backNotice ?? 'If found, please return to the school.';
    $footerLine = $footerLine ?? 'Contact';
    $footerLines = $footerLines ?? (($footerLine ?? null) ? [$footerLine] : []);
    $cardLabel = $cardLabel ?? ($previewType === 'admit' ? 'ADMIT CARD' : 'SEAT CARD');
    $examTypeLabel = $examTypeLabel ?? null;
    $examName = $examName ?? null;
    $logoUrl = $logoUrl ?? null;
    $showBack = $showBack ?? true;
    $showSchoolDetailFront = $showSchoolDetailFront ?? true;
    $showSchoolDetailBack = $showSchoolDetailBack ?? true;
    $showSloganFront = $showSloganFront ?? true;
    $showSloganBack = $showSloganBack ?? true;
    $showTitleFront = $showTitleFront ?? true;
    $showTitleBack = $showTitleBack ?? true;
    $showLogoFront = $showLogoFront ?? true;
    $showLogoBack = $showLogoBack ?? true;
    $showPhotoFront = $showPhotoFront ?? true;
    $showExamTypeFront = $showExamTypeFront ?? true;
    $showExamNameFront = $showExamNameFront ?? true;
    $showBackNotice = $showBackNotice ?? true;
    $showFooterFront = $showFooterFront ?? true;
    $showFooterBack = $showFooterBack ?? true;
    $focusTargets = $focusTargets ?? [];
    $previewLabel = $previewLabel ?? null;
    $frontTitleId = $prefix . 'LivePreviewTitleFront';
    $backTitleId = $prefix . 'LivePreviewTitleBack';
    $previewCardWidthValue = (float) ($previewCardWidthValue ?? ($previewType === 'admit' ? 9.4 : 5.4));
    $previewCardHeightValue = (float) ($previewCardHeightValue ?? ($previewType === 'admit' ? 6.6 : 8.4));
    $previewCardDimensionUnit = $previewCardDimensionUnit ?? 'cm';
    $previewCardUnit = in_array($previewCardDimensionUnit, ['cm', 'px'], true) ? $previewCardDimensionUnit : 'cm';
    $previewCardWidthCss = $previewCardWidthValue . $previewCardUnit;
    $previewCardHeightCss = $previewCardHeightValue . $previewCardUnit;
    $previewCardRatio = $previewCardHeightValue > 0 ? round($previewCardWidthValue / $previewCardHeightValue, 4) : 1;
    $previewLogoSizeValue = (float) 0.8;
    $previewPhotoWidthValue = (float) 1.8;
    $previewPhotoHeightValue = (float) 2.7;
    $previewFrontPaddingValue = (float) 0.2;
    $previewBackPaddingValue = (float) 0.17;
    $previewGapValue = (float) 0.14;
    $isIdPreview = $previewType === 'id';
    $focusFor = function (string $key) use ($focusTargets) {
        return $focusTargets[$key] ?? null;
    };
@endphp

<div
    id="{{ $prefix }}LivePreview"
    class="card-preview-shell"
    style="
        --preview-bg:#1e3a5f;
        --preview-school-name-color:#ffffff;
        --preview-school-detail-color:#e5e7eb;
        --preview-slogan-color:#e5e7eb;
        --preview-title-color:#ffffff;
        --preview-exam-type-color:#ffffff;
        --preview-exam-name-color:#e5e7eb;
        --preview-back-notice-color:#94a3b8;
        --preview-footer-color:#e5e7eb;
        --preview-logo-size:{{ $previewLogoSizeValue }}{{ $previewCardUnit }};
        --preview-photo-width:{{ $previewPhotoWidthValue }}{{ $previewCardUnit }};
        --preview-photo-height:{{ $previewPhotoHeightValue }}{{ $previewCardUnit }};
        --preview-front-padding:{{ $previewFrontPaddingValue }}{{ $previewCardUnit }};
        --preview-back-padding:{{ $previewBackPaddingValue }}{{ $previewCardUnit }};
        --preview-gap:{{ $previewGapValue }}{{ $previewCardUnit }};
        --preview-card-width:{{ $previewCardWidthCss }};
        --preview-card-height:{{ $previewCardHeightCss }};
        --preview-card-ratio:{{ $previewCardRatio }};
        --card-theme-bg: linear-gradient(135deg, #1e3a5f, #2563eb);
        --card-theme-accent: #1e3a5f;
        --id-card-school-name-color:#ffffff;
        --id-card-school-detail-color:#e5e7eb;
        --id-card-slogan-color:#e5e7eb;
        --id-card-title-color:#ffffff;
        --id-card-name-color:#1e3a5f;
        --id-card-student-detail-align:flex-start;
        --id-card-student-detail-text-align:left;
        --id-card-back-notice-color:#94a3b8;
        --id-card-footer-color:#e5e7eb;
        --id-card-school-name-font-size:7.2pt;
        --id-card-school-detail-font-size:5.4pt;
        --id-card-slogan-font-size:4.8pt;
        --id-card-title-font-size:4.7pt;
        --id-card-name-font-size:7.2pt;
        --id-card-width:{{ $previewCardWidthCss }};
        --id-card-height:{{ $previewCardHeightCss }};
        --id-card-gap:{{ $previewGapValue }}{{ $previewCardUnit }};
        --id-card-front-padding:{{ $previewFrontPaddingValue }}{{ $previewCardUnit }};
        --id-card-back-padding:{{ $previewBackPaddingValue }}{{ $previewCardUnit }};
        --id-card-photo-width:{{ $previewPhotoWidthValue }}{{ $previewCardUnit }};
        --id-card-photo-height:{{ $previewPhotoHeightValue }}{{ $previewCardUnit }};
        --id-card-logo-size:{{ $previewLogoSizeValue }}{{ $previewCardUnit }};
        --admit-card-theme-bg: linear-gradient(135deg, #1e3a5f, #2563eb);
        --admit-card-theme-accent: #1e3a5f;
        --admit-card-school-name-color:#ffffff;
        --admit-card-school-detail-color:#e5e7eb;
        --admit-card-slogan-color:#e5e7eb;
        --admit-card-slogan-font-size:4.8pt;
        --admit-card-title-color:#ffffff;
        --admit-card-name-color:#111827;
        --admit-card-exam-type-color:#ffffff;
        --admit-card-exam-name-color:#e5e7eb;
        --admit-card-student-detail-align:left;
        --admit-card-student-detail-font-size:8.5pt;
        --admit-card-student-detail-color:#111827;
        --admit-card-front-align:center;
        --admit-card-front-padding:0.8mm;
        --admit-card-photo-width:20mm;
        --admit-card-photo-height:30mm;
        --admit-card-logo-size:8mm;
        --admit-card-school-name-font-size:7.2pt;
        --admit-card-school-detail-font-size:5.4pt;
        --admit-card-title-font-size:4.7pt;
        --admit-card-name-font-size:7.2pt;
        --admit-card-exam-type-font-size:7.4pt;
        --admit-card-exam-name-font-size:6.8pt;
    "
>
    <div class="card-preview-shell__header">
        <div>
            <div class="card-preview-shell__title">Live Preview</div>
            <div class="card-preview-shell__subtitle">Click an element to edit its setting.</div>
        </div>
        <div class="d-flex align-items-center" style="gap:8px; flex-wrap: wrap; justify-content:flex-end;">
            @if($previewLabel)
                <span id="{{ $prefix }}PreviewLabel" class="card-preview-shell__badge">{{ $previewLabel }}</span>
            @endif
            @if($isIdPreview && $showBack)
                <div class="btn-group btn-group-sm card-preview-shell__switcher" role="group" aria-label="Preview side selector">
                    <button type="button" class="btn btn-outline-secondary js-card-preview-side active btn-secondary" data-preview-target="{{ $prefix }}" data-preview-side="front">Front</button>
                    <button type="button" class="btn btn-outline-secondary js-card-preview-side" data-preview-target="{{ $prefix }}" data-preview-side="back">Back</button>
                </div>
            @endif
        </div>
    </div>

    <div class="card-preview-shell__grid card-preview-shell__grid--single">
        @if($isIdPreview)
            <div class="card-preview-stage card-preview-stage--pair">
                <div class="card-preview-card card-preview-card--pair">
                    <div class="id-card-pair">
                        <div
                            id="{{ $prefix }}LivePreviewFront"
                            class="id-card"
                            style="width: {{ $previewCardWidthCss }}; height: {{ $previewCardHeightCss }};"
                        >
                            <div class="id-card__header id-card__header--front card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_name') }}" style="background: var(--card-theme-bg);">
                                @if($showLogoFront)
                                    <div class="card-preview-clickable" data-preview-focus-target="{{ $focusFor('logo') }}">
                                        @if($logoUrl)
                                            <img id="{{ $prefix }}LivePreviewLogoFront" src="{{ $logoUrl }}" alt="Logo preview" class="id-card__logo">
                                        @else
                                            <span class="small font-weight-bold text-white">LOGO</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="id-card__school-name card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_name') }}">{{ $schoolName }}</div>
                                @if($showSloganFront && $slogan)
                                    <div class="id-card__slogan card-preview-clickable" data-preview-focus-target="{{ $focusFor('slogan') }}">{{ $slogan }}</div>
                                @endif
                                @if($showTitleFront && $frontTitle)
                                    <div id="{{ $frontTitleId }}" class="id-card__label-badge card-preview-clickable" data-preview-focus-target="{{ $focusFor('title') }}">{{ $frontTitle }}</div>
                                @endif
                            </div>

                            <div class="id-card__front-body">
                                @if($showPhotoFront)
                                    <div class="d-flex justify-content-center">
                                        <div
                                            id="{{ $prefix }}LivePreviewPhoto"
                                            class="id-card__photo card-preview-clickable"
                                            data-preview-focus-target="{{ $focusFor('logo') }}"
                                            style="display:flex;align-items:center;justify-content:center;overflow:hidden;"
                                        >
                                            <div style="position:relative;width:100%;height:100%;">
                                                <div style="position:absolute;left:50%;top:42%;width:42%;height:42%;border-radius:50%;transform:translate(-50%,-62%);background:#cbd5e1;"></div>
                                                <div style="position:absolute;left:50%;bottom:8%;width:68%;height:33%;border-radius:18px 18px 10px 10px;transform:translateX(-50%);background:#cbd5e1;"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="id-card__info">
                                    <div class="id-card__name card-preview-clickable" data-preview-focus-target="{{ $focusFor('name') }}">Student Name</div>
                                    <div class="id-card__divider"></div>
                                    <div class="id-card__rows">
                                        <div class="id-card__row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                            <span class="id-card__lbl">ID</span>
                                            <span class="id-card__val">0001</span>
                                        </div>
                                        <div class="id-card__row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                            <span class="id-card__lbl">Class</span>
                                            <span class="id-card__val">One / A</span>
                                        </div>
                                        <div class="id-card__row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                            <span class="id-card__lbl">Roll</span>
                                            <span class="id-card__val">12</span>
                                        </div>
                                        <div class="id-card__row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                            <span class="id-card__lbl">Session</span>
                                            <span class="id-card__val">2025-2026</span>
                                        </div>
                                        <div class="id-card__row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                            <span class="id-card__lbl">DOB</span>
                                            <span class="id-card__val">29 Jun 2010</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($showFooterFront)
                                <div id="{{ $prefix }}LivePreviewFooterFront" class="id-card__footer id-card__footer--front card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                    @if($schoolContactLine1)
                                        <span>📞 {{ $schoolContactLine1 }}</span>
                                    @endif
                                    @if($schoolContactLine2)
                                        <span>📞 {{ $schoolContactLine2 }}</span>
                                    @endif
                                    @if($schoolWebsite)
                                        <span>🌐 {{ $schoolWebsite }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($showBack)
                            <div
                                id="{{ $prefix }}LivePreviewBack"
                                class="id-card id-card--back {{ $showBack ? 'd-none' : '' }}"
                                style="width: {{ $previewCardWidthCss }}; height: {{ $previewCardHeightCss }};"
                            >
                                <div class="id-card__header id-card__header--back card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_name') }}" style="background: var(--card-theme-bg);">
                                    @if($showLogoBack)
                                        <div class="card-preview-clickable" data-preview-focus-target="{{ $focusFor('logo') }}">
                                            @if($logoUrl)
                                                <img id="{{ $prefix }}LivePreviewLogoBack" src="{{ $logoUrl }}" alt="Logo preview" class="id-card__logo">
                                            @else
                                                <span class="small font-weight-bold text-white">LOGO</span>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="id-card__school-name card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_name') }}">{{ $schoolName }}</div>
                                    @if($showSloganBack && $slogan)
                                        <div class="id-card__slogan card-preview-clickable" data-preview-focus-target="{{ $focusFor('slogan') }}">{{ $slogan }}</div>
                                    @endif
                                    @if($showTitleBack && $backTitle)
                                        <div id="{{ $backTitleId }}" class="id-card__label-badge card-preview-clickable" data-preview-focus-target="{{ $focusFor('title') }}">{{ $backTitle }}</div>
                                    @endif
                                </div>

                                <div class="id-card__back-body">
                                    <div class="id-card__back-section">
                                        <div class="id-card__back-title card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">School Contact</div>
                                        @if($showSchoolDetailBack && $schoolDetailLine)
                                            <div class="id-card__back-row card-preview-clickable" data-preview-focus-target="{{ $focusFor('school_detail') }}">
                                                <span class="id-card__lbl">Address</span>
                                                <span class="id-card__val">{{ $schoolDetailLine }}</span>
                                            </div>
                                        @endif
                                        @if($schoolContactLine1 || $schoolContactLine2)
                                            <div class="id-card__back-row card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                                <span class="id-card__lbl">Contact</span>
                                                <span class="id-card__val">{{ implode(', ', array_filter([$schoolContactLine1, $schoolContactLine2])) }}</span>
                                            </div>
                                        @endif
                                        @if($schoolWhatsapp)
                                            <div class="id-card__back-row card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                                <span class="id-card__lbl">WhatsApp</span>
                                                <span class="id-card__val">{{ $schoolWhatsapp }}</span>
                                            </div>
                                        @endif
                                        @if($schoolEmail)
                                            <div class="id-card__back-row card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                                <span class="id-card__lbl">Email</span>
                                                <span class="id-card__val">{{ $schoolEmail }}</span>
                                            </div>
                                        @endif
                                        @if($schoolWebsite)
                                            <div class="id-card__back-row card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                                <span class="id-card__lbl">Web</span>
                                                <span class="id-card__val">{{ $schoolWebsite }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if($schoolQrUrl)
                                        <div style="display:flex;justify-content:center;">
                                            <img src="{{ $schoolQrUrl }}" class="id-card__qr" alt="WhatsApp QR">
                                        </div>
                                    @endif

                                    @if($showBackNotice)
                                        <div class="id-card__back-notice card-preview-clickable" data-preview-focus-target="{{ $focusFor('back_notice') }}">{{ $backNotice }}</div>
                                    @endif
                                </div>

                                @if($showFooterBack)
                                    <div id="{{ $prefix }}LivePreviewFooterBack" class="id-card__footer id-card__footer--back card-preview-clickable" data-preview-focus-target="{{ $focusFor('footer') }}">
                                        <span>📱 {{ $footerLine }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="card-preview-stage card-preview-stage--admit">
                <div class="card-preview-card card-preview-card--admit">
                    @include('pages.admit-seat-cards._card', [
                        'cardWidthStyle' => 'var(--admit-card-preview-width, ' . $previewCardWidthCss . ')',
                        'cardHeightStyle' => 'var(--admit-card-preview-height, ' . $previewCardHeightCss . ')',
                        'cardLabel' => $cardLabel,
                        'schoolName' => $schoolName,
                        'schoolAddress' => $schoolDetailLine,
                        'slogan' => $slogan,
                        'examTypeLabel' => $examTypeLabel,
                        'examName' => $examName,
                        'studentName' => 'Student Name',
                        'studentCid' => '0001',
                        'studentRoll' => '12',
                        'studentClass' => 'One',
                        'studentSection' => 'A',
                        'studentSession' => '2025-2026',
                        'logoPath' => $logoUrl,
                        'logoId' => $prefix . 'LivePreviewLogoFront',
                        'photoPath' => asset('assets/img/male-placeholder.png'),
                        'photoAlt' => 'Student photo preview',
                        'principalLabel' => 'Principal',
                        'showLogoFront' => $showLogoFront,
                        'showSchoolDetailFront' => $showSchoolDetailFront,
                        'showSloganFront' => $showSloganFront,
                        'showTitleFront' => $showTitleFront,
                        'showPhotoFront' => $showPhotoFront,
                        'showExamTypeFront' => $showExamTypeFront,
                        'showExamNameFront' => $showExamNameFront,
                        'showFooterFront' => $showFooterFront,
                        'footerLines' => $footerLines,
                        'focusTargets' => $focusTargets,
                        'frontTitleId' => $frontTitleId,
                    ])
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.card-preview-shell {
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 16px;
    border: 1px solid #dbe4ee;
    border-radius: 24px;
    background:
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.10), transparent 35%),
        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.08), transparent 30%),
        linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
}

.card-preview-shell__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.card-preview-shell__title {
    font-size: 0.98rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.01em;
}

.card-preview-shell__subtitle {
    font-size: 0.82rem;
    color: #64748b;
    margin-top: 2px;
}

.card-preview-shell__badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.36rem 0.7rem;
    border-radius: 999px;
    border: 1px solid #cbd5e1;
    background: #fff;
    color: #0f172a;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.card-preview-shell__switcher .btn {
    color: #111827 !important;
    border-color: #cbd5e1;
    background: #ffffff;
}

.card-preview-shell__switcher .btn:hover,
.card-preview-shell__switcher .btn:focus,
.card-preview-shell__switcher .btn.active,
.card-preview-shell__switcher .btn.btn-secondary {
    color: #ffffff !important;
    background: #6b7280;
    border-color: #6b7280;
    box-shadow: none;
}

.card-preview-shell__grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    justify-items: center;
    align-items: start;
}

.card-preview-shell__grid--single {
    grid-template-columns: minmax(0, 1fr);
}

.card-preview-stage {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}

.card-preview-stage--pair {
    gap: var(--preview-gap, 0.14cm);
    flex-wrap: wrap;
}

.card-preview-stage--admit {
    width: 100%;
}

.card-preview-card {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    overflow: visible;
    background: transparent;
    border: 0;
    box-shadow: none;
}

.card-preview-card--pair {
    width: auto;
}

.card-preview-card--admit {
    max-width: 100%;
}

.card-preview-clickable {
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.15s ease, outline-color 0.15s ease;
}

.card-preview-clickable:hover {
    transform: translateY(-1px);
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.12);
}

.card-preview-clickable.is-focused {
    outline: 2px solid rgba(37, 99, 235, 0.65);
    outline-offset: 2px;
    border-radius: 12px;
}

.admit-card__slogan {
    margin-top: 0.35mm;
    font-size: var(--admit-card-slogan-font-size, 4.8pt);
    line-height: 1.1;
    color: var(--admit-card-slogan-color, #e5e7eb);
    font-weight: 600;
    word-break: break-word;
}

@media (max-width: 1199.98px) {
    .card-preview-shell__grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767.98px) {
    .card-preview-shell {
        border-radius: 18px;
        padding: 12px;
    }

    .card-preview-shell__header {
        align-items: flex-start;
    }
}
</style>
