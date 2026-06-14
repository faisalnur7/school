@page {
    size: A4 portrait;
    margin: 19.05mm;
}

.admit-card-pages {
    display: flex;
    flex-direction: column;
    gap: 3mm;
}

.admit-card-page {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    justify-content: center;
    gap: 3mm 3mm;
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
    background: #fff;
    border: 0.35mm solid #111;
    border-radius: 2mm;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    font-family: Arial, Helvetica, sans-serif;
    color: #111;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.admit-card__header {
    padding: 1.4mm 1.6mm 1mm;
    text-align: center;
    border-bottom: 0.2mm solid #d1d5db;
}

.admit-card__school {
    font-size: 6.9pt;
    font-weight: 800;
    line-height: 1.08;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.admit-card__meta {
    font-size: 4pt;
    line-height: 1.12;
    margin-top: 0.45mm;
    color: #333;
    word-break: break-word;
}

.admit-card__exam {
    margin-top: 0.8mm;
}

.admit-card__exam-label {
    display: inline-block;
    border: 0.25mm solid #111;
    padding: 0.4mm 1.6mm;
    font-size: 4.2pt;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.admit-card__body {
    flex: 1;
    display: flex;
    padding: 1.6mm 1.7mm 1.2mm;
    gap: 1.6mm;
    align-items: flex-start;
}

.admit-card__photo-wrap {
    flex: 0 0 15mm;
}

.admit-card__photo {
    width: 15mm;
    height: 18mm;
    object-fit: cover;
    border: 0.25mm solid #222;
    border-radius: 1.1mm;
    filter: grayscale(1);
}

.admit-card__info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.8mm;
    min-width: 0;
}

.admit-card__name {
    font-size: 6.2pt;
    font-weight: 800;
    line-height: 1.08;
    text-align: left;
    word-break: break-word;
}

.admit-card__rows {
    display: flex;
    flex-direction: column;
    gap: 0.45mm;
}

.admit-card__row {
    display: flex;
    gap: 1.3mm;
    align-items: flex-start;
    line-height: 1.1;
    font-size: 4.25pt;
}

.admit-card__lbl {
    min-width: 11mm;
    font-weight: 700;
    text-transform: uppercase;
    color: #666;
    flex-shrink: 0;
    letter-spacing: 0.03em;
}

.admit-card__val {
    font-weight: 700;
    color: #111;
    word-break: break-word;
}

.admit-card__footer {
    border-top: 0.2mm solid #d1d5db;
    padding: 0.8mm 1.6mm;
    font-size: 3.9pt;
    line-height: 1.1;
    display: flex;
    justify-content: space-between;
    gap: 2mm;
    color: #444;
}

.admit-card__footer span {
    white-space: nowrap;
}

@media screen {
    .admit-card-pages {
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

    .admit-card {
        box-shadow: none !important;
    }
}
