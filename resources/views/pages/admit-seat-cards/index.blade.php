@extends('layouts.master')

@section('styles')
    <style>
        @include('pages.admit-seat-cards._styles') input[type="color"] {
            width: 40px;
            height: 28px;
            padding: 0;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            cursor: pointer;
            overflow: hidden;
            appearance: none;
            -webkit-appearance: none;
            padding:3px;
        }

        @page {
            size: {{ $layout['pageWidthMm'] ?? 210 }}mm {{ $layout['pageHeightMm'] ?? 297 }}mm;
            margin: {{ $layout['marginTopMm'] ?? 10 }}mm {{ $layout['marginRightMm'] ?? 6.35 }}mm {{ $layout['marginBottomMm'] ?? 4 }}mm {{ $layout['marginLeftMm'] ?? 6.35 }}mm;
        }

        /* Chrome/Edge (WebKit) wrap the swatch in these pseudo-elements */
        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
            border-radius: 5px;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 5px;
        }

        /* Firefox uses a different pseudo-element */
        input[type="color"]::-moz-color-swatch {
            border: none;
            border-radius: 5px;
        }

        .admit-seat-cards-page {
            --asc-primary: #2563eb;
            --asc-primary-dark: #1d4ed8;
            --asc-primary-soft: rgba(37, 99, 235, 0.08);
            --asc-surface: #ffffff;
            --asc-surface-alt: #f8fafc;
            --asc-border: #dbe4ee;
            --asc-border-strong: #cbd5e1;
            --asc-text: #0f172a;
            --asc-muted: #475569;
            --asc-muted-2: #64748b;
            --asc-radius-xl: 18px;
            --asc-radius-lg: 14px;
            --asc-radius-md: 12px;
            --asc-shadow-sm: 0 6px 16px rgba(15, 23, 42, 0.04);
            --asc-shadow-md: 0 10px 24px rgba(15, 23, 42, 0.05);
            --asc-shadow-lg: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .dropzone .dz-preview.dz-image-preview {
            margin: 0;
        }

        .admit-seat-cards-page .id-card-upload-box {
            padding: 1rem;
            border: 1px solid var(--asc-border);
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.94) 100%);
            box-shadow: var(--asc-shadow-sm);
        }

        .admit-seat-cards-page .id-card-upload-box .dropzone {
            min-height: 112px;
            border: 1px dashed #cbd5e1 !important;
            border-radius: 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(241, 245, 249, 0.92) 100%);
            padding: 0.8rem !important;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease, background-color .18s ease;
        }

        .admit-seat-cards-page .id-card-upload-box .dropzone.dz-started {
            background: #fff;
            border-style: solid !important;
        }

        .admit-seat-cards-page .id-card-upload-box .dropzone.dz-drag-hover {
            border-color: var(--asc-primary) !important;
            box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.08);
            transform: translateY(-1px);
        }

        .admit-seat-cards-page .id-card-upload-box .dz-message {
            margin: 0;
            color: var(--asc-muted);
        }

        .admit-seat-cards-page .id-card-upload-box .dz-message .font-weight-bold {
            color: var(--asc-text);
        }

        .admit-seat-cards-page .id-card-upload-box .dz-preview {
            margin: 0;
        }

        .admit-seat-cards-page .id-card-upload-box .dz-preview .btn[data-dz-remove] {
            border-radius: 12px;
            padding: 0.45rem 0.85rem;
            font-weight: 700;
        }

        .admit-seat-cards-page .id-card-upload-preview {
            padding: 0.9rem 0.85rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: var(--asc-shadow-sm);
        }

        .admit-seat-cards-page .id-card-upload-preview img {
            display: block;
            margin: 0 auto;
            max-width: 100%;
        }

        .admit-seat-cards-page .admit-seat-typography-card {
            border: 1px solid var(--asc-border);
            border-radius: var(--asc-radius-xl);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.96) 100%);
            box-shadow: var(--asc-shadow-lg);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-typography-header {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 1px solid #e5e7eb;
            padding: 0.8rem 0.95rem;
        }

        .admit-seat-cards-page .admit-seat-typography-header .badge {
            width: 36px !important;
            height: 36px !important;
            flex: 0 0 36px;
            font-size: 1rem;
            box-shadow: 0 6px 14px rgba(37, 99, 235, 0.2);
        }

        .admit-seat-cards-page .admit-seat-typography-header h6 {
            font-size: 1rem;
            color: #0f172a;
        }

        .admit-seat-cards-page .admit-seat-typography-body {
            padding: 0.75rem;
            background: #f8fafc;
        }

        .modal-header .close {
            margin: 0 !important;
        }

        .admit-seat-cards-page .admit-seat-typography-row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            margin-bottom: 0;
            min-height: 64px;
            padding: 0.6rem 0.7rem;
            border: 1px solid #e5e7eb;
            border-radius: var(--asc-radius-lg);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.94) 100%);
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
            align-items: center;
        }

        .admit-seat-cards-page .admit-seat-typography-body > .row {
            margin-left: -0.45rem;
            margin-right: -0.45rem;
            row-gap: 0.55rem;
        }

        .admit-seat-cards-page .admit-seat-typography-body > .row > [class*="col-"] {
            padding-left: 0.45rem;
            padding-right: 0.45rem;
        }

        .admit-seat-cards-page .admit-seat-typography-row .csm-tc-name {
            color: #1e293b;
            font-size: 0.86rem;
            line-height: 1.35;
        }

        .admit-seat-cards-page .admit-seat-typography-row .csm-typography-control {
            min-height: 40px;
            font-size: 0.82rem;
        }

        .admit-seat-cards-page .admit-seat-typography-row .csm-color-row {
            display: flex !important;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.45rem 0.75rem !important;
            min-height: 0;
        }

        .admit-seat-cards-page .admit-seat-typography-row .csm-color-native {
            width: 32px;
            height: 32px;
            min-width: 32px !important;
            max-width: 32px !important;
            flex: 0 0 32px;
        }

        .admit-seat-cards-page .color-control-label {
            flex: 0 0 100%;
            color: #64748b;
            font-size: 0.56rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            line-height: 1;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .admit-seat-cards-page .csm-color-group {
            display: flex;
            align-items: center;
            flex: 1 1 42%;
            flex-wrap: wrap;
            gap: 0.38rem 0.5rem;
            min-width: 0;
        }

        .admit-seat-cards-page .csm-color-group .color-control-label {
            flex: 0 0 100%;
        }

        .admit-seat-cards-page .csm-color-group .color-control-label:not(:first-child) {
            margin-top: 0.15rem;
        }

        .admit-seat-cards-page .csm-color-group .border-transparent-toggle {
            margin-left: 0;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .admit-seat-cards-page .csm-color-group .csm-color-native + span:not(.color-control-label) {
            margin-left: 0.12rem;
        }

        .admit-seat-cards-page .border-transparent-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.8rem;
            line-height: 1;
            white-space: nowrap;
            cursor: pointer;
        }

        .admit-seat-cards-page .border-transparent-toggle input {
            accent-color: #2563eb;
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .admit-seat-cards-page .border-transparent-toggle:has(input:checked) {
            border-color: #93c5fd;
            background: #eff6ff;
            color: #2563eb;
        }

        .admit-seat-cards-page .border-transparent-toggle:focus-within {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        @media (min-width: 768px) {
            .admit-seat-cards-page .admit-seat-typography-row {
                flex-wrap: nowrap;
            }

            .admit-seat-cards-page .admit-seat-typography-row > .col-md-4:first-child {
                flex: 0 0 42%;
                max-width: 42%;
                display: flex;
                align-items: center;
            }

            .admit-seat-cards-page .admit-seat-typography-row > .col-md-4:nth-child(2) {
                flex: 0 0 30%;
                max-width: 30%;
            }

            .admit-seat-cards-page .admit-seat-typography-row > .col-md-4:nth-child(3) {
                flex: 0 0 28%;
                max-width: 28%;
            }

            .admit-seat-cards-page .admit-seat-typography-body > .row {
                align-items: start;
            }
        }

        .admit-seat-cards-page .admit-seat-typography-row:last-child {
            margin-bottom: 0;
        }

        .admit-seat-cards-page .admit-seat-typography-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock > .row > .col-12 {
            margin-bottom: 0.4rem !important;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock > .row > .col-12:last-child {
            margin-bottom: 0 !important;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded {
            padding: 0.42rem 0.55rem !important;
            border-color: #d8e1ec !important;
            border-left: 3px solid #2563eb !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.04);
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded > .row {
            align-items: center;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded > .row > .col-md-2 {
            display: flex;
            align-items: center;
            min-height: 34px;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded > .row > .col-md-2 strong {
            color: #0f172a;
            font-size: 0.8rem;
            font-weight: 800;
            line-height: 1.25;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded .small.font-weight-bold {
            color: #475569 !important;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .admit-seat-spacing-lock {
            color: #64748b !important;
            line-height: 1;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .admit-seat-spacing-lock[aria-pressed="true"] {
            color: #2563eb !important;
        }

        .admit-seat-cards-page .admit-card__student-field--sortable {
            cursor: grab;
            border-radius: 1mm;
            transition: background-color .15s ease, outline-color .15s ease, opacity .15s ease;
        }

        .admit-seat-cards-page .admit-card__student-field--dragging {
            cursor: grabbing;
            opacity: .45;
        }

        .admit-seat-cards-page .admit-card__student-field--drag-over {
            outline: .45mm dashed #2563eb;
            outline-offset: .35mm;
        }

        .admit-seat-cards-page #admitSeatLivePreview [data-preview-spacing-key] {
            cursor: grab;
            touch-action: none;
        }

        .admit-seat-cards-page #admitSeatLivePreview .admit-card__preview-dragging {
            cursor: grabbing;
            outline: 1px solid #2563eb;
            outline-offset: 2px;
            opacity: .82;
        }

        .admit-seat-cards-page #admitSeatLivePreview .admit-card__resize-handle {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 13px;
            height: 13px;
            z-index: 20;
            cursor: nwse-resize;
            touch-action: none;
            background: linear-gradient(135deg, transparent 0 42%, #2563eb 43% 50%, transparent 51% 63%, #2563eb 64% 71%, transparent 72%);
            border-radius: 0 0 2px 0;
        }

        .admit-seat-cards-page #admitSeatLivePreview .admit-card__resize-handle:hover {
            background-color: rgba(37, 99, 235, .12);
        }

        .admit-seat-cards-page #admitSeatLivePreview .admit-card__resize-handle--active {
            background-color: rgba(37, 99, 235, .2);
        }

        /* Keep the editor preview free to drag without changing print sizing rules. */
        .admit-seat-cards-page #admitSeatLivePreview .admit-card {
            overflow: visible;
        }

        .admit-seat-cards-page #admitSeatTextSpacingBlock .admit-seat-spacing-input {
            height: 32px;
            min-height: 32px;
            padding: 0.25rem 0.5rem;
            font-size: 0.74rem;
        }

        @media (min-width: 420px) {
            .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded > .row {
                display: grid;
                grid-template-columns: minmax(74px, 1fr) minmax(0, 2.5fr) minmax(0, 2.5fr);
                margin-left: -0.45rem;
                margin-right: -0.45rem;
                column-gap: 0;
            }

            .admit-seat-cards-page #admitSeatTextSpacingBlock .border.rounded > .row > [class*="col-"] {
                width: auto;
                max-width: none;
                flex: none;
            }
        }

        .admit-seat-cards-page .admit-seat-layout-card {
            border: 1px solid var(--asc-border);
            border-radius: var(--asc-radius-xl);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.96) 100%);
            box-shadow: var(--asc-shadow-lg);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-layout-header {
            padding: 0.95rem 1rem 0.9rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .admit-seat-cards-page .admit-seat-layout-header .badge {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .admit-seat-cards-page .admit-seat-layout-header h6 {
            letter-spacing: -0.01em;
        }

        .admit-seat-cards-page .admit-seat-layout-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .admit-seat-layout-note {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 0.85rem;
            padding: 0.56rem 0.78rem;
            border: 1px solid var(--asc-border);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--asc-muted);
            font-size: 0.78rem;
            font-weight: 700;
            box-shadow: var(--asc-shadow-sm);
        }

        .admit-seat-cards-page .admit-seat-layout-field {
            height: 100%;
            padding: 0.88rem 0.95rem;
            border: 1px solid var(--asc-border);
            border-radius: var(--asc-radius-lg);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.95) 100%);
            box-shadow: 0 3px 12px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .admit-seat-cards-page .admit-seat-layout-field:hover {
            border-color: var(--asc-border-strong);
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page .admit-seat-layout-label {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--asc-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.25;
        }

        .admit-seat-cards-page .card-settings-modal-content label[data-toggle="tooltip"] {
            cursor: help;
        }

        .admit-seat-cards-page .admit-seat-layout-control {
            width: 100%;
            min-height: 38px;
            border-radius: var(--asc-radius-md);
        }

        .admit-seat-cards-page .csm-color-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: nowrap;
        }

        .admit-seat-cards-page .csm-typography-control {
            min-height: 38px;
            border-radius: var(--asc-radius-md);
            border-color: var(--asc-border-strong);
            box-shadow: none;
            background: #fff;
            color: var(--asc-text);
        }

        .admit-seat-cards-page .csm-typography-control:focus {
            border-color: #94a3b8;
            box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.16);
        }

        .admit-seat-cards-page .csm-color-native {
            width: 54px !important;
            min-width: 54px !important;
            max-width: 54px !important;
            height: 38px !important;
            padding: 0.18rem !important;
            border-radius: var(--asc-radius-md);
            border: 1px solid var(--asc-border-strong);
            background: #ffffff;
            cursor: pointer;
            flex: 0 0 54px;
        }

        .admit-seat-cards-page .csm-color-native::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        .admit-seat-cards-page .csm-color-native::-webkit-color-swatch {
            border: 0;
            border-radius: 8px;
        }

        .admit-seat-cards-page .csm-color-preview {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            border: 1px solid #d1d5db;
            border-radius: var(--asc-radius-md);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
        }

        .admit-seat-cards-page .card-settings-modal-content {
            border: 1px solid var(--asc-border);
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(180deg, #fbfdff 0%, #ffffff 42%);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.14);
        }

        .admit-seat-cards-page .card-settings-modal-header {
            padding: 0.95rem 1.15rem;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }

        .admit-seat-cards-page .card-settings-modal-body {
            padding: 0.95rem 1.15rem 1.1rem;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 18%);
        }

        .admit-seat-cards-page .card-settings-modal-footer {
            padding: 0.95rem 1.15rem;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .dedicated-settings-page {
            min-height: calc(100vh - 1rem);
            margin: 0;
            width: 100%;
            padding: 0;
            background: #f8fafc;
        }

        .admit-seat-settings-content {
            margin: 16px 16px !important;
        }

        .admit-seat-settings-container {
            padding: 0 !important;
            margin: 0 !important;
        }

        .dedicated-settings-page .modal {
            position: static;
            display: block !important;
            opacity: 1 !important;
            overflow: visible;
            padding-right: 0 !important;
        }

        .dedicated-settings-page .modal-dialog {
            width: 100%;
            max-width: none;
            min-height: 0;
            margin: 0 auto;
            transform: none !important;
            display: block;
        }

        .dedicated-settings-page .modal-content {
            min-height: calc(100vh - 1rem);
        }

        .dedicated-settings-page .admit-seat-cards-modal-layout {
            display: flex;
            flex-direction: column;
            margin-left: 0;
            margin-right: 0;
        }

        .dedicated-settings-page .admit-seat-cards-modal-preview,
        .dedicated-settings-page .admit-seat-cards-modal-settings {
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
            padding-left: 0;
            padding-right: 0;
        }

        .dedicated-settings-page .admit-seat-cards-modal-preview {
            margin-bottom: 1rem !important;
        }

        .dedicated-settings-page .csm-preview-sticky {
            position: static;
        }

        .dedicated-settings-page .admit-seat-cards-modal-settings {
            margin-top: 0.25rem;
        }

        .admit-seat-tabs {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.35rem;
            padding: 0.32rem;
            border: 1px solid var(--asc-border);
            border-radius: 20px;
            background: #ffffff;
            box-shadow: var(--asc-shadow-sm);
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
        }

        .admit-seat-tabs .nav-item {
            margin-bottom: 0;
            flex: 1 1 0;
            min-width: 0;
        }

        .admit-seat-tabs .nav-link {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            border-radius: 999px;
            padding: 0.46rem 0.82rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--asc-muted);
            background: transparent !important;
            white-space: nowrap;
            text-align: center;
            line-height: 1.1;
            transition: background-color 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
        }

        .admit-seat-tabs .nav-link:hover {
            color: var(--asc-text);
            background: rgba(226, 232, 240, 0.55);
        }

        .admit-seat-tabs .nav-link.active {
            color: #fff !important;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.18) !important;
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel>.tab-pane>.card {
            border: 1px solid var(--asc-border);
            border-radius: var(--asc-radius-xl);
            background: #ffffff;
            box-shadow: var(--asc-shadow-md);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel>.tab-pane>.card>.card-header {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        .admit-seat-cards-page .admit-seat-cards-settings-panel>.tab-pane>.card>.card-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .card-settings-modal-content .form-control:not([type="color"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        .admit-seat-cards-page .card-settings-modal-content .custom-select,
        .admit-seat-cards-page .card-settings-modal-content select.form-control,
        .admit-seat-cards-page .card-settings-modal-content textarea.form-control {
            min-height: 38px;
            border-radius: var(--asc-radius-md);
            border: 1px solid var(--asc-border-strong);
            background-color: #fff;
            color: var(--asc-text);
            box-shadow: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .admit-seat-cards-page .card-settings-modal-content .form-control:not([type="color"]):not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus,
        .admit-seat-cards-page .card-settings-modal-content .custom-select:focus,
        .admit-seat-cards-page .card-settings-modal-content select.form-control:focus,
        .admit-seat-cards-page .card-settings-modal-content textarea.form-control:focus {
            border-color: #94a3b8;
            box-shadow: 0 0 0 4px rgba(148, 163, 184, 0.16);
        }

        .admit-seat-cards-page .card-settings-modal-content input[type="number"] {
            font-variant-numeric: tabular-nums;
        }

        .admit-seat-cards-page .card-settings-modal-content .btn-group-toggle .btn {
            border-radius: var(--asc-radius-md);
            border-color: var(--asc-border-strong);
            background: #fff;
            color: var(--asc-muted);
            font-weight: 700;
            box-shadow: none;
        }

        .admit-seat-cards-page .card-settings-modal-content .btn-group-toggle .btn.active {
            background: linear-gradient(135deg, var(--asc-primary), var(--asc-primary-dark));
            border-color: var(--asc-primary-dark);
            color: #fff;
            box-shadow: 0 10px 18px rgba(37, 99, 235, 0.18);
        }

        .admit-seat-cards-page .card-settings-modal-content .text-muted.d-block,
        .admit-seat-cards-page .card-settings-modal-content .small.text-muted.d-block {
            font-size: 0.72rem;
            line-height: 1.25;
            color: #94a3b8 !important;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-panel {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border-bottom: 1px solid #eef2f7;
            padding: 0.95rem 1rem;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-body {
            padding: 1rem;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            padding: 0.9rem;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            background: #fcfcfd;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            align-items: end;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-control {
            min-height: 42px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .admit-seat-cards-page .admit-seat-cards-filter-control:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .admit-seat-cards-page .admit-seat-cards-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            grid-column: 1 / -1;
        }

        .admit-seat-cards-page .result-filter-icon-btn {
            min-width: 42px;
            min-height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1rem;
        }

        /* Admin dark-mode treatment for the admit/seat-card workspace. */
        html[data-theme='dark'] .admit-seat-cards-page,
        html.dark .admit-seat-cards-page {
            --asc-surface: #111827;
            --asc-surface-alt: #0f172a;
            --asc-border: #334155;
            --asc-border-strong: #475569;
            --asc-text: #f8fafc;
            --asc-muted: #cbd5e1;
            --asc-muted-2: #94a3b8;
            --asc-primary-soft: rgba(96, 165, 250, 0.14);
            --asc-shadow-sm: 0 6px 16px rgba(2, 6, 23, 0.24);
            --asc-shadow-md: 0 10px 24px rgba(2, 6, 23, 0.3);
            --asc-shadow-lg: 0 18px 40px rgba(2, 6, 23, 0.38);
            color: #e2e8f0;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-panel,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-panel,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-header,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-header,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-body,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-body {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme='dark'] .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel,
        html.dark .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel {
            background: #111827 !important;
            border-color: #334155 !important;
        }
        html[data-theme='dark'] .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel > .card-header,
        html[data-theme='dark'] .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel > .card-body,
        html.dark .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel > .card-body {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html.dark .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel > .card-header,
        html[data-theme='dark'] .content-wrapper .admit-seat-cards-page .admit-seat-cards-filter-panel > .card-header {
            background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-form,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-form {
            background: #0f172a !important;
            border-color: #334155 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-header,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-header {
            background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .card-title,
        html.dark .admit-seat-cards-page .card-title,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-label,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-label,
        html[data-theme='dark'] .admit-seat-cards-page label,
        html.dark .admit-seat-cards-page label {
            color: #cbd5e1 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .text-dark,
        html.dark .admit-seat-cards-page .text-dark,
        html[data-theme='dark'] .admit-seat-cards-page .text-muted,
        html.dark .admit-seat-cards-page .text-muted {
            color: #cbd5e1 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-control,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-control,
        html[data-theme='dark'] .admit-seat-cards-page .form-control,
        html.dark .admit-seat-cards-page .form-control,
        html[data-theme='dark'] .admit-seat-cards-page select,
        html.dark .admit-seat-cards-page select,
        html[data-theme='dark'] .admit-seat-cards-page textarea,
        html.dark .admit-seat-cards-page textarea {
            background-color: #0f172a !important;
            border-color: #475569 !important;
            color: #f8fafc !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-control:focus,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-control:focus,
        html[data-theme='dark'] .admit-seat-cards-page .form-control:focus,
        html.dark .admit-seat-cards-page .form-control:focus,
        html[data-theme='dark'] .admit-seat-cards-page select:focus,
        html.dark .admit-seat-cards-page select:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, .18) !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-cards-filter-control option,
        html.dark .admit-seat-cards-page .admit-seat-cards-filter-control option {
            background: #0f172a;
            color: #f8fafc;
        }
        html[data-theme='dark'] .admit-seat-cards-page .card-settings-modal-content,
        html.dark .admit-seat-cards-page .card-settings-modal-content,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-typography-card,
        html.dark .admit-seat-cards-page .admit-seat-typography-card,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-layout-card,
        html.dark .admit-seat-cards-page .admit-seat-layout-card,
        html[data-theme='dark'] .admit-seat-cards-page .id-card-upload-box,
        html.dark .admit-seat-cards-page .id-card-upload-box,
        html[data-theme='dark'] .admit-seat-cards-page .id-card-upload-preview,
        html.dark .admit-seat-cards-page .id-card-upload-preview {
            background: #111827 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-typography-header,
        html.dark .admit-seat-cards-page .admit-seat-typography-header,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-layout-header,
        html.dark .admit-seat-cards-page .admit-seat-layout-header,
        html[data-theme='dark'] .admit-seat-cards-page .card-settings-modal-header,
        html.dark .admit-seat-cards-page .card-settings-modal-header,
        html[data-theme='dark'] .admit-seat-cards-page .card-settings-modal-footer,
        html.dark .admit-seat-cards-page .card-settings-modal-footer {
            background: #172033 !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-typography-row,
        html.dark .admit-seat-cards-page .admit-seat-typography-row,
        html[data-theme='dark'] .admit-seat-cards-page .admit-seat-layout-field,
        html.dark .admit-seat-cards-page .admit-seat-layout-field {
            background: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .select2-container--default .select2-selection--single,
        html.dark .admit-seat-cards-page .select2-container--default .select2-selection--single {
            background: #0f172a !important;
            border-color: #475569 !important;
        }
        html[data-theme='dark'] .admit-seat-cards-page .select2-container--default .select2-selection--single .select2-selection__rendered,
        html.dark .admit-seat-cards-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #f8fafc !important;
        }
        html[data-theme='dark'] .select2-container--default .select2-dropdown,
        html.dark .select2-container--default .select2-dropdown {
            background: #111827 !important;
            border-color: #475569 !important;
        }

        @media (max-width: 1199.98px) {
            .admit-seat-cards-page .admit-seat-cards-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .admit-seat-cards-page .admit-seat-cards-filter-grid {
                grid-template-columns: 1fr;
            }

            .admit-seat-cards-page .admit-seat-cards-filter-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .admit-seat-cards-page .admit-seat-cards-filter-actions>* {
                width: 100%;
                justify-content: center;
            }
        }

        @media print {
            .card {
                border: none;
                box-shadow: none;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
    @php
        $resolvePreviewAsset = static function (?string $path): ?string {
            return $path && file_exists(public_path($path)) ? asset($path) : null;
        };
        $previewSchoolLogoUrl = $resolvePreviewAsset($setting?->logo ?? null);
        $schoolLogoUrl = $previewSchoolLogoUrl;
        $currentCardLogoUrl = $resolvePreviewAsset($cardSettings?->card_logo ?? null) ?: $previewSchoolLogoUrl;
        $currentCardPrincipalSignatureUrl = $resolvePreviewAsset($cardSettings?->card_principal_signature ?? null);
        $currentCardPhotoFit = old('card_photo_fit', $cardSettings?->card_photo_fit ?? 'cover');
        $selectedColorType = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
        $selectedTransparent = old('card_is_transparent', $cardSettings?->card_is_transparent ?? false);
    @endphp
    @if (!($settingsOnly ?? false))
    <div class="container-fluid admit-seat-cards-page">
        <div class="card card-outline card-primary no-print result-filter-panel admit-seat-cards-filter-panel">
            <div
                class="card-header d-flex justify-content-start align-items-center flex-wrap gap-2 admit-seat-cards-filter-header">
                <a href="{{ route('results.hub') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
                <div class="flex flex-col">
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-filter mr-2 text-info"></i>Filter Options
                    </h4>
                    <small class="text-white-50">Generate admit or seat cards by year, class, exam, and layout.</small>
                </div>
            </div>
            <div class="card-body admit-seat-cards-filter-body">
                <form method="GET" action="{{ route('results.admit-seat-cards.index') }}" id="filterForm"
                    class="admit-seat-cards-filter-form">
                    <div class="admit-seat-cards-filter-grid">
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Academic Year <span
                                    class="text-danger">*</span></label>
                            <select name="session_id" id="sessionSelect"
                                class="form-control form-control-sm admit-seat-cards-filter-control">
                                <option value="">— Select Year —</option>
                                @foreach ($sessions as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Class</label>
                            <select name="class_id" id="classSelect"
                                class="form-control form-control-sm admit-seat-cards-filter-control">
                                <option value="">All Classes</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c->id }}"
                                        {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Section</label>
                            <select name="section_id" id="sectionSelect"
                                class="form-control form-control-sm admit-seat-cards-filter-control">
                                <option value="">All Sections</option>
                                @foreach ($sections as $sec)
                                    <option value="{{ $sec->id }}"
                                        {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Group</label>
                            <select name="group_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                                <option value="">All Groups</option>
                                @foreach ($groups as $g)
                                    <option value="{{ $g->id }}"
                                        {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Exam Type</label>
                            <select name="exam_type" class="form-control form-control-sm admit-seat-cards-filter-control"
                                id="examTypeSelect">
                                <option value="">All Types</option>
                                <option value="tutorial" {{ ($examType ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorial
                                    Exam</option>
                                <option value="term" {{ ($examType ?? '') === 'term' ? 'selected' : '' }}>Terminal Exam
                                </option>
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Exam Name</label>
                            <select name="exam_id" class="form-control form-control-sm admit-seat-cards-filter-control"
                                id="examSelect"
                                data-exams-url="{{ route('results.admit-seat-cards.exams') }}"
                                @disabled(empty(request('session_id')) || empty($examType ?? null))>
                                <option value="">
                                    {{ empty(request('session_id')) ? '-- Select Session First --' : (empty($examType ?? null) ? '-- Select Exam Type First --' : '-- Select Exam --') }}
                                </option>
                                @foreach ($exams as $exam)
                                    <option value="{{ $exam->id }}"
                                        {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ $exam->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Card Type</label>
                            <select name="card_type" class="form-control form-control-sm admit-seat-cards-filter-control">
                                <option value="admit_card"
                                    {{ ($cardType ?? 'admit_card') === 'admit_card' ? 'selected' : '' }}>Admit Card
                                </option>
                                <option value="seat_card"
                                    {{ ($cardType ?? 'admit_card') === 'seat_card' ? 'selected' : '' }}>Seat Card</option>
                            </select>
                        </div>
                        <div class="admit-seat-cards-filter-group">
                            <label class="font-weight-bold admit-seat-cards-filter-label">Student ID</label>
                            <input type="text" name="student_cid"
                                class="form-control form-control-sm admit-seat-cards-filter-control"
                                value="{{ request('student_cid') }}" placeholder="Enter Student ID" autocomplete="off">
                        </div>
                        <div class="admit-seat-cards-filter-actions">
                            <a href="{{ route('results.admit-seat-cards.settings.edit', ['card_type' => $cardType ?? 'admit_card']) }}"
                                class="btn btn-outline-primary btn-sm result-filter-icon-btn" title="Card settings"
                                aria-label="Card settings">
                                <i class="fas fa-sliders-h"></i>
                            </a>
                            <button type="submit" class="btn btn-dark btn-sm result-filter-icon-btn" title="Generate"
                                aria-label="Generate">
                                <i class="fas fa-id-card"></i>
                            </button>
                            <a href="{{ route('results.admit-seat-cards.index') }}"
                                class="btn btn-outline-secondary btn-sm result-filter-icon-btn" title="Reset"
                                aria-label="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                            @if ($students->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm result-filter-icon-btn"
                                    onclick="window.print()" title="Print" aria-label="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                                <a href="{{ route('results.admit-seat-cards.pdf', request()->query()) }}"
                                    class="btn btn-danger btn-sm result-filter-icon-btn" target="_blank" title="PDF"
                                    aria-label="Download PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if (!request('session_id') && !request('student_cid'))
            <div class="text-center py-5 text-muted no-print">
                <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
                <p class="mb-1">Select Academic Year or enter a Student ID to generate admit or seat cards.</p>
            </div>
        @elseif($students->isEmpty())
            <div class="text-center py-5 text-muted no-print">
                <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
                <p>No students found for the selected filters.</p>
            </div>
        @else
            @php
                $requestedCardsPerPage = (int) ($layout['requestedCardsPerPage'] ?? ($layout['cardsPerPage'] ?? 8));
                $effectiveCardsPerPage = (int) ($layout['cardsPerPage'] ?? 8);
                $maxCardsPerPage = (int) ($layout['maxCardsPerPage'] ?? $effectiveCardsPerPage);
                $layoutIsClamped = $requestedCardsPerPage > $effectiveCardsPerPage;
            @endphp

            <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
                <span class="badge badge-light border px-3 py-2" style="font-size:12px">{{ $students->count() }}
                    Students</span>
                <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                    {{ $effectiveCardsPerPage }} cards/page
                </span>
                <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                    {{ $layout['cardsPerRow'] ?? 2 }} cards/row
                </span>
                <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                    {{ number_format($layout['cardWidthMm'] ?? 0, 1) }}mm ×
                    {{ number_format($layout['cardHeightMm'] ?? 0, 1) }}mm
                </span>
                <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                    {{ number_format($layout['gridGapMm'] ?? 0, 1) }}mm gap
                </span>
            </div>

            @if ($layoutIsClamped)
                <div class="alert alert-warning no-print py-2 px-3 mb-3">
                    Requested {{ $requestedCardsPerPage }} cards/page, but only {{ $effectiveCardsPerPage }} fit on
                    {{ number_format($layout['pageWidthMm'] ?? 210, 2) }} × {{ number_format($layout['pageHeightMm'] ?? 297, 2) }}mm
                    with the current card size, row count, and gap.
                    @if ($layout['requestedColumnsFit'] ?? true)
                        @if ($layout['contentFitsRecommendedHeight'] ?? true)
                            To fit all {{ $requestedCardsPerPage }} cards/page across {{ $layout['requestedPageRows'] ?? 1 }} rows,
                            set Card Height to at most
                            {{ number_format($layout['recommendedCardHeightValue'] ?? 0, 2) }}{{ ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm' }}
                            ({{ number_format($layout['recommendedCardHeightMm'] ?? 0, 2) }}mm),
                            or reduce the gap.
                        @else
                            Reducing Card Height alone will crop the current content. The current photo, typography, spacing,
                            visibility, padding, and footer settings need approximately
                            {{ number_format($layout['minimumCardHeightValue'] ?? 0, 2) }}{{ ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm' }}
                            ({{ number_format($layout['minimumCardHeightMm'] ?? 0, 2) }}mm).
                            To fit {{ $requestedCardsPerPage }} cards/page at the calculated
                            {{ number_format($layout['recommendedCardHeightValue'] ?? 0, 2) }}{{ ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm' }},
                            reduce Photo Height, Front Padding, vertical text spacing, font sizes, or optional visible fields;
                            otherwise reduce Cards / Page.
                        @endif
                    @else
                        Height alone cannot fit the requested row width. Reduce Card Width or Cards / Row first;
                        the maximum recommended height is
                        {{ number_format($layout['recommendedCardHeightValue'] ?? 0, 2) }}{{ ($layout['cardDimensionUnit'] ?? 'cm') === 'px' ? 'px' : 'cm' }}
                        ({{ number_format($layout['recommendedCardHeightMm'] ?? 0, 2) }}mm).
                    @endif
                </div>
            @endif

            @include('pages.admit-seat-cards._cards', [
                'students' => $students,
                'setting' => $setting,
                'cardSettings' => $cardSettings ?? null,
                'renderForPdf' => false,
                'cardType' => $cardType ?? 'admit_card',
                'examType' => $examType ?? null,
                'selectedExam' => $selectedExam ?? null,
                'layout' => $layout ?? [],
            ])
        @endif
    </div>
    @endif

    <div class="{{ ($settingsOnly ?? false) ? 'admit-seat-cards-page dedicated-settings-page' : '' }}">
    <div class="modal fade {{ ($settingsOnly ?? false) ? 'show' : '' }}" id="cardSettingsModal" tabindex="-1"
        role="dialog" aria-labelledby="cardSettingsModalLabel"
        aria-hidden="{{ ($settingsOnly ?? false) ? 'false' : 'true' }}">
        <div class="modal-dialog modal-dialog-centered modal-xl card-settings-modal-dialog" role="document">
            <div class="modal-content card-settings-modal-content">
                <form method="POST" action="{{ route('results.admit-seat-cards.settings') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @php
                        $cardPositionKeys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type', 'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'];
                    @endphp
                    <div id="admitSeatElementPositionInputs" class="d-none" aria-hidden="true">
                        @foreach($cardPositionKeys as $positionKey)
                            <input type="hidden" name="card_element_positions[{{ $positionKey }}][x]"
                                data-element-position-key="{{ $positionKey }}" data-element-position-axis="x"
                                value="{{ data_get($cardSettings?->card_element_positions, "$positionKey.x", 0) }}">
                            <input type="hidden" name="card_element_positions[{{ $positionKey }}][y]"
                                data-element-position-key="{{ $positionKey }}" data-element-position-axis="y"
                                value="{{ data_get($cardSettings?->card_element_positions, "$positionKey.y", 0) }}">
                        @endforeach
                    </div>
                    <div id="admitSeatElementSizeInputs" class="d-none" aria-hidden="true">
                        <input type="hidden" name="card_element_sizes[exam_name][width]"
                            data-element-size-key="exam_name" data-element-size-axis="width"
                            value="{{ data_get($cardSettings?->card_element_sizes, 'exam_name.width', '') }}">
                        <input type="hidden" name="card_element_sizes[exam_name][height]"
                            data-element-size-key="exam_name" data-element-size-axis="height"
                            value="{{ data_get($cardSettings?->card_element_sizes, 'exam_name.height', '') }}">
                        <input type="hidden" name="card_element_sizes[header][height]"
                            data-element-size-key="header" data-element-size-axis="height"
                            value="{{ data_get($cardSettings?->card_element_sizes, 'header.height', '') }}">
                    </div>
                    <div class="modal-header card-settings-modal-header">
                        <div>
                            <h5 class="modal-title mb-1" id="cardSettingsModalLabel">
                                {{ old('card_type', $cardType ?? 'admit_card') === 'seat_card' ? 'Seat Card Settings' : 'Admit Card Settings' }}
                            </h5>
                            <small class="text-muted d-block" id="cardSettingsModalTypeLabel">Switch between admit and
                                seat card presets.</small>
                            <small class="text-muted d-block">Save a single layout profile for search, print, and PDF
                                output.</small>
                        </div>
                        <button type="button" class="btn btn-outline-info btn-sm ml-auto mr-2"
                            data-toggle="modal" data-target="#admitSeatUserManualModal">
                            <i class="fas fa-book-open mr-1" aria-hidden="true"></i>User Manual
                        </button>
                        <div class="ml-auto btn-group btn-group-sm csm-type-switcher" role="group"
                            aria-label="Card type selector">
                            <button type="button"
                                class="btn btn-outline-primary js-card-type-switch {{ old('card_type', $cardType ?? 'admit_card') === 'admit_card' ? 'active' : '' }}"
                                data-card-type="admit_card" data-card-label="Admit Card Settings">Admit Card</button>
                            <button type="button"
                                class="btn btn-outline-primary js-card-type-switch {{ old('card_type', $cardType ?? 'admit_card') === 'seat_card' ? 'active' : '' }}"
                                data-card-type="seat_card" data-card-label="Seat Card Settings">Seat Card</button>
                        </div>
                        <span id="cardSettingsDirtyBadge" class="badge badge-warning align-self-center d-none">Unsaved
                            changes</span>
                        @if ($settingsOnly ?? false)
                            <a href="{{ route('results.admit-seat-cards.index', ['card_type' => $cardType ?? 'admit_card']) }}"
                                class="close card-settings-modal-close" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </a>
                        @else
                            <button type="button" class="close card-settings-modal-close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">×</span>
                            </button>
                        @endif
                    </div>
                    <div class="modal-body card-settings-modal-body">
                        <input type="hidden" name="card_type"
                            value="{{ old('card_type', $cardType ?? 'admit_card') }}">
                        <input type="hidden" name="card_show_school_detail_back"
                            value="{{ old('card_show_school_detail_back', $cardSettings?->card_show_school_detail_back ?? true) ? 1 : 0 }}">
                        <input type="hidden" name="card_show_slogan_back"
                            value="{{ old('card_show_slogan_back', $cardSettings?->card_show_slogan_back ?? true) ? 1 : 0 }}">
                        <input type="hidden" name="card_show_title_back"
                            value="{{ old('card_show_title_back', $cardSettings?->card_show_title_back ?? true) ? 1 : 0 }}">
                        <input type="hidden" name="card_show_back_notice"
                            value="{{ old('card_show_back_notice', $cardSettings?->card_show_back_notice ?? true) ? 1 : 0 }}">
                        @php
                            $selectedColorType = old('card_color_type', $cardSettings?->card_color_type ?? 'gradient');
                            $selectedTransparent = old(
                                'card_is_transparent',
                                $cardSettings?->card_is_transparent ?? false,
                            );
                            $currentCardPhotoFit = old('card_photo_fit', $cardSettings?->card_photo_fit ?? 'cover');
                            $resolveLogoUrl = function (?string $path) {
                                if (!$path) {
                                    return null;
                                }

                                return file_exists(public_path($path)) ? asset($path) : null;
                            };
                            $schoolLogoUrl = $resolveLogoUrl($setting?->logo ?? null);
                            $currentCardLogoUrl = $resolveLogoUrl($cardSettings?->card_logo ?? null) ?: $schoolLogoUrl;
                            $currentCardPrincipalSignatureUrl = $resolveLogoUrl(
                                $cardSettings?->card_principal_signature ?? null,
                            );
                        @endphp
                        <div class="row align-items-stretch admit-seat-cards-modal-layout">
                            <div class="col-12 col-lg-4 mb-3 admit-seat-cards-modal-preview">
                                <div class="csm-preview-sticky">
                                    @include('pages.card-settings._live-preview', [
                                        'prefix' => 'admitSeat',
                                        'previewType' => 'admit',
                                        'showBack' => false,
                                        'previewLabel' =>
                                            $cardType === 'seat_card' ? 'Seat Card Preview' : 'Admit Card Preview',
                                        'schoolName' => $setting?->name ?? 'School Name',
                                        'schoolDetailLine' => $setting?->address ?? '',
                                        'slogan' => $setting?->slogan ?? 'Stay Green, Be Bright',
                                        'cardLabel' => $cardType === 'seat_card' ? 'SEAT CARD' : 'ADMIT CARD',
                                        'backTitle' => 'BACK',
                                        'backNotice' => 'If found, please return to the school.',
                                        {{-- Keep the settings preview populated even without an exam filter in the URL. --}}
                                        'examTypeLabel' => $examType
                                            ? (strtolower($examType) === 'term'
                                                ? 'Terminal Exam'
                                                : 'Tutorial Exam')
                                            : 'Terminal Exam',
                                        'examName' => $selectedExam?->name ?? 'First Terminal Exam',
                                        'footerLines' => array_values(
                                            array_filter([
                                                $setting?->contact_number_1,
                                                $setting?->whatsapp_number,
                                            ])),
                                        'logoUrl' => $currentCardLogoUrl,
                                        'principalSignatureUrl' => $currentCardPrincipalSignatureUrl,
                                        {{-- Render all preview visibility targets; JavaScript applies saved switch states. --}}
                                        'showSchoolDetailFront' => true,
                                        'showSloganFront' => true,
                                        'showTitleFront' => true,
                                        'showLogoFront' => true,
                                        'showPhotoFront' => true,
                                        {{-- Render preview-only visibility targets once; JavaScript applies the current switches. --}}
                                        'showFatherNameFront' => true,
                                        'showMotherNameFront' => true,
                                        'showStudentNameLabelFront' => true,
                                        'showRollFront' => true,
                                        'showClassFront' => true,
                                        'showSectionFront' => true,
                                        'showSessionFront' => true,
                                        'showVerticalLabelFront' => true,
                                        'examNameBadgeFront' => true,
                                        'showExamTypeFront' => true,
                                        'showExamNameFront' => true,
                                        'showFooterFront' => true,
                                        'studentFieldOrder' => $cardSettings?->card_student_field_order ?? [],
                                        'cardElementPositions' => $cardSettings?->card_element_positions ?? [],
                                        'cardElementSizes' => $cardSettings?->card_element_sizes ?? [],
                                        'previewCardWidthValue' => $cardSettings?->card_width_value ?? 9.4,
                                        'previewCardHeightValue' => $cardSettings?->card_height_value ?? 6.6,
                                        'previewCardDimensionUnit' => $cardSettings?->card_dimension_unit ?? 'cm',
                                        'cardPhotoFit' => $currentCardPhotoFit,
                                        'focusTargets' => [
                                            'logo' => 'admitSeatCardLogoInput',
                                            'school_name' => 'admitSeatSchoolNameColor',
                                            'school_detail' => 'admitSeatSchoolDetailColor',
                                            'title' => 'admitSeatTitleColor',
                                            'vertical_label' => 'admitSeatVerticalLabelColor',
                                            'name' => 'admitSeatNameColor',
                                            'student_detail_alignment' => 'admitSeatStudentDetailAlignment',
                                            'student_detail_font_size' => 'admitSeatStudentDetailFontSize',
                                            'student_detail_color' => 'admitSeatStudentDetailColor',
                                            'exam_type' => 'admitSeatExamTypeColor',
                                            'exam_name' => 'admitSeatExamNameColor',
                                            'footer' => 'admitSeatFooterColor',
                                            'photo' => 'admitSeatPhotoWidth',
                                            'principal_signature' => 'admitSeatPrincipalSignatureInput',
                                        ],
                                    ])
                                </div>
                            </div>

                            <div class="col-12 col-lg-8 admit-seat-cards-modal-settings">
                                <ul class="nav admit-seat-tabs csm-section-tabs mb-2" id="admitSeatSettingsTabs"
                                    role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="admitSeatLayoutTab" data-toggle="tab"
                                            href="#admitSeatLayoutPane" role="tab"
                                            aria-controls="admitSeatLayoutPane" aria-selected="true">Layout &amp; Grid</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatPhotoTab" data-toggle="tab"
                                            href="#admitSeatPhotoPane" role="tab" aria-controls="admitSeatPhotoPane"
                                            aria-selected="false">Photo &amp; Logo</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatPageTab" data-toggle="tab"
                                            href="#admitSeatPagePane" role="tab" aria-controls="admitSeatPagePane"
                                            aria-selected="false">Page Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatAlignmentTab" data-toggle="tab"
                                            href="#admitSeatAlignmentPane" role="tab"
                                            aria-controls="admitSeatAlignmentPane" aria-selected="false">Text Alignment &amp; Spacing</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatTypographyTab" data-toggle="tab"
                                            href="#admitSeatTypographyPane" role="tab"
                                            aria-controls="admitSeatTypographyPane" aria-selected="false">Typography &amp;
                                            Colors</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatBackgroundTab" data-toggle="tab"
                                            href="#admitSeatBackgroundPane" role="tab"
                                            aria-controls="admitSeatBackgroundPane" aria-selected="false">Background</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="admitSeatVisibilityTab" data-toggle="tab"
                                            href="#admitSeatVisibilityPane" role="tab"
                                            aria-controls="admitSeatVisibilityPane" aria-selected="false">Visibility</a>
                                    </li>
                                </ul>

                                <div class="tab-content admit-seat-cards-settings-panel">
                                    <div class="tab-pane fade show active" id="admitSeatLayoutPane" role="tabpanel"
                                        aria-labelledby="admitSeatLayoutTab">
                                        <div class="card mb-2 shadow-sm admit-seat-layout-card">
                                            <div class="card-header admit-seat-layout-header">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap"
                                                    style="gap: 0.75rem;">
                                                    <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                                        <span class="badge badge-primary"><i
                                                                class="fas fa-vector-square"></i></span>
                                                        <div>
                                                            <h6 class="mb-0 font-weight-bold">Layout &amp; Grid</h6>
                                                            <small class="text-muted d-block">Tune the card grid, spacing,
                                                                and page alignment.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body admit-seat-layout-body">
                                                @if ($layoutIsClamped ?? false)
                                                    <div class="alert alert-warning py-2 px-3 mb-3 small rounded-lg border-0"
                                                        style="background:#fff7ed;color:#9a3412;">
                                                        Only {{ $maxCardsPerPage }} cards fit on the configured page with the current
                                                        layout.
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label"
                                                                for="admitSeatCardsPerPage" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Total cards rendered on a page."
                                                                aria-label="Total cards rendered on a page.">Cards /
                                                                Page</label>
                                                            <input type="number" name="cards_per_page"
                                                                id="admitSeatCardsPerPage"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="1" max="12"
                                                                value="{{ old('cards_per_page', $cardSettings?->cards_per_page ?? 8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label"
                                                                for="admitSeatCardsPerRow" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="How many cards sit side by side."
                                                                aria-label="How many cards sit side by side.">Cards /
                                                                Row</label>
                                                            <input type="number" name="cards_per_row"
                                                                id="admitSeatCardsPerRow"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="1" max="10"
                                                                value="{{ old('cards_per_row', $cardSettings?->cards_per_row ?? 2) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label" for="admitSeatGridGap"
                                                                data-toggle="tooltip" data-placement="top"
                                                                title="Spacing between cards on the sheet."
                                                                aria-label="Spacing between cards on the sheet.">Grid
                                                                Gap</label>
                                                            <input type="number" name="grid_gap_value"
                                                                id="admitSeatGridGap"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="0.1" step="0.01"
                                                                value="{{ old('grid_gap_value', $cardSettings?->grid_gap_value ?? 0.85) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label"
                                                                for="admitSeatCardWidth" data-toggle="tooltip"
                                                                data-placement="top" title="Width of each rendered card."
                                                                aria-label="Width of each rendered card.">Card
                                                                Width</label>
                                                            <input type="number" name="card_width_value"
                                                                id="admitSeatCardWidth"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="0.1" step="0.01"
                                                                value="{{ old('card_width_value', $cardSettings?->card_width_value ?? 9.4) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label"
                                                                for="admitSeatCardHeight" data-toggle="tooltip"
                                                                data-placement="top" title="Height of each rendered card."
                                                                aria-label="Height of each rendered card.">Card
                                                                Height</label>
                                                            <input type="number" name="card_height_value"
                                                                id="admitSeatCardHeight"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="0.1" step="0.01"
                                                                value="{{ old('card_height_value', $cardSettings?->card_height_value ?? 6.6) }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-md-3 mb-1">
                                                        <div class="admit-seat-layout-field">
                                                            <label class="admit-seat-layout-label"
                                                                for="admitSeatFrontPadding" data-toggle="tooltip"
                                                                data-placement="top"
                                                                title="Inner spacing inside the front card."
                                                                aria-label="Inner spacing inside the front card.">Front
                                                                Padding</label>
                                                            <input type="number" name="card_front_padding_value"
                                                                id="admitSeatFrontPadding"
                                                                class="csm-input csm-typography-control form-control form-control-sm admit-seat-layout-control"
                                                                min="0" step="0.1"
                                                                value="{{ old('card_front_padding_value', $cardSettings?->card_front_padding_value ?? 0.8) }}">
                                                        </div>
                                                    </div>

                                                    <input type="hidden" name="card_dimension_unit" value="cm"
                                                        id="admitSeatDimensionUnit" />
                                                    <input type="hidden" name="card_back_alignment" value="center"
                                                        id="admitSeatBackAlignment" />
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatPagePane" role="tabpanel"
                                        aria-labelledby="admitSeatPageTab">
                                        <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i class="fas fa-file-alt"></i></span>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold">Page Settings</h6>
                                                        <small class="text-muted d-block">Set the paper size and printable margins.</small>
                                                    </div>
                                                </div>
                                                <span class="badge badge-light border">Millimetres</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach ([
                                                        ['page_width_mm', 'admitSeatPageWidth', 'Page Width', 50, 1000, 210, 'Paper width.'],
                                                        ['page_height_mm', 'admitSeatPageHeight', 'Page Height', 50, 1400, 297, 'Paper height.'],
                                                        ['page_margin_top_mm', 'admitSeatPageMarginTop', 'Top Margin', 0, 100, 10, 'Space reserved above the cards.'],
                                                        ['page_margin_right_mm', 'admitSeatPageMarginRight', 'Right Margin', 0, 100, 6.35, 'Space reserved on the right.'],
                                                        ['page_margin_bottom_mm', 'admitSeatPageMarginBottom', 'Bottom Margin', 0, 100, 4, 'Space reserved below the cards.'],
                                                        ['page_margin_left_mm', 'admitSeatPageMarginLeft', 'Left Margin', 0, 100, 6.35, 'Space reserved on the left.'],
                                                    ] as [$field, $id, $label, $min, $max, $fallback, $help])
                                                        <div class="col-12 col-md-4 mb-3">
                                                            <div class="form-group mb-0">
                                                                <label class="admit-seat-layout-label" for="{{ $id }}">{{ $label }}</label>
                                                                <input type="number" name="{{ $field }}" id="{{ $id }}"
                                                                    class="csm-input form-control form-control-sm"
                                                                    min="{{ $min }}" max="{{ $max }}" step="0.01"
                                                                    value="{{ old($field, $cardSettings?->{$field} ?? $fallback) }}">
                                                                <small class="text-muted d-block mt-1">{{ $help }}</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="alert alert-info py-2 px-3 mb-0 small">
                                                    Usable area: {{ number_format($layout['usableWidthMm'] ?? 197.3, 2) }}mm ×
                                                    {{ number_format($layout['usableHeightMm'] ?? 283, 2) }}mm.
                                                    Capacity is recalculated after saving.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatPhotoPane" role="tabpanel"
                                        aria-labelledby="admitSeatPhotoTab">
                                        <div class="card mb-2 shadow-sm">
                                            <div
                                                class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i
                                                            class="fas fa-image"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Photo &amp; Logo</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="row">
                                                    <div class="col-12 col-md-3 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label
                                                                class="d-block mb-1 small font-weight-bold text-dark">Photo
                                                                Width</label>
                                                            <input type="number" name="card_photo_width_value"
                                                                id="admitSeatPhotoWidth"
                                                                class="csm-input form-control form-control-sm"
                                                                min="0.1" step="0.1"
                                                                value="{{ old('card_photo_width_value', $cardSettings?->card_photo_width_value ?? 1.8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label
                                                                class="d-block mb-1 small font-weight-bold text-dark">Photo
                                                                Height</label>
                                                            <input type="number" name="card_photo_height_value"
                                                                id="admitSeatPhotoHeight"
                                                                class="csm-input form-control form-control-sm"
                                                                min="0.1" step="0.1"
                                                                value="{{ old('card_photo_height_value', $cardSettings?->card_photo_height_value ?? 2.7) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark"
                                                                for="admitSeatPhotoFit">Photo Fit</label>
                                                            <select name="card_photo_fit" id="admitSeatPhotoFit"
                                                                class="csm-select form-control form-control-sm">
                                                                <option value="cover"
                                                                    {{ $currentCardPhotoFit === 'cover' ? 'selected' : '' }}>
                                                                    Cover</option>
                                                                <option value="contain"
                                                                    {{ $currentCardPhotoFit === 'contain' ? 'selected' : '' }}>
                                                                    Contain</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label
                                                                class="d-block mb-1 small font-weight-bold text-dark">Logo
                                                                Size</label>
                                                            <input type="number" name="card_logo_size_value"
                                                                id="admitSeatLogoSize"
                                                                class="csm-input form-control form-control-sm"
                                                                min="0.1" step="0.1"
                                                                value="{{ old('card_logo_size_value', $cardSettings?->card_logo_size_value ?? 0.8) }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-3 mb-2">
                                                        <div class="form-group mb-0">
                                                            <label class="d-block mb-1 small font-weight-bold text-dark">Signature Size</label>
                                                            <input type="number" name="card_signature_size_value"
                                                                id="admitSeatSignatureSize"
                                                                class="csm-input form-control form-control-sm"
                                                                min="0.1" step="0.1"
                                                                value="{{ old('card_signature_size_value', $cardSettings?->card_signature_size_value ?? 1.4) }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div class="id-card-upload-box">
                                                            <label class="d-block mb-2 small font-weight-bold text-dark"
                                                                for="admitSeatCardLogoInput">School Logo</label>
                                                            <div id="admitSeatCardLogoDropzone"
                                                                class="dropzone rounded border bg-white p-2"
                                                                data-existing-image-url="{{ $currentCardLogoUrl ?: '' }}"
                                                                data-existing-image-name="{{ basename($cardSettings?->card_logo ?? ($setting?->logo ?? 'card-logo.png')) }}"
                                                                style="min-height: 110px;">
                                                                <input type="file" name="card_logo"
                                                                    id="admitSeatCardLogoInput" class="d-none"
                                                                    accept="image/*">
                                                                <div class="dz-message needsclick text-center py-3">
                                                                    <div class="font-weight-bold">Drop logo here or click
                                                                        to browse</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div
                                                            class="id-card-upload-preview text-center flex flex-col items-center justify-center">
                                                            <label
                                                                class="d-block mb-2 small font-weight-bold text-dark">Logo
                                                                Preview</label>
                                                            <img id="admitSeatCardLogoPreview"
                                                                src="{{ $currentCardLogoUrl ?? '' }}" alt="Logo preview"
                                                                class="img-fluid {{ $currentCardLogoUrl ? '' : 'd-none' }}"
                                                                style="max-height: 96px; object-fit: contain;">
                                                            <div
                                                                class="small text-muted {{ $currentCardLogoUrl ? 'd-none' : '' }}">
                                                                No custom logo uploaded.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div class="id-card-upload-box">
                                                            <label class="d-block mb-2 small font-weight-bold text-dark"
                                                                for="admitSeatPrincipalSignatureInput">Principal
                                                                Signature</label>
                                                            <div id="admitSeatCardPrincipalSignatureDropzone"
                                                                class="dropzone rounded border bg-white p-2"
                                                                data-existing-image-url="{{ $currentCardPrincipalSignatureUrl ?: '' }}"
                                                                data-existing-image-name="{{ basename($cardSettings?->card_principal_signature ?? 'principal-signature.png') }}"
                                                                style="min-height: 110px;">
                                                                <input type="file" name="card_principal_signature"
                                                                    id="admitSeatPrincipalSignatureInput" class="d-none"
                                                                    accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                                                                <div class="dz-message needsclick text-center py-3">
                                                                    <div class="font-weight-bold">Drop signature here or
                                                                        click to browse</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6 mb-2">
                                                        <div
                                                            class="id-card-upload-preview text-center flex flex-col items-center justify-center">
                                                            <label
                                                                class="d-block mb-2 small font-weight-bold text-dark">Signature
                                                                Preview</label>
                                                            <img id="admitSeatPrincipalSignaturePreview"
                                                                src="{{ $currentCardPrincipalSignatureUrl ?? '' }}"
                                                                alt="Principal signature preview"
                                                                class="img-fluid {{ $currentCardPrincipalSignatureUrl ? '' : 'd-none' }}"
                                                                style="max-height: 96px; object-fit: contain;">
                                                            <div
                                                                class="small text-muted {{ $currentCardPrincipalSignatureUrl ? 'd-none' : '' }}">
                                                                No principal signature uploaded.</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatAlignmentPane" role="tabpanel"
                                        aria-labelledby="admitSeatAlignmentTab">
                                        <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-align-left"></i></span>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">Text Alignment</h6>
                                                    <small class="text-muted">Control the overall card and student-detail alignment.</small>
                                                </div>
                                            </div>
                                            <div class="card-body p-2" id="admitSeatAlignmentContent"></div>
                                        </div>
                                    </div>

                                    <div class="admit-seat-alignment-spacing-section" id="admitSeatSpacingSection">
                                        <div class="card mb-2 shadow-sm">
                                            <div class="card-header py-2 bg-light d-flex align-items-center">
                                                <span class="badge badge-primary mr-2"><i class="fas fa-arrows-alt"></i></span>
                                                <div>
                                                    <h6 class="mb-0 font-weight-bold">Text Spacing</h6>
                                                    <small class="text-muted">Control padding and margin for each text group in millimetres.</small>
                                                </div>
                                            </div>
                                            <div class="card-body p-2" id="admitSeatSpacingContent"></div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatTypographyPane" role="tabpanel"
                                        aria-labelledby="admitSeatTypographyTab">
                                        <div class="card mb-2 shadow-sm admit-seat-typography-card">
                                            <div
                                                class="card-header py-2 d-flex align-items-center justify-content-between admit-seat-typography-header">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"
                                                        style="width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;"><i
                                                            class="fas fa-font"></i></span>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold" style="letter-spacing:-0.01em;">
                                                            Typography &amp; Colors</h6>
                                                        <small class="text-muted">Control every text size, color, and
                                                            alignment used on the card face.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body admit-seat-typography-body">

                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="row align-items-center admit-seat-typography-row ">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">School Name</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_school_name_font_size"
                                                                    id="admitSeatSchoolNameFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_school_name_font_size', $cardSettings?->card_school_name_font_size ?? 7.2) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color"
                                                                        name="card_school_name_text_color"
                                                                        id="admitSeatSchoolNameColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_school_name_text_color', $cardSettings?->card_school_name_text_color ?? '#ffffff') }}">
                                                                    <span id="admitSeatSchoolNameColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[school_name]"
                                                                        id="admitSeatSchoolNameBorderColor" class="csm-color-native"
                                                                        title="School Name border color"
                                                                        value="{{ old('card_border_colors.school_name', data_get($cardSettings?->card_border_colors, 'school_name', '#ffffff')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[school_name]" value="1" {{ old('card_border_transparent.school_name', data_get($cardSettings?->card_border_transparent, 'school_name', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">School Address</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_school_detail_font_size"
                                                                    id="admitSeatSchoolDetailFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_school_detail_font_size', $cardSettings?->card_school_detail_font_size ?? 5.4) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color"
                                                                        name="card_school_detail_text_color"
                                                                        id="admitSeatSchoolDetailColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_school_detail_text_color', $cardSettings?->card_school_detail_text_color ?? '#e5e7eb') }}">
                                                                    <span id="admitSeatSchoolDetailColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[school_detail]"
                                                                        id="admitSeatSchoolDetailBorderColor" class="csm-color-native"
                                                                        title="School Address border color"
                                                                        value="{{ old('card_border_colors.school_detail', data_get($cardSettings?->card_border_colors, 'school_detail', '#ffffff')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[school_detail]" value="1" {{ old('card_border_transparent.school_detail', data_get($cardSettings?->card_border_transparent, 'school_detail', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Slogan</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_slogan_font_size"
                                                                    id="admitSeatSloganFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_slogan_font_size', $cardSettings?->card_slogan_font_size ?? 4.8) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_slogan_text_color"
                                                                        id="admitSeatSloganColor" class="csm-color-native"
                                                                        value="{{ old('card_slogan_text_color', $cardSettings?->card_slogan_text_color ?? '#e5e7eb') }}">
                                                                    <span id="admitSeatSloganColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[slogan]"
                                                                        id="admitSeatSloganBorderColor" class="csm-color-native"
                                                                        title="Slogan border color"
                                                                        value="{{ old('card_border_colors.slogan', data_get($cardSettings?->card_border_colors, 'slogan', '#ffffff')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[slogan]" value="1" {{ old('card_border_transparent.slogan', data_get($cardSettings?->card_border_transparent, 'slogan', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Front Title</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_title_font_size"
                                                                    id="admitSeatTitleFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_title_font_size', $cardSettings?->card_title_font_size ?? 4.7) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_title_text_color"
                                                                        id="admitSeatTitleColor" class="csm-color-native"
                                                                        value="{{ old('card_title_text_color', $cardSettings?->card_title_text_color ?? '#ffffff') }}">
                                                                    <span id="admitSeatTitleColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[title]"
                                                                        id="admitSeatTitleBorderColor" class="csm-color-native"
                                                                        title="Front Title border color"
                                                                        value="{{ old('card_border_colors.title', data_get($cardSettings?->card_border_colors, 'title', '#ffffff')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[title]" value="1" {{ old('card_border_transparent.title', data_get($cardSettings?->card_border_transparent, 'title', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Vertical Label</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_vertical_label_font_size"
                                                                    id="admitSeatVerticalLabelFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_vertical_label_font_size', $cardSettings?->card_vertical_label_font_size ?? 5.2) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_vertical_label_text_color"
                                                                        id="admitSeatVerticalLabelColor" class="csm-color-native"
                                                                        value="{{ old('card_vertical_label_text_color', $cardSettings?->card_vertical_label_text_color ?? '#16a085') }}">
                                                                    <span id="admitSeatVerticalLabelColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[vertical_label]"
                                                                        id="admitSeatVerticalLabelBorderColor" class="csm-color-native"
                                                                        title="Vertical Label border color"
                                                                        value="{{ old('card_border_colors.vertical_label', data_get($cardSettings?->card_border_colors, 'vertical_label', '#16a085')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[vertical_label]" value="1" {{ old('card_border_transparent.vertical_label', data_get($cardSettings?->card_border_transparent, 'vertical_label', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Student Name</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_name_font_size"
                                                                    id="admitSeatNameFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_name_font_size', $cardSettings?->card_name_font_size ?? 7.2) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_name_text_color"
                                                                        id="admitSeatNameColor" class="csm-color-native"
                                                                        value="{{ old('card_name_text_color', $cardSettings?->card_name_text_color ?? '#111827') }}">
                                                                    <span id="admitSeatNameColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Exam Type</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_exam_type_font_size"
                                                                    id="admitSeatExamTypeFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_exam_type_font_size', $cardSettings?->card_exam_type_font_size ?? 7.4) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_exam_type_text_color"
                                                                        id="admitSeatExamTypeColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_exam_type_text_color', $cardSettings?->card_exam_type_text_color ?? '#ffffff') }}">
                                                                    <span id="admitSeatExamTypeColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[exam_type]"
                                                                        id="admitSeatExamTypeBorderColor" class="csm-color-native"
                                                                        title="Exam Type border color"
                                                                        value="{{ old('card_border_colors.exam_type', data_get($cardSettings?->card_border_colors, 'exam_type', '#ffffff')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[exam_type]" value="1" {{ old('card_border_transparent.exam_type', data_get($cardSettings?->card_border_transparent, 'exam_type', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Exam Name / Badge</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_exam_name_font_size"
                                                                    id="admitSeatExamNameFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_exam_name_font_size', $cardSettings?->card_exam_name_font_size ?? 6.8) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_exam_name_text_color"
                                                                        id="admitSeatExamNameColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_exam_name_text_color', $cardSettings?->card_exam_name_text_color ?? '#e5e7eb') }}">
                                                                    <span id="admitSeatExamNameColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                    <input type="color" name="card_border_colors[exam_name]"
                                                                        id="admitSeatExamNameBorderColor" class="csm-color-native"
                                                                        title="Exam Name / Badge border color"
                                                                        value="{{ old('card_border_colors.exam_name', data_get($cardSettings?->card_border_colors, 'exam_name', '#fff200')) }}">
                                                                    <label class="border-transparent-toggle" title="Transparent border" aria-label="Transparent border"><input class="sr-only" type="checkbox" name="card_border_transparent[exam_name]" value="1" {{ old('card_border_transparent.exam_name', data_get($cardSettings?->card_border_transparent, 'exam_name', false)) ? 'checked' : '' }}><i class="fas fa-border-none" aria-hidden="true"></i><span class="sr-only">Transparent border</span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row mb-0">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Student Fields</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_student_detail_font_size"
                                                                    id="admitSeatStudentDetailFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_student_detail_font_size', $cardSettings?->card_student_detail_font_size ?? 8.5) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color"
                                                                        name="card_student_detail_text_color"
                                                                        id="admitSeatStudentDetailColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_student_detail_text_color', $cardSettings?->card_student_detail_text_color ?? '#111827') }}">
                                                                    <span id="admitSeatStudentDetailColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="admitSeatTextAlignmentBlock" class="row align-items-center admit-seat-typography-row mb-0">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Text Alignment</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <select name="card_front_alignment"
                                                                    id="admitSeatFrontAlignment"
                                                                    class="csm-input csm-select form-control form-control-sm"
                                                                    title="Controls the overall front card text alignment."
                                                                    aria-label="Controls the overall front card text alignment.">
                                                                    <option value="left" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'left' ? 'selected' : '' }}>Left</option>
                                                                    <option value="center" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'center' ? 'selected' : '' }}>Center</option>
                                                                    <option value="right" {{ old('card_front_alignment', $cardSettings?->card_front_alignment ?? 'center') === 'right' ? 'selected' : '' }}>Right</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <select name="card_student_detail_alignment"
                                                                    id="admitSeatStudentDetailAlignment"
                                                                    class="csm-input csm-select form-control form-control-sm"
                                                                    title="Controls the alignment of student detail text."
                                                                    aria-label="Controls the alignment of student detail text.">
                                                                    <option value="left" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'left' ? 'selected' : '' }}>Details Left</option>
                                                                    <option value="center" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'center' ? 'selected' : '' }}>Details Center</option>
                                                                    <option value="right" {{ old('card_student_detail_alignment', $cardSettings?->card_student_detail_alignment ?? 'left') === 'right' ? 'selected' : '' }}>Details Right</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row align-items-center admit-seat-typography-row mb-0">
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <strong class="csm-tc-name d-block">Front Footer</strong>
                                                            </div>
                                                            <div class="col-12 col-md-4 mb-1 mb-md-0">
                                                                <input type="number" name="card_footer_font_size"
                                                                    id="admitSeatFooterFontSize"
                                                                    class="csm-input csm-typography-control form-control form-control-sm"
                                                                    min="1" step="0.1"
                                                                    value="{{ old('card_footer_font_size', $cardSettings?->card_footer_font_size ?? 4.5) }}">
                                                            </div>
                                                            <div class="col-12 col-md-4">
                                                                <div class="csm-color-row flex justify-center items-center flex-row gap-2">
                                                                    <input type="color" name="card_footer_text_color"
                                                                        id="admitSeatFooterColor" class="csm-color-native"
                                                                        value="{{ old('card_footer_text_color', $cardSettings?->card_footer_text_color ?? '#e5e7eb') }}">
                                                                    <span id="admitSeatFooterColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        @php
                                                            $typographySpacing = $cardSettings?->card_typography_spacing ?? [];
                                                            $spacingRows = [
                                                                'school_name' => 'School Name',
                                                                'school_detail' => 'School Address',
                                                                'slogan' => 'Slogan',
                                                                'title' => 'Front Title / Vertical Label',
                                                                'name' => 'Student Name',
                                                                'exam_type' => 'Exam Type',
                                                                'exam_name' => 'Exam Name / Badge',
                                                                'student_detail' => 'Student Fields',
                                                                'footer' => 'Front Footer',
                                                                'logo' => 'Logo',
                                                                'photo' => 'Student Photo',
                                                                'signature' => 'Principal Signature',
                                                                'vertical_label' => 'Vertical Admit Label',
                                                            ];
                                                        @endphp
                                                        <div id="admitSeatTextSpacingBlock" class="col-12 mt-3 pt-3 border-top">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <strong class="csm-tc-name">Text Spacing</strong>
                                                                <small class="text-muted">Padding and margin are in mm.</small>
                                                            </div>
                                                            <div class="row">
                                                                @foreach ($spacingRows as $spacingKey => $spacingLabel)
                                                                    <div class="col-12 col-md-6 mb-2" data-spacing-visibility-key="{{ $spacingKey }}">
                                                                        <div class="border rounded p-2 h-100 bg-light">
                                                                            <div class="row align-items-start">
                                                                                <div class="col-12 col-md-2 mb-2 mb-md-0">
                                                                                    <strong class="d-block small">{{ $spacingLabel }}</strong>
                                                                                </div>
                                                                                @foreach (['padding' => 'Padding', 'margin' => 'Margin'] as $spacingType => $spacingTypeLabel)
                                                                                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                                                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                                                            <div class="small font-weight-bold text-muted">{{ $spacingTypeLabel }}</div>
                                                                                            <button type="button" class="btn btn-link btn-sm p-0 text-muted admit-seat-spacing-lock"
                                                                                                data-spacing-lock-key="{{ $spacingKey }}" data-spacing-lock-type="{{ $spacingType }}"
                                                                                                aria-pressed="false" title="Lock all sides to the same value">
                                                                                                <i class="fas fa-unlock" aria-hidden="true"></i>
                                                                                                <span class="sr-only">Lock {{ $spacingTypeLabel }} sides</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="row no-gutters mx-n1">
                                                                                            @foreach (['top' => ['Top', 'fa-arrow-up'], 'right' => ['Right', 'fa-arrow-right'], 'bottom' => ['Bottom', 'fa-arrow-down'], 'left' => ['Left', 'fa-arrow-left']] as $side => [$sideLabel, $sideIcon])
                                                                                                @php
                                                                                                    $storedSideValue = data_get($typographySpacing, "$spacingKey.$spacingType.$side");
                                                                                                    $storedBoxValue = data_get($typographySpacing, "$spacingKey.$spacingType");
                                                                                                    $spacingValue = $storedSideValue ?? (is_array($storedBoxValue) ? 0 : ($storedBoxValue ?? ($spacingType === 'padding' ? ($cardSettings?->card_text_padding_value ?? 0) : ($cardSettings?->card_text_margin_value ?? 0))));
                                                                                                @endphp
                                                                                                <div class="col-3 px-1">
                                                                                                    <label class="small text-muted mb-1 d-block text-center" for="admitSeatSpacing{{ str($spacingKey)->studly() }}{{ str($spacingType)->studly() }}{{ $sideLabel }}" title="{{ $sideLabel }}" aria-label="{{ $sideLabel }}">
                                                                                                        <i class="fas {{ $sideIcon }}" aria-hidden="true"></i><span class="sr-only">{{ $sideLabel }}</span>
                                                                                                    </label>
                                                                                                    <input type="number" name="card_typography_spacing[{{ $spacingKey }}][{{ $spacingType }}][{{ $side }}]"
                                                                                                        id="admitSeatSpacing{{ str($spacingKey)->studly() }}{{ str($spacingType)->studly() }}{{ $sideLabel }}"
                                                                                                        class="csm-input form-control form-control-sm admit-seat-spacing-input"
                                                                                                        data-spacing-key="{{ $spacingKey }}" data-spacing-type="{{ $spacingType }}" data-spacing-side="{{ $side }}"
                                                                                                        min="{{ $spacingType === 'margin' ? -100 : 0 }}" max="{{ $spacingType === 'margin' ? 100 : 10 }}" step="any"
                                                                                                        value="{{ old("card_typography_spacing.$spacingKey.$spacingType.$side", $spacingValue) }}">
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatBackgroundPane" role="tabpanel"
                                        aria-labelledby="admitSeatBackgroundTab">
                                        <div class="card mb-2 shadow-sm">
                                            <div
                                                class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i
                                                            class="fas fa-fill-drip"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Background</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="form-group mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            name="card_is_transparent" id="admitSeatCardIsTransparent"
                                                            value="1" {{ $selectedTransparent ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="admitSeatCardIsTransparent">Transparent Background</label>
                                                    </div>
                                                </div>

                                                <div
                                                    class="admit-seat-card-color-controls {{ $selectedTransparent ? 'd-none' : '' }}">
                                                    <div class="row">
                                                        <div class="col-12 col-md-4 mb-2">
                                                            <div class="form-group mb-0">
                                                                <label
                                                                    class="d-block mb-1 small font-weight-bold text-dark">Background
                                                                    Type</label>
                                                                <div class="btn-group btn-group-toggle csm-bg-toggle d-flex"
                                                                    data-toggle="buttons">
                                                                    <label
                                                                        class="btn btn-outline-primary btn-sm flex-fill {{ $selectedColorType === 'gradient' ? 'active' : '' }}">
                                                                        <input type="radio" name="card_color_type"
                                                                            value="gradient"
                                                                            {{ $selectedColorType === 'gradient' ? 'checked' : '' }}>
                                                                        Gradient
                                                                    </label>
                                                                    <label
                                                                        class="btn btn-outline-primary btn-sm flex-fill {{ $selectedColorType === 'solid' ? 'active' : '' }}">
                                                                        <input type="radio" name="card_color_type"
                                                                            value="solid"
                                                                            {{ $selectedColorType === 'solid' ? 'checked' : '' }}>
                                                                        Solid
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-12 col-md-3 mb-2 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                            <div class="form-group mb-0">
                                                                <label
                                                                    class="d-block mb-1 small font-weight-bold text-dark">Gradient
                                                                    Start</label>
                                                                <span
                                                                    class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                    <input type="color" name="card_color_gradient_1"
                                                                        id="admitSeatCardColorGradient1"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_color_gradient_1', $cardSettings?->card_color_gradient_1 ?? '#1e3a5f') }}">
                                                                    <span id="admitSeatCardColorGradient1Preview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-12 col-md-3 mb-2 admit-seat-card-gradient-field {{ $selectedColorType === 'solid' ? 'd-none' : '' }}">
                                                            <div class="form-group mb-0">
                                                                <label
                                                                    class="d-block mb-1 small font-weight-bold text-dark">Gradient
                                                                    End</label>
                                                                <span
                                                                    class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                    <input type="color" name="card_color_gradient_2"
                                                                        id="admitSeatCardColorGradient2"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_color_gradient_2', $cardSettings?->card_color_gradient_2 ?? '#2563eb') }}">
                                                                    <span id="admitSeatCardColorGradient2Preview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="col-12 col-md-3 mb-2 admit-seat-card-solid-field {{ $selectedColorType === 'solid' ? '' : 'd-none' }}">
                                                            <div class="form-group mb-0">
                                                                <label
                                                                    class="d-block mb-1 small font-weight-bold text-dark">Solid
                                                                    Color</label>
                                                                <span
                                                                    class="csm-color-swatch-input csm-color-swatch-input-block d-flex align-items-center">
                                                                    <input type="color" name="card_solid_color"
                                                                        id="admitSeatCardSolidColor"
                                                                        class="csm-color-native"
                                                                        value="{{ old('card_solid_color', $cardSettings?->card_solid_color ?? '#1e3a5f') }}">
                                                                    <span id="admitSeatCardSolidColorPreview"
                                                                        class="d-inline-block rounded ml-0"
                                                                        style="width:32px;height:32px;border:1px solid #d1d5db;vertical-align:middle;"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-0 mt-2">
                                                        <label
                                                            class="d-block mb-1 small font-weight-bold text-dark">Preview</label>
                                                        <div id="admitSeatCardThemePreview" class="border rounded"
                                                            style="height: 36px;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="admitSeatVisibilityPane" role="tabpanel"
                                        aria-labelledby="admitSeatVisibilityTab">
                                        <div class="card mb-2 shadow-sm">
                                            <div
                                                class="card-header py-2 bg-light d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"><i
                                                            class="fas fa-eye"></i></span>
                                                    <h6 class="mb-0 font-weight-bold">Visibility</h6>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                @php
                                                    $studentFieldOrderLabels = [
                                                        'student_name' => 'Student Name',
                                                        'student_id' => 'ID',
                                                        'father_name' => "Father's Name",
                                                        'mother_name' => "Mother's Name",
                                                        'roll' => 'Roll',
                                                        'class' => 'Class',
                                                        'section' => 'Section',
                                                        'session' => 'Session',
                                                    ];
                                                    $savedStudentFieldOrder = $cardSettings?->card_student_field_order ?: array_keys($studentFieldOrderLabels);
                                                    $studentFieldOrderPositions = array_flip($savedStudentFieldOrder);
                                                @endphp
                                                <div class="border rounded p-2 mb-3 bg-light">
                                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                                        <strong class="small">Student Field Order</strong>
                                                        <small class="text-muted">Use 1 for first, 2 for second, and so on.</small>
                                                    </div>
                                                    <div class="row">
                                                        @foreach ($studentFieldOrderLabels as $fieldKey => $fieldLabel)
                                                            <div class="col-12 col-sm-6 col-lg-3 mb-2">
                                                                <label class="small text-muted mb-1" for="admitSeatStudentFieldOrder{{ str($fieldKey)->studly() }}">{{ $fieldLabel }}</label>
                                                                <input type="number" min="1" max="8" step="1"
                                                                    name="card_student_field_order[{{ $fieldKey }}]"
                                                                    id="admitSeatStudentFieldOrder{{ str($fieldKey)->studly() }}"
                                                                    class="form-control form-control-sm admit-seat-student-field-order"
                                                                    value="{{ old("card_student_field_order.$fieldKey", ($studentFieldOrderPositions[$fieldKey] ?? 0) + 1) }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_logo_front" id="admitSeatShowLogoFront"
                                                                {{ old('card_show_logo_front', $cardSettings?->card_show_logo_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowLogoFront">Front Logo</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_photo_front" id="admitSeatShowPhotoFront"
                                                                {{ old('card_show_photo_front', $cardSettings?->card_show_photo_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowPhotoFront">Photo</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_father_name_front"
                                                                id="admitSeatShowFatherNameFront"
                                                                {{ old('card_show_father_name_front', $cardSettings?->card_show_father_name_front ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowFatherNameFront">Father's Name</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_mother_name_front"
                                                                id="admitSeatShowMotherNameFront"
                                                                {{ old('card_show_mother_name_front', $cardSettings?->card_show_mother_name_front ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowMotherNameFront">Mother's Name</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_student_name_label_front"
                                                                id="admitSeatShowStudentNameLabelFront"
                                                                {{ old('card_show_student_name_label_front', $cardSettings?->card_show_student_name_label_front ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowStudentNameLabelFront">Student Name Label</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_roll_front"
                                                                id="admitSeatShowRollFront"
                                                                {{ old('card_show_roll_front', $cardSettings?->card_show_roll_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowRollFront">Roll</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_class_front"
                                                                id="admitSeatShowClassFront"
                                                                {{ old('card_show_class_front', $cardSettings?->card_show_class_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowClassFront">Class</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_section_front"
                                                                id="admitSeatShowSectionFront"
                                                                {{ old('card_show_section_front', $cardSettings?->card_show_section_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowSectionFront">Section</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_session_front"
                                                                id="admitSeatShowSessionFront"
                                                                {{ old('card_show_session_front', $cardSettings?->card_show_session_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowSessionFront">Session</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_vertical_label_front"
                                                                id="admitSeatShowVerticalLabelFront"
                                                                {{ old('card_show_vertical_label_front', $cardSettings?->card_show_vertical_label_front ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowVerticalLabelFront">Vertical Admit Label</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_exam_name_badge_front"
                                                                id="admitSeatExamNameBadgeFront"
                                                                {{ old('card_exam_name_badge_front', $cardSettings?->card_exam_name_badge_front ?? false) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatExamNameBadgeFront">Exam Name Badge</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_school_detail_front"
                                                                id="admitSeatShowSchoolDetailFront"
                                                                {{ old('card_show_school_detail_front', $cardSettings?->card_show_school_detail_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowSchoolDetailFront">School Address</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_slogan_front"
                                                                id="admitSeatShowSloganFront"
                                                                {{ old('card_show_slogan_front', $cardSettings?->card_show_slogan_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowSloganFront">Slogan</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_title_front" id="admitSeatShowTitleFront"
                                                                {{ old('card_show_title_front', $cardSettings?->card_show_title_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowTitleFront">Front Title</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_footer_front"
                                                                id="admitSeatShowFooterFront"
                                                                {{ old('card_show_footer_front', $cardSettings?->card_show_footer_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowFooterFront">Front Footer</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_exam_type_front"
                                                                id="admitSeatShowExamTypeFront"
                                                                {{ old('card_show_exam_type_front', $cardSettings?->card_show_exam_type_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowExamTypeFront">Exam Type</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6 col-lg-4 mb-2">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input"
                                                                name="card_show_exam_name_front"
                                                                id="admitSeatShowExamNameFront"
                                                                {{ old('card_show_exam_name_front', $cardSettings?->card_show_exam_name_front ?? true) ? 'checked' : '' }}>
                                                            <label class="custom-control-label"
                                                                for="admitSeatShowExamNameFront">Exam Name</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="csm-footnote">These settings are saved once and used by search and PDF
                                        output.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer card-settings-modal-footer">
                            @if ($settingsOnly ?? false)
                                <a href="{{ route('results.admit-seat-cards.index', ['card_type' => $cardType ?? 'admit_card']) }}"
                                    class="btn btn-outline-secondary">Close</a>
                            @else
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                            @endif
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="admitSeatUserManualModal" tabindex="-1" role="dialog"
        aria-labelledby="admitSeatUserManualModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="admitSeatUserManualModalLabel">Admit / Seat Card User Manual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body small">
                    <h6 class="font-weight-bold">1. Choose the card profile</h6>
                    <p>Use Admit Card or Seat Card at the top of the settings panel. Each profile is saved separately.</p>

                    <h6 class="font-weight-bold">2. Resize the card</h6>
                    <p>Drag the blue handle at the bottom-right corner of the live card preview. Width and height update automatically in the selected unit.</p>

                    <h6 class="font-weight-bold">3. Move content</h6>
                    <p>Drag a logo, photo, signature, text block, footer, or student-details block to reposition it. Hold <kbd>Shift</kbd> while dragging to change padding. Hold <kbd>Alt</kbd> to change margin.</p>

                    <h6 class="font-weight-bold">4. Reorder student fields</h6>
                    <p>Drag student rows up or down inside the student-details area. The order is saved and used for print and PDF output.</p>

                    <h6 class="font-weight-bold">5. Visibility and styling</h6>
                    <p>Use Visibility to show or hide fields. Typography &amp; Colors controls font sizes and colors. Text Alignment &amp; Spacing controls alignment and four-sided spacing.</p>

                    <h6 class="font-weight-bold">6. Print safety</h6>
                    <p>Warnings appear when content leaves the card area or overlaps the footer. Move the element, reduce spacing, or increase the card height before printing.</p>

                    <div class="alert alert-info py-2 mb-0">
                        Save Settings after editing. The saved profile is used by search results, print output, and PDF output.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Got it</button>
                </div>
            </div>
        </div>
    </div>

    @php
        $cardSettingsPayload = $cardSettingsMap
            ->mapWithKeys(function ($setting) {
                return [
                    (string) $setting->card_type => [
                        'cards_per_page' => $setting->cards_per_page,
                        'cards_per_row' => $setting->cards_per_row,
                        'card_width_value' => $setting->card_width_value,
                        'card_height_value' => $setting->card_height_value,
                        'grid_gap_value' => $setting->grid_gap_value,
                        'card_dimension_unit' => $setting->card_dimension_unit,
                        'page_width_mm' => $setting->page_width_mm,
                        'page_height_mm' => $setting->page_height_mm,
                        'page_margin_top_mm' => $setting->page_margin_top_mm,
                        'page_margin_right_mm' => $setting->page_margin_right_mm,
                        'page_margin_bottom_mm' => $setting->page_margin_bottom_mm,
                        'page_margin_left_mm' => $setting->page_margin_left_mm,
                        'card_front_alignment' => $setting->card_front_alignment,
                        'card_back_alignment' => $setting->card_back_alignment,
                        'card_front_padding_value' => $setting->card_front_padding_value,
                        'card_back_padding_value' => $setting->card_back_padding_value,
                        'card_photo_width_value' => $setting->card_photo_width_value,
                        'card_photo_height_value' => $setting->card_photo_height_value,
                        'card_photo_fit' => $setting->card_photo_fit,
                        'card_logo_size_value' => $setting->card_logo_size_value,
                        'card_signature_size_value' => $setting->card_signature_size_value,
                        'card_school_name_font_size' => $setting->card_school_name_font_size,
                        'card_school_detail_font_size' => $setting->card_school_detail_font_size,
                        'card_slogan_font_size' => $setting->card_slogan_font_size,
                        'card_slogan_text_color' => $setting->card_slogan_text_color,
                        'card_title_font_size' => $setting->card_title_font_size,
                        'card_vertical_label_font_size' => $setting->card_vertical_label_font_size,
                        'card_name_font_size' => $setting->card_name_font_size,
                        'card_name_text_color' => $setting->card_name_text_color,
                        'card_exam_type_font_size' => $setting->card_exam_type_font_size,
                        'card_exam_name_font_size' => $setting->card_exam_name_font_size,
                        'card_footer_font_size' => $setting->card_footer_font_size,
                        'card_student_detail_alignment' => $setting->card_student_detail_alignment,
                        'card_student_detail_font_size' => $setting->card_student_detail_font_size,
                        'card_student_detail_text_color' => $setting->card_student_detail_text_color,
                        'card_text_padding_value' => $setting->card_text_padding_value,
                        'card_text_margin_value' => $setting->card_text_margin_value,
                        'card_typography_spacing' => $setting->card_typography_spacing ?? [],
                        'card_student_field_order' => $setting->card_student_field_order ?? [],
                        'card_element_positions' => $setting->card_element_positions ?? [],
                        'card_element_sizes' => $setting->card_element_sizes ?? [],
                        'card_border_colors' => $setting->card_border_colors ?? [],
                        'card_border_transparent' => $setting->card_border_transparent ?? [],
                        'card_is_transparent' => $setting->card_is_transparent,
                        'card_color_type' => $setting->card_color_type,
                        'card_color_gradient_1' => $setting->card_color_gradient_1,
                        'card_color_gradient_2' => $setting->card_color_gradient_2,
                        'card_solid_color' => $setting->card_solid_color,
                        'card_school_name_text_color' => $setting->card_school_name_text_color,
                        'card_school_detail_text_color' => $setting->card_school_detail_text_color,
                        'card_title_text_color' => $setting->card_title_text_color,
                        'card_vertical_label_text_color' => $setting->card_vertical_label_text_color,
                        'card_exam_type_text_color' => $setting->card_exam_type_text_color,
                        'card_exam_name_text_color' => $setting->card_exam_name_text_color,
                        'card_footer_text_color' => $setting->card_footer_text_color,
                        'card_show_school_detail_front' => $setting->card_show_school_detail_front,
                        'card_show_school_detail_back' => $setting->card_show_school_detail_back,
                        'card_show_slogan_front' => $setting->card_show_slogan_front,
                        'card_show_slogan_back' => $setting->card_show_slogan_back,
                        'card_show_title_front' => $setting->card_show_title_front,
                        'card_show_title_back' => $setting->card_show_title_back,
                        'card_show_logo_front' => $setting->card_show_logo_front,
                        'card_show_logo_back' => $setting->card_show_logo_back,
                        'card_show_photo_front' => $setting->card_show_photo_front,
                        'card_show_father_name_front' => $setting->card_show_father_name_front,
                        'card_show_mother_name_front' => $setting->card_show_mother_name_front,
                        'card_show_student_name_label_front' => $setting->card_show_student_name_label_front,
                        'card_show_roll_front' => $setting->card_show_roll_front,
                        'card_show_class_front' => $setting->card_show_class_front,
                        'card_show_section_front' => $setting->card_show_section_front,
                        'card_show_session_front' => $setting->card_show_session_front,
                        'card_show_vertical_label_front' => $setting->card_show_vertical_label_front,
                        'card_exam_name_badge_front' => $setting->card_exam_name_badge_front,
                        'card_show_footer_front' => $setting->card_show_footer_front,
                        'card_show_footer_back' => $setting->card_show_footer_back,
                        'card_show_exam_type_front' => $setting->card_show_exam_type_front,
                        'card_show_exam_name_front' => $setting->card_show_exam_name_front,
                        'card_show_back_notice' => $setting->card_show_back_notice,
                        'card_logo_url' =>
                            $setting->card_logo && file_exists(public_path($setting->card_logo))
                                ? asset($setting->card_logo)
                                : null,
                        'card_principal_signature_url' =>
                            $setting->card_principal_signature &&
                            file_exists(public_path($setting->card_principal_signature))
                                ? asset($setting->card_principal_signature)
                                : null,
                    ],
                ];
            })
            ->toArray();
    @endphp

    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        if (typeof Dropzone !== 'undefined') {
            // Disable auto-discovery before DOMContentLoaded so it cannot attach
            // to this .dropzone element before our custom initializer runs.
            Dropzone.autoDiscover = false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const classSelect = document.getElementById('classSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            const cardTypeSelect = document.querySelector('#filterForm select[name="card_type"]');
            const cardSettingsModal = document.getElementById('cardSettingsModal');
            const cardSettingsForm = cardSettingsModal?.querySelector('form');

            // Keep alignment and spacing fields in their dedicated settings areas.
            const alignmentBlock = document.getElementById('admitSeatTextAlignmentBlock');
            const alignmentContent = document.getElementById('admitSeatAlignmentContent');
            const alignmentPane = document.getElementById('admitSeatAlignmentPane');
            const spacingSection = document.getElementById('admitSeatSpacingSection');
            const spacingBlock = document.getElementById('admitSeatTextSpacingBlock');
            const spacingContent = document.getElementById('admitSeatSpacingContent');
            if (alignmentBlock && alignmentContent) alignmentContent.appendChild(alignmentBlock);
            if (alignmentPane && spacingSection) {
                alignmentPane.appendChild(spacingSection);
            }
            if (spacingBlock && spacingContent) {
                spacingBlock.classList.remove('mt-3', 'pt-3', 'border-top');
                spacingContent.appendChild(spacingBlock);
            }

            const settingsTabs = $('#admitSeatSettingsTabs');
            const validSettingsTabTargets = new Set(
                Array.from(document.querySelectorAll('#admitSeatSettingsTabs a[data-toggle="tab"]'))
                    .map((tab) => tab.getAttribute('href'))
                    .filter(Boolean)
            );

            function rememberSettingsTab(target) {
                if (!target || !validSettingsTabTargets.has(target)) return;

                const url = new URL(window.location.href);
                url.hash = target;
                window.history.replaceState(null, '', url.toString());
            }

            settingsTabs.on('shown.bs.tab', 'a[data-toggle="tab"]', function() {
                rememberSettingsTab(this.getAttribute('href'));
            });

            const rememberedSettingsTab = window.location.hash;
            if (validSettingsTabTargets.has(rememberedSettingsTab)) {
                settingsTabs.find(`a[data-toggle="tab"][href="${rememberedSettingsTab}"]`).tab('show');
            }

            const cardSettingsModalTitle = document.getElementById('cardSettingsModalLabel');
            const cardSettingsTypeLabel = document.getElementById('cardSettingsModalTypeLabel');
            const dirtyBadge = document.getElementById('cardSettingsDirtyBadge');
            const admitSeatCardIsTransparent = document.getElementById('admitSeatCardIsTransparent');
            const admitSeatCardThemePreview = document.getElementById('admitSeatCardThemePreview');
            const admitSeatPreviewLabel = document.getElementById('admitSeatPreviewLabel');
            const admitSeatLivePreviewTitleFront = document.getElementById('admitSeatLivePreviewTitleFront');
            const admitSeatCardColorGradient1 = document.getElementById('admitSeatCardColorGradient1');
            const admitSeatCardColorGradient2 = document.getElementById('admitSeatCardColorGradient2');
            const admitSeatCardSolidColor = document.getElementById('admitSeatCardSolidColor');
            const admitSeatSchoolNameColor = document.getElementById('admitSeatSchoolNameColor');
            const admitSeatSchoolDetailColor = document.getElementById('admitSeatSchoolDetailColor');
            const admitSeatTitleColor = document.getElementById('admitSeatTitleColor');
            const admitSeatVerticalLabelColor = document.getElementById('admitSeatVerticalLabelColor');
            const admitSeatExamTypeColor = document.getElementById('admitSeatExamTypeColor');
            const admitSeatExamNameColor = document.getElementById('admitSeatExamNameColor');
            const admitSeatFooterColor = document.getElementById('admitSeatFooterColor');
            const admitSeatSchoolNameFontSize = document.getElementById('admitSeatSchoolNameFontSize');
            const admitSeatSchoolDetailFontSize = document.getElementById('admitSeatSchoolDetailFontSize');
            const admitSeatSloganFontSize = document.getElementById('admitSeatSloganFontSize');
            const admitSeatSloganColor = document.getElementById('admitSeatSloganColor');
            const admitSeatTitleFontSize = document.getElementById('admitSeatTitleFontSize');
            const admitSeatVerticalLabelFontSize = document.getElementById('admitSeatVerticalLabelFontSize');
            const admitSeatNameFontSize = document.getElementById('admitSeatNameFontSize');
            const admitSeatNameColor = document.getElementById('admitSeatNameColor');
            const admitSeatExamTypeFontSize = document.getElementById('admitSeatExamTypeFontSize');
            const admitSeatExamNameFontSize = document.getElementById('admitSeatExamNameFontSize');
            const admitSeatFooterFontSize = document.getElementById('admitSeatFooterFontSize');
            const admitSeatStudentDetailAlignment = document.getElementById('admitSeatStudentDetailAlignment');
            const admitSeatStudentDetailFontSize = document.getElementById('admitSeatStudentDetailFontSize');
            const admitSeatStudentDetailColor = document.getElementById('admitSeatStudentDetailColor');
            const admitSeatCardColorGradient1Preview = document.getElementById(
            'admitSeatCardColorGradient1Preview');
            const admitSeatCardColorGradient2Preview = document.getElementById(
            'admitSeatCardColorGradient2Preview');
            const admitSeatCardSolidColorPreview = document.getElementById('admitSeatCardSolidColorPreview');
            let modalScrollY = 0;
            const admitSeatSchoolNameColorPreview = document.getElementById('admitSeatSchoolNameColorPreview');
            const admitSeatSchoolDetailColorPreview = document.getElementById('admitSeatSchoolDetailColorPreview');
            const admitSeatSloganColorPreview = document.getElementById('admitSeatSloganColorPreview');
            const admitSeatTitleColorPreview = document.getElementById('admitSeatTitleColorPreview');
            const admitSeatVerticalLabelColorPreview = document.getElementById('admitSeatVerticalLabelColorPreview');
            const admitSeatNameColorPreview = document.getElementById('admitSeatNameColorPreview');
            const admitSeatExamTypeColorPreview = document.getElementById('admitSeatExamTypeColorPreview');
            const admitSeatExamNameColorPreview = document.getElementById('admitSeatExamNameColorPreview');
            const admitSeatFooterColorPreview = document.getElementById('admitSeatFooterColorPreview');
            const admitSeatStudentDetailColorPreview = document.getElementById(
            'admitSeatStudentDetailColorPreview');
            const admitSeatShowLogoFront = document.getElementById('admitSeatShowLogoFront');
            const admitSeatShowSchoolDetailFront = document.getElementById('admitSeatShowSchoolDetailFront');
            const admitSeatShowSloganFront = document.getElementById('admitSeatShowSloganFront');
            const admitSeatShowTitleFront = document.getElementById('admitSeatShowTitleFront');
            const admitSeatShowPhotoFront = document.getElementById('admitSeatShowPhotoFront');
            const admitSeatShowFooterFront = document.getElementById('admitSeatShowFooterFront');
            const admitSeatShowExamTypeFront = document.getElementById('admitSeatShowExamTypeFront');
            const admitSeatShowExamNameFront = document.getElementById('admitSeatShowExamNameFront');
            const admitSeatShowFatherNameFront = document.getElementById('admitSeatShowFatherNameFront');
            const admitSeatShowMotherNameFront = document.getElementById('admitSeatShowMotherNameFront');
            const admitSeatShowStudentNameLabelFront = document.getElementById('admitSeatShowStudentNameLabelFront');
            const admitSeatShowRollFront = document.getElementById('admitSeatShowRollFront');
            const admitSeatShowClassFront = document.getElementById('admitSeatShowClassFront');
            const admitSeatShowSectionFront = document.getElementById('admitSeatShowSectionFront');
            const admitSeatShowSessionFront = document.getElementById('admitSeatShowSessionFront');
            const admitSeatShowVerticalLabelFront = document.getElementById('admitSeatShowVerticalLabelFront');
            const admitSeatExamNameBadgeFront = document.getElementById('admitSeatExamNameBadgeFront');
            const admitSeatCardLogoDropzone = document.getElementById('admitSeatCardLogoDropzone');
            const admitSeatCardLogoInput = document.getElementById('admitSeatCardLogoInput');
            const admitSeatCardPrincipalSignatureDropzone = document.getElementById(
                'admitSeatCardPrincipalSignatureDropzone');
            const admitSeatCardPrincipalSignatureInput = document.getElementById(
            'admitSeatPrincipalSignatureInput');
            const admitSeatCardPrincipalSignaturePreview = document.getElementById(
                'admitSeatPrincipalSignaturePreview');
            const admitSeatLivePreview = document.getElementById('admitSeatLivePreview');
            const admitSeatLivePreviewLogoFront = document.getElementById('admitSeatLivePreviewLogoFront');
            const admitSeatLivePreviewSignatureFront = document.getElementById(
            'admitSeatLivePreviewSignatureFront');
            const admitSeatCardWidth = cardSettingsForm?.elements.namedItem('card_width_value');
            const admitSeatCardHeight = cardSettingsForm?.elements.namedItem('card_height_value');
            const admitSeatCardDimensionUnit = cardSettingsForm?.elements.namedItem('card_dimension_unit');
            const admitSeatFrontAlignment = cardSettingsForm?.elements.namedItem('card_front_alignment');
            const admitSeatPhotoWidth = document.getElementById('admitSeatPhotoWidth');
            const admitSeatPhotoHeight = document.getElementById('admitSeatPhotoHeight');
            const admitSeatPhotoFit = cardSettingsForm?.elements.namedItem('card_photo_fit');
            const admitSeatLogoSize = document.getElementById('admitSeatLogoSize');
            const admitSeatSignatureSize = document.getElementById('admitSeatSignatureSize');
            const admitSeatCardLogoPreview = document.getElementById('admitSeatCardLogoPreview');
            const selectedSection = @json(request('section_id'));
            const hasValidationErrors = @json($errors->any());
            const cardSettingsMap = @json($cardSettingsPayload);
            const defaultCardType = @json($cardType ?? 'admit_card');
            const fallbackSchoolLogo = @json($schoolLogoUrl);
            let activeCardSettings = {};
            let previewLogoUrl = null;
            let previewPrincipalSignatureUrl = null;
            let admitSeatCardLogoDropzoneInstance = null;
            let admitSeatCardPrincipalSignatureDropzoneInstance = null;
            const initialCardPrincipalSignaturePreviewSrc = admitSeatCardPrincipalSignaturePreview?.getAttribute(
                'src') || '';
            const defaultThemeSettings = {
                card_is_transparent: false,
                card_color_type: 'gradient',
                card_front_alignment: 'center',
                card_back_alignment: 'center',
                card_front_padding_value: 0.8,
                card_back_padding_value: 0.8,
                card_photo_width_value: 1.8,
                card_photo_height_value: 2.7,
                card_photo_fit: 'cover',
                card_logo_size_value: 0.8,
                card_signature_size_value: 1.4,
                card_school_name_font_size: 7.2,
                card_school_detail_font_size: 5.4,
                card_slogan_font_size: 4.8,
                card_slogan_text_color: '#e5e7eb',
                card_title_font_size: 4.7,
                card_vertical_label_font_size: 5.2,
                card_name_font_size: 7.2,
                card_name_text_color: '#111827',
                card_exam_type_font_size: 7.4,
                card_exam_name_font_size: 6.8,
                card_footer_font_size: 4.5,
                card_student_detail_alignment: 'left',
                card_student_detail_font_size: 8.5,
                card_student_detail_text_color: '#111827',
                card_text_padding_value: 0,
                card_text_margin_value: 0,
                card_element_sizes: { exam_name: {} },
                card_border_colors: {},
                card_border_transparent: {},
                card_show_school_detail_front: true,
                card_show_school_detail_back: true,
                card_show_slogan_front: true,
                card_show_slogan_back: true,
                card_show_title_front: true,
                card_show_title_back: true,
                card_color_gradient_1: '#1e3a5f',
                card_color_gradient_2: '#2563eb',
                card_solid_color: '#1e3a5f',
                card_school_name_text_color: '#ffffff',
                card_school_detail_text_color: '#e5e7eb',
                card_title_text_color: '#ffffff',
                card_vertical_label_text_color: '#16a085',
                card_exam_type_text_color: '#ffffff',
                card_exam_name_text_color: '#e5e7eb',
                card_footer_text_color: '#e5e7eb',
                card_show_logo_front: true,
                card_show_logo_back: true,
                card_show_photo_front: true,
                card_show_footer_front: true,
                card_show_footer_back: true,
                card_show_exam_type_front: true,
                card_show_exam_name_front: true,
                card_show_back_notice: true,
                card_student_field_order: ['student_name', 'student_id', 'father_name', 'mother_name', 'roll', 'class',
                    'section', 'session'
                ],
            };

            function settingKeyFromCardType(cardType) {
                return cardType === 'seat_card' ? '2' : '1';
            }

            function settingLabelFromCardType(cardType) {
                return cardType === 'seat_card' ? 'Seat Card Settings' : 'Admit Card Settings';
            }

            function previewLabelFromCardType(cardType) {
                return cardType === 'seat_card' ? 'Seat Card Preview' : 'Admit Card Preview';
            }

            function getSelectedCardColorType() {
                return cardSettingsForm?.querySelector('input[name="card_color_type"]:checked')?.value ||
                'gradient';
            }

            function setSelectedCardColorType(value) {
                if (!cardSettingsForm) return;

                const normalized = value === 'solid' ? 'solid' : 'gradient';
                cardSettingsForm.querySelectorAll('input[name="card_color_type"]').forEach((radio) => {
                    radio.checked = radio.value === normalized;
                    radio.closest('label')?.classList.toggle('active', radio.checked);
                });
            }

            function syncCardTypeSwitcher(cardType) {
                const normalized = cardType === 'seat_card' ? 'seat_card' : 'admit_card';
                $('.js-card-type-switch').each(function() {
                    const $button = $(this);
                    const isActive = $button.data('card-type') === normalized;
                    $button.toggleClass('active btn-primary', isActive);
                    $button.toggleClass('btn-outline-primary', !isActive);
                });

                if (cardTypeSelect) {
                    cardTypeSelect.value = normalized;
                }
            }

            function setDirtyState(isDirty) {
                if (!dirtyBadge) return;
                dirtyBadge.classList.toggle('d-none', !isDirty);
            }

            function setPreviewElementVisible(selector, isVisible) {
                if (!admitSeatLivePreview) return;
                admitSeatLivePreview.querySelectorAll(selector).forEach((element) => {
                    element.classList.toggle('d-none', !isVisible);
                });
            }

            function refreshStudentFieldOrder() {
                if (!admitSeatLivePreview || !cardSettingsForm) return;

                const defaultOrder = ['student_name', 'student_id', 'father_name', 'mother_name', 'roll', 'class',
                    'section', 'session'
                ];
                const order = {};
                defaultOrder.forEach((field, index) => {
                    const input = cardSettingsForm.elements.namedItem(`card_student_field_order[${field}]`);
                    const value = parseInt(input?.value || `${index + 1}`, 10);
                    order[field] = Number.isFinite(value) ? value : index + 1;
                });

                admitSeatLivePreview.querySelectorAll('.admit-card__rows').forEach((rows) => {
                    const fields = Array.from(rows.querySelectorAll(':scope > [data-preview-field-order]'));
                    fields.forEach((field) => {
                        field.setAttribute('draggable', 'true');
                        field.classList.add('admit-card__student-field--sortable');
                    });
                    fields
                        .sort((a, b) => {
                            const aOrder = order[a.dataset.previewFieldOrder] ?? Number.MAX_SAFE_INTEGER;
                            const bOrder = order[b.dataset.previewFieldOrder] ?? Number.MAX_SAFE_INTEGER;
                            return aOrder - bOrder || defaultOrder.indexOf(a.dataset.previewFieldOrder) - defaultOrder.indexOf(b.dataset.previewFieldOrder);
                        })
                        .forEach((field) => rows.appendChild(field));
                });
            }

            function syncStudentFieldOrderInputs(rows) {
                if (!cardSettingsForm || !rows) return;

                Array.from(rows.querySelectorAll(':scope > [data-preview-field-order]')).forEach((field, index) => {
                    const input = cardSettingsForm.elements.namedItem(
                        `card_student_field_order[${field.dataset.previewFieldOrder}]`
                    );
                    if (input) input.value = index + 1;
                });
            }

            let draggedStudentField = null;
            admitSeatLivePreview?.addEventListener('dragstart', function(event) {
                const field = event.target.closest('.admit-card__student-field--sortable');
                if (!field) return;

                draggedStudentField = field;
                field.classList.add('admit-card__student-field--dragging');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', field.dataset.previewFieldOrder || '');
            });

            admitSeatLivePreview?.addEventListener('dragover', function(event) {
                const target = event.target.closest('.admit-card__student-field--sortable');
                if (!draggedStudentField || !target || target === draggedStudentField) return;

                event.preventDefault();
                target.classList.add('admit-card__student-field--drag-over');
            });

            admitSeatLivePreview?.addEventListener('dragleave', function(event) {
                event.target.closest('.admit-card__student-field--sortable')?.classList.remove(
                    'admit-card__student-field--drag-over');
            });

            admitSeatLivePreview?.addEventListener('drop', function(event) {
                const target = event.target.closest('.admit-card__student-field--sortable');
                if (!draggedStudentField || !target || target === draggedStudentField) return;

                event.preventDefault();
                const rows = target.parentElement;
                const insertAfter = event.clientY > target.getBoundingClientRect().top + target.offsetHeight / 2;
                rows.insertBefore(draggedStudentField, insertAfter ? target.nextSibling : target);
                syncStudentFieldOrderInputs(rows);
                setDirtyState(true);
                refreshStudentFieldOrder();
            });

            admitSeatLivePreview?.addEventListener('dragend', function() {
                if (draggedStudentField) {
                    draggedStudentField.classList.remove('admit-card__student-field--dragging');
                }
                admitSeatLivePreview.querySelectorAll('.admit-card__student-field--drag-over').forEach((field) => {
                    field.classList.remove('admit-card__student-field--drag-over');
                });
                draggedStudentField = null;
            });

            let draggedPreviewElement = null;
            let draggedPreviewState = null;
            let resizingPreviewElement = null;
            let suppressPreviewClick = false;

            function getPreviewSpacingInput(spacingKey, spacingType, side) {
                return cardSettingsForm?.elements.namedItem(
                    `card_typography_spacing[${spacingKey}][${spacingType}][${side}]`
                );
            }

            function updatePreviewSpacingFromDrag(spacingKey, spacingType, side, deltaMm) {
                const input = getPreviewSpacingInput(spacingKey, spacingType, side);
                if (!input) return;

                const currentValue = parseFloat(input.value || '0');
                const minimum = spacingType === 'margin' ? -100 : 0;
                const maximum = spacingType === 'margin' ? 100 : 10;
                const nextValue = Math.max(minimum, Math.min(maximum, (Number.isFinite(currentValue) ? currentValue : 0) + deltaMm));
                input.value = nextValue.toFixed(2).replace(/\.00$/, '');
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            function updatePreviewPositionFromDrag(positionKey, axis, deltaMm) {
                const input = cardSettingsForm?.querySelector(
                    `[data-element-position-key="${positionKey}"][data-element-position-axis="${axis}"]`
                );
                if (!input) return;

                const currentValue = parseFloat(input.value || '0');
                const nextValue = Math.max(-100, Math.min(100, (Number.isFinite(currentValue) ? currentValue : 0) + deltaMm));
                input.value = nextValue.toFixed(2).replace(/\.00$/, '');
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            function updatePreviewElementSize(sizeKey, axis, deltaMm, element) {
                const input = cardSettingsForm?.querySelector(
                    `[data-element-size-key="${sizeKey}"][data-element-size-axis="${axis}"]`
                );
                if (!input || !element) return;

                const currentValue = parseFloat(input.value || '');
                const rect = element.getBoundingClientRect();
                const measuredValue = axis === 'width' ? rect.width : rect.height;
                const nextValue = Math.max(axis === 'width' ? 1 : 0.5, Math.min(axis === 'width' ? 100 : 30,
                    (Number.isFinite(currentValue) ? currentValue : previewPixelsToMillimetres(measuredValue,
                        resizingPreviewElement?.cardRect || rect)) + deltaMm));
                input.value = nextValue.toFixed(2).replace(/\.00$/, '');
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            admitSeatLivePreview?.addEventListener('pointerdown', function(event) {
                const handle = event.target.closest('[data-preview-resize-handle]');
                if (!handle || event.button !== 0) return;

                event.preventDefault();
                event.stopPropagation();
                resizingPreviewElement = {
                    element: handle.closest('[data-preview-resize-key]'),
                    sizeKey: handle.closest('[data-preview-resize-key]')?.dataset.previewResizeKey,
                    handle,
                    lastX: event.clientX,
                    lastY: event.clientY,
                    cardRect: admitSeatLivePreview.querySelector('.admit-card')?.getBoundingClientRect() ||
                        admitSeatLivePreview.getBoundingClientRect(),
                };
                handle.setPointerCapture?.(event.pointerId);
                handle.classList.add('admit-card__element-resize-handle--active');
            });

            admitSeatLivePreview?.addEventListener('pointermove', function(event) {
                if (!resizingPreviewElement) return;

                event.preventDefault();
                const state = resizingPreviewElement;
                const horizontalMm = previewPixelsToMillimetres(event.clientX - state.lastX, state.cardRect);
                const verticalMm = previewPixelsToMillimetres(event.clientY - state.lastY, state.cardRect);
                if (state.sizeKey !== 'header' && Math.abs(horizontalMm) > 0.001) {
                    updatePreviewElementSize(state.sizeKey, 'width', horizontalMm, state.element);
                }
                if (Math.abs(verticalMm) > 0.001) updatePreviewElementSize(state.sizeKey, 'height', verticalMm, state.element);
                state.lastX = event.clientX;
                state.lastY = event.clientY;
                setDirtyState(true);
            });

            function finishPreviewElementResize(event) {
                if (!resizingPreviewElement) return;

                resizingPreviewElement.handle.classList.remove('admit-card__element-resize-handle--active');
                resizingPreviewElement.handle.releasePointerCapture?.(event.pointerId);
                resizingPreviewElement = null;
            }

            admitSeatLivePreview?.addEventListener('pointerup', finishPreviewElementResize);
            admitSeatLivePreview?.addEventListener('pointercancel', finishPreviewElementResize);

            function refreshPreviewSafetyNotice() {
                if (!admitSeatLivePreview) return;

                const notice = admitSeatLivePreview.querySelector('.card-preview-safety-note');
                const card = admitSeatLivePreview.querySelector('.admit-card');
                if (!notice || !card) return;

                const cardRect = card.getBoundingClientRect();
                const issues = [];
                const trackedElements = [
                    ['student details', '.admit-card__rows'],
                    ['student photo', '.admit-card__photo-wrap'],
                    ['principal signature', '.admit-card__signature'],
                    ['footer', '.admit-card__footer'],
                ];
                trackedElements.forEach(([label, selector]) => {
                    const element = card.querySelector(selector);
                    if (!element || element.classList.contains('d-none')) return;

                    const rect = element.getBoundingClientRect();
                    if (rect.left < cardRect.left || rect.right > cardRect.right || rect.top < cardRect.top || rect.bottom > cardRect.bottom) {
                        issues.push(`${label} extends outside the card safe area`);
                    }
                });

                const signature = card.querySelector('.admit-card__signature');
                const footer = card.querySelector('.admit-card__footer');
                if (signature && footer && !signature.classList.contains('d-none') && !footer.classList.contains('d-none')) {
                    const signatureRect = signature.getBoundingClientRect();
                    const footerRect = footer.getBoundingClientRect();
                    const overlaps = signatureRect.left < footerRect.right && signatureRect.right > footerRect.left &&
                        signatureRect.top < footerRect.bottom && signatureRect.bottom > footerRect.top;
                    if (overlaps) issues.push('principal signature overlaps the footer');
                }

                notice.textContent = issues.length
                    ? `Print warning: ${issues.join('; ')}. Move the element, reduce its spacing, or increase card height.`
                    : '';
                notice.classList.toggle('d-none', issues.length === 0);
            }

            function previewPixelsToMillimetres(delta, cardRect) {
                const cardWidthMm = parseFloat(getComputedStyle(admitSeatLivePreview).getPropertyValue(
                    '--admit-card-preview-width')) || 94;
                return delta * (cardWidthMm / Math.max(cardRect.width, 1));
            }

            let resizingPreviewCard = null;

            function resizePreviewDimension(input, deltaPx, unit) {
                if (!input) return;

                const currentValue = parseFloat(input.value || '0');
                const deltaValue = unit === 'px' ? deltaPx : (deltaPx * 2.54) / 96;
                const nextValue = Math.max(0.1, (Number.isFinite(currentValue) ? currentValue : 0) + deltaValue);
                input.value = nextValue.toFixed(2).replace(/\.00$/, '');
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            admitSeatLivePreview?.addEventListener('pointerdown', function(event) {
                const handle = event.target.closest('.admit-card__resize-handle');
                if (!handle || event.button !== 0) return;

                event.preventDefault();
                resizingPreviewCard = {
                    handle,
                    lastX: event.clientX,
                    lastY: event.clientY,
                    unit: admitSeatCardDimensionUnit?.value || 'cm',
                };
                handle.setPointerCapture?.(event.pointerId);
                handle.classList.add('admit-card__resize-handle--active');
            });

            admitSeatLivePreview?.addEventListener('pointermove', function(event) {
                if (!resizingPreviewCard) return;

                event.preventDefault();
                resizePreviewDimension(admitSeatCardWidth, event.clientX - resizingPreviewCard.lastX,
                    resizingPreviewCard.unit);
                resizePreviewDimension(admitSeatCardHeight, event.clientY - resizingPreviewCard.lastY,
                    resizingPreviewCard.unit);
                resizingPreviewCard.lastX = event.clientX;
                resizingPreviewCard.lastY = event.clientY;
                setDirtyState(true);
            });

            function finishPreviewResize(event) {
                if (!resizingPreviewCard) return;

                resizingPreviewCard.handle.classList.remove('admit-card__resize-handle--active');
                resizingPreviewCard.handle.releasePointerCapture?.(event.pointerId);
                resizingPreviewCard = null;
            }

            admitSeatLivePreview?.addEventListener('pointerup', finishPreviewResize);
            admitSeatLivePreview?.addEventListener('pointercancel', finishPreviewResize);
            admitSeatLivePreview?.addEventListener('keydown', function(event) {
                const handle = event.target.closest('.admit-card__resize-handle');
                if (!handle || !['ArrowRight', 'ArrowLeft', 'ArrowDown', 'ArrowUp'].includes(event.key)) return;

                event.preventDefault();
                const unit = admitSeatCardDimensionUnit?.value || 'cm';
                const step = unit === 'px' ? (event.shiftKey ? 10 : 2) : (event.shiftKey ? 0.5 : 0.1);
                const horizontalDelta = event.key === 'ArrowRight' ? step : (event.key === 'ArrowLeft' ? -step : 0);
                const verticalDelta = event.key === 'ArrowDown' ? step : (event.key === 'ArrowUp' ? -step : 0);
                if (horizontalDelta) resizePreviewDimension(admitSeatCardWidth, unit === 'px' ? horizontalDelta : horizontalDelta * 96 / 2.54, unit);
                if (verticalDelta) resizePreviewDimension(admitSeatCardHeight, unit === 'px' ? verticalDelta : verticalDelta * 96 / 2.54, unit);
                setDirtyState(true);
            });

            admitSeatLivePreview?.addEventListener('pointerdown', function(event) {
                if (event.button !== 0 || event.target.closest('input, button, a, select, textarea')) return;
                if (event.target.closest('[data-preview-resize-handle]')) return;
                if (event.target.closest('.admit-card__student-field--sortable')) return;

                const element = event.target.closest('[data-preview-spacing-key]');
                if (!element || !admitSeatLivePreview.contains(element)) return;

                event.preventDefault();
                draggedPreviewElement = element;
                draggedPreviewState = {
                    spacingKey: element.dataset.previewSpacingKey,
                    startX: event.clientX,
                    startY: event.clientY,
                    lastX: event.clientX,
                    lastY: event.clientY,
                    moved: false,
                    spacingType: event.altKey ? 'margin' : (event.shiftKey ? 'padding' : 'position'),
                    cardRect: admitSeatLivePreview.querySelector('.admit-card')?.getBoundingClientRect() ||
                        admitSeatLivePreview.getBoundingClientRect(),
                };
                element.classList.add('admit-card__preview-dragging');
                element.setPointerCapture?.(event.pointerId);
            });

            admitSeatLivePreview?.addEventListener('pointermove', function(event) {
                if (!draggedPreviewElement || !draggedPreviewState) return;

                const deltaX = event.clientX - draggedPreviewState.lastX;
                const deltaY = event.clientY - draggedPreviewState.lastY;
                if (Math.abs(event.clientX - draggedPreviewState.startX) > 3 ||
                    Math.abs(event.clientY - draggedPreviewState.startY) > 3) {
                    draggedPreviewState.moved = true;
                }
                if (!draggedPreviewState.moved) return;

                event.preventDefault();
                const horizontalMm = previewPixelsToMillimetres(deltaX, draggedPreviewState.cardRect);
                const verticalMm = previewPixelsToMillimetres(deltaY, draggedPreviewState.cardRect);
                if (draggedPreviewState.spacingType === 'position') {
                    if (Math.abs(horizontalMm) > 0.001) {
                        updatePreviewPositionFromDrag(draggedPreviewState.spacingKey, 'x', horizontalMm);
                    }
                    if (Math.abs(verticalMm) > 0.001) {
                        updatePreviewPositionFromDrag(draggedPreviewState.spacingKey, 'y', verticalMm);
                    }
                } else {
                    if (Math.abs(horizontalMm) > 0.001) {
                        updatePreviewSpacingFromDrag(draggedPreviewState.spacingKey, draggedPreviewState.spacingType,
                            'left', horizontalMm);
                    }
                    if (Math.abs(verticalMm) > 0.001) {
                        updatePreviewSpacingFromDrag(draggedPreviewState.spacingKey, draggedPreviewState.spacingType,
                            'top', verticalMm);
                    }
                }
                draggedPreviewState.lastX = event.clientX;
                draggedPreviewState.lastY = event.clientY;
                setDirtyState(true);
            });

            function finishPreviewDrag(event) {
                if (!draggedPreviewElement || !draggedPreviewState) return;

                suppressPreviewClick = draggedPreviewState.moved;
                draggedPreviewElement.classList.remove('admit-card__preview-dragging');
                draggedPreviewElement.releasePointerCapture?.(event.pointerId);
                draggedPreviewElement = null;
                draggedPreviewState = null;
            }

            admitSeatLivePreview?.addEventListener('pointerup', finishPreviewDrag);
            admitSeatLivePreview?.addEventListener('pointercancel', finishPreviewDrag);
            admitSeatLivePreview?.addEventListener('click', function(event) {
                if (!suppressPreviewClick) return;
                event.preventDefault();
                event.stopImmediatePropagation();
                suppressPreviewClick = false;
            }, true);

            function lockPageScroll() {
                modalScrollY = window.scrollY || window.pageYOffset || 0;
                document.documentElement.style.overflow = 'hidden';
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.top = `-${modalScrollY}px`;
                document.body.style.width = '100%';
            }

            function unlockPageScroll() {
                document.documentElement.style.overflow = '';
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.width = '';
                window.scrollTo(0, modalScrollY || 0);
            }

            function normalizeTooltipText(value) {
                return (value || '').replace(/\s+/g, ' ').trim();
            }

            function getTooltipTextFromLabel($label) {
                const explicit = normalizeTooltipText($label.attr('data-tooltip-content'));
                if (explicit) {
                    return explicit;
                }

                const $hint = $label.nextAll(
                    '.admit-seat-layout-hint, .text-muted.d-block, .small.text-muted, small.text-muted').first();
                const hintText = normalizeTooltipText($hint.text());
                const existingTitle = normalizeTooltipText($label.attr('title'));
                const labelText = normalizeTooltipText($label.text());

                return hintText || existingTitle || labelText;
            }

            function initializeLayoutTooltips(context) {
                const root = context ? (context.jquery ? context : $(context)) : $('#cardSettingsModal');
                const $labels = root.find('label');
                if (!$labels.length || typeof $labels.tooltip !== 'function') return;

                $labels.each(function() {
                    const $label = $(this);
                    const tooltipText = getTooltipTextFromLabel($label);

                    if (!tooltipText) {
                        return;
                    }

                    $label.attr('title', tooltipText);

                    $label.attr('data-toggle', 'tooltip');
                    $label.attr('data-placement', $label.attr('data-placement') || 'top');
                });

                $labels.tooltip('dispose');
                $labels.tooltip({
                    container: 'body',
                    trigger: 'hover focus',
                });
            }

            function syncLogoFileInput(file) {
                if (!admitSeatCardLogoInput) {
                    return;
                }

                if (!file) {
                    admitSeatCardLogoInput.value = '';
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                admitSeatCardLogoInput.files = dataTransfer.files;
            }

            function updateLogoPreview(url) {
                previewLogoUrl = url || null;
                const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';

                if (admitSeatLivePreviewLogoFront) {
                    admitSeatLivePreviewLogoFront.src = logoUrl;
                    admitSeatLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
                }

                if (admitSeatCardLogoPreview) {
                    admitSeatCardLogoPreview.src = logoUrl;
                    admitSeatCardLogoPreview.classList.toggle('d-none', !logoUrl);
                }
            }

            function resetLogoDropzonePreview() {
                if (!admitSeatCardLogoDropzoneInstance) {
                    updateLogoPreview(null);
                    syncLogoFileInput(null);
                    return;
                }

                admitSeatCardLogoDropzoneInstance.removeAllFiles(true);
                const currentUrl = activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
                if (currentUrl) {
                    addExistingLogoPreview(currentUrl, basenameFromPath(activeCardSettings.card_logo_url ?
                        activeCardSettings.card_logo_url : (fallbackSchoolLogo || 'card-logo.png')));
                } else {
                    updateLogoPreview(null);
                }
                syncLogoFileInput(null);
            }

            function basenameFromPath(path, fallbackName = 'card-logo.png') {
                if (!path) {
                    return fallbackName;
                }

                return String(path).split('/').pop() || fallbackName;
            }

            function addExistingLogoPreview(url, name) {
                if (!admitSeatCardLogoDropzoneInstance || !url) {
                    updateLogoPreview(url || null);
                    return;
                }

                const mockFile = {
                    name: name || 'card-logo.png',
                    size: 0,
                    accepted: true,
                    isExisting: true,
                    previewUrl: url,
                };

                admitSeatCardLogoDropzoneInstance.emit('addedfile', mockFile);
                admitSeatCardLogoDropzoneInstance.emit('thumbnail', mockFile, url);
                admitSeatCardLogoDropzoneInstance.emit('complete', mockFile);
                admitSeatCardLogoDropzoneInstance.files.push(mockFile);
                updateLogoPreview(url);
            }

            function initializeLogoDropzone() {
                if (!admitSeatCardLogoDropzone || typeof Dropzone === 'undefined' ||
                    admitSeatCardLogoDropzoneInstance) {
                    return;
                }

                const existingImageUrl = admitSeatCardLogoDropzone.dataset.existingImageUrl || '';
                const existingImageName = admitSeatCardLogoDropzone.dataset.existingImageName || 'card-logo.png';

                admitSeatCardLogoDropzoneInstance = new Dropzone(admitSeatCardLogoDropzone, {
                    url: window.location.href,
                    method: 'post',
                    autoProcessQueue: false,
                    clickable: true,
                    maxFiles: 1,
                    acceptedFiles: 'image/*',
                    previewsContainer: admitSeatCardLogoDropzone,
                    addRemoveLinks: false,
                    dictDefaultMessage: '',
                    previewTemplate: `
                <div class="dz-preview dz-file-preview mb-2 w-100">
                    <div class="d-flex align-items-center border rounded-lg bg-white p-2 shadow-sm">
                        <div class="mr-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded border bg-light" style="width:56px;height:56px;">
                            <img data-dz-thumbnail class="rounded bg-white" style="width:48px;height:48px;object-fit:contain;">
                        </div>
                        <div class="flex-grow-1 min-width-0 pr-2">
                            <div class="text-truncate font-weight-bold text-dark small mb-1" data-dz-name></div>
                            <div class="small text-muted" data-dz-size></div>
                        </div>
                        <a class="btn btn-sm btn-outline-danger flex-shrink-0" data-dz-remove href="javascript:void(0)">Remove</a>
                    </div>
                </div>`,
                    init: function() {
                        if (existingImageUrl) {
                            addExistingLogoPreview(existingImageUrl, existingImageName);
                        }

                        this.on('addedfile', function(file) {
                            if (!file || file.accepted === false) {
                                return;
                            }

                            if (file.isExisting) {
                                updateLogoPreview(file.previewUrl || existingImageUrl);
                                return;
                            }

                            const existingFiles = this.files.slice(0, -1);
                            existingFiles.forEach((existingFile) => {
                                if (existingFile !== file) {
                                    this.removeFile(existingFile);
                                }
                            });

                            syncLogoFileInput(file);

                            const reader = new FileReader();
                            reader.onload = function(event) {
                                updateLogoPreview(event.target.result);
                            };
                            reader.readAsDataURL(file);
                        });

                        this.on('removedfile', function() {
                            if (this.files.length === 0) {
                                syncLogoFileInput(null);
                                updateLogoPreview(null);
                            }
                        });
                    },
                });
            }

            function syncPrincipalSignatureFileInput(file) {
                if (!admitSeatCardPrincipalSignatureInput) {
                    return;
                }

                if (!file) {
                    admitSeatCardPrincipalSignatureInput.value = '';
                    return;
                }

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                admitSeatCardPrincipalSignatureInput.files = dataTransfer.files;
            }

            function updatePrincipalSignaturePreview(url) {
                previewPrincipalSignatureUrl = url || null;
                const signatureUrl = previewPrincipalSignatureUrl || activeCardSettings
                    .card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';

                if (admitSeatCardPrincipalSignaturePreview) {
                    admitSeatCardPrincipalSignaturePreview.src = signatureUrl;
                    admitSeatCardPrincipalSignaturePreview.classList.toggle('d-none', !signatureUrl);
                }

                if (admitSeatLivePreviewSignatureFront) {
                    admitSeatLivePreviewSignatureFront.src = signatureUrl;
                    admitSeatLivePreviewSignatureFront.classList.toggle('d-none', !signatureUrl);
                }
            }

            function resetPrincipalSignatureDropzonePreview() {
                if (!admitSeatCardPrincipalSignatureDropzoneInstance) {
                    updatePrincipalSignaturePreview(null);
                    syncPrincipalSignatureFileInput(null);
                    return;
                }

                admitSeatCardPrincipalSignatureDropzoneInstance.removeAllFiles(true);
                const currentUrl = activeCardSettings.card_principal_signature_url ||
                    initialCardPrincipalSignaturePreviewSrc || '';
                if (currentUrl) {
                    addExistingPrincipalSignaturePreview(currentUrl, basenameFromPath(activeCardSettings
                        .card_principal_signature_url || '', 'principal-signature.png'));
                } else {
                    updatePrincipalSignaturePreview(null);
                }
                syncPrincipalSignatureFileInput(null);
            }

            function addExistingPrincipalSignaturePreview(url, name) {
                if (!admitSeatCardPrincipalSignatureDropzoneInstance || !url) {
                    updatePrincipalSignaturePreview(url || null);
                    return;
                }

                const mockFile = {
                    name: name || 'principal-signature.png',
                    size: 0,
                    accepted: true,
                    isExisting: true,
                    previewUrl: url,
                };

                admitSeatCardPrincipalSignatureDropzoneInstance.emit('addedfile', mockFile);
                admitSeatCardPrincipalSignatureDropzoneInstance.emit('thumbnail', mockFile, url);
                admitSeatCardPrincipalSignatureDropzoneInstance.emit('complete', mockFile);
                admitSeatCardPrincipalSignatureDropzoneInstance.files.push(mockFile);
                updatePrincipalSignaturePreview(url);
            }

            function initializePrincipalSignatureDropzone() {
                if (!admitSeatCardPrincipalSignatureDropzone || typeof Dropzone === 'undefined' ||
                    admitSeatCardPrincipalSignatureDropzoneInstance) {
                    return;
                }

                const existingImageUrl = admitSeatCardPrincipalSignatureDropzone.dataset.existingImageUrl || '';
                const existingImageName = admitSeatCardPrincipalSignatureDropzone.dataset.existingImageName ||
                    'principal-signature.png';

                admitSeatCardPrincipalSignatureDropzoneInstance = new Dropzone(
                    admitSeatCardPrincipalSignatureDropzone, {
                        url: window.location.href,
                        method: 'post',
                        autoProcessQueue: false,
                        clickable: true,
                        maxFiles: 1,
                        acceptedFiles: 'image/png,image/jpeg',
                        previewsContainer: admitSeatCardPrincipalSignatureDropzone,
                        addRemoveLinks: false,
                        dictDefaultMessage: '',
                        previewTemplate: `
                <div class="dz-preview dz-file-preview mb-2 w-100">
                    <div class="d-flex align-items-center border rounded-lg bg-white p-2 shadow-sm">
                        <div class="mr-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded border bg-light" style="width:56px;height:56px;">
                            <img data-dz-thumbnail class="rounded bg-white" style="width:48px;height:48px;object-fit:contain;">
                        </div>
                        <div class="flex-grow-1 min-width-0 pr-2">
                            <div class="text-truncate font-weight-bold text-dark small mb-1" data-dz-name></div>
                            <div class="small text-muted" data-dz-size></div>
                        </div>
                        <a class="btn btn-sm btn-outline-danger flex-shrink-0" data-dz-remove href="javascript:void(0)">Remove</a>
                    </div>
                </div>`,
                        init: function() {
                            if (existingImageUrl) {
                                addExistingPrincipalSignaturePreview(existingImageUrl, existingImageName);
                            }

                            this.on('addedfile', function(file) {
                                if (!file || file.accepted === false) {
                                    return;
                                }

                                if (file.isExisting) {
                                    updatePrincipalSignaturePreview(file.previewUrl ||
                                        existingImageUrl);
                                    return;
                                }

                                const existingFiles = this.files.slice(0, -1);
                                existingFiles.forEach((existingFile) => {
                                    if (existingFile !== file) {
                                        this.removeFile(existingFile);
                                    }
                                });

                                syncPrincipalSignatureFileInput(file);

                                const reader = new FileReader();
                                reader.onload = function(event) {
                                    updatePrincipalSignaturePreview(event.target.result);
                                };
                                reader.readAsDataURL(file);
                            });

                            this.on('removedfile', function() {
                                if (this.files.length === 0) {
                                    syncPrincipalSignatureFileInput(null);
                                    updatePrincipalSignaturePreview(null);
                                }
                            });
                        },
                    });
            }

            function applyCardSettings(cardType) {
                if (!cardSettingsForm) return;

                const normalizedCardType = cardType || defaultCardType;
                const key = settingKeyFromCardType(normalizedCardType);
                const settings = cardSettingsMap[key] || cardSettingsMap['1'] || {};
                activeCardSettings = settings;
                previewLogoUrl = null;
                previewPrincipalSignatureUrl = null;

                const fields = [
                    'cards_per_page',
                    'cards_per_row',
                    'card_width_value',
                    'card_height_value',
                    'grid_gap_value',
                    'card_dimension_unit',
                    'page_width_mm',
                    'page_height_mm',
                    'page_margin_top_mm',
                    'page_margin_right_mm',
                    'page_margin_bottom_mm',
                    'page_margin_left_mm',
                    'card_front_alignment',
                    'card_back_alignment',
                    'card_front_padding_value',
                    'card_back_padding_value',
                    'card_photo_width_value',
                    'card_photo_height_value',
                    'card_photo_fit',
                    'card_logo_size_value',
                    'card_signature_size_value',
                    'card_school_name_font_size',
                    'card_school_detail_font_size',
                    'card_slogan_font_size',
                    'card_slogan_text_color',
                    'card_title_font_size',
                    'card_vertical_label_font_size',
                    'card_name_font_size',
                    'card_name_text_color',
                    'card_exam_type_font_size',
                    'card_exam_name_font_size',
                    'card_footer_font_size',
                    'card_student_detail_alignment',
                    'card_student_detail_font_size',
                    'card_student_detail_text_color',
                    'card_text_padding_value',
                    'card_text_margin_value',
                    'card_slogan_text_color',
                    'card_is_transparent',
                    'card_color_gradient_1',
                    'card_color_gradient_2',
                    'card_solid_color',
                    'card_school_name_text_color',
                    'card_school_detail_text_color',
                    'card_slogan_text_color',
                    'card_title_text_color',
                    'card_vertical_label_text_color',
                    'card_exam_type_text_color',
                    'card_exam_name_text_color',
                    'card_footer_text_color',
                    'card_show_school_detail_front',
                    'card_show_school_detail_back',
                    'card_show_slogan_front',
                    'card_show_slogan_back',
                    'card_show_title_front',
                    'card_show_title_back',
                    'card_show_logo_front',
                    'card_show_logo_back',
                    'card_show_photo_front',
                    'card_show_father_name_front',
                    'card_show_mother_name_front',
                    'card_show_student_name_label_front',
                    'card_show_roll_front',
                    'card_show_class_front',
                    'card_show_section_front',
                    'card_show_session_front',
                    'card_show_vertical_label_front',
                    'card_exam_name_badge_front',
                    'card_show_footer_front',
                    'card_show_footer_back',
                    'card_show_exam_type_front',
                    'card_show_exam_name_front',
                    'card_show_back_notice',
                ];
                fields.forEach((field) => {
                    const input = cardSettingsForm.elements.namedItem(field);
                    const value = field === 'card_is_transparent' ?
                        ((settings[field] ?? defaultThemeSettings[field]) ? '1' : '0') :
                        (settings[field] ?? defaultThemeSettings[field]);
                    if (input && value !== undefined && value !== null) {
                        if (input.type === 'checkbox') {
                            input.checked = !!value && value !== '0' && value !== 'false';
                        } else {
                            input.value = value;
                        }
                    }
                });

                const defaultStudentFieldOrder = ['student_name', 'student_id', 'father_name', 'mother_name', 'roll',
                    'class', 'section', 'session'
                ];
                const storedStudentFieldOrder = Array.isArray(settings.card_student_field_order) && settings.card_student_field_order.length
                    ? settings.card_student_field_order
                    : defaultStudentFieldOrder;
                const studentFieldOrderPositions = Object.fromEntries(storedStudentFieldOrder.map((field, index) => [field,
                    index + 1]));
                defaultStudentFieldOrder.forEach((field, index) => {
                    const input = cardSettingsForm.elements.namedItem(`card_student_field_order[${field}]`);
                    if (input) input.value = studentFieldOrderPositions[field] ?? index + 1;
                });

                const elementPositionKeys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type',
                    'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'
                ];
                const storedElementPositions = settings.card_element_positions || {};
                elementPositionKeys.forEach((positionKey) => {
                    ['x', 'y'].forEach((axis) => {
                        const input = cardSettingsForm.querySelector(
                            `[data-element-position-key="${positionKey}"][data-element-position-axis="${axis}"]`
                        );
                        if (input) input.value = storedElementPositions?.[positionKey]?.[axis] ?? 0;
                    });
                });

                const storedElementSizes = settings.card_element_sizes || {};
                ['exam_name', 'header'].forEach((sizeKey) => {
                    ['width', 'height'].forEach((axis) => {
                        const input = cardSettingsForm.querySelector(
                            `[data-element-size-key="${sizeKey}"][data-element-size-axis="${axis}"]`
                        );
                        if (input) input.value = storedElementSizes?.[sizeKey]?.[axis] ?? '';
                    });
                });

                const borderColorDefaults = {
                    school_name: '#ffffff', school_detail: '#ffffff', slogan: '#ffffff',
                    title: '#ffffff', exam_type: '#ffffff', exam_name: '#fff200', vertical_label: '#16a085'
                };
                Object.entries(borderColorDefaults).forEach(([borderKey, fallback]) => {
                    const input = cardSettingsForm.querySelector(
                        `input[name="card_border_colors[${borderKey}]"]`
                    );
                    if (input) input.value = settings.card_border_colors?.[borderKey] ?? fallback;
                    const transparentInput = cardSettingsForm.querySelector(
                        `input[name="card_border_transparent[${borderKey}]"]`
                    );
                    if (transparentInput) transparentInput.checked = !!settings.card_border_transparent?.[borderKey];
                });

                const spacingSettings = settings.card_typography_spacing || {};
                ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type', 'exam_name', 'student_detail',
                    'footer', 'logo', 'photo', 'signature', 'vertical_label']
                    .forEach((spacingKey) => {
                        ['padding', 'margin'].forEach((spacingType) => {
                            const fallback = spacingType === 'padding' ? (settings.card_text_padding_value ?? 0) :
                                (settings.card_text_margin_value ?? 0);
                            ['top', 'right', 'bottom', 'left'].forEach((side) => {
                                const input = cardSettingsForm.elements.namedItem(
                                    `card_typography_spacing[${spacingKey}][${spacingType}][${side}]`
                                );
                                const stored = spacingSettings?.[spacingKey]?.[spacingType];
                                const value = stored && typeof stored === 'object' ? (stored[side] ?? fallback) : (stored ?? fallback);
                                if (input) input.value = value;
                            });
                        });
                    });

                setSelectedCardColorType(settings.card_color_type ?? defaultThemeSettings.card_color_type);

                const cardTypeInput = cardSettingsForm.elements.namedItem('card_type');
                if (cardTypeInput) {
                    cardTypeInput.value = normalizedCardType;
                }

                if (cardSettingsModalTitle) {
                    cardSettingsModalTitle.textContent = settingLabelFromCardType(normalizedCardType);
                }

                if (admitSeatPreviewLabel) {
                    admitSeatPreviewLabel.textContent = previewLabelFromCardType(normalizedCardType);
                }

                if (admitSeatLivePreviewTitleFront) {
                    admitSeatLivePreviewTitleFront.textContent = normalizedCardType === 'seat_card' ? 'SEAT CARD' :
                        'ADMIT CARD';
                }

                syncCardTypeSwitcher(normalizedCardType);
                resetLogoDropzonePreview();
                resetPrincipalSignatureDropzonePreview();
                setDirtyState(false);

                refreshCardThemeControls();
            }

            function refreshSpacingVisibility() {
                const show = {
                    school_name: true,
                    school_detail: admitSeatShowSchoolDetailFront?.checked ?? true,
                    slogan: admitSeatShowSloganFront?.checked ?? true,
                    title: (admitSeatShowTitleFront?.checked ?? true) || (admitSeatShowVerticalLabelFront?.checked ?? false),
                    name: true,
                    exam_type: admitSeatShowExamTypeFront?.checked ?? true,
                    exam_name: (admitSeatShowExamNameFront?.checked ?? true) && (admitSeatExamNameBadgeFront?.checked ?? false),
                    student_detail: true,
                    footer: admitSeatShowFooterFront?.checked ?? true,
                    logo: admitSeatShowLogoFront?.checked ?? true,
                    photo: admitSeatShowPhotoFront?.checked ?? true,
                    signature: true,
                    vertical_label: admitSeatShowVerticalLabelFront?.checked ?? false,
                };

                cardSettingsForm?.querySelectorAll('[data-spacing-visibility-key]').forEach((row) => {
                    const key = row.dataset.spacingVisibilityKey;
                    row.classList.toggle('d-none', show[key] === false);
                });
            }

            function addTypographyColorLabels() {
                cardSettingsForm?.querySelectorAll('.admit-seat-typography-body .csm-color-row input[type="color"]')
                    .forEach((input) => {
                        if (input.previousElementSibling?.classList.contains('color-control-label')) return;

                        const label = document.createElement('span');
                        label.className = 'color-control-label';
                        label.textContent = input.name.includes('card_border_colors') ? 'Border color' : 'Text color';
                        input.before(label);
                    });

                cardSettingsForm?.querySelectorAll('.admit-seat-typography-body .csm-color-row')
                    .forEach((colorRow) => {
                        if (colorRow.querySelector('.csm-color-group')) return;

                        const labels = [...colorRow.querySelectorAll(':scope > .color-control-label')];
                        labels.forEach((label) => {
                            const group = document.createElement('span');
                            group.className = 'csm-color-group';
                            colorRow.insertBefore(group, label);

                            let node = label;
                            while (node) {
                                const next = node.nextElementSibling;
                                group.appendChild(node);
                                if (!next || next.classList.contains('color-control-label')) break;
                                node = next;
                            }
                        });
                    });
            }

            function refreshTypographyVisibility() {
                const visibilityRules = [
                    ['card_school_detail_font_size', () => admitSeatShowSchoolDetailFront?.checked ?? true],
                    ['card_slogan_font_size', () => admitSeatShowSloganFront?.checked ?? true],
                    ['card_title_font_size', () => admitSeatShowTitleFront?.checked ?? true],
                    ['card_vertical_label_font_size', () => admitSeatShowVerticalLabelFront?.checked ?? false],
                    ['card_exam_type_font_size', () => admitSeatShowExamTypeFront?.checked ?? true],
                    ['card_exam_name_font_size', () => (admitSeatShowExamNameFront?.checked ?? true) && (admitSeatExamNameBadgeFront?.checked ?? false)],
                    ['card_footer_font_size', () => admitSeatShowFooterFront?.checked ?? true],
                ];

                visibilityRules.forEach(([fieldName, isVisible]) => {
                    const field = cardSettingsForm?.elements.namedItem(fieldName);
                    const row = field?.closest('.admit-seat-typography-row');
                    if (row) row.classList.toggle('d-none', !isVisible());
                });
            }

            function refreshCardThemeControls() {
                if (!cardSettingsForm) return;

                addTypographyColorLabels();
                refreshSpacingVisibility();
                refreshTypographyVisibility();

                const isTransparent = admitSeatCardIsTransparent?.checked === true;
                const colorType = getSelectedCardColorType();
                const gradient1 = admitSeatCardColorGradient1?.value || '#1e3a5f';
                const gradient2 = admitSeatCardColorGradient2?.value || '#2563eb';
                const solid = admitSeatCardSolidColor?.value || gradient1;
                const theme = isTransparent ?
                    'transparent' :
                    (colorType === 'solid' ?
                        solid :
                        `linear-gradient(135deg, ${gradient1}, ${gradient2})`);

                if (isTransparent) {
                    if (admitSeatSchoolNameColor && admitSeatSchoolNameColor.value === '#ffffff')
                        admitSeatSchoolNameColor.value = '#111827';
                    if (admitSeatSchoolDetailColor && admitSeatSchoolDetailColor.value === '#e5e7eb')
                        admitSeatSchoolDetailColor.value = '#334155';
                    if (admitSeatTitleColor && admitSeatTitleColor.value === '#ffffff') admitSeatTitleColor.value =
                        '#111827';
                    if (admitSeatExamTypeColor && admitSeatExamTypeColor.value === '#ffffff') admitSeatExamTypeColor
                        .value = '#111827';
                    if (admitSeatExamNameColor && admitSeatExamNameColor.value === '#e5e7eb') admitSeatExamNameColor
                        .value = '#334155';
                }

                $('.admit-seat-card-color-controls').toggleClass('d-none', isTransparent);

                if (!isTransparent && colorType === 'solid') {
                    $('.admit-seat-card-gradient-field').addClass('d-none');
                    $('.admit-seat-card-solid-field').removeClass('d-none');
                } else if (!isTransparent) {
                    $('.admit-seat-card-gradient-field').removeClass('d-none');
                    $('.admit-seat-card-solid-field').addClass('d-none');
                } else {
                    $('.admit-seat-card-gradient-field').addClass('d-none');
                    $('.admit-seat-card-solid-field').addClass('d-none');
                }

                if (admitSeatCardThemePreview) {
                    admitSeatCardThemePreview.style.background = theme;
                    admitSeatCardThemePreview.style.borderStyle = isTransparent ? 'dashed' : 'solid';
                }

                if (admitSeatSchoolNameColorPreview) {
                    admitSeatSchoolNameColorPreview.style.background = admitSeatSchoolNameColor?.value || '#ffffff';
                }

                if (admitSeatSchoolDetailColorPreview) {
                    admitSeatSchoolDetailColorPreview.style.background = admitSeatSchoolDetailColor?.value ||
                        '#e5e7eb';
                }

                if (admitSeatSloganFontSize) {
                    admitSeatLivePreview.style.setProperty('--admit-card-slogan-font-size',
                        `${parseFloat(admitSeatSloganFontSize.value || '4.8') || 4.8}pt`);
                }

                if (admitSeatSloganColorPreview) {
                    admitSeatSloganColorPreview.style.background = admitSeatSloganColor?.value || '#e5e7eb';
                }

                if (admitSeatTitleColorPreview) {
                    admitSeatTitleColorPreview.style.background = admitSeatTitleColor?.value || '#ffffff';
                }

                if (admitSeatVerticalLabelColorPreview) {
                    admitSeatVerticalLabelColorPreview.style.background = admitSeatVerticalLabelColor?.value || '#16a085';
                }

                if (admitSeatNameColorPreview) {
                    admitSeatNameColorPreview.style.background = admitSeatNameColor?.value || '#111827';
                }

                if (admitSeatExamTypeColorPreview) {
                    admitSeatExamTypeColorPreview.style.background = admitSeatExamTypeColor?.value || '#ffffff';
                }

                if (admitSeatExamNameColorPreview) {
                    admitSeatExamNameColorPreview.style.background = admitSeatExamNameColor?.value || '#e5e7eb';
                }

                if (admitSeatFooterColorPreview) {
                    admitSeatFooterColorPreview.style.background = admitSeatFooterColor?.value || '#e5e7eb';
                }

                if (admitSeatStudentDetailColorPreview) {
                    admitSeatStudentDetailColorPreview.style.background = admitSeatStudentDetailColor?.value ||
                        '#111827';
                }

                if (admitSeatCardColorGradient1Preview) {
                    admitSeatCardColorGradient1Preview.style.background = gradient1;
                }

                if (admitSeatCardColorGradient2Preview) {
                    admitSeatCardColorGradient2Preview.style.background = gradient2;
                }

                if (admitSeatCardSolidColorPreview) {
                    admitSeatCardSolidColorPreview.style.background = solid;
                }

                if (admitSeatLivePreview) {
                    const spacingKeys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type',
                        'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'
                    ];
                    const spacingTypes = ['padding', 'margin'];
                    const spacingSides = ['top', 'right', 'bottom', 'left'];
                    spacingKeys.forEach((spacingKey) => {
                        spacingTypes.forEach((spacingType) => {
                            spacingSides.forEach((side) => {
                                const spacingInput = cardSettingsForm?.elements.namedItem(
                                    `card_typography_spacing[${spacingKey}][${spacingType}][${side}]`
                                );
                                const spacingValue = parseFloat(spacingInput?.value || '0');
                                const normalizedSpacing = Number.isFinite(spacingValue) ? spacingValue : 0;
                                admitSeatLivePreview.style.setProperty(
                                    `--admit-card-${spacingKey}-${spacingType}-${side}`,
                                    `${normalizedSpacing}mm`
                                );
                            });
                        });
                    });

                    const positionKeys = ['school_name', 'school_detail', 'slogan', 'title', 'name', 'exam_type',
                        'exam_name', 'student_detail', 'footer', 'logo', 'photo', 'signature', 'vertical_label'
                    ];
                    positionKeys.forEach((positionKey) => {
                        ['x', 'y'].forEach((axis) => {
                            const positionInput = cardSettingsForm?.querySelector(
                                `[data-element-position-key="${positionKey}"][data-element-position-axis="${axis}"]`
                            );
                            const positionValue = parseFloat(positionInput?.value || '0');
                            const cssProperty = `--admit-card-position-${positionKey}-${axis}`;
                            const cssValue = `${Number.isFinite(positionValue) ? positionValue : 0}mm`;
                            admitSeatLivePreview.style.setProperty(cssProperty, cssValue);
                            admitSeatLivePreview.querySelectorAll('.admit-card').forEach((card) => {
                                card.style.setProperty(cssProperty, cssValue);
                            });
                        });
                    });

                    ['exam_name', 'header'].forEach((sizeKey) => {
                        ['width', 'height'].forEach((axis) => {
                            const sizeInput = cardSettingsForm?.querySelector(
                                `[data-element-size-key="${sizeKey}"][data-element-size-axis="${axis}"]`
                            );
                            const sizeValue = parseFloat(sizeInput?.value || '');
                            const cssProperty = sizeKey === 'header'
                                ? `--admit-card-header-${axis}`
                                : `--admit-card-element-${sizeKey}-${axis}`;
                            const cssValue = Number.isFinite(sizeValue) ? `${sizeValue}mm` : 'auto';
                            admitSeatLivePreview.style.setProperty(cssProperty, cssValue);
                            admitSeatLivePreview.querySelectorAll('.admit-card').forEach((card) => {
                                card.style.setProperty(cssProperty, cssValue);
                            });
                        });
                    });

                    const borderColorDefaults = {
                        school_name: '#ffffff', school_detail: '#ffffff', slogan: '#ffffff',
                        title: '#ffffff', exam_type: '#ffffff', exam_name: '#fff200', vertical_label: '#16a085'
                    };
                    Object.entries(borderColorDefaults).forEach(([borderKey, fallback]) => {
                        const borderInput = cardSettingsForm?.querySelector(
                            `input[name="card_border_colors[${borderKey}]"]`
                        );
                        const transparentInput = cardSettingsForm?.querySelector(
                            `input[name="card_border_transparent[${borderKey}]"]`
                        );
                        const borderValue = borderInput?.value || fallback;
                        const cssProperty = `--admit-card-${borderKey.replace('_', '-')}-border-color`;
                        const cssBorderValue = transparentInput?.checked ? 'transparent' : borderValue;
                        admitSeatLivePreview.style.setProperty(cssProperty, cssBorderValue);
                        admitSeatLivePreview.querySelectorAll('.admit-card').forEach((card) => {
                            card.style.setProperty(cssProperty, cssBorderValue);
                        });
                    });

                    admitSeatLivePreview.style.setProperty('--preview-bg', theme);
                    admitSeatLivePreview.style.setProperty('--preview-school-name-color', admitSeatSchoolNameColor
                        ?.value || '#ffffff');
                    admitSeatLivePreview.style.setProperty('--preview-school-detail-color',
                        admitSeatSchoolDetailColor?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--preview-slogan-color', admitSeatSloganColor?.value ||
                        admitSeatSchoolDetailColor?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--preview-title-color', admitSeatTitleColor?.value ||
                        '#ffffff');
                    admitSeatLivePreview.style.setProperty('--preview-exam-type-color', admitSeatExamTypeColor
                        ?.value || '#ffffff');
                    admitSeatLivePreview.style.setProperty('--preview-exam-name-color', admitSeatExamNameColor
                        ?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--preview-back-notice-color', admitSeatExamNameColor
                        ?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--preview-footer-color', admitSeatFooterColor
                        ?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--preview-student-detail-align',
                        admitSeatStudentDetailAlignment?.value || 'left');
                    admitSeatLivePreview.style.setProperty('--preview-student-detail-font-size',
                        `${admitSeatStudentDetailFontSize?.value || 8.5}px`);
                    admitSeatLivePreview.style.setProperty('--preview-student-detail-color',
                        admitSeatStudentDetailColor?.value || '#111827');
                    admitSeatLivePreview.style.setProperty('--admit-card-theme-bg', theme);
                    admitSeatLivePreview.style.setProperty('--admit-card-theme-accent', isTransparent ?
                        'transparent' : (colorType === 'solid' ? solid : gradient1));
                    admitSeatLivePreview.style.setProperty('--admit-card-school-name-color',
                        admitSeatSchoolNameColor?.value || '#ffffff');
                    admitSeatLivePreview.style.setProperty('--admit-card-school-detail-color',
                        admitSeatSchoolDetailColor?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--admit-card-slogan-color', admitSeatSloganColor
                        ?.value || admitSeatSchoolDetailColor?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--admit-card-title-color', admitSeatTitleColor?.value ||
                        '#ffffff');
                    admitSeatLivePreview.style.setProperty('--admit-card-vertical-label-color',
                        admitSeatVerticalLabelColor?.value || '#16a085');
                    admitSeatLivePreview.style.setProperty('--admit-card-name-color', admitSeatNameColor?.value ||
                        '#111827');
                    admitSeatLivePreview.style.setProperty('--admit-card-exam-type-color', admitSeatExamTypeColor
                        ?.value || '#ffffff');
                    admitSeatLivePreview.style.setProperty('--admit-card-exam-name-color', admitSeatExamNameColor
                        ?.value || '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--admit-card-footer-color', admitSeatFooterColor?.value ||
                        '#e5e7eb');
                    admitSeatLivePreview.style.setProperty('--admit-card-student-detail-align',
                        admitSeatStudentDetailAlignment?.value || 'left');
                    admitSeatLivePreview.style.setProperty('--admit-card-student-detail-font-size',
                        `${admitSeatStudentDetailFontSize?.value || 8.5}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-student-detail-color',
                        admitSeatStudentDetailColor?.value || '#111827');
                    admitSeatLivePreview.style.setProperty('--admit-card-front-align', admitSeatFrontAlignment
                        ?.value || 'center');
                    const examAlignment = {
                        left: 'flex-start',
                        right: 'flex-end',
                        center: 'center'
                    }[admitSeatFrontAlignment?.value || 'center'] || 'center';
                    admitSeatLivePreview.style.setProperty('--admit-card-exam-align', examAlignment);

                    const unit = admitSeatCardDimensionUnit?.value || 'cm';
                    const cardWidthValue = parseFloat(admitSeatCardWidth?.value || '9.4') || 9.4;
                    const cardHeightValue = parseFloat(admitSeatCardHeight?.value || '6.6') || 6.6;
                    const widthValue = parseFloat(admitSeatPhotoWidth?.value || '1.8') || 1.8;
                    const heightValue = parseFloat(admitSeatPhotoHeight?.value || '2.7') || 2.7;
                    const photoFitValue = admitSeatPhotoFit?.value || 'cover';
                    const logoSizeValue = parseFloat(admitSeatLogoSize?.value || '0.8') || 0.8;
                    const signatureSizeValue = parseFloat(admitSeatSignatureSize?.value || '1.4') || 1.4;
                    const frontPaddingRaw = parseFloat(cardSettingsForm?.elements.namedItem(
                        'card_front_padding_value')?.value || '0.8');
                    const frontPaddingMm = Number.isFinite(frontPaddingRaw) ? frontPaddingRaw : 0.8;
                    const cardWidthMm = unit === 'px' ? (cardWidthValue / 96) * 25.4 : cardWidthValue * 10;
                    const cardHeightMm = unit === 'px' ? (cardHeightValue / 96) * 25.4 : cardHeightValue * 10;
                    admitSeatLivePreview.style.setProperty('--admit-card-preview-width', `${cardWidthMm}mm`);
                    admitSeatLivePreview.style.setProperty('--admit-card-preview-height', `${cardHeightMm}mm`);
                    admitSeatLivePreview.style.setProperty('--preview-card-ratio',
                        `${(cardWidthMm / cardHeightMm).toFixed(4)}`);
                    admitSeatLivePreview.style.setProperty('--admit-card-front-padding', `${frontPaddingMm}mm`);
                    admitSeatLivePreview.style.setProperty('--admit-card-photo-width', `${widthValue}cm`);
                    admitSeatLivePreview.style.setProperty('--admit-card-photo-height', `${heightValue}cm`);
                    admitSeatLivePreview.style.setProperty('--admit-card-photo-fit', photoFitValue);
                    admitSeatLivePreview.style.setProperty('--admit-card-logo-size', `${logoSizeValue}cm`);
                    admitSeatLivePreview.style.setProperty('--admit-card-signature-size', `${signatureSizeValue}cm`);
                    admitSeatLivePreview.querySelectorAll('.admit-card__signature').forEach((signature) => {
                        signature.style.setProperty('--admit-card-signature-size', `${signatureSizeValue}cm`);
                    });
                    admitSeatLivePreview.style.setProperty('--admit-card-school-name-font-size',
                        `${parseFloat(admitSeatSchoolNameFontSize?.value || '7.2') || 7.2}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-school-detail-font-size',
                        `${parseFloat(admitSeatSchoolDetailFontSize?.value || '5.4') || 5.4}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-slogan-font-size',
                        `${parseFloat(admitSeatSloganFontSize?.value || '4.8') || 4.8}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-title-font-size',
                        `${parseFloat(admitSeatTitleFontSize?.value || '4.7') || 4.7}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-vertical-label-font-size',
                        `${parseFloat(admitSeatVerticalLabelFontSize?.value || '5.2') || 5.2}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-name-font-size',
                        `${parseFloat(admitSeatNameFontSize?.value || '7.2') || 7.2}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-exam-type-font-size',
                        `${parseFloat(admitSeatExamTypeFontSize?.value || '7.4') || 7.4}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-exam-name-font-size',
                        `${parseFloat(admitSeatExamNameFontSize?.value || '6.8') || 6.8}pt`);
                    admitSeatLivePreview.style.setProperty('--admit-card-footer-font-size',
                        `${parseFloat(admitSeatFooterFontSize?.value || '4.5') || 4.5}pt`);

                    setPreviewElementVisible('.admit-card__logo, #admitSeatLivePreviewLogoFront', !!(
                        admitSeatShowLogoFront?.checked ?? true));
                    setPreviewElementVisible('.admit-card__logo-wrap', !!(admitSeatShowLogoFront?.checked ?? true));
                    setPreviewElementVisible('.admit-card__address', !!(admitSeatShowSchoolDetailFront?.checked ??
                        true));
                    setPreviewElementVisible('.admit-card__slogan', !!(admitSeatShowSloganFront?.checked ?? true));
                    setPreviewElementVisible('.admit-card__exam-label', !!(admitSeatShowTitleFront?.checked ??
                        true));
                    setPreviewElementVisible('.admit-card__exam-type', !!(admitSeatShowExamTypeFront?.checked ??
                        true));
                    const showExamName = !!(admitSeatShowExamNameFront?.checked ?? true);
                    const showExamNameBadge = !!(admitSeatExamNameBadgeFront?.checked ?? false);
                    setPreviewElementVisible('.admit-card__exam-name', showExamName && showExamNameBadge);
                    setPreviewElementVisible('.admit-card__photo-wrap', !!(admitSeatShowPhotoFront?.checked ??
                        true));
                    setPreviewElementVisible('.admit-card__footer', !!(admitSeatShowFooterFront?.checked ?? true));
                    setPreviewElementVisible('[data-preview-visibility="father_name"]', !!(
                        admitSeatShowFatherNameFront?.checked ?? false));
                    setPreviewElementVisible('[data-preview-visibility="mother_name"]', !!(
                        admitSeatShowMotherNameFront?.checked ?? false));
                    setPreviewElementVisible('[data-preview-visibility="roll"]', !!(admitSeatShowRollFront?.checked ??
                        true));
                    setPreviewElementVisible('[data-preview-visibility="class"]', !!(admitSeatShowClassFront?.checked ??
                        true));
                    setPreviewElementVisible('[data-preview-visibility="section"]', !!(
                        admitSeatShowSectionFront?.checked ?? true));
                    setPreviewElementVisible('[data-preview-visibility="session"]', !!(
                        admitSeatShowSessionFront?.checked ?? true));
                    setPreviewElementVisible('[data-preview-visibility="student_name_label"]', !!(
                        admitSeatShowStudentNameLabelFront?.checked ?? false));
                    setPreviewElementVisible('[data-preview-visibility="student_name_value"]', !(admitSeatShowStudentNameLabelFront
                        ?.checked ?? false));
                    setPreviewElementVisible('.admit-card__vertical-label', !!(admitSeatShowVerticalLabelFront?.checked ??
                        false));
                    admitSeatLivePreview.querySelectorAll('.admit-card__body').forEach((body) => {
                        body.classList.toggle('admit-card__body--with-vertical-label', !!(
                            admitSeatShowVerticalLabelFront?.checked ?? false));
                    });
                    admitSeatLivePreview.querySelectorAll('.admit-card__exam-name').forEach((examName) => {
                        examName.classList.toggle('admit-card__exam-name--badge', showExamNameBadge);
                    });
                    refreshStudentFieldOrder();
                    refreshPreviewSafetyNotice();
                }

                if (admitSeatLivePreviewLogoFront) {
                    const logoUrl = previewLogoUrl || activeCardSettings.card_logo_url || fallbackSchoolLogo || '';
                    admitSeatLivePreviewLogoFront.src = logoUrl;
                    admitSeatLivePreviewLogoFront.classList.toggle('d-none', !logoUrl);
                }

                if (admitSeatCardPrincipalSignaturePreview) {
                    const signatureUrl = previewPrincipalSignatureUrl || activeCardSettings
                        .card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';
                    admitSeatCardPrincipalSignaturePreview.src = signatureUrl;
                    admitSeatCardPrincipalSignaturePreview.classList.toggle('d-none', !signatureUrl);
                }

                if (admitSeatLivePreviewSignatureFront) {
                    const signatureUrl = previewPrincipalSignatureUrl || activeCardSettings
                        .card_principal_signature_url || initialCardPrincipalSignaturePreviewSrc || '';
                    admitSeatLivePreviewSignatureFront.src = signatureUrl;
                    admitSeatLivePreviewSignatureFront.classList.toggle('d-none', !signatureUrl);
                }
            }

            function loadSections(classId, selectedSectionId = null) {
                if (!sectionSelect) return;

                if (!classId) {
                    sectionSelect.innerHTML = '<option value="">All Sections</option>';
                    if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
                    return;
                }

                sectionSelect.innerHTML = '<option value="">Loading...</option>';
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));

                fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to load sections');
                        return response.json();
                    })
                    .then(data => {
                        const sections = Array.isArray(data?.sections) ? data.sections : [];
                        let html = '<option value="">All Sections</option>';

                        sections.forEach(section => {
                            const selected = String(selectedSectionId) === String(section.id) ?
                                'selected' : '';
                            html +=
                                `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                        });

                        sectionSelect.innerHTML = html;
                        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
                    })
                    .catch(() => {
                        sectionSelect.innerHTML = '<option value="">All Sections</option>';
                        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
                    });
            }

            $(document).on('change', '#classSelect', function() {
                loadSections(this.value);
            });

            $(document).on('click', '#cardSettingsModal .js-card-type-switch', function() {
                const nextCardType = $(this).data('card-type');
                applyCardSettings(nextCardType);
            });

            function setPreviewSide(target, side) {
                const normalized = side === 'back' ? 'back' : 'front';
                const $preview = $(`#${target}LivePreview`);
                if (!$preview.length) return;

                $preview.find('.js-card-preview-side').removeClass('active btn-secondary').addClass(
                    'btn-outline-secondary');
                $preview.find(`.js-card-preview-side[data-preview-side="${normalized}"]`).addClass(
                    'active btn-secondary').removeClass('btn-outline-secondary');

                const $front = $(`#${target}LivePreviewFront`);
                const $back = $(`#${target}LivePreviewBack`);
                if (!$front.length || !$back.length) return;

                if (normalized === 'back') {
                    $front.addClass('d-none');
                    $back.removeClass('d-none');
                } else {
                    $front.removeClass('d-none');
                    $back.addClass('d-none');
                }
            }

            $(document).on('click', '.js-card-preview-side', function() {
                setPreviewSide($(this).data('preview-target'), $(this).data('preview-side'));
            });

            $(document).on('input change',
                '#admitSeatPhotoWidth, #admitSeatPhotoHeight, #admitSeatLogoSize, #admitSeatSignatureSize, select[name="card_dimension_unit"]',
                refreshCardThemeControls);

            $(document).on('change',
                '#admitSeatCardIsTransparent, input[name="card_color_type"], #admitSeatCardColorGradient1, #admitSeatCardColorGradient2, #admitSeatCardSolidColor, #admitSeatSchoolNameColor, #admitSeatSchoolDetailColor, #admitSeatTitleColor, #admitSeatNameColor, #admitSeatExamTypeColor, #admitSeatExamNameColor, #admitSeatStudentDetailAlignment, #admitSeatStudentDetailFontSize, #admitSeatStudentDetailColor',
                function() {
                    if (this && this.name === 'card_color_type') {
                        $(this).closest('.btn-group-toggle').find('label').removeClass('active');
                        $(this).closest('label').addClass('active');
                    }
                    refreshCardThemeControls();
                });

            if (cardSettingsForm) {
                const syncLockedSpacing = (input) => {
                    const lock = cardSettingsForm.querySelector(
                        `[data-spacing-lock-key="${input.dataset.spacingKey}"][data-spacing-lock-type="${input.dataset.spacingType}"]`
                    );
                    if (!lock || lock.getAttribute('aria-pressed') !== 'true') return;

                    cardSettingsForm.querySelectorAll(
                        `input[data-spacing-key="${input.dataset.spacingKey}"][data-spacing-type="${input.dataset.spacingType}"]`
                    ).forEach((sideInput) => {
                        sideInput.value = input.value;
                    });
                };

                cardSettingsForm.querySelectorAll('.admit-seat-spacing-lock').forEach((lock) => {
                    lock.addEventListener('click', function() {
                        const locked = this.getAttribute('aria-pressed') === 'true';
                        const nextState = !locked;
                        this.setAttribute('aria-pressed', nextState ? 'true' : 'false');
                        this.classList.toggle('text-primary', nextState);
                        this.classList.toggle('text-muted', !nextState);
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.toggle('fa-lock', nextState);
                            icon.classList.toggle('fa-unlock', !nextState);
                        }

                        if (nextState) {
                            const firstSide = cardSettingsForm.querySelector(
                                `input[data-spacing-key="${this.dataset.spacingLockKey}"][data-spacing-type="${this.dataset.spacingLockType}"]`
                            );
                            if (firstSide) syncLockedSpacing(firstSide);
                        }
                        setDirtyState(true);
                    });
                });

                cardSettingsForm.addEventListener('input', function(event) {
                    const input = event.target.closest('.admit-seat-spacing-input');
                    if (input) syncLockedSpacing(input);
                });
                cardSettingsForm.addEventListener('input', refreshCardThemeControls);
                cardSettingsForm.addEventListener('change', refreshCardThemeControls);
                cardSettingsForm.addEventListener('input', function() {
                    setDirtyState(true);
                });
                cardSettingsForm.addEventListener('change', function() {
                    setDirtyState(true);
                });
                cardSettingsForm.addEventListener('submit', function() {
                    setDirtyState(false);
                });
            }

            initializeLogoDropzone();
            initializePrincipalSignatureDropzone();

            if (classSelect && classSelect.value) {
                loadSections(classSelect.value, selectedSection);
            }

            $('#cardSettingsModal').on('show.bs.modal', function() {
                if (!hasValidationErrors) {
                    applyCardSettings(cardTypeSelect?.value || defaultCardType);
                }
                lockPageScroll();
                setDirtyState(false);
            });

            $('#cardSettingsModal').on('shown.bs.modal', function() {
                initializeLayoutTooltips(this);
                refreshCardThemeControls();
            });

            $('#cardSettingsModal').on('hidden.bs.modal', function() {
                $(this).find('label').tooltip('dispose');
                unlockPageScroll();
            });

            @if ($errors->any())
                $('#cardSettingsModal').modal('show');
            @endif

            @if ($settingsOnly ?? false)
                applyCardSettings('{{ $cardType ?? 'admit_card' }}');
                refreshCardThemeControls();
            @endif
        });
    </script>

    <script>
        (function() {
            const form = document.getElementById('filterForm');
            const sessionSelect = document.getElementById('sessionSelect');
            const examTypeSelect = document.getElementById('examTypeSelect');
            const examSelect = document.getElementById('examSelect');
            const examsUrl = examSelect?.dataset.examsUrl;

            function setExamSelectDisabled(isDisabled) {
                if (!examSelect) return;
                examSelect.disabled = isDisabled;
            }

            function refreshSelect2(selectEl) {
                if (!selectEl || !window.jQuery) return;

                const $select = window.jQuery(selectEl);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.trigger('change.select2');
                }
            }

            function renderExamOptions(exams, selectedId = '') {
                if (!examSelect) return;

                const hasSessionAndType = !!sessionSelect?.value && !!examTypeSelect?.value;
                const placeholder = hasSessionAndType ? '-- Select Exam --' : '-- Select Session and Exam Type First --';
                let html = `<option value="">${placeholder}</option>`;

                (Array.isArray(exams) ? exams : []).forEach((exam) => {
                    const selected = String(selectedId) === String(exam.id) ? 'selected' : '';
                    html += `<option value="${exam.id}" ${selected}>${exam.name} (${exam.type_label})</option>`;
                });

                examSelect.innerHTML = html;
                setExamSelectDisabled(!hasSessionAndType);
                refreshSelect2(examSelect);
            }

            function loadExamOptions(sessionId, examType, selectedId = '') {
                if (!examSelect) return;

                if (!sessionId || !examType) {
                    renderExamOptions([]);
                    examSelect.value = '';
                    refreshSelect2(examSelect);
                    return;
                }

                setExamSelectDisabled(true);
                examSelect.innerHTML = '<option value="">Loading...</option>';

                fetch(`${examsUrl}?session_id=${encodeURIComponent(sessionId)}&exam_type=${encodeURIComponent(examType)}`)
                    .then((response) => {
                        if (!response.ok) throw new Error('Failed to load exams');
                        return response.json();
                    })
                    .then((data) => {
                        renderExamOptions(data?.exams || [], selectedId);
                    })
                    .catch(() => {
                        renderExamOptions([]);
                    });
            }

            function handleSessionChange(value) {
                if (examSelect) {
                    examSelect.value = '';
                }
                loadExamOptions(value, examTypeSelect?.value || '');
                refreshSelect2(sessionSelect);
            }

            function handleExamTypeChange(value) {
                if (examSelect) {
                    examSelect.value = '';
                }
                loadExamOptions(sessionSelect?.value || '', value);
                refreshSelect2(examTypeSelect);
            }

            function bindExamFilterEvents() {
                if (!window.jQuery) return;

                const $document = window.jQuery(document);
                $document.off('.admitSeatExamFilters');
                $document.on('change.admitSeatExamFilters select2:select.admitSeatExamFilters select2:clear.admitSeatExamFilters',
                    '#sessionSelect',
                    function() {
                        handleSessionChange(this.value);
                    });
                $document.on('change.admitSeatExamFilters select2:select.admitSeatExamFilters select2:clear.admitSeatExamFilters',
                    '#examTypeSelect',
                    function() {
                        handleExamTypeChange(this.value);
                    });
                $document.on('change.admitSeatExamFilters select2:select.admitSeatExamFilters',
                    '#examSelect',
                    function() {
                        form?.submit();
                    });
            }

            bindExamFilterEvents();

            if (sessionSelect?.value && examTypeSelect?.value) {
                loadExamOptions(sessionSelect.value, examTypeSelect.value, examSelect?.value || '');
            } else {
                renderExamOptions([]);
            }
        })();
    </script>

@endsection
