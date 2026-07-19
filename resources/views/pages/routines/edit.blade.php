@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-edit mr-2"></i>Edit Routine
                    </h4>
                    <small class="text-white-50">Update the selected routine.</small>
                </div>
                <a href="{{ route('routines.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('routines.update', $routine->id) }}" id="routineForm">
            @csrf
            <div class="card-body">
                @include('hr._alerts')

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('pages.routines._form', ['routine' => $routine])
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('routines.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm">Update Routine</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const classSelect = document.getElementById('routine_class_id');
    const sectionSelect = document.getElementById('routine_section_id');
    const subjectSelect = document.getElementById('routine_subject_id');

    function resetOptions(select, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
    }

    function fillSections(items, selectedId) {
        resetOptions(sectionSelect, 'Select section');
        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.name_en;
            if (selectedId && String(selectedId) === String(item.id)) {
                option.selected = true;
            }
            sectionSelect.appendChild(option);
        });
    }

    function fillSubjects(items, selectedId) {
        resetOptions(subjectSelect, 'Select subject');
        items.forEach((item) => {
            const subject = item.subject || item;
            if (!subject) return;

            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.code ? `${subject.name} (${subject.code})` : subject.name;
            if (selectedId && String(selectedId) === String(subject.id)) {
                option.selected = true;
            }
            subjectSelect.appendChild(option);
        });
    }

    async function loadClassData(classId, selectedSectionId = null, selectedSubjectId = null) {
        if (!classId) {
            resetOptions(sectionSelect, 'Select section');
            resetOptions(subjectSelect, 'Select subject');
            return;
        }

        const sectionsUrl = sectionSelect.dataset.sectionsUrl + '?class_id=' + encodeURIComponent(classId);
        const subjectsUrl = subjectSelect.dataset.subjectsUrl + '?class_id=' + encodeURIComponent(classId);

        const [sectionsResponse, subjectsResponse] = await Promise.all([
            fetch(sectionsUrl),
            fetch(subjectsUrl)
        ]);

        const sections = await sectionsResponse.json();
        const subjects = await subjectsResponse.json();

        fillSections(sections, selectedSectionId);
        fillSubjects(subjects, selectedSubjectId);
    }

    classSelect.addEventListener('change', function () {
        loadClassData(this.value);
    });

    sectionSelect.addEventListener('change', function () {
        if (classSelect.value) {
            loadClassData(classSelect.value, this.value, subjectSelect.value);
        }
    });

    if (classSelect.value) {
        loadClassData(
            classSelect.value,
            @json(old('section_id', $routine->section_id)),
            @json(old('subject_id', $routine->subject_id))
        );
    }
})();
</script>
@endsection
