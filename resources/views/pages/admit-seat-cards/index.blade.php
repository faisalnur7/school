@extends('layouts.master')

@section('contents')
<div class="container-fluid admit-seat-cards-page">
    <div class="card card-outline no-print result-filter-panel admit-seat-cards-filter-panel">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 admit-seat-cards-filter-header">
            <div class="flex flex-col">
                <h3 class="card-title mb-0 text-dark">
                    <i class="fas fa-filter mr-2 text-info"></i>Filter Options
                </h3>
                <small class="text-muted">Generate admit or seat cards by year, class, exam, and layout.</small>
            </div>
        </div>
        <div class="card-body admit-seat-cards-filter-body">
            <form method="GET" action="{{ route('results.admit-seat-cards.index') }}" id="filterForm" class="admit-seat-cards-filter-form">
                <div class="admit-seat-cards-filter-grid">
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Academic Year <span class="text-danger">*</span></label>
                        <select name="session_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">— Select Year —</option>
                            @foreach($sessions as $s)
                                <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Class</label>
                        <select name="class_id" id="classSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Section</label>
                        <select name="section_id" id="sectionSelect" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Sections</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Group</label>
                        <select name="group_id" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="">All Groups</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Type</label>
                        <select name="exam_type" class="form-control form-control-sm admit-seat-cards-filter-control" id="examTypeSelect">
                            <option value="">All Types</option>
                            <option value="tutorial" {{ ($examType ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                            <option value="term" {{ ($examType ?? '') === 'term' ? 'selected' : '' }}>Terminal Exam</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Exam Name</label>
                        <select name="exam_id" class="form-control form-control-sm admit-seat-cards-filter-control" id="examSelect">
                            <option value="">All Exams</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->name }} ({{ $exam->type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Card Type</label>
                        <select name="card_type" class="form-control form-control-sm admit-seat-cards-filter-control">
                            <option value="admit_card" {{ ($cardType ?? 'admit_card') === 'admit_card' ? 'selected' : '' }}>Admit Card</option>
                            <option value="seat_card" {{ ($cardType ?? 'admit_card') === 'seat_card' ? 'selected' : '' }}>Seat Card</option>
                        </select>
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Cards / Page</label>
                        <input
                            type="number"
                            name="cards_per_page"
                            class="form-control form-control-sm admit-seat-cards-filter-control"
                            min="1"
                            max="12"
                            value="{{ $layout['cardsPerPage'] ?? request('cards_per_page', 8) }}">
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Cards / Row</label>
                        <input
                            type="number"
                            name="cards_per_row"
                            class="form-control form-control-sm admit-seat-cards-filter-control"
                            min="1"
                            max="10"
                            value="{{ $layout['cardsPerRow'] ?? request('cards_per_row', 2) }}">
                    </div>
                    <div class="admit-seat-cards-filter-group">
                        <label class="font-weight-bold admit-seat-cards-filter-label">Student ID</label>
                        <input
                            type="text"
                            name="student_cid"
                            class="form-control form-control-sm admit-seat-cards-filter-control"
                            value="{{ request('student_cid') }}"
                            placeholder="Enter Student ID"
                            autocomplete="off">
                    </div>
                    <div class="admit-seat-cards-filter-actions">
                        <button type="submit" class="btn btn-dark btn-sm result-filter-icon-btn" title="Generate" aria-label="Generate">
                            <i class="fas fa-id-card"></i>
                        </button>
                        <a href="{{ route('results.admit-seat-cards.index') }}" class="btn btn-outline-secondary btn-sm result-filter-icon-btn" title="Reset" aria-label="Reset">
                            <i class="fas fa-times"></i>
                        </a>
                        @if($students->isNotEmpty())
                            <button type="button" class="btn btn-success btn-sm result-filter-icon-btn" onclick="window.print()" title="Print" aria-label="Print">
                                <i class="fas fa-print"></i>
                            </button>
                            <a href="{{ route('results.admit-seat-cards.pdf', request()->query()) }}" class="btn btn-danger btn-sm result-filter-icon-btn" target="_blank" title="PDF" aria-label="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('session_id') && !request('student_cid'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year or enter a Student ID to generate admit or seat cards.</p>
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">{{ $students->count() }} Students</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $layout['cardsPerPage'] ?? 8 }} cards/page
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ $layout['cardsPerRow'] ?? 2 }} cards/row
            </span>
        </div>

        @include('pages.admit-seat-cards._cards', [
            'students' => $students,
            'setting' => $setting,
            'renderForPdf' => false,
            'cardType' => $cardType ?? 'admit_card',
            'examType' => $examType ?? null,
            'selectedExam' => $selectedExam ?? null,
            'layout' => $layout ?? [],
        ])
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const selectedSection = @json(request('section_id'));

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        if (!classId) {
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then(data => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach(section => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                sectionSelect.innerHTML = html;
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            })
            .catch(() => {
                sectionSelect.innerHTML = '<option value="">All Sections</option>';
                if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
            });
    }

    $(document).on('change', '#classSelect', function () {
        loadSections(this.value);
    });

    if (classSelect && classSelect.value) {
        loadSections(classSelect.value, selectedSection);
    }
});
</script>

<script>
const form = document.getElementById('filterForm');
document.getElementById('examTypeSelect')?.addEventListener('change', function () {
    const examSelect = document.getElementById('examSelect');
    if (examSelect) {
        examSelect.value = '';
    }
    form?.submit();
});
document.getElementById('examSelect')?.addEventListener('change', function () {
    form?.submit();
});
</script>

<style>
@include('pages.admit-seat-cards._styles')

.admit-seat-cards-page .admit-seat-cards-filter-panel {
    background: #ffffff;
    border: 1px solid #e7e5e4;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.admit-seat-cards-page .admit-seat-cards-filter-header {
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #eef2f7;
    padding: 0.95rem 1rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-body {
    padding: 1rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-form {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.admit-seat-cards-page .admit-seat-cards-filter-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 0.75rem;
    align-items: end;
}

.admit-seat-cards-page .admit-seat-cards-filter-label {
    display: block;
    margin-bottom: 0.35rem;
    font-size: 0.77rem;
    font-weight: 700;
    color: #6b7280;
}

.admit-seat-cards-page .admit-seat-cards-filter-control {
    min-height: 46px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #111827;
    font-size: 0.92rem;
    box-shadow: none;
}

.admit-seat-cards-page .admit-seat-cards-filter-control:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.05);
}

.admit-seat-cards-page .admit-seat-cards-filter-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.65rem;
    flex-wrap: wrap;
    grid-column: 1 / -1;
}

.admit-seat-cards-page .result-filter-icon-btn {
    min-width: 46px;
    min-height: 46px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.7rem 1rem;
}

@media (max-width: 1199.98px) {
    .admit-seat-cards-page .admit-seat-cards-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 767.98px) {
    .admit-seat-cards-page .admit-seat-cards-filter-grid {
        grid-template-columns: 1fr;
    }

    .admit-seat-cards-page .admit-seat-cards-filter-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .admit-seat-cards-page .admit-seat-cards-filter-actions > * {
        width: 100%;
        justify-content: center;
    }
}

@media print {
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endsection
