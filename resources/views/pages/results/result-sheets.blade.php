@extends('layouts.master')

@section('contents')
    <style>
        .result-sheets-page { padding: 12px 16px 24px; color: #1f2937; }
        .result-sheets-card { border: 1px solid #dfe7f1; border-top: 3px solid #2563eb; border-radius: 14px; box-shadow: 0 4px 16px rgba(15, 23, 42, .06); overflow: hidden; }
        .result-sheets-card .card-header { padding: 14px 18px; background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%); border-bottom: 1px solid #dbeafe; }
        .result-sheets-card .card-body { padding: 18px; }
        .result-sheets-title { color: #172554; font-size: 20px; font-weight: 800; }
        .result-sheets-label { color: #334155; font-weight: 700; }
        .result-sheets-select { height: 42px; border: 1px solid #cbd5e1; border-radius: 9px; font-weight: 600; }
        .result-sheets-select:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .result-sheets-actions { border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 16px; }
        .result-sheets-choice-label { display: block; margin-bottom: 8px; color: #1e293b; font-weight: 700; }
        .result-sheets-choice { margin: 0 6px 6px 0; border-radius: 8px; font-weight: 700; }
        .result-sheets-exams { display: flex; flex-wrap: wrap; gap: 8px; }
        .result-sheets-exam-card { min-width: 170px; padding: 10px 14px; border: 1px solid #2563eb; border-radius: 9px; background: #fff; color: #1d4ed8; font-weight: 700; text-align: left; transition: .15s ease; }
        .result-sheets-exam-card:hover, .result-sheets-exam-card.is-selected { background: #2563eb; color: #fff; box-shadow: 0 4px 10px rgba(37, 99, 235, .18); }
        .result-sheets-exam-card small { display: block; margin-top: 2px; opacity: .75; font-weight: 600; }
    </style>

    <div class="container-fluid result-sheets-page">
        <div class="card result-sheets-card">
            <div class="card-header d-flex align-items-center">
                <a href="{{ route('results.hub') }}" class="btn btn-sm btn-secondary mr-3">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
                <div>
                    <div class="result-sheets-title"><i class="fas fa-table text-primary mr-2"></i>Result Sheets</div>
                    <small class="text-muted">Select an academic session and exam to view the result sheet.</small>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('results.result-sheets') }}" id="resultSheetsForm">
                    <input type="hidden" name="session_id" id="resultSession" value="{{ $sessionId }}">
                    <input type="hidden" name="exam_type" id="resultExamType" value="{{ $examType }}">
                    <input type="hidden" name="exam_id" id="resultExam" value="{{ $examId }}" data-exams-url="{{ route('results.result-sheets.exams', [], false) }}">

                    <div class="mb-3">
                        <span class="result-sheets-choice-label">Select Academic Session:</span>
                        <div>
                            @foreach ($sessions as $session)
                                <button type="button" class="btn result-sheets-choice {{ $sessionId == $session->id ? 'btn-primary' : 'btn-outline-primary' }}" data-session-id="{{ $session->id }}">
                                    {{ $session->name_en ?? $session->name_bn }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3 {{ $sessionId ? '' : 'd-none' }}" id="resultExamTypeChoices">
                        <span class="result-sheets-choice-label">Select Exam Type:</span>
                        <div>
                            @foreach ($examTypes as $type => $label)
                                <button type="button" class="btn result-sheets-choice {{ $examType === $type ? 'btn-primary' : 'btn-outline-primary' }}" data-exam-type="{{ $type }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3 {{ $sessionId && $examType ? '' : 'd-none' }}" id="resultExamChoices">
                        <span class="result-sheets-choice-label">Select Exam:</span>
                        <div class="result-sheets-exams" id="resultExamCards">
                            @foreach ($exams as $exam)
                                <button type="button" class="result-sheets-exam-card {{ $examId == $exam->id ? 'is-selected' : '' }}" data-exam-id="{{ $exam->id }}">
                                    {{ $exam->name }}
                                    <small>{{ $exam->exam_category === 'terminal' ? 'Terminal Exam' : 'Tutorial Exam' }}</small>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="result-sheets-actions">
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>After selecting an exam, choose Class, Section, and Group where applicable.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('resultSheetsForm');
            const session = document.getElementById('resultSession');
            const type = document.getElementById('resultExamType');
            const exam = document.getElementById('resultExam');
            const typeChoices = document.getElementById('resultExamTypeChoices');
            const examChoices = document.getElementById('resultExamChoices');
            const examCards = document.getElementById('resultExamCards');
            const examsUrl = exam?.dataset.examsUrl;

            function renderExamOptions(exams, selectedId = '') {
                if (!examCards) return;
                let html = '';
                (Array.isArray(exams) ? exams : []).forEach((item) => {
                    const selected = String(selectedId) === String(item.id) ? 'is-selected' : '';
                    const label = item.exam_category === 'terminal' ? 'Terminal Exam' : 'Tutorial Exam';
                    html += `<button type="button" class="result-sheets-exam-card ${selected}" data-exam-id="${item.id}">${item.name}<small>${label}</small></button>`;
                });
                examCards.innerHTML = html || '<span class="text-muted">No exams found for this selection.</span>';
                examChoices?.classList.toggle('d-none', !session?.value || !type?.value);
            }

            function loadExamOptions(selectedId = '') {
                if (!session?.value || !type?.value) {
                    renderExamOptions([]);
                    return;
                }

                if (examChoices) examChoices.classList.remove('d-none');
                if (examCards) examCards.innerHTML = '<span class="text-muted">Loading exams...</span>';
                fetch(`${examsUrl}?session_id=${encodeURIComponent(session.value)}&exam_type=${encodeURIComponent(type.value)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to load exams');
                        return response.json();
                    })
                    .then(data => renderExamOptions(data?.exams || [], selectedId))
                    .catch(() => renderExamOptions([]));
            }

            document.addEventListener('click', (event) => {
                const sessionButton = event.target.closest('[data-session-id]');
                const typeButton = event.target.closest('[data-exam-type]');
                const examButton = event.target.closest('[data-exam-id]');

                if (sessionButton) {
                    session.value = sessionButton.dataset.sessionId;
                    type.value = '';
                    exam.value = '';
                    typeChoices?.classList.remove('d-none');
                    examChoices?.classList.add('d-none');
                    document.querySelectorAll('[data-session-id]').forEach((button) => button.classList.toggle('btn-primary', button === sessionButton));
                    document.querySelectorAll('[data-session-id]').forEach((button) => button.classList.toggle('btn-outline-primary', button !== sessionButton));
                } else if (typeButton) {
                    type.value = typeButton.dataset.examType;
                    exam.value = '';
                    document.querySelectorAll('[data-exam-type]').forEach((button) => button.classList.toggle('btn-primary', button === typeButton));
                    document.querySelectorAll('[data-exam-type]').forEach((button) => button.classList.toggle('btn-outline-primary', button !== typeButton));
                    loadExamOptions();
                } else if (examButton) {
                    exam.value = examButton.dataset.examId;
                    form?.submit();
                }
            });

            if (session?.value && type?.value) {
                loadExamOptions(exam.value);
            }
        })();
    </script>
@endsection
