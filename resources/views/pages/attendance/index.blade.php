@extends('layouts.master')

@section('contents')
    <div class="container-fluid attendance-workspace attendance-mobile-compact">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        @endif
        <div class="attendance-hero">
            <div class="attendance-hero__icon" aria-hidden="true">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="attendance-hero__copy">
                <h1 class="attendance-hero__title">Attendance</h1>
                <p class="attendance-hero__text">Manage student attendance records</p>
            </div>
        </div>

        <div class="card attendance-page-panel shadow-sm border-0 mb-3">
            <div class="card-header attendance-page-panel__header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="attendance-page-panel__label">Attendance Controls</div>
                        <h4 class="card-title mb-0 attendance-page-panel__title">
                            <i class="fas fa-clipboard-check mr-2"></i>Student Attendance
                        </h4>
                    </div>
                    <div class="attendance-page-panel__hint">
                        Load the class list, mark students, then save.
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="attendance-filter-panel">
                    <div class="attendance-filter-row">
                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="att_session_id">Session</label>
                            <select id="att_session_id" class="form-control form-control-sm attendance-filter-control" required>
                                <option value="">Select Session</option>
                                @foreach ($sessions as $session)
                                    <option value="{{ $session->id }}" {{ (string) $selectedSessionId === (string) $session->id ? 'selected' : '' }}>{{ $session->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="classSelect">Class</label>
                            <select id="classSelect" class="form-control form-control-sm attendance-filter-control" required>
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ (string) $selectedClassId === (string) $class->id ? 'selected' : '' }}>{{ $class->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="sectionSelect">Section</label>
                            <select id="sectionSelect" class="form-control form-control-sm attendance-filter-control" required>
                                <option value="">Select Section</option>
                            </select>
                        </div>

                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="att_date">Date</label>
                            <input type="date" id="att_date" class="form-control form-control-sm attendance-filter-control"
                                value="{{ $defaultDate }}" required />
                        </div>

                        <div class="attendance-filter-action">
                            <button type="button" id="btnLoadStudents" class="btn btn-primary btn-sm attendance-filter-btn">
                                Load Students
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="attendanceStudentsWrap" class="attendance-results-slot">
            <div class="attendance-empty-state">
                <div class="attendance-empty-state__icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h5>Load a class to start marking</h5>
                <p>Select a session, class, section, and date, then click <strong>Load Students</strong>.</p>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .attendance-hero {
            align-items: center;
            justify-content: flex-start;
            padding: 1.2rem 1.3rem;
            border-radius: 1.35rem;
            background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
        }

        .attendance-hero__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            margin-right: 1rem;
            border-radius: 0.8rem;
            background: rgba(148, 163, 184, 0.22);
            color: #fff;
            font-size: 1.35rem;
        }

        .attendance-hero__title {
            font-size: 1.35rem;
        }

        .attendance-hero__text {
            margin-top: 0.35rem;
            color: #d1fae5;
            font-size: 0.9rem;
        }

        .attendance-filter-control {
            min-height: 42px;
            height: 42px;
        }

        .attendance-filter-control[type='date'] {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
            line-height: 1.2;
        }

        .attendance-filter-control[type='date']::-webkit-calendar-picker-indicator {
            margin-top: 0;
            margin-bottom: 0;
        }

        .attendance-filter-field .select2-container--default .select2-selection--single {
            min-height: 42px;
            height: 42px;
        }

        .attendance-mark-table {
            width: 100%;
            table-layout: fixed;
            font-size: 0.86rem;
        }

        .attendance-mark-table th,
        .attendance-mark-table td {
            padding: 0.5rem 0.65rem;
            line-height: 1.25;
        }

        .attendance-mark-table th:first-child,
        .attendance-mark-table td:first-child {
            width: 120px;
        }

        .attendance-mark-table th:nth-child(2),
        .attendance-mark-table td:nth-child(2) {
            width: 90px;
        }

        .attendance-mark-table td:last-child {
            overflow-wrap: anywhere;
        }

        .attendance-hero__access {
            display: inline-flex;
        }

        .attendance-hero__pill-row {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 0.55rem;
        }

        .attendance-hero__pill-row .attendance-hero__eyebrow {
            margin-bottom: 0;
        }

        .attendance-hero__access .attendance-hero__chip {
            display: inline-flex;
            width: auto;
            min-height: 0;
            align-items: center;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            gap: 0.35rem;
            line-height: 1.1;
        }

        .attendance-hero__access .attendance-hero__chip-label {
            font-size: 0.65rem;
            line-height: 1.1;
        }

        .attendance-hero__access .attendance-hero__chip-value {
            font-size: 0.72rem;
            line-height: 1.1;
        }

        @media (min-width: 992px) {
            .attendance-mobile-compact .attendance-filter-row {
                grid-template-columns: minmax(150px, 1.15fr) repeat(3, minmax(150px, 1fr)) auto;
                gap: 0.85rem;
            }

            .attendance-mobile-compact .attendance-filter-action .attendance-filter-btn {
                min-width: 126px;
            }

            .attendance-mobile-compact .attendance-page-panel__hint {
                white-space: nowrap;
            }
        }

        @media (max-width: 767.98px) {
            .attendance-mobile-compact {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                gap: 0.55rem;
            }

            .attendance-mobile-compact .attendance-hero {
                flex-direction: row;
                align-items: center;
                padding: 0.7rem;
                border-radius: 1rem;
            }

            .attendance-mobile-compact .attendance-hero__icon {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
                margin-right: 0.7rem;
                border-radius: 0.65rem;
                font-size: 1.1rem;
            }

            .attendance-mobile-compact .attendance-hero__eyebrow {
                margin-bottom: 0;
                padding: 0.2rem 0.55rem;
                font-size: 0.65rem;
                height: 26px;
                box-sizing: border-box;
            }

            .attendance-mobile-compact .attendance-hero__access {
                margin-bottom: 0;
            }

            .attendance-mobile-compact .attendance-hero__pill-row {
                margin-bottom: 0.35rem;
            }

            .attendance-mobile-compact .attendance-hero__access .attendance-hero__chip {
                width: auto;
                justify-content: flex-start;
                min-height: 26px;
                height: 26px;
                padding-top: 0.2rem;
                padding-bottom: 0.2rem;
                box-sizing: border-box;
            }

            .attendance-mobile-compact .attendance-hero__title {
                font-size: 1.1rem;
                white-space: nowrap;
            }

            .attendance-mobile-compact .attendance-hero__copy {
                min-width: 0;
            }

            .attendance-mobile-compact .attendance-hero__text {
                margin-top: 0.25rem;
                font-size: 0.76rem;
                line-height: 1.3;
            }

            .attendance-mobile-compact .attendance-hero__meta {
                gap: 0.35rem;
            }

            .attendance-mobile-compact .attendance-hero__chip,
            .attendance-mobile-compact .attendance-hero__settings {
                width: 100%;
                justify-content: center;
                min-height: 34px;
                padding-top: 0.35rem;
                padding-bottom: 0.35rem;
            }

            .attendance-mobile-compact .attendance-page-panel {
                border-radius: 0.9rem;
            }

            .attendance-mobile-compact .attendance-page-panel__header {
                padding: 0.5rem 0.65rem;
            }

            .attendance-mobile-compact .attendance-page-panel__hint {
                margin-top: 0.2rem;
                font-size: 0.7rem;
            }

            .attendance-mobile-compact .attendance-page-panel .card-body {
                padding: 0.45rem;
            }

            .attendance-mobile-compact .attendance-filter-panel {
                padding: 0.4rem;
                border-radius: 0.8rem;
            }

            .attendance-mobile-compact .attendance-filter-row {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.25rem;
            }

            .attendance-mobile-compact .attendance-filter-label {
                margin-bottom: 0.1rem;
                font-size: 0.66rem;
            }

            .attendance-mobile-compact .attendance-filter-control,
            .attendance-mobile-compact .attendance-filter-field .select2-container--default .select2-selection--single,
            .attendance-mobile-compact .attendance-filter-btn {
                min-height: 34px;
                height: 34px;
                border-radius: 0.55rem !important;
                font-size: 0.78rem;
            }

            .attendance-mobile-compact .attendance-filter-control {
                padding: 0.3rem 0.55rem;
            }

            .attendance-mobile-compact .attendance-filter-control[type='date'] {
                padding: 0.3rem 0.55rem;
                line-height: 1.2;
            }

            .attendance-mobile-compact .attendance-filter-action,
            .attendance-mobile-compact .attendance-filter-action .btn {
                width: 100%;
            }

            .attendance-mobile-compact .attendance-filter-action {
                grid-column: 1 / -1;
            }

            .attendance-mobile-compact .attendance-empty-state,
            .attendance-mobile-compact .attendance-loading-state {
                flex-direction: column;
                padding: 0.9rem 0.7rem;
                text-align: center;
            }

            .attendance-mobile-compact .attendance-results-slot {
                min-height: 0;
                gap: 0.6rem;
            }

            .attendance-mobile-compact .attendance-empty-state__icon {
                width: 2.5rem;
                height: 2.5rem;
                font-size: 1rem;
            }

            .attendance-mobile-compact .attendance-empty-state h5 {
                font-size: 0.9rem;
            }

            .attendance-mobile-compact .attendance-empty-state p {
                max-width: 270px;
                font-size: 0.76rem;
            }

            .attendance-mobile-compact .attendance-empty-state--results {
                justify-content: center;
                text-align: center;
            }

            .attendance-mobile-compact .attendance-results-card {
                border-radius: 0.9rem;
            }

            .attendance-mobile-compact .attendance-results-card__header {
                padding: 0.75rem;
            }

            .attendance-mobile-compact .attendance-mark-table {
                min-width: 480px;
                font-size: 0.8rem;
            }

            .attendance-mobile-compact .attendance-mark-table th,
            .attendance-mobile-compact .attendance-mark-table td {
                padding: 0.55rem 0.6rem;
            }

            .attendance-mobile-compact .attendance-results-card__footer {
                padding: 0.75rem;
            }

            .attendance-mobile-compact .attendance-save-btn {
                width: 100%;
            }
        }
    </style>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        const sectionSelect = document.getElementById('sectionSelect');
        const attendanceStudentsWrap = document.getElementById('attendanceStudentsWrap');
        let attendanceAutoLoadPending = {{ request()->filled('session_id') && request()->filled('school_class_id') && request()->filled('section_id') && request()->filled('date') ? 'true' : 'false' }};
        let attendanceAutoLoadAttempts = 0;

        function getAttendanceCheckboxes() {
            if (!attendanceStudentsWrap) {
                return [];
            }

            return Array.from(attendanceStudentsWrap.querySelectorAll('input[name="present_ids[]"]'));
        }

        function syncSelectAllCheckboxState() {
            const master = document.getElementById('attendanceSelectAll');
            if (!master) {
                return;
            }

            const boxes = getAttendanceCheckboxes();
            if (!boxes.length) {
                master.checked = false;
                master.indeterminate = false;
                return;
            }

            const checkedCount = boxes.filter((box) => box.checked).length;
            master.checked = checkedCount === boxes.length;
            master.indeterminate = checkedCount > 0 && checkedCount < boxes.length;
        }

        function bindAttendanceCheckboxSync() {
            const master = document.getElementById('attendanceSelectAll');
            if (master) {
                master.checked = true;
                master.indeterminate = false;

                master.addEventListener('change', function() {
                    const boxes = getAttendanceCheckboxes();
                    boxes.forEach((box) => {
                        box.checked = master.checked;
                    });
                    syncSelectAllCheckboxState();
                });
            }

            getAttendanceCheckboxes().forEach((box) => {
                box.addEventListener('change', syncSelectAllCheckboxState);
            });

            syncSelectAllCheckboxState();
        }

        function attemptAttendanceAutoLoad() {
            if (!attendanceAutoLoadPending) {
                return;
            }

            attendanceAutoLoadAttempts += 1;
            if (attendanceAutoLoadAttempts > 20) {
                attendanceAutoLoadPending = false;
                return;
            }

            const sessionId = document.getElementById('att_session_id').value;
            const classId = document.getElementById('classSelect').value;
            const sectionId = document.getElementById('sectionSelect').value;
            const date = document.getElementById('att_date').value;

            if (sessionId && classId && sectionId && date) {
                attendanceAutoLoadPending = false;
                document.getElementById('btnLoadStudents').click();
                return;
            }

            setTimeout(attemptAttendanceAutoLoad, 200);
        }

        document.getElementById('classSelect').addEventListener('change', function() {
            const classId = this.value;
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (!classId) return;
            fetch(`{{ route('load_section_groups') }}?school_class_id=${classId}`)
                .then(async r => {
                    if (!r.ok) throw new Error('Failed to load sections');
                    return r.json();
                })
                .then(data => {
                    const sections = Array.isArray(data?.sections) ? data.sections : [];
                    sections.forEach(s => {
                        sectionSelect.insertAdjacentHTML('beforeend',
                            `<option value="${s.id}">${s.name_en}</option>`);
                    });
                    attemptAttendanceAutoLoad();
                })
                .catch(() => {
                    sectionSelect.innerHTML = '<option value="">Select Section</option>';
                });
        });

        document.getElementById('btnLoadStudents').addEventListener('click', function() {
            const sessionId = document.getElementById('att_session_id').value;
            const classId = document.getElementById('classSelect').value;
            const sectionId = document.getElementById('sectionSelect').value;
            const date = document.getElementById('att_date').value;

            if (!attendanceStudentsWrap) {
                return;
            }

            attendanceStudentsWrap.innerHTML = `
                <div class="attendance-loading-state">
                    <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                    <div>
                        <h6 class="mb-1">Loading students</h6>
                        <div class="text-muted small">Fetching the roster for the selected class.</div>
                    </div>
                </div>`;

            const qs = new URLSearchParams({
                session_id: sessionId,
                class_id: classId,
                section_id: sectionId,
                date: date
            });
            fetch(`/teacher/attendance/load?${qs.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async r => {
                    if (r.ok) return {
                        ok: true,
                        html: await r.text()
                    };
                    let msg = 'Failed to load students.';
                    try {
                        const j = await r.json();
                        msg = j.message || msg;
                    } catch (e) {
                        // ignore
                    }
                    return {
                        ok: false,
                        msg,
                        status: r.status
                    };
                })
                .then(res => {
                    if (res.ok) {
                        attendanceStudentsWrap.innerHTML = res.html;
                        bindAttendanceCheckboxSync();
                        return;
                    }
                    attendanceStudentsWrap.innerHTML = `<div class="alert alert-danger mb-0">${res.msg}</div>`;
                });
        });

        bindAttendanceCheckboxSync();
        attemptAttendanceAutoLoad();
    </script>
@endsection
