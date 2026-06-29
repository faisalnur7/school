@extends('layouts.master')

@section('styles')
    <style>
        .payment-report-page {
            width: 100%;
        }

        .payment-report-page .payment-report-shell {
            width: 100%;
            padding: 0.25rem 0 1.5rem;
        }

        .payment-report-page .payment-report-card,
        .payment-report-page .payment-report-group-card {
            background: #ffffff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        }

        .payment-report-page .payment-report-card {
            padding: 0.95rem;
            margin-bottom: 1rem;
        }

        .payment-report-page .payment-report-filter-card {
            position: relative;
        }

        .payment-report-page .report-header-body {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
        }

        .payment-report-page .report-header-copy {
            min-width: 0;
        }

        .payment-report-page .payment-report-form {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .payment-report-page .payment-report-grid {
            display: grid;
            gap: 0.75rem;
        }

        .payment-report-page .payment-report-grid--primary {
            grid-template-columns: minmax(220px, 2fr) repeat(5, minmax(120px, 1fr)) auto auto;
            align-items: center;
        }

        .payment-report-page .payment-report-grid--secondary {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .payment-report-page .payment-report-filter-actions {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
            flex-wrap: wrap;
            justify-self: end;
        }

        .payment-report-page .payment-report-filter-actions--submit {
            justify-content: flex-start;
            justify-self: end;
        }

        .payment-report-page .payment-report-field label {
            display: block;
            margin-bottom: 0.35rem;
            font-size: 0.77rem;
            font-weight: 700;
            color: #6b7280;
        }

        .payment-report-page .payment-report-search-input,
        .payment-report-page .payment-report-filter-select {
            width: 100%;
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #111827;
            font-size: 0.92rem;
            box-shadow: none;
        }

        .payment-report-page .payment-report-search-input:focus,
        .payment-report-page .payment-report-filter-select:focus {
            border-color: #cbd5e1;
            box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
        }

        .payment-report-page .payment-report-more-filters {
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

        .payment-report-page .payment-report-more-filters:hover {
            background: #f8fafc;
            color: #111827;
        }

        .payment-report-page .payment-report-filter-count {
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

        .payment-report-page .payment-report-advanced-filters {
            display: none;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.9rem;
        }

        .payment-report-page .payment-report-advanced-filters:not(.hidden) {
            display: block;
        }

        .payment-report-page .payment-report-advanced-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .payment-report-page .payment-report-action-btn {
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

        .payment-report-page .payment-report-action-btn.btn-dark {
            background: #111111;
            border-color: #111111;
        }

        .payment-report-page .payment-report-action-btn.btn-outline-secondary {
            border-color: #d6d3d1;
            color: #374151;
            background: #fff;
        }

        .payment-report-page .payment-report-action-btn.btn-outline-secondary:hover {
            background: #f8fafc;
            color: #111827;
        }

        .payment-report-page .payment-report-pdf-panel {
            margin-top: 0.35rem;
            border: 1px solid #dbe4f0;
            border-radius: 12px;
            padding: 1rem 1rem 1.1rem;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
        }

        .payment-report-page .payment-report-pdf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .payment-report-page .payment-report-pdf-copy {
            min-width: 0;
        }

        .payment-report-page .payment-report-pdf-title {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #111827;
        }

        .payment-report-page .payment-report-pdf-subtitle {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.83rem;
            line-height: 1.45;
            color: #6b7280;
        }

        .payment-report-page .payment-report-pdf-toggle {
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

        .payment-report-page .payment-report-pdf-toggle .form-check-input {
            margin-top: 0;
        }

        .payment-report-page .payment-report-pdf-checks {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.95rem 1rem;
        }

        .payment-report-page .payment-report-pdf-option {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            margin: 0;
            min-width: 0;
        }

        .payment-report-page .payment-report-pdf-option .form-check-input {
            flex: 0 0 auto;
            margin-top: 0.1rem;
            margin-left: 0;
        }

        .payment-report-page .payment-report-pdf-panel .form-check-input {
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

        .payment-report-page .payment-report-pdf-panel .form-check-input:checked {
            background-color: #111111;
            border-color: #111111;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%23ffffff'/%3E%3C/svg%3E");
        }

        .payment-report-page .payment-report-pdf-panel .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(17, 17, 17, 0.12);
        }

        .payment-report-page .payment-report-pdf-panel .form-check-input:hover {
            border-color: #000000;
        }

        .payment-report-page .payment-report-pdf-option .form-check-label {
            margin-bottom: 0;
            font-size: 0.88rem;
            font-weight: 600;
            color: #1f2937;
        }

        .payment-report-page .payment-report-group-card {
            overflow: hidden;
            margin-bottom: 1rem;
            border-radius: 0;
            padding:3px;
        }

        .payment-report-page .payment-report-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .payment-report-page .payment-report-group-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .payment-report-page .payment-report-table-wrap {
            overflow-x: auto;
        }

        .payment-report-page .payment-report-table {
            width: 100%;
            min-width: 900px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .payment-report-page .payment-report-table thead th {
            border: 0;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.9rem 1rem;
            background: #fff;
            color: #374151;
            font-size: 0.82rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .payment-report-page .payment-report-table tbody td {
            border: 0;
            border-bottom: 1px solid #f3f4f6;
            padding: 1rem;
            vertical-align: top;
            font-size: 0.9rem;
            color: #111827;
        }

        .payment-report-page .payment-report-table tbody tr:hover {
            background: #fcfcfd;
        }

        .payment-report-page .payment-report-table tfoot td {
            border: 0;
            border-top: 1px solid #f1f5f9;
            padding: 0.9rem 1rem;
            background: #f8fafc;
            color: #111827;
        }

        .payment-report-page .payment-report-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: #6b7280;
        }

        @media (max-width: 1199.98px) {
            .payment-report-page .payment-report-grid--primary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .payment-report-page .payment-report-pdf-checks,
            .payment-report-page .payment-report-advanced-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .payment-report-page .payment-report-pdf-header {
                flex-direction: column;
                align-items: stretch;
            }

            .payment-report-page .payment-report-grid--primary,
            .payment-report-page .payment-report-advanced-grid,
            .payment-report-page .payment-report-pdf-checks {
                grid-template-columns: 1fr;
            }

            .payment-report-page .payment-report-filter-actions,
            .payment-report-page .payment-report-filter-actions--submit {
                width: 100%;
            }

            .payment-report-page .payment-report-filter-actions > *,
            .payment-report-page .payment-report-filter-actions--submit > * {
                width: 100%;
                justify-content: center;
            }
        }

        html[data-theme='dark'] .payment-report-page {
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-card,
        html[data-theme='dark'] .payment-report-page .payment-report-group-card,
        html[data-theme='dark'] .payment-report-page .payment-report-pdf-panel {
            background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
            border-color: rgba(148, 163, 184, 0.18);
            box-shadow: 0 10px 24px rgba(2, 6, 23, 0.26);
        }

        html[data-theme='dark'] .payment-report-page .payment-report-field label,
        html[data-theme='dark'] .payment-report-page .payment-report-pdf-subtitle,
        html[data-theme='dark'] .payment-report-page .payment-report-pdf-toggle,
        html[data-theme='dark'] .payment-report-page .payment-report-pdf-option .form-check-label {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-advanced-filters {
            border-top-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .payment-report-page .payment-report-search-input,
        html[data-theme='dark'] .payment-report-page .payment-report-filter-select {
            border-color: rgba(148, 163, 184, 0.2);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-search-input:focus,
        html[data-theme='dark'] .payment-report-page .payment-report-filter-select:focus {
            border-color: rgba(96, 165, 250, 0.35);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        html[data-theme='dark'] .payment-report-page .payment-report-more-filters {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-more-filters:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-filter-count {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-action-btn.btn-outline-secondary {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(15, 23, 42, 0.96);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-action-btn.btn-outline-secondary:hover {
            background: #1e293b;
            color: #f8fafc;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-group-header,
        html[data-theme='dark'] .payment-report-page .payment-report-table thead th {
            border-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .payment-report-page .payment-report-group-title,
        html[data-theme='dark'] .payment-report-page .payment-report-pdf-title {
            color: #f8fafc;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-table thead th {
            background: #1e293b;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-table tbody td {
            border-bottom-color: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .payment-report-page .payment-report-table tbody tr:hover {
            background: rgba(30, 41, 59, 0.78);
        }

        html[data-theme='dark'] .payment-report-page .payment-report-table tfoot td {
            background: rgba(15, 23, 42, 0.96);
            border-top-color: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid payment-report-page">
        @include('partials.report-header')

        <div class="payment-report-shell">
            <div class="payment-report-card payment-report-filter-card">
                @php
                    $selectedCategoryKeys = $selectedCategoryKeys ?? ($availableCategories->pluck('column_key')->all() ?? []);
                    $reportPdfQuery = collect([
                        'student_id' => request('student_id'),
                        'session_id' => request('session_id'),
                        'class_id' => request('class_id'),
                        'section_id' => request('section_id'),
                        'from_date' => request('from_date'),
                        'to_date' => request('to_date'),
                        'date' => request('date'),
                        'columns_present' => request()->has('columns_present') ? 1 : null,
                        'columns' => request('columns'),
                    ])->filter(function ($value) {
                        return is_array($value) ? ! empty($value) : filled($value);
                    })->all();
                @endphp

                <form method="GET" action="{{ route('fees.payment-report') }}" class="payment-report-form mb-3">
                    <div class="payment-report-grid payment-report-grid--primary">
                        <div class="payment-report-field">
                            <label for="payment-report-student-id">Student ID</label>
                            <input
                                type="text"
                                id="payment-report-student-id"
                                name="student_id"
                                value="{{ old('student_id', request('student_id')) }}"
                                class="form-control payment-report-search-input"
                                placeholder="Search specific student"
                            >
                        </div>

                        <div class="payment-report-field">
                            <label for="payment-report-session">Academic Session</label>
                            <select name="session_id" id="payment-report-session" class="form-control payment-report-filter-select">
                                <option value="">All Sessions</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="payment-report-field">
                            <label for="classSelect">Class</label>
                            <select name="class_id" id="classSelect" class="form-control payment-report-filter-select">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="payment-report-field">
                            <label for="sectionSelect">Section</label>
                            <select name="section_id" id="sectionSelect" class="form-control payment-report-filter-select">
                                <option value="">All Sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="payment-report-field">
                            <label for="payment-report-from-date">From Date <span class="text-danger">*</span></label>
                            <input
                                type="date"
                                id="payment-report-from-date"
                                name="from_date"
                                value="{{ old('from_date', $fromDate ?? request('from_date', request('date'))) }}"
                                class="form-control payment-report-filter-select"
                            >
                        </div>

                        <div class="payment-report-field">
                            <label for="payment-report-to-date">To Date <span class="text-danger">*</span></label>
                            <input
                                type="date"
                                id="payment-report-to-date"
                                name="to_date"
                                value="{{ old('to_date', $toDate ?? request('to_date', request('date'))) }}"
                                class="form-control payment-report-filter-select"
                            >
                        </div>

                        <div class="payment-report-filter-actions">
                            <button class="payment-report-more-filters filter_button" type="button" id="payment-report-toggle-filters" title="More Filters" aria-label="More Filters" aria-expanded="false">
                                <i class="fas fa-sliders-h"></i>
                                <span class="payment-report-filter-count" data-filter-count>0</span>
                            </button>
                        </div>

                        <div class="payment-report-filter-actions payment-report-filter-actions--submit">
                            <button type="submit" class="btn btn-dark payment-report-action-btn" title="Apply Filters" aria-label="Apply Filters">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('fees.payment-report') }}" class="btn btn-outline-secondary payment-report-action-btn" title="Reset" aria-label="Reset">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                            <button type="button" class="btn btn-success payment-report-action-btn" onclick="window.print()" title="Print" aria-label="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route('fees.payment-report.pdf', $reportPdfQuery) }}" class="btn btn-danger payment-report-action-btn" title="Export PDF" aria-label="Export PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
                    </div>

                    <div class="payment-report-advanced-filters hidden" id="paymentReportAdvancedFilters">
                        <input type="hidden" name="columns_present" value="1">

                        <div class="payment-report-pdf-panel">
                            <div class="payment-report-pdf-header">
                                <div class="payment-report-pdf-copy">
                                    <p class="payment-report-pdf-title">PDF Column Selection</p>
                                    <small class="payment-report-pdf-subtitle">Choose which fee columns should appear in the exported PDF list.</small>
                                </div>
                                <div class="form-check payment-report-pdf-toggle mb-0">
                                    <input class="form-check-input" type="checkbox" id="payment-report-toggle-all" {{ ! $availableCategories->isEmpty() && count($selectedCategoryKeys) === count($availableCategories) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment-report-toggle-all">Select all</label>
                                </div>
                            </div>

                            <div class="payment-report-pdf-checks">
                                @foreach($availableCategories as $category)
                                    <div class="form-check payment-report-pdf-option">
                                        <input
                                            class="form-check-input payment-report-column-checkbox"
                                            type="checkbox"
                                            name="columns[]"
                                            value="{{ $category->column_key }}"
                                            id="payment-report-column-{{ $category->column_key }}"
                                            {{ in_array($category->column_key, $selectedCategoryKeys, true) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label ml-4" for="payment-report-column-{{ $category->column_key }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const toggleAll = document.getElementById('payment-report-toggle-all');
                        const checks = Array.from(document.querySelectorAll('.payment-report-column-checkbox'));
                        const toggleButton = document.getElementById('payment-report-toggle-filters');
                        const advancedFilters = document.getElementById('paymentReportAdvancedFilters');
                        const countBadge = document.querySelector('[data-filter-count]');
                        const filterFields = Array.from(document.querySelectorAll(
                            'input[name="student_id"], select[name="session_id"], select[name="class_id"], select[name="section_id"], input[name="from_date"], input[name="to_date"]'
                        ));

                        function syncToggleState() {
                            if (!toggleAll || !checks.length) return;
                            toggleAll.checked = checks.every((checkbox) => checkbox.checked);
                        }

                        function setFilterCount() {
                            let count = 0;

                            filterFields.forEach((field) => {
                                const value = field.value;
                                if (Array.isArray(value) ? value.length : value !== null && value !== '') {
                                    count++;
                                }
                            });

                            if (countBadge) {
                                countBadge.textContent = count;
                            }

                            return count;
                        }

                        function refreshAdvancedPanelState() {
                            if (!advancedFilters || !toggleButton) return;

                            const activeCount = setFilterCount();
                            if (activeCount > 0) {
                                advancedFilters.classList.remove('hidden');
                                toggleButton.setAttribute('aria-expanded', 'true');
                            } else {
                                toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
                            }
                        }

                        if (toggleButton && advancedFilters) {
                            toggleButton.addEventListener('click', function () {
                                advancedFilters.classList.toggle('hidden');
                                toggleButton.setAttribute('aria-expanded', advancedFilters.classList.contains('hidden') ? 'false' : 'true');
                            });
                        }

                        if (toggleAll) {
                            toggleAll.addEventListener('change', function () {
                                checks.forEach((checkbox) => {
                                    checkbox.checked = toggleAll.checked;
                                });
                            });
                        }

                        checks.forEach((checkbox) => checkbox.addEventListener('change', syncToggleState));
                        filterFields.forEach((field) => {
                            field.addEventListener('change', refreshAdvancedPanelState);
                            field.addEventListener('keyup', refreshAdvancedPanelState);
                        });

                        syncToggleState();
                        refreshAdvancedPanelState();
                    });
                </script>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const classSelect = document.getElementById('classSelect');
                        const sectionSelect = document.getElementById('sectionSelect');
                        const selectedSection = @json(request('section_id'));

                        function refreshSectionSelect() {
                            if (!sectionSelect) return;
                            if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
                        }

                        function loadSections(classId, selectedSectionId = null) {
                            if (!sectionSelect) return;

                            if (!classId) {
                                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                                refreshSectionSelect();
                                return;
                            }

                            sectionSelect.innerHTML = '<option value="">Loading...</option>';
                            refreshSectionSelect();

                            fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
                                .then(response => {
                                    if (!response.ok) throw new Error('Failed to load sections');
                                    return response.json();
                                })
                                .then(data => {
                                    const sections = Array.isArray(data?.sections) ? data.sections : [];
                                    let html = '<option value="">All Sections</option>';

                                    sections.forEach(section => {
                                        const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                                        html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                                    });

                                    sectionSelect.innerHTML = html;
                                    refreshSectionSelect();
                                })
                                .catch(() => {
                                    sectionSelect.innerHTML = '<option value="">All Sections</option>';
                                    refreshSectionSelect();
                                });
                        }

                        $(document).on('change', '#classSelect', function () {
                            loadSections(this.value);
                        });

                        if (classSelect && classSelect.value) {
                            loadSections(classSelect.value, selectedSection);
                        }
                    });
                </script>
            </div>

            <div class="payment-report-card">
                <hr>

                @if(!$dateLabel)
                    <div class="payment-report-empty">
                        <i class="fas fa-filter fa-2x mb-2"></i>
                        <p class="mb-0">Select a date range to generate the student payment report.</p>
                    </div>
                @elseif($categories->isEmpty())
                    <div class="payment-report-empty">
                        <i class="fas fa-columns fa-2x mb-2"></i>
                        <p class="mb-0">Select at least one column to generate the report.</p>
                    </div>
                @elseif($rows->isEmpty())
                    <div class="payment-report-empty">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p class="mb-0">No payment records found for the selected date range.</p>
                    </div>
                @else
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info"><i class="fas fa-user-graduate"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Students</span>
                                    <span class="info-box-number">{{ $rows->sum(fn($g) => $g->students->count()) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Grand Total Paid</span>
                                    <span class="info-box-number">{{ number_format($rows->sum(fn($g) => $g->students->sum('grand_total')), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Filter</span>
                                    <span class="info-box-number">{{ $dateLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach($rows as $group)
                        <div class="payment-report-group-card">
                            <div class="payment-report-group-header">
                                <h5 class="payment-report-group-title">
                                    <strong>Class:</strong> {{ $group->class_name }} | <strong>Section:</strong> {{ $group->section_name }}
                                </h5>
                            </div>
                            <div class="payment-report-table-wrap">
                                <table class="table table-hover table-sm payment-report-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            @foreach($categories as $category)
                                                <th class="text-right">{{ $category->name }}</th>
                                            @endforeach
                                            <th class="text-right">Grand Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($group->students as $index => $row)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $row->student_cid }}</td>
                                                <td>{{ $row->student_name }}</td>
                                                @foreach($categories as $category)
                                                    <td class="text-right">{{ number_format($row->{$category->column_key}, 2) }}</td>
                                                @endforeach
                                                <td class="text-right font-weight-bold">{{ number_format($row->grand_total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold">
                                            <td colspan="3">Subtotal</td>
                                            @foreach($categories as $category)
                                                <td class="text-right">{{ number_format($group->students->sum(fn($r) => $r->{$category->column_key}), 2) }}</td>
                                            @endforeach
                                            <td class="text-right">{{ number_format($group->students->sum('grand_total'), 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            html, body {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .main-sidebar,
            .main-header,
            .content-header,
            hr,
            .info-box,
            button,
            a.btn {
                display: none !important;
            }

            .content-wrapper {
                margin-left: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .container-fluid.payment-report-page {
                max-width: none !important;
                padding: 0 !important;
            }

            .payment-report-shell {
                padding: 0 !important;
            }

            .payment-report-filter-card {
                display: none !important;
            }

            .payment-report-card {
                box-shadow: none !important;
                border-color: #d1d5db !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .payment-report-group-card {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .payment-report-group-header {
                padding: 0.5rem 0.75rem 0.45rem !important;
            }

            .payment-report-group-title {
                font-size: 10px !important;
            }

            .report-header-card .report-header-body {
                display: table !important;
                width: 100% !important;
                table-layout: fixed !important;
            }

            .report-header-card .report-header-logo,
            .report-header-card .report-header-copy {
                display: table-cell !important;
                vertical-align: middle !important;
            }

            .report-header-copy{
                padding-left: 20px !important;
            }

            .report-header-card .report-header-logo {
                width: 58px !important;
                padding-right: 12px !important;
            }

            .payment-report-table-wrap {
                overflow: visible !important;
            }

            .payment-report-table {
                width: 100% !important;
                min-width: 0 !important;
                table-layout: fixed !important;
                font-size: 8px !important;
            }

            .payment-report-table thead th,
            .payment-report-table tbody td,
            .payment-report-table tfoot td {
                padding: 0.22rem 0.28rem !important;
                white-space: normal !important;
                word-break: break-word !important;
                overflow-wrap: anywhere !important;
            }

            .payment-report-table thead th {
                font-size: 7px !important;
                line-height: 1.2 !important;
            }

            .payment-report-table tbody td {
                font-size: 7px !important;
                line-height: 1.15 !important;
            }

            .payment-report-table tfoot td {
                font-size: 7px !important;
            }

            .payment-report-table th:nth-child(1),
            .payment-report-table td:nth-child(1) {
                width: 3%;
            }

            .payment-report-table th:nth-child(2),
            .payment-report-table td:nth-child(2) {
                width: 9%;
            }

            .payment-report-table th:nth-child(3),
            .payment-report-table td:nth-child(3) {
                width: 12%;
            }

            .payment-report-table th:last-child,
            .payment-report-table td:last-child {
                width: 8%;
            }
        }
    </style>
@endsection
