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
    gap: 4mm;
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
    padding: var(--admit-card-front-padding, 1.7mm);
    text-align: var(--admit-card-front-align, center);
    border-bottom: 0.3mm solid var(--admit-card-theme-accent, #d1d5db);
    background: var(--admit-card-theme-bg, #ffffff);
    position: relative;
    z-index: 1;
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
}

.admit-card__brand-text {
    flex: 1;
    min-width: 0;
}

.admit-card__school {
    font-size: var(--admit-card-school-name-font-size, 7.2pt);
    font-weight: 900;
    line-height: 1.0;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: var(--admit-card-school-name-color, #ffffff);
}

.admit-card__address {
    margin-top: 0.55mm;
    font-size: var(--admit-card-school-detail-font-size, 5.4pt);
    line-height: 1.15;
    color: var(--admit-card-school-detail-color, rgba(255, 255, 255, 0.82));
    font-weight: 600;
    word-break: break-word;
}

.admit-card__exam {
    margin-top: 0;
}

.admit-card__exam-label {
    display: inline-block;
    border: 0.3mm solid var(--admit-card-title-color, rgba(255, 255, 255, 0.55));
    padding: 0.75mm 2.2mm;
    font-size: var(--admit-card-title-font-size, 4.7pt);
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--admit-card-title-color, #ffffff);
    background: rgba(255, 255, 255, 0.16);
    border-radius: 1.2mm;
}

.admit-card__exam-type {
    margin-top: 0.6mm;
    font-size: var(--admit-card-exam-type-font-size, 7.4pt);
    font-weight: 900;
    line-height: 1.05;
    color: var(--admit-card-exam-type-color, #ffffff);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.admit-card__exam-name {
    margin-top: 0.35mm;
    font-size: var(--admit-card-exam-name-font-size, 6.8pt);
    font-weight: 700;
    line-height: 1.08;
    color: var(--admit-card-exam-name-color, rgba(255, 255, 255, 0.86));
}

.admit-card__body {
    flex: 1;
    display: grid;
    grid-template-columns: minmax(0, 1fr) var(--admit-card-photo-width, 20mm);
    grid-template-rows: minmax(0, 1fr) auto;
    padding: var(--admit-card-front-padding, 2mm);
    gap: 2mm;
    align-items: start;
    text-align: var(--admit-card-front-align, center);
    position: relative;
    z-index: 1;
}

.admit-card__photo-wrap {
    height: 100%;
    min-height: 100%;
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
}

.admit-card__name {
    font-size: var(--admit-card-name-font-size, 7.2pt);
    font-weight: 900;
    line-height: 1.0;
    text-align: inherit;
    word-break: break-word;
    color: var(--admit-card-name-color, var(--admit-card-student-detail-color, #111111));
}

.admit-card__rows {
    display: flex;
    flex-direction: column;
    gap: 1.05mm;
}

.admit-card__row {
    display: flex;
    gap: 2mm;
    align-items: flex-start;
    line-height: 1.08;
    font-size: var(--admit-card-student-detail-font-size, 8.5pt);
}

.admit-card__lbl {
    min-width: 14mm;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--admit-card-student-detail-color, #666666);
    flex-shrink: 0;
    letter-spacing: 0.04em;
}

.admit-card__val {
    font-weight: 700;
    color: var(--admit-card-student-detail-color, #111111);
    word-break: break-word;
    font-size: var(--admit-card-student-detail-font-size, 8.5pt);
}

.admit-card__signature {
    position: absolute;
    grid-column: 1 / -1;
    justify-self: center;
    width: min(100%, 44mm);
    min-height: 12mm;
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 0;
    padding-top: 0;
    z-index: 1;
    bottom: 0;
}

.admit-card__signature-line {
    width: 100%;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 4.8mm;
    border-top: 0.35mm solid #525252;
}

.admit-card__signature-image {
    position: absolute;
    left: 50%;
    bottom: 4mm;
    transform: translateX(-50%);
    width: 100%;
    max-height: 8mm;
    object-fit: contain;
    display: block;
    z-index: 2;
}

.admit-card__signature-label {
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    font-size: 5.2pt;
    font-weight: 700;
    color: #3f3f46;
    text-transform: capitalize;
    line-height: 1;
}

.admit-card__footer {
    border-top: 0.3mm solid var(--admit-card-theme-accent, #d1d5db);
    padding: 0.7mm 1.5mm;
    font-size: 4.5pt;
    line-height: 1.05;
    display: flex;
    justify-content: space-between;
    gap: 2mm;
    color: var(--admit-card-school-detail-color, #444444);
    background: #fafafa;
    position: relative;
    z-index: 1;
    text-align: var(--admit-card-back-align, center);
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
