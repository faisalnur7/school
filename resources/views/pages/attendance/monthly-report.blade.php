@extends('layouts.master')

@section('contents')
    <div class="container-fluid attendance-workspace attendance-mobile-compact">
        <div class="rep-head">
            <div class="rep-head__title"><i class="fas fa-calendar-alt"></i> Monthly Attendance Report</div>
            <div class="rep-head__crumb">Review a class's attendance and export it as PDF</div>
        </div>

        <div class="card rep-filters border-0">
            <div class="card-body p-0">
                <div class="attendance-filter-panel">
                    <div class="attendance-filter-row">
                        <div class="attendance-filter-field">
                            <label class="attendance-filter-label" for="rep_session_id">Session</label>
                            <select id="rep_session_id" class="form-control form-control-sm attendance-filter-control" required>
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
                            <label class="attendance-filter-label" for="rep_month">Month</label>
                            <div class="rep-month">
                                <button type="button" class="btn rep-month__nav" id="repMonthPrev" aria-label="Previous month"><i class="fas fa-chevron-left"></i></button>
                                <input type="month" id="rep_month" class="form-control form-control-sm attendance-filter-control"
                                    value="{{ $defaultMonth }}" required />
                                <button type="button" class="btn rep-month__nav" id="repMonthNext" aria-label="Next month"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>

                        <div class="attendance-filter-action">
                            <button type="button" id="btnLoadReport" class="btn btn-sm btn-primary attendance-filter-btn">
                                <i class="fas fa-sync-alt mr-1"></i>Load Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="reportWrap">
            <div class="attendance-empty-state">
                <div class="attendance-empty-state__icon"><i class="fas fa-table"></i></div>
                <h5>Load a report to get started</h5>
                <p>Select a session, class, section and month, then click <strong>Load Report</strong>.</p>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
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
        .rep-month { display: flex; align-items: center; gap: .25rem; }
        .rep-month .form-control { flex: 1; min-width: 0; }
        .rep-month__nav { flex: 0 0 30px; height: 36px; padding: 0; border: 1px solid #e2e8f0; background: #fff; color: #475569; border-radius: .5rem; font-size: .7rem; }
        .rep-month__nav:hover { background: #eff6ff; color: #1d4ed8; }
        .rep-loading { display: flex; align-items: center; justify-content: center; gap: .6rem; padding: 1.2rem 1rem; border-radius: .8rem; background: #fff; color: #64748b; font-size: .85rem; box-shadow: 0 1px 2px rgba(15,23,42,.06); }
        #reportWrap .attendance-empty-state { padding: 1.6rem 1rem; }
        @media (min-width: 992px) {
            .rep-filters .attendance-filter-row { grid-template-columns: repeat(3, minmax(130px, 1fr)) minmax(210px, 1.2fr) auto; }
        }
        @media (max-width: 767.98px) {
            .rep-head__title { font-size: 1rem; }
            .rep-head__crumb { display: none; }
            .rep-filters .attendance-filter-row { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .4rem; }
            .rep-filters .attendance-filter-field:nth-child(3), .rep-filters .attendance-filter-field:nth-child(4), .rep-filters .attendance-filter-action { grid-column: 1 / -1; }
            .rep-filters .attendance-filter-action .btn { width: 100%; }
        }
    </style>
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

        function shiftMonth(delta) {
            if (!repMonthInput || !repMonthInput.value) return;
            const [y, m] = repMonthInput.value.split('-').map(Number);
            const d = new Date(y, m - 1 + delta, 1);
            repMonthInput.value = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
            if (repSectionSelect?.value && repClassSelect?.value && repSessionSelect?.value) {
                loadReportBtn?.click();
            }
        }
        document.getElementById('repMonthPrev')?.addEventListener('click', () => shiftMonth(-1));
        document.getElementById('repMonthNext')?.addEventListener('click', () => shiftMonth(1));

        function exportPdf(btn) {
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Generating...';
            const qs = new URLSearchParams(currentParams());
            fetch(`/teacher/attendance/report/monthly/pdf?${qs}`)
                .then(r => {
                    if (!r.ok) throw new Error('Failed to generate PDF.');
                    return r.blob();
                })
                .then(blob => {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `attendance_${repMonthInput.value}.pdf`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    setTimeout(() => URL.revokeObjectURL(url), 2000);
                })
                .catch(err => alert(err.message))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = original;
                });
        }

        if (loadReportBtn) {
            loadReportBtn.addEventListener('click', function() {
                const p = currentParams();
                const wrap = document.getElementById('reportWrap');
                if (!wrap) return;

                if (!p.session_id || !p.class_id || !p.section_id || !p.month) {
                    wrap.innerHTML = `<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle mr-2"></i>Please select session, class, section and month first.</div>`;
                    return;
                }

                wrap.innerHTML = `<div class="rep-loading"><div class="spinner-border spinner-border-sm text-primary" role="status"></div><span>Loading report...</span></div>`;

                const qs = new URLSearchParams(p);
                fetch(`/teacher/attendance/report/monthly/load?${qs}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(async r => {
                        if (r.ok) return { ok: true, html: await r.text() };
                        let msg = 'Failed to load report.';
                        try {
                            const j = await r.json();
                            msg = j.message || msg;
                        } catch (e) {}
                        return { ok: false, msg };
                    })
                    .then(res => {
                        wrap.innerHTML = res.ok ? res.html :
                            `<div class="alert alert-danger mb-0">${res.msg}</div>`;
                        const pdfBtn = document.getElementById('btnDownloadPdf');
                        if (pdfBtn) {
                            pdfBtn.addEventListener('click', () => exportPdf(pdfBtn));
                        }
                    })
                    .catch(() => {
                        wrap.innerHTML = `<div class="alert alert-danger mb-0">Network error. Please try again.</div>`;
                    });
            });
        }
    </script>
@endsection
