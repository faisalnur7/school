@page {
    size: A4 landscape;
    margin: 1cm 0.8cm 0.8cm 0.8cm;
}

.id-card-pages {
    display: flex;
    flex-direction: column;
    gap: var(--id-card-gap, 0.5cm);
    padding-top: 0.6cm;
}

.id-card-page {
    display: grid;
    justify-content: center;
    align-content: start;
    gap: var(--id-card-gap, 0.5cm) var(--id-card-gap, 0.5cm);
    page-break-inside: avoid;
    break-inside: avoid;
    page-break-after: always;
    break-after: page;
}

.id-card-page:last-child {
    page-break-after: auto;
    break-after: auto;
}

.id-card-pair {
    display: flex;
    gap: var(--id-card-gap, 0.5cm);
    align-items: stretch;
    page-break-inside: avoid;
    break-inside: avoid;
}

.id-card {
    position: relative;
    width: var(--id-card-width, 5.4cm);
    height: var(--id-card-height, 8.4cm);
    overflow: hidden;
    border-radius: 0.35cm;
    background: #fff;
    border: 0.035cm solid #cbd5e1;
    box-shadow: 0 0.1cm 0.3cm rgba(15, 23, 42, 0.14);
    display: flex;
    flex-direction: column;
    font-family: Arial, Helvetica, sans-serif;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.id-card--back {
    border-color: #333;
}

.id-card__header {
    padding: var(--id-card-front-padding, 0.22cm);
    display: flex;
    align-items: center;
    gap: 0.1cm;
}

.id-card__header--front {
    background: var(--card-theme-bg, linear-gradient(135deg, #1e3a5f, #2563eb));
    flex-direction: column;
    text-align: var(--id-card-front-align, center);
    justify-content: center;
    min-height: 2cm;
    align-items: center;
}

.id-card__header--back {
    background: var(--card-theme-bg, linear-gradient(135deg, #1e3a5f, #2563eb));
    justify-content: center;
    min-height: 1.3cm;
    flex-direction: column;
    text-align: var(--id-card-back-align, center);
    align-items: center;
}

.id-card__logo {
    width: var(--id-card-logo-size, 0.8cm);
    height: var(--id-card-logo-size, 0.8cm);
    object-fit: var(--id-card-logo-fit, contain);
}

.id-card__school-name {
    font-size: var(--id-card-school-name-font-size, 7.2pt);
    font-weight: 800;
    line-height: 1.08;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--id-card-school-name-color, #ffffff);
}

.id-card__slogan {
    font-size: var(--id-card-slogan-font-size, 4.8pt);
    opacity: 0.85;
    line-height: 1.05;
    margin-top: 0.03cm;
    color: var(--id-card-slogan-color, var(--id-card-school-detail-color, #e5e7eb));
}

.id-card__label-badge {
    display: inline-block;
    margin-top: 0.1cm;
    padding: 0.06cm 0.2cm;
    border-radius: 1cm;
    border: 0.025cm solid var(--id-card-title-color, rgba(255,255,255,0.45));
    background: rgba(255,255,255,0.16);
    font-size: var(--id-card-title-font-size, 4.7pt);
    font-weight: 700;
    letter-spacing: 0.08em;
    white-space: nowrap;
    color: var(--id-card-title-color, #ffffff);
}

.id-card__front-body {
    flex: 1;
    padding: var(--id-card-front-padding, 0.2cm);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: var(--id-card-front-align, center);
}

.id-card__photo {
    width: var(--id-card-photo-width, 1.8cm);
    height: var(--id-card-photo-height, 2.7cm);
    object-fit: var(--id-card-photo-fit, cover);
    border-radius: 0.18cm;
    border: 0.03cm solid #e2e8f0;
    box-shadow: 0 0.06cm 0.18cm rgba(15, 23, 42, 0.12);
}

.id-card__info {
    width: 100%;
    margin-top: 0.14cm;
}

.id-card__name {
    text-align: center;
    font-size: var(--id-card-name-font-size, 7.2pt);
    font-weight: 800;
    color: var(--id-card-name-color, var(--card-theme-accent, #1e3a5f));
    line-height: 1.15;
    word-break: break-word;
}

.id-card__name-bn {
    text-align: center;
    font-size: 5.4pt;
    color: var(--id-card-school-detail-color, #475569);
    line-height: 1.12;
    margin-top: 0.03cm;
    word-break: break-word;
}

.id-card__divider {
    height: 0.045cm;
    margin: 0.14cm 0 0.16cm;
    border-radius: 0.1cm;
    background: linear-gradient(90deg, var(--card-theme-accent, #1e3a5f), transparent);
}

.id-card__rows {
    display: flex;
    flex-direction: column;
    gap: 0.06cm;
    align-items: var(--id-card-student-detail-align, flex-start);
    text-align: var(--id-card-student-detail-text-align, left);
}

.id-card__row,
.id-card__back-row {
    display: flex;
    gap: 0.13cm;
    align-items: flex-start;
    line-height: 1.12;
}

.id-card__lbl {
    min-width: 0.86cm;
    font-size: 4.2pt;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--id-card-school-detail-color, #94a3b8);
    flex-shrink: 0;
}

.id-card__val {
    font-size: 4.6pt;
    font-weight: 700;
    color: var(--id-card-school-detail-color, #1e293b);
    word-break: break-word;
}

.id-card__blood {
    color: #dc2626 !important;
}

.id-card__footer {
    margin-top: auto;
    padding: 0.13cm 0.18cm;
    font-size: 4.4pt;
    line-height: 1.05;
    color: var(--id-card-footer-color, var(--id-card-school-detail-color, rgba(255,255,255,0.88)));
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 0.15cm;
    text-align: center;
}

.id-card__footer--front,
.id-card__footer--back {
    background: var(--card-theme-bg, linear-gradient(135deg, #1e3a5f, #2563eb));
}

.id-card__back-body {
    flex: 1;
    padding: var(--id-card-back-padding, 0.17cm);
    display: flex;
    flex-direction: column;
    gap: 0.14cm;
    text-align: var(--id-card-back-align, center);
}

.id-card__back-section {
    display: flex;
    flex-direction: column;
    gap: 0.07cm;
    align-items: var(--id-card-student-detail-align, flex-start);
    text-align: var(--id-card-student-detail-text-align, left);
}

.id-card__back-title {
    font-size: var(--id-card-back-title-font-size, var(--id-card-title-font-size, 4.9pt));
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--id-card-title-color, var(--card-theme-accent, #1e3a5f));
    border-bottom: 0.025cm solid var(--id-card-title-color, var(--card-theme-accent, #1e3a5f));
    padding-bottom: 0.08cm;
}

.id-card__back-row .id-card__lbl {
    min-width: 1cm;
    font-size: 4.1pt;
}

.id-card__back-row .id-card__val {
    font-size: var(--id-card-back-value-font-size, var(--id-card-school-detail-font-size, 4.25pt));
}

.id-card__back-notice {
    margin-top: auto;
    padding-top: 0.12cm;
    border-top: 0.025cm dashed #e2e8f0;
    text-align: center;
    font-size: var(--id-card-school-detail-font-size, 4.1pt);
    font-style: italic;
    color: var(--id-card-back-notice-color, #94a3b8);
}

.id-card__signature {
    position: relative;
    width: min(100%, 4.4cm);
    align-self: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 1.35cm;
    padding-top: 0.2cm;
    flex-shrink: 0;
}

.id-card__signature--image {
    padding-top: 0.15cm;
}

.id-card__signature-line {
    width: 100%;
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0.38cm;
    border-top: 0.025cm solid #525252;
}

.id-card__signature-image {
    position: absolute;
    left: 50%;
    bottom: 0.16cm;
    transform: translateX(-50%);
    width: 100%;
    max-height: 0.85cm;
    object-fit: contain;
    display: block;
    z-index: 2;
}

.id-card__signature-label {
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    font-size: 4.5pt;
    font-weight: 700;
    color: #3f3f46;
    text-transform: capitalize;
    line-height: 1;
}

.id-card__qr {
    width: 1.1cm;
    height: 1.1cm;
    object-fit: contain;
    border: 0.025cm solid #e2e8f0;
    border-radius: 0.1cm;
    padding: 0.04cm;
}

@media screen {
    .id-card-pages {
        padding-bottom: 0.8cm;
    }
}

@media print {
    .id-card-pages {
        padding-top: 0.6cm !important;
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

    .id-card-pages {
        gap: 0;
    }

    .id-card-page {
        margin: 0;
    }

    .id-card {
        box-shadow: none !important;
    }
}
