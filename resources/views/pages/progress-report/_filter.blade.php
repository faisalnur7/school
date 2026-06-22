<style>
    .progress-report-toolbar {
        background: #ffffff;
        border: 1px solid #e7e5e4;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        padding: 0.9rem;
        margin-bottom: 1rem;
    }

    .progress-report-filter-form {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
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
        min-height: 46px;
        border-radius: 12px;
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
        min-width: 46px;
        min-height: 46px;
        border-radius: 12px;
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
    <form action="{{ route('result.progress-report.index') }}" method="GET" class="progress-report-filter-form" id="progressReportForm">
        <div class="progress-report-filter-row">
            <div class="progress-report-filter-group">
                <label for="progressSessionSelect">Academic Session <span class="text-danger">*</span></label>
                <select name="session_id" id="progressSessionSelect" class="form-control progress-report-filter-select" required>
                    <option value="">— Select Session —</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$session->id ? 'selected' : '' }}>
                            {{ $session->name_en ?? $session->name_bn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressClassSelect">Class <span class="text-danger">*</span></label>
                <select name="class_id" id="progressClassSelect" class="form-control progress-report-filter-select" required>
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$class->id ? 'selected' : '' }}>
                            {{ $class->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressSectionSelect">Section <span class="text-danger">*</span></label>
                <select name="section_id" id="progressSectionSelect" class="form-control progress-report-filter-select" required>
                    <option value="">— Select Section —</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" {{ (string)($filters['section_id'] ?? '') === (string)$section->id ? 'selected' : '' }}>
                            {{ $section->name_en ?? $section->name_bn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressExamSelect">Exam <span class="text-danger">*</span></label>
                <select name="exam_id" id="progressExamSelect" class="form-control progress-report-filter-select" required>
                    <option value="">— Select Exam —</option>
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}"
                            data-exam-type="{{ $exam->type }}"
                            {{ (string)($filters['exam_id'] ?? '') === (string)$exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="progress-report-filter-group">
                <label for="progressStudentInput">Student ID <small class="text-muted">(optional)</small></label>
                <input type="text" name="student_id" id="progressStudentInput" class="form-control progress-report-filter-input" value="{{ $filters['student_id'] ?? '' }}" placeholder="Leave blank for all students">
            </div>

            <div class="progress-report-filter-actions">
                <button type="submit" class="btn progress-report-action-btn progress-report-action-btn--primary" title="View Report" aria-label="View Report">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" id="progressReportPdfBtn" class="btn btn-danger progress-report-action-btn" title="Download PDF" aria-label="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <a href="{{ route('result.progress-report.index') }}" class="btn progress-report-action-btn progress-report-action-btn--ghost" title="Reset" aria-label="Reset">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var $ = window.jQuery;
        var form = document.getElementById('progressReportForm');
        if (!form || typeof $ === 'undefined') {
            return;
        }

        var classSelect = document.getElementById('progressClassSelect');
        var sectionSelect = document.getElementById('progressSectionSelect');
        var examSelect = document.getElementById('progressExamSelect');
        var pdfBtn = document.getElementById('progressReportPdfBtn');
        var selectedExamId = @json((string) ($filters['exam_id'] ?? ''));

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
                var options = '<option value="">— Select Section —</option>';
                $.each(data, function (i, section) {
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

        if (classSelect) {
            classSelect.addEventListener('change', function () {
                loadSections(this.value, null);
            });
        }

        if (classSelect && classSelect.value) {
            loadSections(classSelect.value, @json($filters['section_id'] ?? null));
        }

        filterExams(selectedExamId);

        if (pdfBtn) {
            pdfBtn.addEventListener('click', function () {
                var params = new URLSearchParams(new FormData(form)).toString();
                window.open('{{ route('result.progress-report.pdf') }}?' + params, '_blank');
            });
        }
    });
})();
</script>
