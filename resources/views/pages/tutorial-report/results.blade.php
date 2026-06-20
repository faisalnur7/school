@extends('layouts.master')

@section('contents')
<div class="col-12 tutorial-report-page">
    @include('partials.report-header')

    <div class="card card-outline mb-4 no-print result-filter-panel tutorial-report-filter-panel">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 tutorial-report-filter-header">
            <h3 class="card-title text-white mb-0"><i class="fas fa-filter mr-2 text-info"></i>Filter Options</h3>
            <small class="text-muted">{{ $exam->name }} &mdash; {{ $exam->academicSession->name_en ?? ($exam->academicSession->name_bn ?? '') }}</small>
        </div>
        <div class="card-body tutorial-report-filter-body">
            <form id="reportFormTop" class="tutorial-report-filter-form" method="GET" action="{{ route('result.tutorial-report.show') }}">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold tutorial-report-filter-label">Academic Session <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control tutorial-report-filter-control" required>
                            <option value="">— Select Session —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$s->id ? 'selected' : '' }}>
                                    {{ $s->name_en ?? $s->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold tutorial-report-filter-label">Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="classSelectTop" class="form-control tutorial-report-filter-control" required>
                            <option value="">— Select Class —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold tutorial-report-filter-label">Section <span class="text-danger">*</span></label>
                        <select name="section_id" id="sectionSelectTop" class="form-control tutorial-report-filter-control" required>
                            <option value="">— Select Section —</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ (string)($filters['section_id'] ?? '') === (string)$section->id ? 'selected' : '' }}>
                                    {{ $section->name_en ?? $section->name_bn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold tutorial-report-filter-label">Tutorial Exam <span class="text-danger">*</span></label>
                        <select name="exam_id" class="form-control tutorial-report-filter-control" required>
                            <option value="">— Select Exam —</option>
                            @foreach($exams as $e)
                                <option value="{{ $e->id }}" {{ (string)($filters['exam_id'] ?? '') === (string)$e->id ? 'selected' : '' }}>
                                    {{ $e->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <label class="font-weight-bold tutorial-report-filter-label">Student ID <small class="text-muted">(optional)</small></label>
                        <input type="text" name="student_id" class="form-control tutorial-report-filter-control" value="{{ $filters['student_id'] ?? '' }}" placeholder="Enter Student ID or CID">
                    </div>
                </div>
                <div class="result-filter-actions mt-2 tutorial-report-filter-actions">
                    <button type="submit" class="btn btn-info result-filter-icon-btn" title="View Report" aria-label="View Report">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="{{ route('result.tutorial-report.index') }}" class="btn btn-secondary result-filter-icon-btn" title="Back" aria-label="Back">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </form>
        </div>
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
            <a href="{{ route('result.tutorial-report.pdf', $filters) }}" target="_blank" class="btn btn-danger btn-sm result-filter-icon-btn" title="PDF" aria-label="PDF">
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

    @foreach($studentsData as $data)
        @php
            $student = $data['student'];
            $academicInfo = $data['academicInfo'] ?? null;
            $rows = $data['rows'];
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
                <div class="ml-auto tutorial-student-total">
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
                <table class="table table-sm table-bordered mb-0 tutorial-report-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th class="text-center" style="width:160px">Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                            <tr class="{{ $r['is_absent'] ? 'table-secondary' : '' }}">
                                <td>{{ $r['subject_name'] }}</td>
                                <td class="text-center">{{ $r['is_absent'] ? 'AB' : number_format($r['obtained'], 1) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No marks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('styles')
<style>
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

@media print {
    body {
        background: #fff !important;
    }

    .no-print,
    .btn,
    .card-header .btn-group {
        display: none !important;
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
