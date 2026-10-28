@extends('layouts.master')

@section('contents')
<div class="col-12 tutorial-report-page">
    <div class="tutorial-report-toolbar no-print">
        <form id="reportFormTop" class="tutorial-report-filter-form" method="GET" action="{{ route('result.tutorial-report.show') }}">
            <div class="tutorial-report-filter-row">
                <div class="tutorial-report-filter-group">
                    <label for="tutorialSessionSelect">Academic Session <span class="text-danger">*</span></label>
                    <select name="session_id" id="tutorialSessionSelect" class="form-control tutorial-report-filter-select" required>
                        <option value="">— Select Session —</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$s->id ? 'selected' : '' }}>
                                {{ $s->name_en ?? $s->name_bn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tutorial-report-filter-group">
                    <label for="classSelectTop">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelectTop" class="form-control tutorial-report-filter-select" required>
                        <option value="">— Select Class —</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tutorial-report-filter-group">
                    <label for="sectionSelectTop">Section <span class="text-danger">*</span></label>
                    <select name="section_id" id="sectionSelectTop" class="form-control tutorial-report-filter-select" required>
                        <option value="">— Select Section —</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ (string)($filters['section_id'] ?? '') === (string)$section->id ? 'selected' : '' }}>
                                {{ $section->name_en ?? $section->name_bn }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tutorial-report-filter-group">
                    <label for="tutorialExamSelect">Exam <span class="text-danger">*</span></label>
                    <select name="exam_id" id="tutorialExamSelect" class="form-control tutorial-report-filter-select" required>
                        <option value="">— Select Exam —</option>
                        @foreach($exams as $e)
                            <option value="{{ $e->id }}" {{ (string)($filters['exam_id'] ?? '') === (string)$e->id ? 'selected' : '' }}>
                                {{ $e->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="tutorial-report-filter-group">
                    <label for="tutorialStudentInput">Student ID <small class="text-muted">(optional)</small></label>
                    <input type="text" name="student_id" id="tutorialStudentInput" class="form-control tutorial-report-filter-input" value="{{ $filters['student_id'] ?? '' }}" placeholder="Leave blank for all students">
                </div>

                <div class="tutorial-report-filter-actions">
                    <button type="submit" class="btn tutorial-report-action-btn tutorial-report-action-btn--primary" title="View Report" aria-label="View Report">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="{{ route('result.tutorial-report.index') }}" class="btn tutorial-report-action-btn tutorial-report-action-btn--ghost" title="Reset" aria-label="Reset">
                        <i class="fas fa-undo-alt"></i>
                    </a>
                </div>
            </div>

            <input type="hidden" name="subject_filter" value="1">
            <div class="tutorial-report-subject-filter is-mobile-collapsed">
                <div class="tutorial-report-subject-filter__header">
                    <div>
                        <label class="mb-0">Subjects</label>
                        <small class="text-muted d-block">Included subjects are selectable; excluded subjects stay visible for reference.</small>
                    </div>
                    <div class="tutorial-report-subject-filter__controls">
                        @if($tutorialSubjects->isNotEmpty())
                            <div class="tutorial-report-subject-filter__actions">
                                <button type="button" class="tutorial-report-subject-filter__action tutorial-report-subject-filter__action--primary" id="tutorialSelectAllSubjects">Select all</button>
                                <button type="button" class="tutorial-report-subject-filter__action tutorial-report-subject-filter__action--secondary" id="tutorialClearAllSubjects">Clear all</button>
                            </div>
                        @endif
                        <button type="button" id="tutorialToggleSubjects" class="tutorial-report-subject-filter__toggle" aria-expanded="false" aria-controls="tutorialSubjectOptions">
                            <i class="fas fa-sliders-h mr-1" aria-hidden="true"></i> Subjects
                        </button>
                    </div>
                </div>
                <div id="tutorialSubjectOptions" class="tutorial-report-subject-filter__options">
                    @forelse($regularTutorialSubjects as $subject)
                        @php
                            $isIncluded = (bool) $subject->include_in_tutorial_exam;
                        @endphp
                        <label class="tutorial-report-subject-option">
                            <input class="form-check-input" type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                {{ in_array((int) $subject->id, array_map('intval', $filters['subject_ids'] ?? []), true) ? 'checked' : '' }}
                                {{ $isIncluded ? '' : 'disabled' }}>
                            <span>{{ $subject->name }} @unless($isIncluded)<small class="text-muted">(Excluded)</small>@endunless</span>
                        </label>
                    @empty
                    @endforelse
                    @if($religionSubjects->isNotEmpty())
                        @php
                            $hasIncludedReligion = $includedReligionSubjectIds->isNotEmpty();
                        @endphp
                        <label class="tutorial-report-subject-option">
                            <input class="form-check-input" type="checkbox" name="religion_subject" value="religion"
                                {{ !empty($filters['religion_subject']) ? 'checked' : '' }}
                                {{ $hasIncludedReligion ? '' : 'disabled' }}>
                            <span>Religion</span>
                            @unless($hasIncludedReligion)<small class="text-muted">(Excluded)</small>@endunless
                        </label>
                    @endif
                    @if($regularTutorialSubjects->isEmpty() && $religionSubjects->isEmpty())
                        <span class="text-muted small">No assigned tutorial subjects found.</span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center shadow"
                style="width:52px;height:52px;background:linear-gradient(135deg,#0891b2,#0e7490);flex-shrink:0">
                <i class="fas fa-clipboard-list text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="mb-0 font-weight-bold text-black">Tutorial Exam Report</h4>
                <small class="text-muted">{{ $exam->name }} &mdash;
                    {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('result.tutorial-report.settings.edit') }}" class="btn btn-outline-primary btn-sm result-filter-icon-btn" title="Report settings" aria-label="Report settings">
                <i class="fas fa-sliders-h"></i>
            </a>
            <a href="{{ route('result.tutorial-report.pdf', collect($filters)->except('results_per_page')->all()) }}" target="_blank" class="btn btn-danger btn-sm result-filter-icon-btn" title="PDF" aria-label="PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
            <button onclick="window.print()" class="btn btn-info btn-sm no-print result-filter-icon-btn" title="Print" aria-label="Print">
                <i class="fas fa-print"></i>
            </button>
            <a href="{{ route('result.tutorial-report.index') }}" class="btn btn-secondary btn-sm no-print result-filter-icon-btn" title="Back" aria-label="Back">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="tutorial-report-context mb-4">
        <div class="tutorial-report-context__item">
            <span class="tutorial-report-context__label">Session</span>
            <span class="tutorial-report-context__value">{{ $reportContext['session'] ?? '—' }}</span>
        </div>
        <div class="tutorial-report-context__item">
            <span class="tutorial-report-context__label">Class</span>
            <span class="tutorial-report-context__value">{{ $reportContext['class'] ?? '—' }}</span>
        </div>
        <div class="tutorial-report-context__item">
            <span class="tutorial-report-context__label">Section</span>
            <span class="tutorial-report-context__value">{{ $reportContext['section'] ?? '—' }}</span>
        </div>
    </div>

    <div class="tutorial-print-only">
        @include('pages.tutorial-report._print-layout')
    </div>

    <div class="tutorial-screen-results">
    @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $academicInfo = $data['academicInfo'] ?? null;
            $rows = collect($data['rows'] ?? []);
            $totalFullMarks = $rows->sum(fn ($row) => (float) ($row['full_marks'] ?? 0));
            $overallPercentage = $totalFullMarks > 0
                ? min(100, round(((float) $data['total_obtained'] / $totalFullMarks) * 100, 1))
                : 0;
        @endphp
        <div class="card mb-4">
            <div class="card-header tutorial-student-strip">
                <div class="tutorial-student-field">
                    <span class="tutorial-student-label" style="color: var(--tutorial-student-label-color) !important;">Student Name</span>
                    <span class="tutorial-student-value" style="color: var(--tutorial-student-value-color) !important;">{{ $student->full_name_en }}</span>
                </div>
                <div class="tutorial-student-field">
                    <span class="tutorial-student-label" style="color: var(--tutorial-student-label-color) !important;">Student ID</span>
                    <span class="tutorial-student-value tutorial-student-mono" style="color: var(--tutorial-student-value-color) !important;">{{ $student->student_cid ?? $student->id }}</span>
                </div>
                <div class="tutorial-student-field">
                    <span class="tutorial-student-label" style="color: var(--tutorial-student-label-color) !important;">Roll</span>
                    <span class="tutorial-student-value tutorial-student-mono" style="color: var(--tutorial-student-value-color) !important;">{{ $academicInfo?->roll ?? '—' }}</span>
                </div>
                @if($academicInfo?->group)
                    <div class="tutorial-student-field">
                        <span class="tutorial-student-label" style="color: var(--tutorial-student-label-color) !important;">Group</span>
                        <span class="tutorial-student-value" style="color: var(--tutorial-student-value-color) !important;">{{ $academicInfo->group->name_en ?? $academicInfo->group->name_bn }}</span>
                    </div>
                @endif
                <div class="ml-auto tutorial-student-total">
                    <span class="badge badge-primary mr-1">Position: {{ $data['rank'] ?? '—' }}</span>
                    <span class="badge badge-info">Total Obtained: {{ number_format($data['total_obtained'], 1) }}</span>
                    {{-- <span class="badge ml-2 js-email-status {{ !empty($statusMap[$student->id]) ? 'badge-success' : 'badge-secondary' }}"
                        id="tutorial-email-status-{{ $student->id }}">
                        {{ !empty($statusMap[$student->id]) ? 'Email Sent' : 'Not Sent' }}
                    </span> --}}
                    {{-- <button type="button"
                        class="btn btn-sm btn-success ml-2 js-send-result-email"
                        data-url="{{ route('result.tutorial-report.email') }}"
                        data-student-id="{{ $student->id }}"
                        data-session-id="{{ $filters['session_id'] }}"
                        data-class-id="{{ $filters['class_id'] }}"
                        data-section-id="{{ $filters['section_id'] }}"
                        data-exam-id="{{ $filters['exam_id'] }}"
                        data-status-id="tutorial-email-status-{{ $student->id }}">
                        <i class="fas fa-envelope mr-1"></i> Send to Parents
                    </button> --}}
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0 tutorial-report-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center" style="width:130px">Full Marks</th>
                            <th class="text-center" style="width:160px">Obtained Marks</th>
                            <th class="text-center" style="width:160px">Highest Marks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="{{ $r['is_absent'] ? 'table-secondary' : '' }}">
                                <td>{{ $r['subject_name'] }}</td>
                                <td class="text-center">{{ number_format((float) ($r['full_marks'] ?? 0), 1) }}</td>
                                <td class="text-center">{{ $r['is_absent'] ? 'AB' : number_format((float) ($r['obtained'] ?? 0), 1) }}</td>
                                <td class="text-center">{{ number_format((float) ($r['highest'] ?? 0), 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No marks found.</td>
                            </tr>
                        @endforelse
                        <tr class="tutorial-report-total">
                            <td>Total</td>
                            <td class="text-center">{{ number_format($totalFullMarks, 1) }}</td>
                            <td class="text-center">{{ number_format((float) $data['total_obtained'], 1) }}</td>
                            <td class="text-center">{{ number_format($overallPercentage, 1) }}%</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>
@endsection

@section('styles')
<style>
.tutorial-report-toolbar {
    background: #ffffff;
    border: 1px solid #e7e5e4;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    padding: 0.9rem;
    margin-bottom: 1rem;
}

.tutorial-print-only {
    display: none;
}

.tutorial-report-filter-form {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.tutorial-report-filter-row {
    display: grid;
    grid-template-columns: repeat(6, minmax(0, 1fr)) auto;
    gap: 0.75rem;
    align-items: end;
}

.tutorial-report-filter-group label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.77rem;
    font-weight: 700;
    color: #6b7280;
}

.tutorial-report-filter-select,
.tutorial-report-filter-input {
    width: 100%;
    min-height: 46px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #111827;
    font-size: 0.92rem;
    box-shadow: none;
}

.tutorial-report-filter-select:focus,
.tutorial-report-filter-input:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
}

.tutorial-report-filter-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.65rem;
    flex-wrap: wrap;
}

.tutorial-report-action-btn {
    min-width: 46px;
    min-height: 46px;
    border-radius: 12px;
}

.tutorial-report-action-btn--primary {
    background: #111827;
    border-color: #111827;
    color: #fff;
}

.tutorial-report-action-btn--primary:hover {
    background: #0f172a;
    border-color: #0f172a;
    color: #fff;
}

.tutorial-report-action-btn--ghost {
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #374151;
}

.tutorial-report-action-btn--ghost:hover {
    background: #f8fafc;
    color: #111827;
}

.tutorial-report-subject-filter {
    border-top: 1px solid #eef2f1;
    padding-top: 0.8rem;
}

.tutorial-report-subject-filter__header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 0.65rem;
}

.tutorial-report-subject-filter__header label {
    color: #374151;
    font-size: 0.82rem;
    font-weight: 700;
}

.tutorial-report-subject-filter__controls {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto;
    white-space: nowrap;
}

.tutorial-report-subject-filter__actions {
    display: flex;
    gap: 0.5rem;
    white-space: nowrap;
}

.tutorial-report-subject-filter__action {
    height: 32px;
    display: inline-flex;
    align-items: center;
    border: 1px solid transparent;
    border-radius: 8px;
    padding: 0.38rem 0.7rem;
    font-size: 0.76rem;
    font-weight: 700;
    line-height: 1.2;
    transition: background-color 160ms ease, border-color 160ms ease, color 160ms ease;
}

.tutorial-report-subject-filter__action--primary {
    background: #111827;
    border-color: #111827;
    color: #fff;
}

.tutorial-report-subject-filter__action--primary:hover,
.tutorial-report-subject-filter__action--primary:focus {
    background: #000;
    border-color: #000;
    color: #fff;
}

.tutorial-report-subject-filter__action--secondary {
    background: #fff;
    border-color: #cbd5e1;
    color: #334155;
}

.tutorial-report-subject-filter__action--secondary:hover,
.tutorial-report-subject-filter__action--secondary:focus {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
}

.tutorial-report-subject-filter__options {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.95rem 1rem;
}

.tutorial-report-subject-option {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    margin: 0;
    color: #1f2937;
    font-size: 0.88rem;
    font-weight: 600;
}

.tutorial-report-subject-option > span {
    margin-left: 1.5rem;
}

.tutorial-report-subject-option input {
    appearance: none;
    -webkit-appearance: none;
    width: 1.05rem;
    height: 1.05rem;
    flex: 0 0 auto;
    margin: 0;
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

.tutorial-report-subject-option input:checked {
    background-color: #111111;
    border-color: #111111;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='none'%3E%3Cpath d='M6.2 11.2 2.9 8l-1.1 1.1 4.4 4.4L14.2 5.5 13.1 4.4 6.2 11.2Z' fill='%23ffffff'/%3E%3C/svg%3E");
}

.tutorial-report-subject-option input:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(17, 17, 17, 0.12);
}

.tutorial-report-subject-option input:hover {
    border-color: #000000;
}

.tutorial-report-subject-option input:disabled {
    background: #e5e7eb;
    border-color: #cbd5e1;
    cursor: not-allowed;
}

.tutorial-report-subject-filter__toggle {
    display: inline-flex;
    align-items: center;
    height: 32px;
    border: 1px solid #dbe4ea;
    border-radius: 8px;
    background: #fff;
    color: #0e7490;
    cursor: pointer;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.38rem 0.7rem;
}

@media (max-width: 1280px) {
    .tutorial-report-filter-row {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .tutorial-report-filter-actions {
        justify-content: flex-start;
    }

    .tutorial-report-subject-filter__options {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .tutorial-report-filter-row {
        grid-template-columns: 1fr;
    }

    .tutorial-report-subject-filter__header {
        align-items: center;
        flex-wrap: wrap;
    }

    .tutorial-report-subject-filter__toggle {
        display: inline-block;
    }

    .tutorial-report-subject-filter__options {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

.tutorial-report-subject-filter.is-mobile-collapsed .tutorial-report-subject-filter__options {
    display: none;
}

.tutorial-report-table thead th {
    background: #0f172a;
    color: #f8fafc;
    border-color: rgba(148, 163, 184, 0.18);
}

.tutorial-report-table thead th:first-child {
    border-top-left-radius: 0;
}

.tutorial-report-table thead th:last-child {
    border-top-right-radius: 0;
}

.tutorial-report-table .tutorial-report-total td {
    background: transparent;
    color: #0f172a;
    font-weight: 800;
}

.tutorial-report-context {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.tutorial-report-page {
    --tutorial-student-label-color: #0f172a;
    --tutorial-student-value-color: #0f172a;
}

.tutorial-report-context__item {
    padding: 0.9rem 1rem;
    border: 1px solid #dbe4f0;
    border-radius: 0.95rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.96) 100%);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
    color: #0f172a !important;
}

.tutorial-report-context__label {
    display: block;
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #0f172a !important;
}

.tutorial-report-context__value {
    display: block;
    font-size: 0.98rem;
    font-weight: 700;
    color: #0f172a !important;
}

.tutorial-student-strip {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 0.9rem 1rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(248, 250, 252, 0.96) 100%);
    border-bottom: 1px solid #dbe4f0;
    color: #0f172a !important;
}

.tutorial-student-field {
    min-width: 0;
}

.tutorial-student-label {
    display: block;
    margin-bottom: 0.2rem;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #0f172a !important;
}

.tutorial-student-value {
    display: block;
    font-size: 0.98rem;
    font-weight: 700;
    color: #0f172a !important;
}

.tutorial-student-total {
    margin-left: auto;
}

.tutorial-student-mono {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

html[data-theme='dark'] .tutorial-report-page .report-header-card {
    background: linear-gradient(180deg, #0f172a 0%, #111827 100%) !important;
    border-color: rgba(148, 163, 184, 0.18) !important;
    box-shadow: 0 12px 30px rgba(2, 6, 23, 0.26);
}

html[data-theme='dark'] .tutorial-report-page .report-header-card .report-header-logo {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.2) !important;
}

html[data-theme='dark'] .tutorial-report-page .report-header-card .report-header-title {
    color: #f8fafc !important;
}

html[data-theme='dark'] .tutorial-report-page .report-header-card .report-header-address,
html[data-theme='dark'] .tutorial-report-page .report-header-card .report-header-contacts {
    color: #cbd5e1 !important;
}

html[data-theme='dark'] .tutorial-report-page .card {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.18) !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .tutorial-report-page .card-header {
    background: #1e293b !important;
    border-color: rgba(148, 163, 184, 0.18) !important;
    color: #f8fafc !important;
}

html[data-theme='dark'] .tutorial-report-page .card-body {
    background: #0f172a !important;
}

html[data-theme='dark'] .tutorial-report-page .text-black,
html[data-theme='dark'] .tutorial-report-page .text-muted,
html[data-theme='dark'] .tutorial-report-page .text-gray-900,
html[data-theme='dark'] .tutorial-report-page .text-gray-800,
html[data-theme='dark'] .tutorial-report-page .text-gray-700,
html[data-theme='dark'] .tutorial-report-page .text-gray-600,
html[data-theme='dark'] .tutorial-report-page .text-slate-800,
html[data-theme='dark'] .tutorial-report-page .text-slate-700,
html[data-theme='dark'] .tutorial-report-page .text-slate-600,
html[data-theme='dark'] .tutorial-report-page .text-slate-500 {
    color: #cbd5e1 !important;
}

html[data-theme='dark'] .tutorial-report-page .badge-info {
    background: #06b6d4 !important;
    color: #fff !important;
}

html[data-theme='dark'] .tutorial-report-context__item {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 8px 20px rgba(2, 6, 23, 0.18);
    color: #ffffff !important;
}

html[data-theme='dark'] .tutorial-report-context__label {
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .tutorial-report-context__value {
    color: #ffffff !important;
}

html[data-theme='dark'] .tutorial-report-page {
    --tutorial-student-label-color: #ffffff;
    --tutorial-student-value-color: #ffffff;
}

html[data-theme='dark'] .tutorial-student-strip {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
    border-bottom-color: rgba(148, 163, 184, 0.18);
    color: #ffffff !important;
}

html[data-theme='dark'] .tutorial-student-label {
    color: var(--tutorial-student-label-color) !important;
}

html[data-theme='dark'] .tutorial-student-value {
    color: var(--tutorial-student-value-color) !important;
}

html[data-theme='dark'] .tutorial-student-mono {
    color: var(--tutorial-student-value-color) !important;
}

html[data-theme='dark'] .tutorial-student-total .badge {
    background: #06b6d4 !important;
    color: #fff !important;
}

html[data-theme='dark'] .tutorial-report-page .card-header,
html[data-theme='dark'] .tutorial-report-page .card-header * {
    color: #ffffff !important;
}

html[data-theme='dark'] .tutorial-report-table thead th {
    background: #1e293b !important;
    color: #f8fafc !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .tutorial-report-table tbody td {
    border-color: rgba(148, 163, 184, 0.16) !important;
}

html[data-theme='dark'] .tutorial-report-table .tutorial-report-total td {
    color: #f8fafc !important;
}

@media print {
    @page {
        size: {{ $reportSettings->page_width_mm ?? 210 }}mm {{ $reportSettings->page_height_mm ?? 297 }}mm;
        margin: {{ $reportSettings->page_margin_top_mm ?? 10 }}mm {{ $reportSettings->page_margin_right_mm ?? 12 }}mm {{ $reportSettings->page_margin_bottom_mm ?? 10 }}mm {{ $reportSettings->page_margin_left_mm ?? 12 }}mm;
    }

    body {
        background: #fff !important;
    }

    .no-print,
    .btn,
    .card-header .btn-group {
        display: none !important;
    }

    .tutorial-report-toolbar,
    .tutorial-report-context,
    .tutorial-report-page > .d-flex,
    .tutorial-report-page > .card,
    .tutorial-report-page > .report-header-card {
        display: none !important;
    }

    .tutorial-screen-results {
        display: none !important;
    }

    .tutorial-print-only {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .tutorial-report-page,
    .tutorial-report-page > * {
        max-width: none !important;
    }

    .tutorial-print-layout {
        width: 100% !important;
        margin: 0 !important;
        color: #000 !important;
    }

    .card,
    .card-body {
        border: 0 !important;
        box-shadow: none !important;
    }

    .report-header-card {
        margin-bottom: 16px !important;
    }

    .tutorial-report-context {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .tutorial-report-context {
        grid-template-columns: 1fr;
    }

    .tutorial-student-strip {
        align-items: flex-start;
    }

    .tutorial-student-total {
        margin-left: 0;
        width: 100%;
    }
}
</style>
@endsection

@section('scripts')
<script>
$(function () {
    var selectedSection = @json($filters['section_id'] ?? null);
    var form = document.getElementById('reportFormTop');
    var subjectFilter = document.querySelector('.tutorial-report-subject-filter');
    var subjectToggle = document.getElementById('tutorialToggleSubjects');

    function loadSections(classId, selectedSectionId = null) {
        var $section = $('#sectionSelectTop');
        if (!classId) {
            $section.html('<option value="">— Select Section —</option>');
            if (window.refreshSelect2) refreshSelect2($section);
            return;
        }

        $section.html('<option value="">Loading...</option>');
        if (window.refreshSelect2) refreshSelect2($section);

        $.get('/ajax/sections-by-class', { class_id: classId }, function (data) {
            var opts = '<option value="">— Select Section —</option>';
            $.each(data, function (i, s) {
                var selected = String(selectedSectionId) === String(s.id) ? 'selected' : '';
                opts += '<option value="' + s.id + '" ' + selected + '>' + (s.name_en || s.name_bn) + '</option>';
            });
            $section.html(opts);
            if (window.refreshSelect2) refreshSelect2($section);
        });
    }

    $('#classSelectTop').on('change', function () {
        loadSections($(this).val());
    });

    if ($('#classSelectTop').val()) {
        loadSections($('#classSelectTop').val(), selectedSection);
    }

    subjectToggle?.addEventListener('click', function () {
        var isCollapsed = subjectFilter.classList.toggle('is-mobile-collapsed');
        subjectToggle.setAttribute('aria-expanded', String(!isCollapsed));
    });

    if (subjectToggle && subjectFilter && window.matchMedia('(min-width: 769px)').matches) {
        subjectFilter.classList.remove('is-mobile-collapsed');
        subjectToggle.setAttribute('aria-expanded', 'true');
    }

    document.getElementById('tutorialSelectAllSubjects')?.addEventListener('click', function () {
        document.querySelectorAll('.tutorial-report-subject-option input[type="checkbox"]:not(:disabled)')
            .forEach((checkbox) => checkbox.checked = true);
    });

    document.getElementById('tutorialClearAllSubjects')?.addEventListener('click', function () {
        document.querySelectorAll('.tutorial-report-subject-option input[type="checkbox"]:not(:disabled)')
            .forEach((checkbox) => checkbox.checked = false);
    });
});

document.querySelectorAll('.js-send-result-email').forEach((btn) => {
    btn.addEventListener('click', async () => {
        if (btn.dataset.sending === '1') return;
        btn.dataset.sending = '1';
        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';

        const payload = {
            session_id: btn.dataset.sessionId,
            class_id: btn.dataset.classId,
            section_id: btn.dataset.sectionId,
            exam_id: btn.dataset.examId,
            student_id: btn.dataset.studentId,
        };

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Failed to send email.');
            }

            const statusEl = document.getElementById(btn.dataset.statusId);
            if (statusEl) {
                statusEl.classList.remove('badge-secondary');
                statusEl.classList.add('badge-success');
                statusEl.textContent = 'Email Sent';
            }
            btn.innerHTML = '<i class="fas fa-check mr-1"></i> Sent';
        } catch (e) {
            alert(e.message || 'Failed to send email.');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        } finally {
            btn.dataset.sending = '0';
        }
    });
});
</script>
@endsection
