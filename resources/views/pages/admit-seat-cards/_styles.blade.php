@page {
    size: A4 portrait;
    margin: 4mm;
}

.admit-card-pages {
    display: flex;
    flex-direction: column;
    gap: 8.5mm;
    padding: 0;
}

.admit-card-page {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    justify-content: center;
    gap: 8.5mm 8.5mm;
    page-break-after: always;
    break-after: page;
    page-break-inside: avoid;
    break-inside: avoid;
    align-content: start;
    justify-items: stretch;
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
    width: 84%;
    max-width: 56mm;
    max-height: 56mm;
    object-fit: contain;
    opacity: 0.18;
    filter: grayscale(1) contrast(1);
    transform: translateY(1mm);
    mix-blend-mode: multiply;
}

.admit-card__header {
    padding: 1.7mm 1.8mm 1.3mm;
    text-align: center;
    border-bottom: 0.3mm solid #d1d5db;
    position: relative;
    z-index: 1;
}

.admit-card__brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2.2mm;
    width: 100%;
}

.admit-card__logo {
    width: 8mm;
    height: 8mm;
    object-fit: contain;
    filter: grayscale(1);
    flex-shrink: 0;
}

.admit-card__brand-text {
    flex: 1;
    min-width: 0;
}

.admit-card__school {
    font-size: 9.8pt;
    font-weight: 900;
    line-height: 1.0;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #111111;
}

.admit-card__address {
    margin-top: 0.55mm;
    font-size: 6.4pt;
    line-height: 1.15;
    color: #444444;
    font-weight: 600;
    word-break: break-word;
}

.admit-card__exam {
    margin-top: 1mm;
}

.admit-card__exam-label {
    display: inline-block;
    border: 0.3mm solid #111111;
    padding: 0.75mm 2.2mm;
    font-size: 5.4pt;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #111111;
    background: #f4f4f5;
    border-radius: 1.2mm;
}

.admit-card__exam-type {
    margin-top: 0.6mm;
    font-size: 7.4pt;
    font-weight: 900;
    line-height: 1.05;
    color: #111111;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.admit-card__exam-name {
    margin-top: 0.35mm;
    font-size: 6.8pt;
    font-weight: 700;
    line-height: 1.08;
    color: #444444;
}

.admit-card__body {
    flex: 1;
    display: flex;
    padding: 2mm 1.7mm 2.2mm;
    gap: 2mm;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.admit-card__photo-wrap {
    flex: 0 0 19mm;
    display: flex;
    align-items: center;
    justify-content: center;
}

.admit-card__photo {
    width: 19mm;
    height: 21mm;
    object-fit: cover;
    border: 0.35mm solid #111111;
    border-radius: 1.2mm;
    filter: grayscale(1);
    box-shadow: 0 0.3mm 1mm rgba(15, 23, 42, 0.12);
}

.admit-card__info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1mm;
    min-width: 0;
}

.admit-card__name {
    font-size: 9.2pt;
    font-weight: 900;
    line-height: 1.02;
    text-align: left;
    word-break: break-word;
    color: #111111;
}

.admit-card__rows {
    display: flex;
    flex-direction: column;
    gap: 0.7mm;
}

.admit-card__row {
    display: flex;
    gap: 1.5mm;
    align-items: flex-start;
    line-height: 1.06;
    font-size: 6pt;
}

.admit-card__lbl {
    min-width: 12mm;
    font-weight: 800;
    text-transform: uppercase;
    color: #666666;
    flex-shrink: 0;
    letter-spacing: 0.04em;
}

.admit-card__val {
    font-weight: 700;
    color: #111111;
    word-break: break-word;
}

.admit-card__footer {
    border-top: 0.3mm solid #d1d5db;
    padding: 0.9mm 1.5mm;
    font-size: 4.5pt;
    line-height: 1.05;
    display: flex;
    justify-content: space-between;
    gap: 2mm;
    color: #444444;
    background: #fafafa;
    position: relative;
    z-index: 1;
}

.admit-card__footer span {
    white-space: nowrap;
}

@media screen {
    .admit-card-pages {
        padding: 1mm 0 2mm;
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

    .admit-card {
        box-shadow: none !important;
    }

    .admit-card-pages {
        padding: 0 !important;
    }
}
