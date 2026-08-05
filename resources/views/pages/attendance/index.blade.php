@extends('layouts.master')

@section('contents')
    <div class="container-fluid attendance-workspace">
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
            <div class="attendance-hero__copy">
                <span class="attendance-hero__eyebrow">Teacher Attendance</span>
                <h1 class="attendance-hero__title">Mark Daily Attendance</h1>
                <p class="attendance-hero__text">
                    Pick a session, class, section, and date to load students and record presence in one pass.
                </p>
            </div>
            <div class="attendance-hero__meta">
                <div class="attendance-hero__chip {{ $isOpenForAll ? 'is-open' : 'is-restricted' }}">
                    <span class="attendance-hero__chip-label">Access</span>
                    <span class="attendance-hero__chip-value">
                        {{ $isOpenForAll ? 'Open for all users' : 'Assigned class only' }}
                    </span>
                </div>
                <a href="{{ route('attendance.settings.index') }}" class="btn btn-light btn-sm attendance-hero__settings">
                    <i class="fas fa-cog mr-1"></i>Settings
                </a>
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
                                    <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="classSelect">Class</label>
                            <select id="classSelect" class="form-control form-control-sm attendance-filter-control" required>
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name_en }}</option>
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

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        const sectionSelect = document.getElementById('sectionSelect');
        const attendanceStudentsWrap = document.getElementById('attendanceStudentsWrap');

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
    </script>
@endsection
