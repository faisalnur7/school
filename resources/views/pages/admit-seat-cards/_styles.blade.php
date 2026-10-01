@page {
    size: A4 portrait;
    margin: 10mm 6.35mm 4mm 6.35mm;
}

.admit-card-pages {
    display: flex;
    flex-direction: column;
    gap: 8.5mm;
    padding: 0;
    padding-top: 6mm;
}

.admit-card-page {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    justify-content: center;
    gap: 8.5mm 8.5mm;
    padding-top: 4mm;
    page-break-after: always;
    break-after: page;
    page-break-inside: avoid;
    break-inside: avoid;
    align-content: start;
    justify-items: stretch;
}

.admit-card-page__header {
    grid-column: 1 / -1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5mm;
    padding: 0 0 2.5mm;
    margin-bottom: 1mm;
    border-bottom: 0.35mm solid #cbd5e1;
    color: #0f172a;
    font-family: Arial, Helvetica, sans-serif;
}

.admit-card-page__header-label {
    font-size: 8pt;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #475569;
}

.admit-card-page__header-value {
    font-size: 10pt;
    font-weight: 900;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.admit-card-page:last-child {
    page-break-after: auto;
    break-after: auto;
}

.admit-card {
    width: 100%;
    height: 100%;
    background: #ffffff;
    border: 0.45mm solid #111111;
    border-radius: 2mm;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
    font-family: Arial, Helvetica, sans-serif;
    color: #111;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    box-shadow: 0 0 0 0.2mm rgba(0, 0, 0, 0.08);
}

.admit-card__watermark {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    overflow: hidden;
    z-index: 0;
}

.admit-card__watermark-logo {
    width: 64%;
    max-width: 42mm;
    max-height: 42mm;
    object-fit: contain;
    opacity: 0.08;
    filter: grayscale(1) contrast(1);
    transform: translateY(0.5mm);
    mix-blend-mode: multiply;
}

.admit-card__header {
    width: 100%;
    height: var(--admit-card-header-height, auto);
    box-sizing: border-box;
    padding: var(--admit-card-front-padding, 1.7mm);
    text-align: var(--admit-card-front-align, center);
    @if ($renderForPdf ?? false)
        border-bottom: 0.3mm solid #d1d5db;
    @else
        border-bottom: 0.3mm solid var(--admit-card-theme-accent, #d1d5db);
    @endif
    background: var(--admit-card-theme-bg, #ffffff);
    position: relative;
    /* Keep the header resize handle above the body at the shared boundary. */
    z-index: 2;
}

.admit-card__element-resize-handle {
    position: absolute;
    right: -1px;
    bottom: -1px;
    width: 13px;
    height: 13px;
    z-index: 20;
    cursor: nwse-resize;
    touch-action: none;
    background: linear-gradient(135deg, transparent 0 42%, #2563eb 43% 50%, transparent 51% 63%, #2563eb 64% 71%, transparent 72%);
}

.admit-card__header-resize-handle {
    cursor: ns-resize;
}

.admit-card__brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    width: 100%;
}

.admit-card__logo {
    width: var(--admit-card-logo-size, 8mm);
    height: var(--admit-card-logo-size, 8mm);
    object-fit: contain;
    filter: grayscale(1);
    flex-shrink: 0;
    padding: var(--admit-card-logo-padding-top, 0mm) var(--admit-card-logo-padding-right, 0mm) var(--admit-card-logo-padding-bottom, 0mm) var(--admit-card-logo-padding-left, 0mm);
    margin: var(--admit-card-logo-margin-top, 0mm) var(--admit-card-logo-margin-right, 0mm) var(--admit-card-logo-margin-bottom, 0mm) var(--admit-card-logo-margin-left, 0mm);
}

.admit-card__logo-wrap {
    position: relative;
    left: var(--admit-card-position-logo-x, 0mm);
    top: var(--admit-card-position-logo-y, 0mm);
}

.admit-card__brand-text {
    flex: 1;
    min-width: 0;
}

.admit-card__school {
    position: relative;
    transform: translate(var(--admit-card-position-school_name-x, 0mm), var(--admit-card-position-school_name-y, 0mm));
    font-size: var(--admit-card-school-name-font-size, 7.2pt);
    font-weight: 900;
    line-height: 1.0;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: var(--admit-card-school-name-color, #ffffff);
    border: 0.3mm solid var(--admit-card-school-name-border-color, transparent);
    padding: var(--admit-card-school_name-padding-top, 0mm) var(--admit-card-school_name-padding-right, 0mm) var(--admit-card-school_name-padding-bottom, 0mm) var(--admit-card-school_name-padding-left, 0mm);
    margin: var(--admit-card-school_name-margin-top, 0mm) var(--admit-card-school_name-margin-right, 0mm) var(--admit-card-school_name-margin-bottom, 0mm) var(--admit-card-school_name-margin-left, 0mm);
}

.admit-card__address {
    position: relative;
    display: inline-block;
    max-width: 100%;
    box-sizing: border-box;
    transform: translate(var(--admit-card-position-school_detail-x, 0mm), var(--admit-card-position-school_detail-y, 0mm));
    margin: calc(0.55mm + var(--admit-card-school_detail-margin-top, 0mm)) var(--admit-card-school_detail-margin-right, 0mm) var(--admit-card-school_detail-margin-bottom, 0mm) var(--admit-card-school_detail-margin-left, 0mm);
    padding: var(--admit-card-school_detail-padding-top, 0mm) var(--admit-card-school_detail-padding-right, 0mm) var(--admit-card-school_detail-padding-bottom, 0mm) var(--admit-card-school_detail-padding-left, 0mm);
    font-size: var(--admit-card-school-detail-font-size, 5.4pt);
    line-height: 1.15;
    color: var(--admit-card-school-detail-color, rgba(255, 255, 255, 0.82));
    border: 0.3mm solid var(--admit-card-school-detail-border-color, transparent);
    font-weight: 600;
    word-break: break-word;
}

.admit-card__slogan {
    position: relative;
    left: var(--admit-card-position-slogan-x, 0mm);
    top: var(--admit-card-position-slogan-y, 0mm);
    padding: var(--admit-card-slogan-padding-top, 0mm) var(--admit-card-slogan-padding-right, 0mm) var(--admit-card-slogan-padding-bottom, 0mm) var(--admit-card-slogan-padding-left, 0mm);
    margin: var(--admit-card-slogan-margin-top, 0mm) var(--admit-card-slogan-margin-right, 0mm) var(--admit-card-slogan-margin-bottom, 0mm) var(--admit-card-slogan-margin-left, 0mm);
    color: var(--admit-card-slogan-color, var(--admit-card-school-detail-color, rgba(255, 255, 255, 0.82)));
    border: 0.3mm solid var(--admit-card-slogan-border-color, transparent);
}

.admit-card__exam {
    margin-top: 0;
    display: flex;
    flex-direction: column;
    align-items: var(--admit-card-exam-align, center);
}

.admit-card__exam-label {
    position: relative;
    left: var(--admit-card-position-title-x, 0mm);
    top: var(--admit-card-position-title-y, 0mm);
    display: inline-block;
    border: 0.3mm solid var(--admit-card-title-border-color, rgba(255, 255, 255, 0.55));
    padding: calc(0.75mm + var(--admit-card-title-padding-top, 0mm)) calc(2.2mm + var(--admit-card-title-padding-right, 0mm)) calc(0.75mm + var(--admit-card-title-padding-bottom, 0mm)) calc(2.2mm + var(--admit-card-title-padding-left, 0mm));
    margin: var(--admit-card-title-margin-top, 0mm) var(--admit-card-title-margin-right, 0mm) var(--admit-card-title-margin-bottom, 0mm) var(--admit-card-title-margin-left, 0mm);
    font-size: var(--admit-card-title-font-size, 4.7pt);
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--admit-card-title-color, #ffffff);
    background: rgba(255, 255, 255, 0.16);
    border-radius: 1.2mm;
}

.admit-card__exam-type {
    position: relative;
    left: var(--admit-card-position-exam_type-x, 0mm);
    top: var(--admit-card-position-exam_type-y, 0mm);
    margin: calc(0.6mm + var(--admit-card-exam_type-margin-top, 0mm)) var(--admit-card-exam_type-margin-right, 0mm) var(--admit-card-exam_type-margin-bottom, 0mm) var(--admit-card-exam_type-margin-left, 0mm);
    padding: var(--admit-card-exam_type-padding-top, 0mm) var(--admit-card-exam_type-padding-right, 0mm) var(--admit-card-exam_type-padding-bottom, 0mm) var(--admit-card-exam_type-padding-left, 0mm);
    font-size: var(--admit-card-exam-type-font-size, 7.4pt);
    font-weight: 900;
    line-height: 1.05;
    color: var(--admit-card-exam-type-color, #ffffff);
    border: 0.3mm solid var(--admit-card-exam-type-border-color, transparent);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.admit-card__exam-name {
    position: relative;
    left: var(--admit-card-position-exam_name-x, 0mm);
    top: var(--admit-card-position-exam_name-y, 0mm);
    margin: calc(0.35mm + var(--admit-card-exam_name-margin-top, 0mm)) var(--admit-card-exam_name-margin-right, 0mm) var(--admit-card-exam_name-margin-bottom, 0mm) var(--admit-card-exam_name-margin-left, 0mm);
    padding: var(--admit-card-exam_name-padding-top, 0mm) var(--admit-card-exam_name-padding-right, 0mm) var(--admit-card-exam_name-padding-bottom, 0mm) var(--admit-card-exam_name-padding-left, 0mm);
    font-size: var(--admit-card-exam-name-font-size, 6.8pt);
    font-weight: 700;
    line-height: 1.08;
    color: var(--admit-card-exam-name-color, rgba(255, 255, 255, 0.86));
    width: var(--admit-card-element-exam-name-width, auto);
    height: var(--admit-card-element-exam-name-height, auto);
    min-width: 0;
    min-height: 0;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
    box-sizing: border-box;
}

/* The badge is one draggable element; its text must not start a native text drag. */
.admit-card__exam-name[data-preview-resize-key] {
    user-select: none;
    -webkit-user-drag: none;
}

.admit-card__exam-name--badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: calc(0.75mm + var(--admit-card-exam_name-padding-top, 0mm)) calc(2.2mm + var(--admit-card-exam_name-padding-right, 0mm)) calc(0.75mm + var(--admit-card-exam_name-padding-bottom, 0mm)) calc(2.2mm + var(--admit-card-exam_name-padding-left, 0mm));
    border: 0.3mm solid var(--admit-card-exam-name-border-color, #fff200);
    border-radius: 1.2mm;
    color: var(--admit-card-exam-name-color, #fff200);
    background: rgba(255, 255, 255, 0.08);
}

.admit-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: var(--admit-card-front-padding, 2mm);
    gap: 2mm;
    text-align: var(--admit-card-front-align, center);
    position: relative;
    z-index: 1;
}

.admit-card__vertical-label {
    position: absolute;
    left: calc(1.5mm + var(--admit-card-position-vertical_label-x, 0mm));
    top: calc(50% + var(--admit-card-position-vertical_label-y, 0mm));
    z-index: 3;
    padding: 1mm 0.7mm;
    border: 0.3mm solid var(--admit-card-vertical-label-border-color, #16a085);
    color: var(--admit-card-vertical-label-color, #16a085);
    background: #ffffff;
    font-size: var(--admit-card-vertical-label-font-size, 5.2pt);
    font-weight: 800;
    letter-spacing: 0.04em;
    writing-mode: vertical-rl;
    transform: translateY(-50%) rotate(180deg);
    padding: calc(1mm + var(--admit-card-vertical_label-padding-top, 0mm)) var(--admit-card-vertical_label-padding-right, 0mm) calc(1mm + var(--admit-card-vertical_label-padding-bottom, 0mm)) var(--admit-card-vertical_label-padding-left, 0mm);
    margin: var(--admit-card-vertical_label-margin-top, 0mm) var(--admit-card-vertical_label-margin-right, 0mm) var(--admit-card-vertical_label-margin-bottom, 0mm) var(--admit-card-vertical_label-margin-left, 0mm);
}

.admit-card__body--with-vertical-label {
    padding-left: 8mm;
}

.admit-card__content {
    flex: 1;
    min-height: 0;
    display: grid;
    grid-template-columns: minmax(0, 1fr) var(--admit-card-photo-width, 20mm);
    gap: 2mm;
    align-items: start;
}

.admit-card__photo-wrap {
    height: auto;
    min-height: 0;
    width: var(--admit-card-photo-width, 20mm);
    min-width: var(--admit-card-photo-width, 20mm);
    display: flex;
    align-items: flex-start;
    justify-content: start;
    justify-self: end;
    flex-direction: column;
    gap: 0;
    position: relative;
    z-index: 2;
    padding: var(--admit-card-photo-padding-top, 0mm) var(--admit-card-photo-padding-right, 0mm) var(--admit-card-photo-padding-bottom, 0mm) var(--admit-card-photo-padding-left, 0mm);
    margin: var(--admit-card-photo-margin-top, 0mm) var(--admit-card-photo-margin-right, 0mm) var(--admit-card-photo-margin-bottom, 0mm) var(--admit-card-photo-margin-left, 0mm);
    left: var(--admit-card-position-photo-x, 0mm);
    top: var(--admit-card-position-photo-y, 0mm);
}

.admit-card__photo {
    width: var(--admit-card-photo-width, 20mm);
    height: var(--admit-card-photo-height, 30mm);
    object-fit: var(--admit-card-photo-fit, cover);
    border: 0.35mm solid #111111;
    border-radius: 1.4mm;
    {{-- filter: grayscale(1); --}}
    box-shadow: 0 0.3mm 1mm rgba(15, 23, 42, 0.12);
}

.admit-card__info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1.3mm;
    text-align: var(--admit-card-student-detail-align, left);
    padding: var(--admit-card-student_detail-padding-top, 0mm) var(--admit-card-student_detail-padding-right, 0mm) var(--admit-card-student_detail-padding-bottom, 0mm) var(--admit-card-student_detail-padding-left, 0mm);
    margin: var(--admit-card-student_detail-margin-top, 0mm) var(--admit-card-student_detail-margin-right, 0mm) var(--admit-card-student_detail-margin-bottom, 0mm) var(--admit-card-student_detail-margin-left, 0mm);
}

.admit-card__name {
    position: relative;
    left: var(--admit-card-position-name-x, 0mm);
    top: var(--admit-card-position-name-y, 0mm);
    font-size: var(--admit-card-name-font-size, 7.2pt);
    font-weight: 900;
    line-height: 1.0;
    text-align: inherit;
    word-break: break-word;
    color: var(--admit-card-name-color, var(--admit-card-student-detail-color, #111111));
    padding: var(--admit-card-name-padding-top, 0mm) var(--admit-card-name-padding-right, 0mm) var(--admit-card-name-padding-bottom, 0mm) var(--admit-card-name-padding-left, 0mm);
    margin: var(--admit-card-name-margin-top, 0mm) var(--admit-card-name-margin-right, 0mm) var(--admit-card-name-margin-bottom, 0mm) var(--admit-card-name-margin-left, 0mm);
}

.admit-card__rows {
    position: relative;
    left: var(--admit-card-position-student_detail-x, 0mm);
    top: var(--admit-card-position-student_detail-y, 0mm);
    display: flex;
    flex-direction: column;
    gap: 1.05mm;
}

.admit-card__student-field {
    min-width: 0;
}

.admit-card__row {
    display: flex;
    gap: 4mm;
    align-items: flex-start;
    line-height: 1.08;
    font-size: var(--admit-card-student-detail-font-size, 8.5pt);
}

.admit-card__row.admit-card__student-name-row {
    padding: 0;
    margin: 0;
}

.admit-card__lbl {
    width: 28mm;
    min-width: 28mm;
    max-width: 28mm;
    flex: 0 0 28mm;
    font-weight: 800;
    text-transform: none;
    color: var(--admit-card-student-detail-color, #666666);
    flex-shrink: 0;
    letter-spacing: 0.04em;
    text-align: left;
    white-space: nowrap;
}

.admit-card__colon {
    flex: 0 0 2mm;
    width: 2mm;
    text-align: center;
    font-weight: 800;
    color: var(--admit-card-student-detail-color, #666666);
}

.admit-card__lbl--titlecase {
    text-transform: none;
}

.admit-card__val {
    min-width: 0;
    flex: 1 1 auto;
    overflow-wrap: anywhere;
    font-weight: 700;
    color: var(--admit-card-student-detail-color, #111111);
    word-break: break-word;
    font-size: var(--admit-card-student-detail-font-size, 8.5pt);
}

.admit-card__signature {
    position: absolute;
    left: calc(50% + var(--admit-card-position-signature-x, 0mm));
    top: calc(50% + 20mm + var(--admit-card-position-signature-y, 0mm));
    transform: translateX(-50%);
    width: var(--admit-card-signature-size, 14mm);
    height: 12mm;
    min-height: 12mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    z-index: 1;
    padding: var(--admit-card-signature-padding-top, 0mm) var(--admit-card-signature-padding-right, 0mm) var(--admit-card-signature-padding-bottom, 0mm) var(--admit-card-signature-padding-left, 0mm);
    margin: var(--admit-card-signature-margin-top, 0mm) var(--admit-card-signature-margin-right, 0mm) var(--admit-card-signature-margin-bottom, 0mm) var(--admit-card-signature-margin-left, 0mm);
}

.admit-card__signature-image {
    position: static;
    width: var(--admit-card-signature-size, 14mm);
    max-width: 100%;
    height: auto;
    object-fit: contain;
    display: block;
}

.admit-card__signature-label {
    position: static;
    font-size: 5.2pt;
    font-weight: 700;
    color: #3f3f46;
    text-transform: capitalize;
    line-height: 1;
    text-align: center;
}

.admit-card__footer {
    @if ($renderForPdf ?? false)
        border-top: 0.3mm solid #d1d5db;
    @else
        border-top: 0.3mm solid var(--admit-card-theme-accent, #d1d5db);
    @endif
    padding: calc(0.7mm + var(--admit-card-footer-padding-top, 0mm)) calc(1.5mm + var(--admit-card-footer-padding-right, 0mm)) calc(0.7mm + var(--admit-card-footer-padding-bottom, 0mm)) calc(1.5mm + var(--admit-card-footer-padding-left, 0mm));
    font-size: var(--admit-card-footer-font-size, 4.5pt);
    line-height: 1.05;
    display: flex;
    justify-content: space-between;
    gap: 2mm;
    color: var(--admit-card-footer-color, var(--admit-card-school-detail-color, #444444));
    background: #fafafa;
    position: relative;
    z-index: 1;
    text-align: var(--admit-card-back-align, center);
    margin: var(--admit-card-footer-margin-top, 0mm) var(--admit-card-footer-margin-right, 0mm) var(--admit-card-footer-margin-bottom, 0mm) var(--admit-card-footer-margin-left, 0mm);
    position: relative;
    left: var(--admit-card-position-footer-x, 0mm);
    top: var(--admit-card-position-footer-y, 0mm);
}

.admit-card__footer span {
    white-space: nowrap;
}

@media screen {
    .admit-card-pages {
        padding: 1mm 24px 2mm;
    }
}

@media print {
    .admit-card-pages {
        padding-top: 6mm !important;
    }

    .no-print,
    .main-sidebar,
    .main-header,
    .content-header {
        display: none !important;
    }

    .content-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
        background: #fff !important;
    }

    body {
        background: #fff !important;
    }

    .admit-card {
        box-shadow: none !important;
    }

    .admit-card-pages {
        padding: 0 !important;
    }
}
