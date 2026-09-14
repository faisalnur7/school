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

        @media (max-width: 767.98px) {
            .exam-table th,
            .exam-table td {
                padding: 0.35rem 0.5rem;
            }

            .exam-actions {
                gap: 0.25rem;
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
                <div class="col-12 d-flex justify-content-between align-items-center">
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

                <form method="GET" class="d-flex flex-wrap align-items-center gap-2">

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
                        <button class="btn btn-sm btn-secondary shadow-sm px-3">
                            Filter
                        </button>
                    </div>

                    {{-- Reset Button --}}
                    <div>
                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-light border shadow-sm px-3">
                            Reset
                        </a>
                    </div>

                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="exam-table-responsive">
                    <table class="table table-hover table-sm mb-0 exam-table">
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
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="exam-name-cell"><strong>{{ $exam->name }}</strong></td>
                                    <td class="exam-type-cell">
                                        <span class="badge badge-{{ $exam->type === 'term' ? 'danger' : 'info' }}">
                                            {{ $exam->type_label }}
                                        </span>
                                    </td>
                                    <td class="exam-session-cell">{{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '-') }}</td>
                                    <td class="exam-year-cell">{{ $exam->year }}</td>
                                    <td class="exam-date-cell">
                                        @if ($exam->start_date)
                                            {{ $exam->start_date->format('d M') }}
                                            @if ($exam->end_date)
                                                – {{ $exam->end_date->format('d M Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="exam-status-cell">
                                        <span
                                            class="badge badge-{{ $exam->status === 'published' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($exam->status) }}
                                        </span>
                                    </td>
                                    <td class="exam-actions-cell">
                                        <div class="exam-actions d-flex justify-content-center align-items-center flex-nowrap">
                                            <a href="{{ route('exams.show', $exam) }}"
                                                class="btn btn-xs btn-info exam-action-btn" title="View" aria-label="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('exams.marks-entry', $exam) }}"
                                                class="btn btn-xs btn-success exam-action-btn" title="Enter Marks"
                                                aria-label="Enter Marks">
                                                <i class="fas fa-keyboard"></i>
                                            </a>
                                            @if ($exam->type === 'term')
                                                <a href="{{ route('exams.terminal-result', $exam) }}"
                                                    class="btn btn-xs btn-warning exam-action-btn" title="Terminal Result"
                                                    aria-label="Terminal Result">
                                                    <i class="fas fa-trophy"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('exams.preview', $exam) }}"
                                                    class="btn btn-xs btn-info exam-action-btn" title="Preview"
                                                    aria-label="Preview">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('exams.edit', $exam) }}"
                                                class="btn btn-xs btn-secondary exam-action-btn" title="Edit"
                                                aria-label="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-xs btn-danger exam-action-btn"
                                                title="Delete" aria-label="Delete"
                                                onclick="if(confirm('Delete this exam?')){this.closest('form').requestSubmit()}"
                                                form="delete-exam-{{ $exam->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-exam-{{ $exam->id }}" method="POST"
                                            action="{{ route('exams.destroy', $exam) }}" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
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
