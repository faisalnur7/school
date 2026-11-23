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
        <div class="rep-head">
            <div class="rep-head__title"><i class="fas fa-clipboard-check"></i> Student Attendance</div>
            <div class="rep-head__crumb">Load the class list, mark students, then save.</div>
        </div>

        <div class="card rep-filters border-0">
            <div class="card-body p-0">
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
                min-height: 40px;
                height: 40px;
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

        /* ---- Design refresh ---- */
        .attendance-hero { box-shadow: 0 10px 30px -12px rgba(15, 23, 42, .45); }
        .attendance-results-card { border-radius: 1.1rem; overflow: hidden; }
        .attendance-results-card__header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .6rem; }
        .attendance-summary { display: flex; flex-wrap: wrap; gap: .4rem; }
        .attendance-summary__chip { display: inline-flex; align-items: center; gap: .35rem; padding: .25rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 600; background: #eff6ff; color: #1d4ed8; }
        .attendance-summary__chip--present { background: #dcfce7; color: #15803d; }
        .attendance-summary__chip--absent { background: #fee2e2; color: #b91c1c; }
        .attendance-table-wrap { max-height: 65vh; overflow: auto; }
        .attendance-mark-table thead th { position: sticky; top: 0; z-index: 2; background: #f8fafc; text-transform: uppercase; font-size: .72rem; letter-spacing: .04em; color: #475569; }
        .attendance-mark-table th:first-child, .attendance-mark-table td:first-child { width: 130px; }
        .attendance-row { cursor: pointer; transition: background .15s; }
        .attendance-row.is-absent { background: #fef2f2 !important; }
        .attendance-row.is-absent .attendance-row__name { color: #b91c1c; }
        .attendance-mark-table input[type=checkbox] { width: 20px; height: 20px; cursor: pointer; accent-color: #16a34a; vertical-align: middle; }
        .attendance-select-all { display: inline-flex; align-items: center; gap: .4rem; cursor: pointer; }
        .attendance-results-card__footer { display: flex; align-items: center; justify-content: flex-end; gap: 1rem; padding: .85rem 1rem; background: #fff; border-top: 1px solid #e2e8f0; }
        .attendance-results-card__hint { color: #64748b; font-size: .8rem; }
        .attendance-save-btn { min-width: 130px; font-weight: 600; border-radius: .6rem; }
        @media (max-width: 767.98px) {
            .attendance-mobile-compact .attendance-mark-table { min-width: 0; table-layout: auto; display: block; }
            .attendance-mobile-compact .attendance-mark-table thead,
            .attendance-mobile-compact .attendance-mark-table tbody { display: block; }
            .attendance-mobile-compact .attendance-table-wrap { max-height: none; }
            .attendance-mobile-compact .attendance-mark-table thead tr { display: block; }
            .attendance-mobile-compact .attendance-mark-table thead th:nth-child(n+2) { display: none; }
            .attendance-mobile-compact .attendance-mark-table thead th:first-child { display: block; width: 100%; text-align: left !important; padding: .5rem .85rem; }
            .attendance-mobile-compact .attendance-mark-table tr.attendance-row {
                display: grid; grid-template-columns: 1.5rem max-content max-content 1fr;
                grid-template-areas: "check name name name" "check roll sid .";
                align-items: center; column-gap: .45rem; row-gap: .2rem;
                padding: .5rem .85rem; border-bottom: 1px solid #eef2f7;
            }
            .attendance-mobile-compact .attendance-mark-table tbody td {
                display: block; border: 0 !important; padding: 0 !important; width: auto !important;
                height: auto; min-height: 0; line-height: 1.25;
            }
            .attendance-mobile-compact .attendance-row__check { grid-area: check; align-self: center; text-align: left !important; }
            .attendance-mobile-compact .attendance-row__check input { display: block; margin: 0; }
            .attendance-mobile-compact .attendance-row__name { grid-area: name; font-weight: 700; font-size: .85rem; }
            .attendance-mobile-compact .attendance-row__roll,
            .attendance-mobile-compact .attendance-row__sid {
                font-size: .68rem; font-weight: 600; color: #475569; background: #f1f5f9;
                padding: .1rem .45rem !important; border-radius: 999px; white-space: nowrap;
            }
            .attendance-mobile-compact .attendance-row__roll { grid-area: roll; }
            .attendance-mobile-compact .attendance-row__sid { grid-area: sid; }
            .attendance-mobile-compact .attendance-row__roll::before { content: 'Roll '; font-weight: 500; color: #64748b; }
            .attendance-mobile-compact .attendance-row__sid::before { content: 'CID '; font-weight: 500; color: #64748b; }
            .attendance-mobile-compact .attendance-row.is-absent .attendance-row__roll,
            .attendance-mobile-compact .attendance-row.is-absent .attendance-row__sid { background: #fee2e2; }
            .attendance-mobile-compact .attendance-mark-table input[type=checkbox] { width: 20px; height: 20px; }
            .attendance-mobile-compact .attendance-results-card__footer { position: sticky; bottom: 0; z-index: 5; box-shadow: 0 -6px 16px -8px rgba(15,23,42,.25); }
        }

        /* ---- Compact layout ---- */
        .rep-head { display: flex; flex-wrap: wrap; align-items: baseline; gap: .15rem .9rem; margin-bottom: .6rem; }
        .rep-head__title { font-size: 1.1rem; font-weight: 700; color: #0f172a; }
        .rep-head__title i { color: #2563eb; margin-right: .4rem; }
        .rep-head__crumb { font-size: .78rem; color: #64748b; }
        .rep-filters { border-radius: .8rem; box-shadow: 0 1px 2px rgba(15,23,42,.06); margin-bottom: .75rem; }
        .rep-filters .attendance-filter-panel { padding: .65rem .75rem; border: 0; background: transparent; box-shadow: none; }
        .rep-filters .attendance-filter-row { gap: .6rem; align-items: end; }
        .rep-filters .attendance-filter-label { margin-bottom: .15rem; font-size: .66rem; letter-spacing: .04em; text-transform: uppercase; color: #64748b; }
        .attendance-filter-control, .attendance-filter-btn { min-height: 36px; height: 36px; font-size: .82rem; border-radius: .5rem; }
        .attendance-filter-field .select2-container--default .select2-selection--single { min-height: 36px; height: 36px; }
        .attendance-results-card { border-radius: .8rem; }
        .attendance-results-card__header { padding: .55rem .75rem; }
        .attendance-results-card__eyebrow { display: none; }
        .attendance-results-card__title { font-size: .9rem; }
        .attendance-mark-table th, .attendance-mark-table td { padding: .3rem .6rem; }
        .attendance-mark-table { font-size: .82rem; }
        .attendance-table-wrap { max-height: 68vh; }
        .attendance-results-card__footer { padding: .55rem .75rem; }
        .attendance-save-btn { min-width: 110px; padding: .3rem 1rem; font-size: .85rem; }
        .attendance-summary__chip { padding: .15rem .55rem; font-size: .7rem; }
        .attendance-empty-state { padding: 1.6rem 1rem; }
        @media (min-width: 992px) {
            .rep-filters .attendance-filter-row { grid-template-columns: repeat(3, minmax(130px, 1fr)) minmax(180px, 1fr) auto; }
        }
        @media (max-width: 767.98px) {
            .rep-head__title { font-size: 1rem; }
            .rep-head__crumb { display: none; }
            .rep-filters .attendance-filter-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .4rem; }
            .rep-filters .attendance-filter-action { grid-column: 1 / -1; }
            .rep-filters .attendance-filter-action .btn { width: 100%; }
            .attendance-mobile-compact .attendance-mark-table tr.attendance-row { padding: .3rem .75rem; }
        }
        .content-wrapper .attendance-mark-table td,
        .content-wrapper .attendance-mark-table th { padding: .3rem .75rem !important; }
        .attendance-mark-table th:nth-child(3), .attendance-mark-table td:nth-child(3) { width: 150px; }
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

        function updateAttendanceSummary(present, total) {
            const p = document.getElementById('attPresentCount');
            const a = document.getElementById('attAbsentCount');
            if (p) p.textContent = present;
            if (a) a.textContent = total - present;
            getAttendanceCheckboxes().forEach((box) => {
                const row = box.closest('tr');
                if (row) row.classList.toggle('is-absent', !box.checked);
            });
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
            updateAttendanceSummary(checkedCount, boxes.length);
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
                const row = box.closest('tr');
                if (row) {
                    row.addEventListener('click', function(e) {
                        if (e.target.tagName === 'INPUT') return;
                        box.checked = !box.checked;
                        syncSelectAllCheckboxState();
                    });
                }
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
