@extends('layouts.master')

@section('styles')
    <style>
        .exam-table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .exam-table {
            min-width: 980px;
            table-layout: fixed;
        }

        .exam-table th,
        .exam-table td {
            padding: 0.45rem 0.6rem;
            vertical-align: middle;
            line-height: 1.15;
        }

        .exam-table thead th {
            white-space: nowrap;
        }

        .exam-table tbody td {
            font-size: 0.875rem;
        }

        .exam-table .exam-name-cell {
            width: 24%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .exam-table .exam-session-cell {
            width: 18%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .exam-table .exam-date-cell,
        .exam-table .exam-status-cell,
        .exam-table .exam-type-cell,
        .exam-table .exam-year-cell {
            white-space: nowrap;
        }

        .exam-table .exam-actions-cell {
            white-space: nowrap;
        }

        /* Fixed layout split the width evenly: "#" got 130px while the five
           36px action buttons (plus gaps) overflowed their column. */
        .exam-table thead th:first-child {
            width: 48px;
        }

        .exam-table thead th:nth-child(5) {
            width: 72px;
        }

        .exam-table thead th:last-child {
            width: 236px;
        }

        /* Fit the table to its box instead of a 980px minimum that hid the Actions
           column off to the right. A container query, not a viewport one, so it
           also works at laptop widths with the sidebar open. */
        .exam-table-responsive {
            container: exam-list / inline-size;
        }

        @container exam-list (max-width: 999px) {
            .exam-table {
                min-width: 0;
                table-layout: auto;
            }

            /* A global table rule gives every cell 16px + 24px side padding */
            .exam-table th,
            .exam-table td {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            .exam-table th:first-child,
            .exam-table td:first-child {
                display: none;
            }

            .exam-table .exam-name-cell {
                width: auto;
                white-space: normal;
                min-width: 120px;
            }

            .exam-table .exam-date-cell {
                white-space: normal;
                min-width: 96px;
            }

            .exam-table .exam-session-cell {
                width: auto;
            }

            .exam-table .badge {
                white-space: nowrap;
            }

            .exam-table thead th:last-child {
                width: auto;
            }

            .exam-table .exam-actions {
                gap: 0.25rem;
            }

            .exam-table td.exam-actions-cell,
            .exam-table thead th:last-child {
                padding-left: 0.5rem !important;
                padding-right: 0.75rem !important;
            }

            .exam-table .exam-actions .exam-action-btn {
                width: 32px;
                min-width: 32px;
                height: 32px;
                min-height: 32px;
            }
        }

        /* Narrow boxes: Year is already in the Date column ("01 Oct – 31 Oct 2026") */
        @container exam-list (max-width: 819px) {
            .exam-table th:nth-child(5),
            .exam-table td.exam-year-cell {
                display: none !important;
            }
        }

        @media (min-width: 768px) and (max-width: 1099.98px) {
            .exams-mobile-filters > div {
                flex: 1 1 130px;
                min-width: 0 !important;
            }

            .exams-mobile-filters > div:nth-child(n+4) {
                flex: 0 0 auto;
            }
        }

        /* Phone: one card per exam (shared .ds-table--stack layout) instead of a
           980px table that scrolled sideways and hid the exam name. */
        @media (max-width: 767.98px) {
            .exam-table-responsive {
                overflow: visible;
            }

            .exam-table {
                min-width: 0;
                table-layout: auto;
            }

            /* Same 12px page gutter as other pages, one 12px gutter inside the card */
            .exams-page {
                padding-left: 0;
                padding-right: 0;
            }

            .content-wrapper .exams-page .card .card-body.exams-list-body {
                padding: 0 !important;
            }

            .exams-page .exam-table.ds-table--stack tr {
                padding: 0.75rem;
            }

            .exams-page .exam-table.ds-table--stack td {
                padding: 2px 0 !important;
                font-size: 0.875rem;
            }

            .exams-page .exam-table.ds-table--stack td[data-label] {
                min-height: 26px;
            }

            .exams-page .exam-table.ds-table--stack td.ds-stack-title {
                padding-bottom: 4px !important;
            }

            .exam-table.ds-table--stack .exam-name-cell {
                width: auto;
                white-space: normal;
                overflow: visible;
            }

            .exam-table.ds-table--stack .exam-session-cell {
                width: auto;
            }

            .exam-table.ds-table--stack td.ds-stack-actions {
                margin-top: 0.5rem;
            }

            .exam-table.ds-table--stack .exam-actions {
                justify-content: flex-start !important;
                flex-wrap: wrap !important;
                gap: 0.5rem;
            }

            .exam-table.ds-table--stack .exam-actions .exam-action-btn {
                width: 40px;
                min-width: 40px;
                height: 40px;
                min-height: 40px;
            }
        }

        @media (max-width: 575px) {
            .exams-mobile-header {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 8px;
                padding: 10px 12px;
            }

            .exams-mobile-header > div:first-child {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                align-items: center;
                gap: 8px;
                min-width: 0;
            }

            .exams-mobile-header > div:first-child .btn {
                margin-right: 0 !important;
                white-space: nowrap;
            }

            .exams-mobile-header h4 {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 15px;
            }

            .exams-mobile-header > a {
                width: 100%;
                min-height: 40px;
                margin: 0 !important;
                padding: 8px 6px;
                white-space: nowrap;
            }

            .exams-filter-card > .card-body {
                padding: 12px !important;
            }

            .exams-mobile-filters {
                display: grid !important;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 8px;
                align-items: center;
            }

            .exams-mobile-filters > div {
                width: 100%;
                min-width: 0 !important;
            }

            /* Status | Type, then Session full width, then Filter | Reset */
            .exams-mobile-filters > div:nth-child(3) {
                grid-column: 1 / -1;
            }

            .exams-mobile-filters select,
            .exams-mobile-filters .btn {
                width: 100%;
                min-height: 40px;
            }

            .exams-mobile-filters .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }

        .exam-actions {
            gap: 0.35rem;
        }

        .exam-actions .exam-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            min-width: 36px;
            height: 36px;
            min-height: 36px;
            padding: 0;
            line-height: 1;
            vertical-align: middle;
            overflow: hidden;
        }

        .exam-actions .exam-action-btn i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            line-height: 1;
            margin: 0;
        }

        .exams-filter-card .card-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-bottom: 0;
        }

        html[data-theme='dark'] .exams-page .card,
        html[data-theme='dark'] .exams-page .card-header,
        html[data-theme='dark'] .exams-page .card-body,
        html[data-theme='dark'] .exams-page .card-footer {
            background-color: #111827 !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .exams-page .exams-filter-card .card-header {
            background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .exams-page .card-body > form[method='GET'] {
            background-color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .exams-page .form-control {
            background-color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .exams-page .form-control option {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-selection--single {
            background-color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.28) !important;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8 !important;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-top-color: #94a3b8;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-dropdown,
        html[data-theme='dark'] .exams-page .select2-container--default .select2-results__option {
            background-color: #111827 !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            color: #cbd5e1 !important;
        }

        html[data-theme='dark'] .exams-page .select2-container--default .select2-results__option--highlighted {
            background-color: #2563eb !important;
            color: #fff !important;
        }

        html[data-theme='dark'] .exams-page .btn-light {
            background-color: #1e293b;
            border-color: rgba(148, 163, 184, 0.3);
            color: #e2e8f0;
        }

        html[data-theme='dark'] .exams-page .btn-secondary {
            background: #2563eb !important;
            border-color: #2563eb !important;
            color: #fff !important;
        }

        html[data-theme='dark'] .exams-page .btn-secondary:hover,
        html[data-theme='dark'] .exams-page .btn-secondary:focus {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
        }

        html[data-theme='dark'] .exams-page .thead-light th {
            background-color: #1e293b !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .exams-page .table tbody tr {
            background-color: #111827;
            border-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .exams-page .table tbody tr:hover {
            background-color: #1e293b;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid exams-page">

        <div class="card card-outline card-primary mb-3 exams-filter-card">
            <div class="card-header">
                <div class="col-12 d-flex justify-content-between align-items-center exams-mobile-header">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('results.hub') }}" class="btn btn-sm btn-secondary mr-3">
                            <i class="fas fa-arrow-left mr-1"></i>Back
                        </a>
                        <h4 class="font-weight-bold mb-0 text-white"><i class="fas fa-file-alt text-primary mr-2"></i>Exams</h4>
                    </div>
                    <a href="{{ route('exams.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i> New Exam
                    </a>
                </div>
            </div>
            <div class="card-body py-2">

                <form method="GET" class="d-flex flex-wrap align-items-center gap-2 exams-mobile-filters">

                    {{-- Status --}}
                    <div style="min-width: 160px;">
                        <select name="status" class="form-control form-control-sm border rounded shadow-sm">
                            <option value="">Active Exams</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    {{-- Type --}}
                    <div style="min-width: 160px;">
                        <select name="type" class="form-control form-control-sm border rounded shadow-sm">
                            <option value="">All Types</option>
                            <option value="term" {{ request('type') == 'term' ? 'selected' : '' }}>Terminal</option>
                            <option value="tutorial" {{ request('type') == 'tutorial' ? 'selected' : '' }}>Tutorial</option>
                        </select>
                    </div>

                    {{-- Session --}}
                    <div style="min-width: 200px;">
                        <select name="session_id" class="form-control form-control-sm border rounded shadow-sm">
                            <option value="">All Sessions</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}"
                                    {{ request('session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name_en ?? $session->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Button --}}
                    <div>
                        <button type="submit" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-filter mr-1" aria-hidden="true"></i>Filter
                        </button>
                    </div>

                    {{-- Reset Button --}}
                    <div>
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-secondary px-3">
                            <i class="fas fa-undo mr-1" aria-hidden="true"></i>Reset
                        </a>
                    </div>

                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body p-0 exams-list-body">
                <div class="exam-table-responsive">
                    <table class="table table-hover table-sm mb-0 exam-table ds-table--stack">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Exam Name</th>
                                <th>Type</th>
                                <th>Session</th>
                                <th>Year</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                                <tr>
                                    <td class="ds-hide-sm">{{ $loop->iteration }}</td>
                                    <td class="exam-name-cell ds-stack-title"><strong>{{ $exam->name }}</strong></td>
                                    <td class="exam-type-cell" data-label="{{ __('Type') }}">
                                        <span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }}">
                                            {{ $exam->type_label }}
                                        </span>
                                    </td>
                                    <td class="exam-session-cell" data-label="{{ __('Session') }}">{{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '-') }}</td>
                                    <td class="exam-year-cell" data-label="{{ __('Year') }}">{{ $exam->year }}</td>
                                    <td class="exam-date-cell" data-label="{{ __('Date') }}">
                                        @if ($exam->start_date)
                                            {{ $exam->start_date->format('d M') }}
                                            @if ($exam->end_date)
                                                – {{ $exam->end_date->format('d M Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="exam-status-cell" data-label="{{ __('Status') }}">
                                        <span class="badge badge-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td class="exam-actions-cell ds-stack-actions">
                                        <div class="exam-actions d-flex justify-content-center align-items-center flex-nowrap">
                                            <a href="{{ route('exams.show', $exam) }}"
                                                class="btn btn-xs btn-outline-secondary exam-action-btn" title="View" aria-label="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if (!$exam->isArchived())
                                                <a href="{{ route('exams.marks-entry', $exam) }}"
                                                    class="btn btn-xs btn-outline-secondary exam-action-btn" title="Enter Marks"
                                                    aria-label="Enter Marks">
                                                    <i class="fas fa-keyboard"></i>
                                                </a>
                                            @endif
                                            @if ($exam->type === 'term')
                                                <a href="{{ route('exams.terminal-result', $exam) }}"
                                                    class="btn btn-xs btn-outline-secondary exam-action-btn" title="Terminal Result"
                                                    aria-label="Terminal Result">
                                                    <i class="fas fa-trophy"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('exams.preview', $exam) }}"
                                                    class="btn btn-xs btn-outline-secondary exam-action-btn" title="Preview"
                                                    aria-label="Preview">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            @endif
                                            @if (!$exam->isArchived())
                                            <a href="{{ route('exams.edit', $exam) }}"
                                                class="btn btn-xs btn-outline-secondary exam-action-btn" title="Edit"
                                                aria-label="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                            @if ($exam->isArchived())
                                                <button type="submit" class="btn btn-xs btn-outline-secondary exam-action-btn"
                                                    title="Restore" aria-label="Restore"
                                                    data-confirm="{{ __('Restore “:name” as a draft?', ['name' => $exam->name]) }}"
                                                    data-confirm-text="{{ __('Its marks will be preserved.') }}"
                                                    data-confirm-action="{{ __('Restore exam') }}"
                                                    data-confirm-cancel="{{ __('Cancel') }}"
                                                    data-confirm-tone="primary"
                                                    form="restore-exam-{{ $exam->id }}">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @else
                                                @php($requiresArchive = $exam->status === 'published' || $exam->marks_count > 0)
                                                <button type="submit" class="btn btn-xs {{ $requiresArchive ? 'btn-outline-secondary' : 'btn-outline-danger' }} exam-action-btn"
                                                    title="{{ $requiresArchive ? 'Archive' : 'Delete' }}"
                                                    aria-label="{{ $requiresArchive ? 'Archive' : 'Delete' }}"
                                                    data-confirm="{{ $requiresArchive ? __('Archive “:name”?', ['name' => $exam->name]) : __('Delete draft exam “:name”?', ['name' => $exam->name]) }}"
                                                    data-confirm-text="{{ $requiresArchive ? __('Student marks and historical records will be preserved.') : __('This cannot be undone.') }}"
                                                    data-confirm-action="{{ $requiresArchive ? __('Archive exam') : __('Delete exam') }}"
                                                    data-confirm-cancel="{{ __('Keep exam') }}"
                                                    @if ($requiresArchive) data-confirm-tone="primary" @endif
                                                    form="delete-exam-{{ $exam->id }}">
                                                    <i class="fas fa-{{ $requiresArchive ? 'archive' : 'trash' }}"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <form id="delete-exam-{{ $exam->id }}" method="POST"
                                            action="{{ route('exams.destroy', $exam) }}" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                        @if ($exam->isArchived())
                                            <form id="restore-exam-{{ $exam->id }}" method="POST"
                                                action="{{ route('exams.restore', $exam) }}" class="d-none">
                                                @csrf
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No exams found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($exams->hasPages())
                <div class="card-footer">{{ $exams->links() }}</div>
            @endif
        </div>
    </div>
@endsection
