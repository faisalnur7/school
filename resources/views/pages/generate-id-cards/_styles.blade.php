@php
    $idColor = $setting?->id_card_color ?? '#1e3a5f';
    $secondary = $setting?->secondary_color ?? '#2563eb';
@endphp

@page {
    size: A4 landscape;
    margin: 8mm;
}

.id-card-pages {
    display: flex;
    flex-direction: column;
    gap: 8mm;
}

.id-card-page {
    display: grid;
    grid-template-columns: repeat(2, max-content);
    justify-content: center;
    align-content: start;
    gap: 6mm 5mm;
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
    gap: 4mm;
    align-items: stretch;
    page-break-inside: avoid;
    break-inside: avoid;
}

.id-card {
    position: relative;
    width: 54mm;
    height: 84mm;
    overflow: hidden;
    border-radius: 3.5mm;
    background: #fff;
    border: 0.35mm solid #cbd5e1;
    box-shadow: 0 1mm 3mm rgba(15, 23, 42, 0.14);
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
    color: #fff;
    padding: 2.2mm 2.2mm 1.8mm;
    display: flex;
    align-items: center;
    gap: 1.8mm;
}

.id-card__header--front {
    background: linear-gradient(135deg, {{ $idColor }}, {{ $secondary }});
    flex-direction: column;
    text-align: center;
    justify-content: center;
    min-height: 19mm;
}

.id-card__header--back {
    background: #222;
    justify-content: center;
    min-height: 13mm;
    flex-direction: column;
    text-align: center;
}

.id-card__logo {
    width: 8mm;
    height: 8mm;
    object-fit: contain;
}

.id-card__school-name {
    font-size: 7.2pt;
    font-weight: 800;
    line-height: 1.08;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.id-card__slogan {
    font-size: 4.8pt;
    opacity: 0.85;
    line-height: 1.05;
    margin-top: 0.3mm;
}

.id-card__label-badge {
    display: inline-block;
    margin-top: 1mm;
    padding: 0.6mm 2mm;
    border-radius: 10mm;
    border: 0.25mm solid rgba(255,255,255,0.45);
    background: rgba(255,255,255,0.16);
    font-size: 4.7pt;
    font-weight: 700;
    letter-spacing: 0.08em;
    white-space: nowrap;
}

.id-card__front-body {
    flex: 1;
    padding: 2mm 2.2mm 1.6mm;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.id-card__photo {
    width: 14.5mm;
    height: 18mm;
    object-fit: cover;
    border-radius: 1.6mm;
    border: 0.3mm solid #e2e8f0;
    box-shadow: 0 0.6mm 1.8mm rgba(15, 23, 42, 0.12);
}

.id-card__info {
    width: 100%;
    margin-top: 1.4mm;
}

.id-card__name {
    text-align: center;
    font-size: 7.2pt;
    font-weight: 800;
    color: {{ $idColor }};
    line-height: 1.15;
    word-break: break-word;
}

.id-card__name-bn {
    text-align: center;
    font-size: 5.4pt;
    color: #475569;
    line-height: 1.12;
    margin-top: 0.3mm;
    word-break: break-word;
}

.id-card__divider {
    height: 0.45mm;
    margin: 1.4mm 0 1.6mm;
    border-radius: 1mm;
    background: linear-gradient(90deg, {{ $idColor }}, transparent);
}

.id-card__rows {
    display: flex;
    flex-direction: column;
    gap: 0.6mm;
}

.id-card__row,
.id-card__back-row {
    display: flex;
    gap: 1.3mm;
    align-items: flex-start;
    line-height: 1.12;
}

.id-card__lbl {
    min-width: 8.6mm;
    font-size: 4.2pt;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #94a3b8;
    flex-shrink: 0;
}

.id-card__val {
    font-size: 4.6pt;
    font-weight: 700;
    color: #1e293b;
    word-break: break-word;
}

.id-card__blood {
    color: #dc2626 !important;
}

.id-card__footer {
    margin-top: auto;
    padding: 1.3mm 1.8mm;
    font-size: 4.4pt;
    line-height: 1.05;
    color: rgba(255,255,255,0.88);
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1.5mm;
    text-align: center;
}

.id-card__back-body {
    flex: 1;
    padding: 1.7mm 2mm 1.5mm;
    display: flex;
    flex-direction: column;
    gap: 1.4mm;
}

.id-card__back-section {
    display: flex;
    flex-direction: column;
    gap: 0.7mm;
}

.id-card__back-title {
    font-size: 4.9pt;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #222;
    border-bottom: 0.25mm solid #cbd5e1;
    padding-bottom: 0.8mm;
}

.id-card__back-row .id-card__lbl {
    min-width: 10mm;
    font-size: 4.1pt;
}

.id-card__back-row .id-card__val {
    font-size: 4.25pt;
}

.id-card__back-notice {
    margin-top: auto;
    padding-top: 1.2mm;
    border-top: 0.25mm dashed #e2e8f0;
    text-align: center;
    font-size: 4.1pt;
    font-style: italic;
    color: #94a3b8;
}

.id-card__qr {
    width: 11mm;
    height: 11mm;
    object-fit: contain;
    border: 0.25mm solid #e2e8f0;
    border-radius: 1mm;
    padding: 0.4mm;
}

@media screen {
    .id-card-pages {
        padding-bottom: 8mm;
    }
}

@media print {
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
