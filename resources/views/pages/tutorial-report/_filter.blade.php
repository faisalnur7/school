<style>
    .tutorial-report-toolbar {
        background: #ffffff;
        border: 1px solid #e7e5e4;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        padding: 0.9rem;
        margin-bottom: 1rem;
    }

    .tutorial-report-filter-form {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .tutorial-report-filter-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr)) auto;
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

    @media (max-width: 1280px) {
        .tutorial-report-filter-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .tutorial-report-filter-actions {
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .tutorial-report-filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="tutorial-report-toolbar no-print">
    <form action="{{ route('result.tutorial-report.index') }}" method="GET" class="tutorial-report-filter-form" id="tutorialReportForm">
        <div class="tutorial-report-filter-row">
            <div class="tutorial-report-filter-group">
                <label for="tutorialSessionSelect">Academic Session <span class="text-danger">*</span></label>
                <select name="session_id" id="tutorialSessionSelect" class="form-control tutorial-report-filter-select" required>
                    <option value="">— Select Session —</option>
                    @foreach ($sessions as $session)
                        <option value="{{ $session->id }}" {{ (string)($filters['session_id'] ?? '') === (string)$session->id ? 'selected' : '' }}>
                            {{ $session->name_en ?? $session->name_bn }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="tutorial-report-filter-group">
                <label for="tutorialClassSelect">Class <span class="text-danger">*</span></label>
                <select name="class_id" id="tutorialClassSelect" class="form-control tutorial-report-filter-select" required>
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ (string)($filters['class_id'] ?? '') === (string)$class->id ? 'selected' : '' }}>
                            {{ $class->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="tutorial-report-filter-group">
                <label for="tutorialSectionSelect">Section <span class="text-danger">*</span></label>
                <select name="section_id" id="tutorialSectionSelect" class="form-control tutorial-report-filter-select" required>
                    <option value="">— Select Section —</option>
                </select>
            </div>

            <div class="tutorial-report-filter-group">
                <label for="tutorialExamSelect">Exam <span class="text-danger">*</span></label>
                <select name="exam_id" id="tutorialExamSelect" class="form-control tutorial-report-filter-select" required>
                    <option value="">— Select Exam —</option>
                    @foreach ($exams as $exam)
                        <option value="{{ $exam->id }}" {{ (string)($filters['exam_id'] ?? '') === (string)$exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
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
                <button type="button" id="tutorialReportPdfBtn" class="btn btn-danger tutorial-report-action-btn" title="Download PDF" aria-label="Download PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <a href="{{ route('result.tutorial-report.index') }}" class="btn tutorial-report-action-btn tutorial-report-action-btn--ghost" title="Reset" aria-label="Reset">
                    <i class="fas fa-undo-alt"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
$(function () {
    var $ = window.jQuery;
    if (typeof $ === 'undefined') {
        return;
    }

    var form = document.getElementById('tutorialReportForm');
    var classSelect = document.getElementById('tutorialClassSelect');
    var sectionSelect = document.getElementById('tutorialSectionSelect');
    var pdfBtn = document.getElementById('tutorialReportPdfBtn');
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

    if (classSelect) {
        $(classSelect).on('change', function () {
            loadSections($(this).val(), null);
        });
    }

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSectionId);
    }

    if (pdfBtn && form) {
        $(pdfBtn).on('click', function () {
            var params = $(form).serialize();
            window.open('{{ route('result.tutorial-report.pdf') }}?' + params, '_blank');
        });
    }
});
</script>
