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
            grid-template-columns: minmax(220px, 2.2fr) repeat(4, minmax(140px, 1fr)) auto;
            gap: 0.75rem;
            align-items: center;
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem;
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
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 0.9rem 1rem;
            background: #fcfcfd;
        }

        .students-directory .students-pdf-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .students-directory .students-pdf-title {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 700;
            color: #111827;
        }

        .students-directory .students-pdf-checks {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem 1rem;
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

        .students-directory .students-serial {
            color: #6b7280;
            font-weight: 600;
            width: 56px;
        }

        .students-directory .student-row-main {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            min-width: 260px;
        }

        .students-directory .student-avatar {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            object-fit: cover;
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            flex-shrink: 0;
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
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            transition: all 0.2s ease;
        }

        .students-directory .student-icon-btn:hover {
            transform: translateY(-1px);
            color: #111827;
            border-color: #d1d5db;
            background: #f9fafb;
        }

        .students-directory .student-icon-btn--danger {
            color: #dc2626;
            border-color: #fecaca;
            background: #fff;
        }

        .students-directory .student-icon-btn--danger:hover {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #b91c1c;
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
        }

        @media (max-width: 767.98px) {
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
