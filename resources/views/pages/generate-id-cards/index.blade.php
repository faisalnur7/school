@extends('layouts.master')

@section('contents')
<div class="container-fluid">

    <div class="card no-print id-card-filter-shell">
        <div class="card-header">
            <h3 class="card-title mb-0 text-white text-lg">Generate ID Cards</h3>
        </div>
        <div class="card-body pb-2 id-card-filter-body">
            <div class="id-card-filter-panel">
            <form method="GET" action="{{ route('students.id-cards') }}" id="filterForm">
                <div class="row id-card-filter-grid">
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="font-weight-bold id-card-filter-label">Academic Year <span class="text-danger">*</span></label>
                            <select name="session_id" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">— Select Year —</option>
                                @foreach($sessions as $s)
                                    <option value="{{ $s->id }}" {{ request('session_id') == $s->id ? 'selected' : '' }}>{{ $s->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Class</label>
                            <select name="class_id" class="form-control form-control-sm" id="classSelect">
                                <option value="">All Classes</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Section</label>
                            <select name="section_id" class="form-control form-control-sm" id="sectionSelect">
                                <option value="">All Sections</option>
                                @foreach($sections as $sec)
                                    <option value="{{ $sec->id }}" {{ request('section_id') == $sec->id ? 'selected' : '' }}>{{ $sec->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Group</label>
                            <select name="group_id" class="form-control form-control-sm">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->id }}" {{ request('group_id') == $g->id ? 'selected' : '' }}>{{ $g->name_en }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Card Type</label>
                            <select name="card_type" class="form-control form-control-sm">
                                <option value="id_card" {{ ($cardType ?? 'id_card') === 'id_card' ? 'selected' : '' }}>ID Card</option>
                                <option value="library_card" {{ ($cardType ?? 'id_card') === 'library_card' ? 'selected' : '' }}>Library Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 id-card-filter-field">
                        <div class="form-group">
                            <label class="id-card-filter-label">Student ID</label>
                            <input
                                type="text"
                                name="student_cid"
                                class="form-control form-control-sm id-card-filter-input"
                                value="{{ request('student_cid') }}"
                                placeholder="Enter Student ID"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center id-card-filter-actions">
                        <div class="form-group mb-0 d-flex flex-wrap" style="gap:6px">
                            <button type="submit" class="btn btn-primary btn-sm id-card-filter-btn" title="Generate">
                                <i class="fas fa-id-card"></i>
                            </button>
                            <a href="{{ route('students.id-cards') }}" class="btn btn-secondary btn-sm id-card-filter-btn" title="Reset">
                                <i class="fas fa-times"></i>
                            </a>
                            @if($students->isNotEmpty())
                                <button type="button" class="btn btn-success btn-sm id-card-filter-btn" onclick="window.print()" title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

    @if(!request('session_id') && !request('student_cid'))
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-id-card fa-3x mb-3 d-block" style="opacity:.3"></i>
            <p class="mb-1">Select Academic Year or enter a Student ID to generate a card.</p>
        </div>
    @elseif($students->isEmpty())
        <div class="text-center py-5 text-muted no-print">
            <i class="fas fa-inbox fa-2x mb-2 d-block" style="opacity:.3"></i>
            <p>No students found for the selected filters.</p>
        </div>
    @else
        <div class="no-print mb-3 d-flex align-items-center" style="gap:8px; flex-wrap: wrap;">
            <span class="badge badge-primary px-3 py-2" style="font-size:13px">{{ $students->count() }} Students</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">
                {{ ($cardType ?? 'id_card') === 'library_card' ? 'Library card' : 'Front + Back per student' }}
            </span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">54mm × 84mm cards</span>
            <span class="badge badge-light border px-3 py-2" style="font-size:12px">Landscape print and PDF</span>
        </div>

        @include('pages.generate-id-cards._cards', ['students' => $students, 'setting' => $setting, 'renderForPdf' => false, 'cardType' => $cardType ?? 'id_card'])
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('classSelect');
    const sectionSelect = document.getElementById('sectionSelect');
    const selectedSection = @json(request('section_id'));

    function refreshSectionSelect() {
        if (!sectionSelect) return;
        if (window.refreshSelect2) window.refreshSelect2($(sectionSelect));
    }

    function replaceSectionOptions(html) {
        if (!sectionSelect) return;

        const $section = $(sectionSelect);
        if ($section.hasClass('select2-hidden-accessible')) {
            $section.select2('destroy');
        }

        sectionSelect.innerHTML = html;

        if (window.reinitSelect2) {
            window.reinitSelect2(sectionSelect.parentElement);
        } else {
            refreshSectionSelect();
        }
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!sectionSelect) return;

        replaceSectionOptions('<option value="">Loading...</option>');

        if (!classId) {
            replaceSectionOptions('<option value="">All Sections</option>');
            return;
        }

        fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then((response) => {
                if (!response.ok) throw new Error('Failed to load sections');
                return response.json();
            })
            .then((data) => {
                const sections = Array.isArray(data?.sections) ? data.sections : [];
                let html = '<option value="">All Sections</option>';

                sections.forEach((section) => {
                    const selected = String(selectedSectionId) === String(section.id) ? 'selected' : '';
                    html += `<option value="${section.id}" ${selected}>${section.name_en}</option>`;
                });

                replaceSectionOptions(html);
            })
            .catch(() => {
                replaceSectionOptions('<option value="">All Sections</option>');
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

<style>
@include('pages.generate-id-cards._styles', ['setting' => $setting])

.id-card-filter-shell {
    background: #fff;
    border: 1px solid rgba(148, 163, 184, 0.2);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    border-radius: 1rem;
    overflow: hidden;
}

.id-card-filter-body {
    padding-top: 1rem;
    background: transparent !important;
}

.id-card-filter-panel {
    padding: 1rem;
    border: 1px solid rgba(148, 163, 184, 0.16);
    border-radius: 0.95rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
}

.id-card-filter-grid {
    row-gap: 1rem;
}

.id-card-filter-label {
    display: inline-flex;
    margin-bottom: 0.35rem;
    color: #334155;
    font-size: 0.84rem;
}

.id-card-filter-input {
    background: #fff;
    color: #0f172a;
}

.id-card-filter-btn {
    min-width: 2.7rem;
    min-height: 2.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.card-header {
    background: linear-gradient(135deg, #1f2a44, #111827);
}

.card-title {
    font-weight: 700;
}

html[data-theme='dark'] .id-card-filter-shell {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.97) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 12px 28px rgba(2, 6, 23, 0.34);
}

html[data-theme='dark'] .id-card-filter-shell .card-header,
html[data-theme='dark'] .id-card-filter-shell .card-body {
    background: rgba(15, 23, 42, 0.97) !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-body {
    background: transparent;
}

html[data-theme='dark'] .id-card-filter-panel {
    background: linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.96) 100%);
    border-color: rgba(148, 163, 184, 0.18);
    box-shadow: 0 10px 24px rgba(2, 6, 23, 0.28);
}

html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--multiple {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--single .select2-selection__clear,
html[data-theme='dark'] .id-card-filter-panel .select2-container--default .select2-selection--multiple .select2-selection__rendered {
    background: transparent !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .select2-container--open .select2-dropdown {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
    box-shadow: 0 16px 32px rgba(2, 6, 23, 0.34) !important;
}

html[data-theme='dark'] .select2-container--open .select2-results__option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .select2-container--open .select2-results__option--highlighted {
    background: #2563eb !important;
    color: #fff !important;
}

html[data-theme='dark'] .id-card-filter-label {
    color: #cbd5e1;
}

html[data-theme='dark'] .id-card-filter-input {
    background: rgba(15, 23, 42, 0.96);
    color: #e2e8f0;
    border-color: rgba(148, 163, 184, 0.22);
}

html[data-theme='dark'] .id-card-filter-input::placeholder {
    color: #94a3b8;
}

html[data-theme='dark'] .id-card-filter-shell .form-control,
html[data-theme='dark'] .id-card-filter-shell .select2-container,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single {
    background-color: #0f172a !important;
    color: #e2e8f0 !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default.select2-container--focus .select2-selection--single,
html[data-theme='dark'] .id-card-filter-shell .form-control:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.18) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__rendered,
html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__placeholder {
    background: transparent !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #94a3b8 transparent transparent transparent !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-dropdown {
    background: #0f172a !important;
    border-color: rgba(148, 163, 184, 0.22) !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-results__option {
    background: #0f172a !important;
    color: #e2e8f0 !important;
}

html[data-theme='dark'] .id-card-filter-shell .select2-container--default .select2-results__option--highlighted {
    background: #2563eb !important;
    color: #fff !important;
}

html[data-theme='dark'] .id-card-filter-shell .btn-secondary {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.22);
    color: #e2e8f0;
}

html[data-theme='dark'] .id-card-filter-shell .btn-secondary:hover,
html[data-theme='dark'] .id-card-filter-shell .btn-secondary:focus {
    background: #1e293b;
    border-color: rgba(148, 163, 184, 0.32);
    color: #f8fafc;
}

html[data-theme='dark'] .id-card-filter-shell .btn-primary {
    background: #2563eb;
    border-color: #2563eb;
}

html[data-theme='dark'] .id-card-filter-shell .btn-success {
    background: #16a34a;
    border-color: #16a34a;
}

html[data-theme='dark'] .id-card-filter-shell .text-muted,
html[data-theme='dark'] .id-card-filter-shell .no-print p {
    color: #cbd5e1 !important;
}

@media print {
    .card {
        border: none;
        box-shadow: none;
    }

    .id-card-pages {
        padding: 0;
    }
}
</style>
@endsection
