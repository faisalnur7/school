@extends('layouts.master')

@section('contents')
    <div class="container-fluid">


        <div class="card card-outline card-primary mb-3">
            <div class="card-header d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0 font-weight-bold text-white">
                    <i class="fas fa-calendar-alt text-primary mr-2"></i>Monthly Attendance Report
                </h4>
            </div>
            <div class="card-body py-2">
                <div class="d-flex flex-wrap align-items-end">
                    <div class="p-1" style="flex: 1; min-width: 180px;">
                        <select id="rep_session_id" class="form-control form-control-sm mr-2 mb-1" required>
                            <option value="">Select Session</option>
                            @foreach ($sessions as $session)
                                <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-1" style="flex: 1; min-width: 140px;">
                        <select id="classSelect" class="form-control form-control-sm mr-2 mb-1" required>
                            <option value="">Select Class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-1" style="flex: 1; min-width: 140px;">

                        <select id="sectionSelect" class="form-control form-control-sm mr-2 mb-1" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>

                    <div class="p-1" style="flex: 1; min-width: 160px;">
                        <input type="month" id="rep_month" class="form-control form-control-sm mr-2 mb-1"
                            value="{{ $defaultMonth }}" required />
                    </div>
                    <div class="p-1" style="min-width: 140px;">
                        <button type="button" id="btnLoadReport" class="btn btn-sm btn-primary mb-1">
                            <i class="fas fa-sync-alt mr-1"></i>Load Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="reportWrap"></div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
    <script>
        const repClassSelect = document.getElementById('classSelect');
        const repSectionSelect = document.getElementById('sectionSelect');
        const repSessionSelect = document.getElementById('rep_session_id');
        const repMonthInput = document.getElementById('rep_month');
        const loadReportBtn = document.getElementById('btnLoadReport');

        if (repClassSelect && repSectionSelect) {
            repClassSelect.addEventListener('change', function() {
            const classId = this.value;
            repSectionSelect.innerHTML = '<option value="">Select Section</option>';
            if (!classId) return;
            fetch(`{{ route('load_section_groups') }}?school_class_id=${classId}`)
                .then(async r => {
                    if (!r.ok) throw new Error('Failed to load sections');
                    return r.json();
                })
                .then(data => {
                    const sections = Array.isArray(data?.sections) ? data.sections : [];
                    sections.forEach(s => {
                        repSectionSelect.insertAdjacentHTML('beforeend',
                            `<option value="${s.id}">${s.name_en}</option>`);
                    });
                })
                .catch(() => {
                    repSectionSelect.innerHTML = '<option value="">Select Section</option>';
                });
            });
        }

        function currentParams() {
            return {
                session_id: repSessionSelect?.value || '',
                class_id: repClassSelect?.value || '',
                section_id: repSectionSelect?.value || '',
                month: repMonthInput?.value || '',
            };
        }

        if (loadReportBtn) {
            loadReportBtn.addEventListener('click', function() {
            const p = currentParams();
            const wrap = document.getElementById('reportWrap');
            if (!wrap) return;
            wrap.innerHTML = `<div class="card card-body text-center text-muted py-4">Loading...</div>`;

            const qs = new URLSearchParams(p);
            fetch(`/teacher/attendance/report/monthly/load?${qs}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async r => {
                    if (r.ok) return {
                        ok: true,
                        html: await r.text()
                    };
                    let msg = 'Failed to load report.';
                    try {
                        const j = await r.json();
                        msg = j.message || msg;
                    } catch (e) {}
                    return {
                        ok: false,
                        msg
                    };
                })
                .then(res => {
                    wrap.innerHTML = res.ok ? res.html :
                        `<div class="alert alert-danger mb-0">${res.msg}</div>`;

                    // Wire PDF button rendered inside the partial
                    const pdfBtn = document.getElementById('btnDownloadPdf');
                    if (pdfBtn) {
                        pdfBtn.addEventListener('click', function() {
                            const qs = new URLSearchParams(currentParams());
                            window.location.href = `/teacher/attendance/report/monthly/pdf?${qs}`;
                        });
                    }
                });
            });
        }
    </script>
@endsection
