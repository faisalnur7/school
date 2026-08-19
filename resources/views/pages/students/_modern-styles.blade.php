{{-- Student Form Modern Styles --}}
<style>
    .student-form-page {
        position: relative;
        padding: 0.65rem 0 1rem;
        background:
            radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 30%),
            radial-gradient(circle at top right, rgba(16, 185, 129, 0.10), transparent 28%),
            linear-gradient(180deg, #f8fbff 0%, #f3f6fb 100%);
    }

    .student-form-shell {
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.5rem;
        background: rgba(255, 255, 255, 0.96);
        box-shadow:
            0 24px 70px rgba(15, 23, 42, 0.08),
            0 2px 8px rgba(15, 23, 42, 0.05);
        backdrop-filter: blur(10px);
    }

    .student-form-header {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.8rem 1.05rem;
        color: #fff;
        background:
            linear-gradient(135deg, #0f172a 0%, #1d4ed8 58%, #2563eb 100%);
    }

    .student-form-header::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            linear-gradient(120deg, rgba(255, 255, 255, 0.12), transparent 40%),
            radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.12), transparent 35%);
        pointer-events: none;
    }

    .student-form-header > * {
        position: relative;
        z-index: 1;
    }

    .student-form-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .student-form-subtitle {
        margin: 0.15rem 0 0;
        max-width: 54rem;
        color: rgba(255, 255, 255, 0.78);
        font-size: 0.8rem;
        line-height: 1.35;
    }

    .student-form-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        justify-content: flex-end;
    }

    .student-form-view-switcher {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.22rem;
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.2);
        backdrop-filter: blur(12px);
    }

    .student-view-toggle {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.48rem 0.75rem;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: rgba(255, 255, 255, 0.78);
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        transition: background-color 0.16s ease, color 0.16s ease, transform 0.16s ease;
    }

    .student-view-toggle:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.12);
    }

    .student-view-toggle.is-active {
        color: #0f172a;
        background: #fff;
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.12);
    }

    .student-form-body {
        padding: 0.85rem;
    }

    .student-section {
        position: relative;
        margin-bottom: 0.7rem;
        padding: 0;
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        overflow: hidden;
    }

    .student-section:hover {
        border-color: rgba(96, 165, 250, 0.35);
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .student-section > summary {
        list-style: none;
        cursor: pointer;
        user-select: none;
    }

    .student-section > summary::-webkit-details-marker {
        display: none;
    }

    .student-section__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin: 0;
        padding: 0.75rem 0.85rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.95);
    }

    .student-section__head h5 {
        margin: 0;
        color: #0f172a;
        font-size: 0.92rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }

    .student-section__head p {
        margin: 0.15rem 0 0;
        color: #64748b;
        font-size: 0.75rem;
        line-height: 1.3;
    }

    .student-section__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.68rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .student-section__chevron {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.6rem;
        height: 1.6rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.08);
        color: #1d4ed8;
        flex: 0 0 auto;
        transition: transform 0.18s ease, background-color 0.18s ease;
    }

    .student-section[open] .student-section__chevron {
        transform: rotate(180deg);
        background: rgba(37, 99, 235, 0.12);
    }

    .student-form-page[data-student-view='tabs'] .student-section {
        display: none;
    }

    .student-form-page[data-student-view='tabs'] .student-section.is-active {
        display: block;
    }

    .student-form-page[data-student-view='tabs'] .student-section > summary {
        display: none;
    }

    .student-section__body {
        padding: 0.75rem 0.85rem 0.85rem;
    }

    .student-tabbar {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        overflow-x: auto;
        padding: 0.2rem 0.1rem 0.55rem;
        margin-bottom: 0.65rem;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }

    .student-tab-button {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 0.48rem 0.9rem;
        border: 1px solid #dbeafe;
        border-radius: 999px;
        background: #fff;
        color: #475569;
        font-size: 0.78rem;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.18s ease;
    }

    .student-tab-button:hover {
        border-color: #93c5fd;
        color: #1d4ed8;
        box-shadow: 0 8px 18px rgba(59, 130, 246, 0.08);
    }

    .student-tab-button.is-active {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        border-color: #1d4ed8;
        color: #fff;
        box-shadow: 0 12px 22px rgba(37, 99, 235, 0.18);
    }

    .student-basic-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 340px);
        gap: 0.9rem;
        align-items: start;
    }

    .student-basic-fields {
        min-width: 0;
        padding: 0.85rem;
        border: 1px solid #dbeafe;
        border-radius: 1rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.98));
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.04);
    }

    .student-basic-media {
        min-width: 0;
        display: flex;
        align-items: flex-start;
    }

    .student-basic-media .student-image-dropzone {
        min-height: 100%;
    }

    .student-basic-media__card {
        width: 100%;
        padding: 0.85rem;
        border: 1px solid #dbeafe;
        border-radius: 1rem;
        background:
            radial-gradient(circle at top right, rgba(59, 130, 246, 0.10), transparent 35%),
            linear-gradient(180deg, rgba(239, 246, 255, 0.88), rgba(255, 255, 255, 0.96));
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        position: sticky;
        top: 0.85rem;
    }

    .student-basic-media__title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin: 0 0 0.7rem;
        color: #0f172a;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .student-basic-media__note {
        margin: 0.45rem 0 0;
        color: #64748b;
        font-size: 0.72rem;
        line-height: 1.35;
    }

    .student-form-page .student-address-synced select,
    .student-form-page .student-address-synced textarea {
        pointer-events: none;
        background: #f8fafc;
        color: #64748b;
    }

    #guardianInfoFields {
        overflow: hidden;
    }

    .student-field-grid {
        gap: 0.8rem;
    }

    #guardianInfoFields .student-field-grid {
        row-gap: 1rem;
    }

    #guardianInfoFields .relative.w-full {
        padding-top: 0.35rem;
    }

    .student-form-page input[type="text"],
    .student-form-page input[type="file"],
    .student-form-page input[type="number"],
    .student-form-page select,
    .student-form-page textarea {
        width: 100%;
        padding: 0.82rem 0.9rem;
        border: 1px solid #d8e1ee;
        border-radius: 0.95rem;
        background: #fff;
        color: #0f172a;
        font-size: 0.91rem;
        line-height: 1.35;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
        transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
    }

    .student-form-page input[type="text"],
    .student-form-page input[type="number"],
    .student-form-page select {
        min-height: 42px;
    }

    .student-form-page textarea {
        min-height: 86px;
        padding-top: 0.82rem;
        padding-bottom: 0.82rem;
    }

    .student-form-page input[type="text"]:focus,
    .student-form-page input[type="number"]:focus,
    .student-form-page input[type="file"]:focus,
    .student-form-page select:focus,
    .student-form-page textarea:focus {
        outline: none;
        border-color: #60a5fa;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
    }

    .student-form-page .student-floating-label {
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .student-form-page .student-floating-input {
        padding-top: 1.02rem;
        padding-bottom: 0.65rem;
    }

    .student-form-page .student-floating-input::placeholder {
        color: transparent;
    }

    .student-form-page .student-floating-input:focus + .student-floating-label,
    .student-form-page .student-floating-input:not(:placeholder-shown) + .student-floating-label,
    .student-form-page .student-floating-input[value]:not([value=""]) + .student-floating-label {
        color: #1d4ed8;
    }

    .student-form-page .form-label,
    .student-form-page label {
        margin-bottom: 0.3rem;
    }

    .student-form-page .student-image-dropzone {
        min-height: 320px;
        padding: 0.85rem;
        border-radius: 1rem;
        border-color: #c7d2fe;
        background:
            linear-gradient(180deg, rgba(239, 246, 255, 0.95), rgba(248, 250, 252, 0.98));
    }

    .student-form-page .student-image-dropzone:hover {
        border-color: #2563eb;
        background:
            linear-gradient(180deg, rgba(239, 246, 255, 0.98), rgba(255, 255, 255, 0.98));
        box-shadow: 0 14px 30px rgba(37, 99, 235, 0.12);
    }

    .student-form-page .student-image-dropzone .dz-message {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 165px;
        margin: 0;
        text-align: center;
    }

    .student-form-page .student-image-dropzone.dz-started .dz-message {
        display: none;
    }

    .student-form-page .student-image-dropzone .dz-preview {
        margin: 0 auto;
        width: 100%;
        max-width: 220px;
    }

    .student-form-page .student-image-dropzone .dz-preview .dz-image {
        width: 220px;
        height: 300px;
        border-radius: 1rem;
    }

    .student-form-page .student-image-dropzone .dz-preview .dz-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .student-form-page .student-image-dropzone .dz-preview .dz-details {
        padding: 0.65rem;
    }

    .student-form-page .student-image-dropzone .dz-preview .dz-remove {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.55rem;
        padding: 0.32rem 0.65rem;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        text-decoration: none;
        font-size: 0.78rem;
        font-weight: 700;
    }

    .student-form-page .student-image-dropzone .dz-preview .dz-remove:hover {
        background: #fecaca;
        color: #991b1b;
    }

    .student-form-page .student-form-actions {
        display: flex;
        gap: 0.55rem;
        flex-wrap: wrap;
        justify-content: flex-end;
        margin-top: 0.1rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(226, 232, 240, 0.95);
    }

    .student-form-page .student-action-btn {
        min-width: 104px;
    }

    .student-form-page .student-muted-note {
        color: #64748b;
        font-size: 0.84rem;
    }

    @media (max-width: 991.98px) {
        .student-form-header,
        .student-section__head {
            flex-direction: column;
            align-items: flex-start;
        }

        .student-form-header-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .student-basic-layout {
            grid-template-columns: 1fr;
        }

        .student-basic-media__card {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .student-form-page {
            padding: 0.5rem 0 1rem;
        }

        .student-form-body {
            padding: 0.75rem;
        }

        .student-section {
            padding: 0.7rem;
            border-radius: 0.95rem;
        }

        .student-basic-fields,
        .student-basic-media__card {
            padding: 0.7rem;
        }

        .student-form-page .student-image-dropzone {
            min-height: 220px;
        }

        .student-form-page .student-image-dropzone .dz-message {
            min-height: 120px;
        }

        .student-form-page .student-image-dropzone .dz-preview .dz-image {
            width: 100%;
            max-width: 155px;
            height: 225px;
        }

        .student-form-view-switcher {
            width: 100%;
            justify-content: stretch;
        }

        .student-view-toggle {
            flex: 1 1 0;
            justify-content: center;
            padding-inline: 0.6rem;
        }
    }

</style>
