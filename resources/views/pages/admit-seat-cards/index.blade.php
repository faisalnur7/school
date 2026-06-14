@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card no-print">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Admit and Seat Cards</h3>
        </div>
        <div class="card-body pb-2">
            <form method="GET" action="{{ route('results.admit-seat-cards.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="font-weight-bold">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Class</label>
                            <select name="class_id" class="form-control form-control-sm">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control form-control-sm">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Group</label>
                            <select name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Exam Type</label>
                            <select name="exam_type" class="form-control form-control-sm" id="examTypeSelect">
                                <option value="">All Types</option>
                                <option value="tutorial" {{ ($examType ?? '') === 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                                <option value="term" {{ ($examType ?? '') === 'term' ? 'selected' : '' }}>Terminal Exam</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Exam Name</label>
                            <select name="exam_id" class="form-control form-control-sm" id="examSelect">
                                <option value="">All Exams</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->name }} ({{ $exam->type_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Card Type</label>
                            <select name="card_type" class="form-control form-control-sm">
                                <option value="admit_card" {{ ($cardType ?? 'admit_card') === 'admit_card' ? 'selected' : '' }}>Admit Card</option>
                                <option value="seat_card" {{ ($cardType ?? 'admit_card') === 'seat_card' ? 'selected' : '' }}>Seat Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cards / Page</label>
                            <input
                                type="number"
                                name="cards_per_page"
                                class="form-control form-control-sm"
                                min="1"
                                max="10"
                                value="{{ $layout['cardsPerPage'] ?? request('cards_per_page', 8) }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cards / Row</label>
                            <input
                                type="number"
                                name="cards_per_row"
                                class="form-control form-control-sm"
                                min="1"
                                max="10"
                                value="{{ $layout['cardsPerRow'] ?? request('cards_per_row', 2) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input
                                type="text"
                                name="student_cid"
                                class="form-control form-control-sm"
                                value="{{ request('student_cid') }}"
                                placeholder="Enter Student ID"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group mb-0 d-flex flex-wrap" style="gap:6px">
                            <button type="submit" class="btn btn-primary btn-sm" title="Generate">
                                <i class="fas fa-id-card"></i>
                            </button>
                            <a href="{{ route('results.admit-seat-cards.index') }}" class="btn btn-secondary btn-sm" title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                            @if($students->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm" onclick="window.print()" title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                                <a href="{{ route('results.admit-seat-cards.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank" title="PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            @endif
                        </div>
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
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">Monochrome portrait print</span>
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

.card-header {
    background: #111827;
}

.card-title {
    font-weight: 700;
}

@media print {
    .card {
        border: none;
        box-shadow: none;
    }
}
</style>
@endsection
