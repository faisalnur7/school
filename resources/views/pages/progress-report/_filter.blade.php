<style>
    .progress-report-filter-card .card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-bottom: 0;
    }

    .progress-report-filter-card .progress-report-filter-card-body {
        background: #fff;
    }

    .progress-report-toolbar {
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
        margin-bottom: 0;
    }

    .progress-report-filter-form {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        padding: 0.9rem;
        border: 1px solid #eef2f7;
        border-radius: 16px;
        background: #fcfcfd;
    }

    .progress-report-filter-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr)) auto;
        gap: 0.75rem;
        align-items: end;
    }

    .progress-report-filter-group label {
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.77rem;
        font-weight: 700;
        color: #6b7280;
    }

    .progress-report-filter-select,
    .progress-report-filter-input {
        width: 100%;
        min-height: 42px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #111827;
        font-size: 0.92rem;
        box-shadow: none;
    }

    .progress-report-filter-select:focus,
    .progress-report-filter-input:focus {
        border-color: #cbd5e1;
        box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
    }

    .progress-report-filter-actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .progress-report-action-btn {
        min-width: 42px;
        min-height: 42px;
        border-radius: 10px;
    }

    .progress-report-action-btn--primary {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .progress-report-action-btn--primary:hover {
        background: #0f172a;
        border-color: #0f172a;
        color: #fff;
    }

    .progress-report-action-btn--ghost {
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
    }

    .progress-report-action-btn--ghost:hover {
        background: #f8fafc;
        color: #111827;
    }

    html[data-theme='dark'] .progress-report-page .progress-report-toolbar,
    html.dark .progress-report-page .progress-report-toolbar {
        background: transparent !important;
        border-color: transparent !important;
        color: #e2e8f0;
        box-shadow: none;
    }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-card-body,
    html.dark .progress-report-page .progress-report-filter-card-body {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0;
    }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-card .card-header,
    html.dark .progress-report-page .progress-report-filter-card .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-form,
    html.dark .progress-report-page .progress-report-filter-form {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-group label,
    html.dark .progress-report-page .progress-report-filter-group label,
    html[data-theme='dark'] .progress-report-page .text-muted,
    html.dark .progress-report-page .text-muted { color: #cbd5e1 !important; }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-select,
    html.dark .progress-report-page .progress-report-filter-select,
    html[data-theme='dark'] .progress-report-page .progress-report-filter-input,
    html.dark .progress-report-page .progress-report-filter-input {
        background: #0f172a !important;
        border-color: #475569 !important;
        color: #f8fafc !important;
    }
    html[data-theme='dark'] .progress-report-page .progress-report-filter-select option,
    html.dark .progress-report-page .progress-report-filter-select option { background: #0f172a; color: #f8fafc; }
    html[data-theme='dark'] .progress-report-page .progress-report-action-btn--ghost,
    html.dark .progress-report-page .progress-report-action-btn--ghost {
        background: #1e293b;
        border-color: #475569;
        color: #e2e8f0;
    }

    @media (max-width: 1280px) {
        .progress-report-filter-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .progress-report-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .progress-report-filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="progress-report-toolbar no-print">
    <form action="{{ route('result.progress-report.index') }}" method="GET" class="progress-report-filter-form"
        id="progressReportForm">
        <div class="progress-report-filter-row">
            <div class="progress-report-filter-group">
                <label for="progressSessionSelect">Academic Session <span class="text-danger">*</span></label>
                <select name="session_id" id="progressSessionSelect" class="form-control progress-report-filter-select"
                    required>
                    <option value="">— Select Session —</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}"
                            {{ (string) ($filters['session_id'] ?? '') === (string) $session->id ? 'selected' : '' }}>
                            {{ $session->name_en ?? $session->name_bn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressClassSelect">Class <span class="text-danger">*</span></label>
                <select name="class_id" id="progressClassSelect" class="form-control progress-report-filter-select"
                    required>
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ (string) ($filters['class_id'] ?? '') === (string) $class->id ? 'selected' : '' }}>
                            {{ $class->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressSectionSelect">Section <span class="text-danger">*</span></label>
                <select name="section_id" id="progressSectionSelect" class="form-control progress-report-filter-select"
                    required>
                    <option value="">— Select Section —</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}"
                            {{ (string) ($filters['section_id'] ?? '') === (string) $section->id ? 'selected' : '' }}>
                            {{ $section->name_en ?? $section->name_bn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressExamSelect">Exam <span class="text-danger">*</span></label>
                <select name="exam_id" id="progressExamSelect" class="form-control progress-report-filter-select"
                    required>
                    <option value="">— Select Exam —</option>
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}" data-exam-type="{{ $exam->type }}"
                            {{ (string) ($filters['exam_id'] ?? '') === (string) $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressStudentInput">Student ID <small class="text-muted">(optional)</small></label>
                <input type="text" name="student_id" id="progressStudentInput"
                    class="form-control progress-report-filter-input" value="{{ $filters['student_id'] ?? '' }}"
                    placeholder="Leave blank for all students">
            </div>

            <div class="progress-report-filter-actions">
                <button type="submit" class="btn progress-report-action-btn progress-report-action-btn--primary"
                    title="View Report" aria-label="View Report">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" id="progressReportPdfBtn" class="btn btn-danger progress-report-action-btn"
                    title="Download PDF" aria-label="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <a href="{{ route('result.progress-report.index') }}"
                    class="btn progress-report-action-btn progress-report-action-btn--ghost" title="Reset"
                    aria-label="Reset">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        var $ = window.jQuery;
        if (typeof $ === 'undefined') {
            return;
        }

        var form = document.getElementById('progressReportForm');
        var sectionSelect = document.getElementById('progressSectionSelect');
        var examSelect = document.getElementById('progressExamSelect');
        var pdfBtn = document.getElementById('progressReportPdfBtn');
        var selectedExamId = @json((string) ($filters['exam_id'] ?? ''));
        var selectedSectionId = @json((string) ($filters['section_id'] ?? ''));

        function refreshSelect2($el) {
            if (window.refreshSelect2) {
                window.refreshSelect2($el);
            }
        }

        function loadSections(classId, selectedSectionId) {
            if (!sectionSelect) {
                return;
            }

            if (!classId) {
                sectionSelect.innerHTML = '<option value="">— Select Section —</option>';
                refreshSelect2($(sectionSelect));
                return;
            }

            sectionSelect.innerHTML = '<option value="">Loading...</option>';
            refreshSelect2($(sectionSelect));

            $.get('/ajax/sections-by-class', { class_id: classId }, function (data) {
                var sections = Array.isArray(data) ? data : ((data && Array.isArray(data.sections)) ? data.sections : []);
                var options = '<option value="">— Select Section —</option>';

                $.each(sections, function (i, section) {
                    var selected = String(selectedSectionId || '') === String(section.id) ? 'selected' : '';
                    options += '<option value="' + section.id + '" ' + selected + '>' + (section.name_en || section.name_bn) + '</option>';
                });

                sectionSelect.innerHTML = options;
                refreshSelect2($(sectionSelect));
            });
        }

        function filterExams(selectedId) {
            if (!examSelect) {
                return;
            }

            var visibleCount = 0;
            Array.prototype.forEach.call(examSelect.options, function (option) {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                var matches = option.dataset.examType === 'term';
                option.hidden = !matches;
                if (matches) {
                    visibleCount++;
                }
            });

            if (selectedId) {
                var selectedOption = examSelect.querySelector('option[value="' + selectedId + '"]');
                if (selectedOption && !selectedOption.hidden) {
                    examSelect.value = selectedId;
                    return;
                }
            }

            examSelect.value = '';
            if (!visibleCount) {
                examSelect.value = '';
            }
        }

        $(document).on('change', '#progressClassSelect', function () {
            loadSections(this.value, null);
        });

        if ($('#progressClassSelect').val()) {
            loadSections($('#progressClassSelect').val(), selectedSectionId);
        }

        filterExams(selectedExamId);

        if (pdfBtn && form) {
            $(pdfBtn).on('click', function () {
                var params = new URLSearchParams(new FormData(form)).toString();
                window.open('{{ route('result.progress-report.pdf') }}?' + params, '_blank');
            });
        }
    });
</script>
