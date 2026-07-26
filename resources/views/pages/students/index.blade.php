@extends('layouts.master')

@section('styles')
    <style>
        .students-directory {
            width: 100%;
        }

        .students-directory .students-page-shell {
            width: 100%;
            padding: 0.25rem 0 1.5rem;
        }

        .students-directory .students-page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .students-directory .students-title {
            margin: 0;
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.03em;
        }

        .students-directory .students-subtitle {
            margin: 0.35rem 0 0;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .students-directory .students-directory-subtitle {
            margin-top: 2px;
            font-size: 12px;
            line-height: 1.4;
            color: #6b7280;
        }

        .students-directory .students-toolbar {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            padding: 0.9rem;
            margin-bottom: 1rem;
        }

        .students-directory .students-filter-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .students-directory .students-filter-row {
            display: grid;
            grid-template-columns: minmax(220px, 2.2fr) repeat(5, minmax(120px, 1fr)) auto auto;
            gap: 0.75rem;
            align-items: center;
        }

        .students-directory .students-filter-row > * {
            min-width: 0;
        }

        .students-directory .students-search-field {
            position: relative;
        }

        .students-directory .students-search-field i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.88rem;
            pointer-events: none;
        }

        .students-directory .students-search-input,
        .students-directory .students-filter-select,
        .students-directory .students-filter-input {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .students-directory .students-search-input {
            padding-left: 2.5rem;
        }

        .students-directory .students-search-input:focus,
        .students-directory .students-filter-select:focus,
        .students-directory .students-filter-input:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .students-directory .students-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .students-directory .students-filter-actions--submit {
            justify-content: flex-start;
        }

        .students-directory .students-more-filters {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #374151;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.7rem 0.95rem;
            white-space: nowrap;
        }

        .students-directory .students-more-filters:hover {
            background: #f8fafc;
            color: #111827;
        }

        .students-directory .students-filter-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.35rem;
            height: 1.35rem;
            padding: 0 0.35rem;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .students-directory .students-advanced-filters {
            display: none;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.9rem;
        }

        .students-directory .students-advanced-filters:not(.hidden) {
            display: block;
        }

        .students-directory .students-advanced-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .students-directory .students-advanced-grid > * {
            min-width: 0;
        }

        .students-directory .students-filter-group label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .students-directory .students-filter-group--wide {
            grid-column: span 2;
        }

        .students-directory .students-pdf-panel {
            margin-top: 0.35rem;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            padding: 1rem 1rem 1.1rem;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .students-directory .students-pdf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .students-directory .students-pdf-copy {
            min-width: 0;
        }

        .students-directory .students-pdf-title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #111827;
        }

        .students-directory .students-pdf-subtitle {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.83rem;
            line-height: 1.45;
        }

        .students-directory .students-pdf-toggle {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding-top: 0.1rem;
            white-space: nowrap;
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
        }

        .students-directory .students-pdf-toggle .form-check-input {
            margin-top: 0;
        }

        .students-directory .students-pdf-checks {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.95rem 1rem;
        }

        .students-directory .students-pdf-option {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin: 0;
            min-width: 0;
        }

        .students-directory .students-pdf-option .form-check-input {
            flex: 0 0 auto;
            margin-top: 0.1rem;
            margin-left: 0;
        }

        .students-directory .students-pdf-panel .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            width: 1.05rem;
            height: 1.05rem;
            border: 2px solid #111111;
            border-radius: 999px;
            background-color: #ffffff;
            background-repeat: no-repeat;
            background-position: center;
            background-size: 0.72rem 0.72rem;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .students-directory .students-pdf-panel .form-check-input:checked {
            background-color: #111111;
            border-color: #111111;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%23ffffff'/%3E%3C/svg%3E");
        }

        .students-directory .students-pdf-panel .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(17, 17, 17, 0.12);
        }

        .students-directory .students-pdf-panel .form-check-input:hover {
            border-color: #000000;
        }

        .students-directory .students-pdf-option .form-check-label {
            margin-bottom: 0;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1f2937;
        }

        .students-directory .students-table-shell {
            background: #fff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .students-directory .students-table-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .students-directory .students-table-title {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: #111827;
        }

        .students-directory .students-table-meta {
            margin-top: 0.2rem;
            font-size: 0.88rem;
            color: #6b7280;
        }

        .students-directory .students-count-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.7rem;
            height: 1.7rem;
            padding: 0 0.5rem;
            margin-left: 0.4rem;
            border-radius: 999px;
            background: #f3f4f6;
            color: #111827;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .students-directory .students-header-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        .students-directory .students-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 44px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            padding: 0.7rem 1rem;
            box-shadow: none;
        }

        .students-directory .students-action-btn.btn-dark {
            background: #111111;
            border-color: #111111;
        }

        .students-directory .students-action-btn.btn-outline-secondary {
            border-color: #d6d3d1;
            color: #374151;
            background: #fff;
        }

        .students-directory .students-action-btn.btn-outline-secondary:hover {
            background: #f8fafc;
            color: #111827;
        }

        .students-directory .students-pdf-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.6rem;
            min-height: 2.6rem;
            padding: 0.45rem 0.65rem;
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fff;
            color: #dc2626;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease;
        }

        .students-directory .students-pdf-action:hover {
            transform: translateY(-1px);
            border-color: #fca5a5;
            background: #fff5f5;
            color: #b91c1c;
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.08);
        }

        .students-directory .students-pdf-action i {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1;
        }

        .students-directory .students-table-wrap {
            overflow-x: auto;
        }

        .students-directory .students-table {
            width: 100%;
            min-width: 1100px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .students-directory .students-table thead th {
            border: 0;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.9rem 1rem;
            background: #fff;
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .students-directory .students-table tbody td {
            border: 0;
            border-bottom: 1px solid #f3f4f6;
            padding: 1rem;
            vertical-align: top;
            font-size: 0.9rem;
            color: #111827;
        }

        .students-directory .students-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .students-directory .students-table tbody tr:hover {
            background: #fcfcfd;
        }

        .students-directory .students-group-row td {
            padding: 0.75rem 1rem;
            font-size: 0.84rem;
            font-weight: 700;
            color: #1f2937;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
        }

        .students-directory .students-group-row--section td {
            padding-left: 1.3rem;
            background: #f9fafb;
        }

        .students-directory .students-group-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.5rem;
            padding: 0.22rem 0.55rem;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .students-directory .students-group-badge--section {
            background: #e0f2fe;
            color: #0369a1;
        }

        .students-directory .students-group-meta {
            margin-left: 0.5rem;
            color: #6b7280;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .students-directory .students-serial {
            color: #6b7280;
            font-weight: 600;
            width: 56px;
        }

        .students-directory .student-row-main {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            min-width: 420px;
        }

        .students-directory .student-avatar {
            width: 72px;
            height: 96px;
            border-radius: 14px;
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .students-directory img.student-avatar {
            object-fit: contain;
            background: #f8fafc;
            padding: 2px;
        }

        .students-directory .student-name {
            font-size: 0.96rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .students-directory .student-subline,
        .students-directory .student-meta-stack,
        .students-directory .student-contact-stack,
        .students-directory .student-guardian-stack {
            display: flex;
            flex-direction: column;
            gap: 0.28rem;
        }

        .students-directory .student-subline {
            color: #6b7280;
            font-size: 0.83rem;
        }

        .students-directory .student-inline-meta,
        .students-directory .student-meta-item,
        .students-directory .student-contact-item {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #6b7280;
            font-size: 0.82rem;
        }

        .students-directory .student-inline-meta i,
        .students-directory .student-meta-item i,
        .students-directory .student-contact-item i {
            color: #9ca3af;
            width: 14px;
            text-align: center;
        }

        .students-directory .student-chip {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.24rem 0.55rem;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .students-directory .student-chip--dark {
            background: #111827;
            color: #fff;
        }

        .students-directory .student-chip--light {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .students-directory .student-chip--info {
            background: #eef2ff;
            color: #4338ca;
        }

        .students-directory .student-chip--success {
            background: #ecfdf5;
            color: #047857;
        }

        .students-directory .student-chip--warning {
            background: #fff7ed;
            color: #c2410c;
        }

        .students-directory .student-status-cell {
            width: 100px;
            text-align: center;
        }

        .students-directory .student-status-form {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .students-directory .student-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .students-directory .student-switch input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .students-directory .student-switch-track {
            position: relative;
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: #e5e7eb;
            transition: background-color 0.2s ease;
            cursor: pointer;
            display: inline-block;
        }

        .students-directory .student-switch-track::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
            transition: transform 0.2s ease;
        }

        .students-directory .student-switch input:checked + .student-switch-track {
            background: #111827;
        }

        .students-directory .student-switch input:checked + .student-switch-track::after {
            transform: translateX(20px);
        }

        .students-directory .student-actions {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            justify-content: flex-end;
            min-width: 150px;
        }

        .students-directory .student-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbe4f0;
            background: #fff;
            color: #1f2937;
            box-shadow: none;
            transition: transform 0.2s ease, border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
            flex: 0 0 auto;
        }

        .students-directory .student-icon-btn:hover {
            transform: translateY(-1px);
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #111827;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
        }

        .students-directory .student-icon-btn--view {
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .students-directory .student-icon-btn--view:hover {
            color: #1d4ed8;
            border-color: #93c5fd;
            background: #eff6ff;
        }

        .students-directory .student-icon-btn--edit {
            color: #374151;
            border-color: #dbe4f0;
        }

        .students-directory .student-icon-btn--edit:hover {
            color: #111827;
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .students-directory .student-icon-btn--danger {
            color: #ef4444;
            border-color: #fecaca;
            background: #fff;
        }

        .students-directory .student-icon-btn--danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .students-directory .student-icon-btn i {
            font-size: 0.98rem;
            line-height: 1;
        }

        .students-directory .students-empty {
            padding: 3.25rem 1rem;
            text-align: center;
            color: #6b7280;
        }

        .students-directory .students-empty i {
            font-size: 2.6rem;
            color: #cbd5e1;
            margin-bottom: 0.9rem;
        }

        .students-directory .students-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            border-top: 1px solid #f1f5f9;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .students-directory .students-pagination nav {
            margin: 0;
        }

        .students-directory .students-pagination .pagination {
            margin: 0;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .students-directory .students-pagination .page-link {
            border: 1px solid #e5e7eb;
            color: #374151;
            border-radius: 10px !important;
            min-width: 38px;
            text-align: center;
            box-shadow: none;
        }

        .students-directory .students-pagination .page-item.active .page-link {
            background: #111827;
            border-color: #111827;
            color: #fff;
        }

        @media (max-width: 1199.98px) {
            .students-directory .students-filter-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .students-directory .students-filter-actions {
                justify-content: flex-start;
            }

            .students-directory .students-advanced-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .students-directory .students-pdf-checks {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .students-directory .students-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.85rem;
            }

            .students-directory .students-card-copy {
                width: 100%;
            }

            .students-directory .students-card-actions {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: flex-start !important;
                width: 100%;
                flex-wrap: nowrap !important;
                gap: 0.5rem;
            }

            .students-directory .students-card-actions .students-pdf-action {
                flex: 0 0 44px;
                min-width: 44px;
                min-height: 44px;
                padding: 0;
            }

            .students-directory .students-card-actions .students-add-student-btn {
                flex: 1 1 auto;
                min-width: 0;
                min-height: 44px;
                white-space: nowrap;
            }

            .students-directory .students-page-header,
            .students-directory .students-table-header,
            .students-directory .students-footer,
            .students-directory .students-pdf-header {
                flex-direction: column;
                align-items: stretch;
            }

            .students-directory .students-title {
                font-size: 1.6rem;
            }

            .students-directory .students-filter-row,
            .students-directory .students-advanced-grid {
                grid-template-columns: 1fr;
            }

            .students-directory .students-pdf-checks {
                grid-template-columns: 1fr;
            }

            .students-directory .students-filter-group--wide {
                grid-column: span 1;
            }

            .students-directory .students-header-actions,
            .students-directory .students-filter-actions {
                width: 100%;
            }

            .students-directory .students-header-actions > *,
            .students-directory .students-filter-actions > * {
                width: 100%;
                justify-content: center;
            }
        }

        html[data-theme='dark'] .students-directory {
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-title,
        html[data-theme='dark'] .students-directory .students-table-title,
        html[data-theme='dark'] .students-directory .students-pdf-title,
        html[data-theme='dark'] .students-directory .student-name {
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .students-directory-subtitle {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .students-directory .students-subtitle,
        html[data-theme='dark'] .students-directory .students-table-meta,
        html[data-theme='dark'] .students-directory .students-pdf-subtitle,
        html[data-theme='dark'] .students-directory .students-footer,
        html[data-theme='dark'] .students-directory .student-subline,
        html[data-theme='dark'] .students-directory .student-inline-meta,
        html[data-theme='dark'] .students-directory .student-meta-item,
        html[data-theme='dark'] .students-directory .student-contact-item,
        html[data-theme='dark'] .students-directory .students-filter-group label {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .students-toolbar,
        html[data-theme='dark'] .students-directory .students-table-shell,
        html[data-theme='dark'] .students-directory .students-pdf-panel {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        html[data-theme='dark'] .students-directory .students-table-header,
        html[data-theme='dark'] .students-directory .students-footer {
            border-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .students-directory .students-advanced-filters {
            border-top-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .students-directory .students-search-input,
        html[data-theme='dark'] .students-directory .students-filter-select,
        html[data-theme='dark'] .students-directory .students-filter-input {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-search-input::placeholder,
        html[data-theme='dark'] .students-directory .students-filter-input::placeholder {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .students-search-field i,
        html[data-theme='dark'] .students-directory .student-inline-meta i,
        html[data-theme='dark'] .students-directory .student-meta-item i,
        html[data-theme='dark'] .students-directory .student-contact-item i {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .students-search-input:focus,
        html[data-theme='dark'] .students-directory .students-filter-select:focus,
        html[data-theme='dark'] .students-directory .students-filter-input:focus {
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        html[data-theme='dark'] .students-directory .students-more-filters {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-more-filters:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .students-filter-count,
        html[data-theme='dark'] .students-directory .students-count-pill {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .students-pdf-title {
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .students-pdf-subtitle {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .students-pdf-toggle,
        html[data-theme='dark'] .students-directory .students-pdf-option .form-check-label {
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-pdf-panel .form-check-input {
            background-color: rgba(15, 23, 42, 0.96);
            border-color: #64748b;
        }

        html[data-theme='dark'] .students-directory .students-pdf-panel .form-check-input:checked {
            background-color: #60a5fa;
            border-color: #60a5fa;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%230b1220'/%3E%3C/svg%3E");
        }

        html[data-theme='dark'] .students-directory .students-pdf-panel .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
        }

        html[data-theme='dark'] .students-directory .students-pdf-panel .form-check-input:hover {
            border-color: #93c5fd;
        }

        html[data-theme='dark'] .students-directory .students-table thead th {
            background: #1e293b;
            border-bottom-color: rgba(148, 163, 184, 0.16);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-table tbody td {
            border-bottom-color: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-table tbody tr:hover {
            background: rgba(30, 41, 59, 0.78);
        }

        html[data-theme='dark'] .students-directory .students-group-row td {
            background: rgba(15, 23, 42, 0.96);
            border-bottom-color: rgba(148, 163, 184, 0.16);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-group-row--section td {
            background: rgba(15, 23, 42, 0.9);
        }

        html[data-theme='dark'] .students-directory .students-group-badge {
            background: rgba(96, 165, 250, 0.16);
            color: #bfdbfe;
        }

        html[data-theme='dark'] .students-directory .students-group-badge--section {
            background: rgba(14, 165, 233, 0.16);
            color: #bae6fd;
        }

        html[data-theme='dark'] .students-directory .students-group-meta {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .student-chip--light {
            background: rgba(15, 23, 42, 0.94);
            border-color: rgba(148, 163, 184, 0.2);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .student-chip--dark {
            background: #e2e8f0;
            color: #0f172a;
        }

        html[data-theme='dark'] .students-directory .student-chip--info {
            background: rgba(96, 165, 250, 0.16);
            color: #bfdbfe;
        }

        html[data-theme='dark'] .students-directory .student-chip--success {
            background: rgba(34, 197, 94, 0.16);
            color: #bbf7d0;
        }

        html[data-theme='dark'] .students-directory .student-chip--warning {
            background: rgba(245, 158, 11, 0.16);
            color: #fde68a;
        }

        html[data-theme='dark'] .students-directory .student-switch-track {
            background: #334155;
        }

        html[data-theme='dark'] .students-directory .student-switch-track::after {
            background: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .student-switch input:checked + .student-switch-track {
            background: #2563eb;
        }

        html[data-theme='dark'] .students-directory .students-action-btn.btn-dark {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        html[data-theme='dark'] .students-directory .students-action-btn.btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.28);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .students-directory .students-action-btn.btn-outline-secondary:hover {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.36);
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .students-pdf-action {
            border-color: rgba(248, 113, 113, 0.34);
            background: rgba(15, 23, 42, 0.96);
            color: #fca5a5;
        }

        html[data-theme='dark'] .students-directory .students-pdf-action:hover {
            background: rgba(127, 29, 29, 0.38);
            border-color: rgba(248, 113, 113, 0.46);
            color: #fecaca;
        }

        html[data-theme='dark'] .students-directory .student-icon-btn {
            background: rgba(15, 23, 42, 0.96);
            border-color: rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
        }

        html[data-theme='dark'] .students-directory .student-icon-btn--view {
            color: #93c5fd;
            border-color: rgba(96, 165, 250, 0.35);
        }

        html[data-theme='dark'] .students-directory .student-icon-btn--edit {
            color: #e2e8f0;
            border-color: rgba(148, 163, 184, 0.2);
        }

        html[data-theme='dark'] .students-directory .student-icon-btn:hover {
            background: #1e293b;
            border-color: rgba(148, 163, 184, 0.3);
            color: #f8fafc;
        }

        html[data-theme='dark'] .students-directory .student-icon-btn--danger {
            color: #fca5a5;
            border-color: rgba(248, 113, 113, 0.3);
            background: rgba(15, 23, 42, 0.96);
        }

        html[data-theme='dark'] .students-directory .student-icon-btn--danger:hover {
            background: rgba(127, 29, 29, 0.45);
            border-color: rgba(248, 113, 113, 0.4);
            color: #fecaca;
        }

        html[data-theme='dark'] .students-directory .students-empty {
            color: #94a3b8;
        }

        html[data-theme='dark'] .students-directory .students-empty i {
            color: #64748b;
        }

        html[data-theme='dark'] .students-directory .students-pagination .page-link {
            background: rgba(15, 23, 42, 0.96);
            border-color: rgba(148, 163, 184, 0.2);
            color: #cbd5e1;
        }

        html[data-theme='dark'] .students-directory .students-pagination .page-item.active .page-link {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }
    </style>
@endsection

@section('contents')
    <div class="students-directory">
        <div class="students-page-shell">
            @include('pages.students.filter')
            @include('pages.students.table')
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.student.filter_scripts')
    @include('scripts.common.load_academic_information')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleAll = document.getElementById('pdf-columns-toggle-all');
            const checkboxes = Array.from(document.querySelectorAll('.pdf-column-checkbox'));

            if (!toggleAll || !checkboxes.length) {
                return;
            }

            const syncToggleState = () => {
                toggleAll.checked = checkboxes.every((checkbox) => checkbox.checked);
            };

            toggleAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = toggleAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', syncToggleState);
            });

            syncToggleState();
        });
    </script>
@endsection
