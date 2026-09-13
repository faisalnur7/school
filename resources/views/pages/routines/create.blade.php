@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0 font-weight-bold text-white">
                        <i class="fas fa-plus-circle mr-2"></i>Create Routine
                    </h4>
                    <small class="text-white-50">Set a class, section, subject, teacher, and time slot.</small>
                </div>
                <a href="{{ route('routines.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('routines.store') }}" id="routineForm">
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

                @include('pages.routines._form')
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('routines.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm">Save Routine</button>
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
        if (window.jQuery && $(sectionSelect).hasClass('select2-hidden-accessible')) {
            $(sectionSelect).trigger('change.select2');
        }
    }

    function fillSubjects(items, selectedId) {
        resetOptions(subjectSelect, 'Select subject');
        const subjectIds = new Set();
        items.forEach((item) => {
            const subject = item.subject || item;
            if (!subject || subjectIds.has(String(subject.id))) return;
            subjectIds.add(String(subject.id));

            const option = document.createElement('option');
            option.value = subject.id;
            option.textContent = subject.code ? `${subject.name} (${subject.code})` : subject.name;
            if (selectedId && String(selectedId) === String(subject.id)) {
                option.selected = true;
            }
            subjectSelect.appendChild(option);
        });
        if (window.jQuery && $(subjectSelect).hasClass('select2-hidden-accessible')) {
            $(subjectSelect).trigger('change.select2');
        }
    }

    function loadSections(classId, selectedSectionId = null) {
        if (!classId) {
            resetOptions(sectionSelect, 'Select section');
            resetOptions(subjectSelect, 'Select subject');
            return;
        }

        sectionSelect.innerHTML = '<option value="">Loading...</option>';
        if (window.jQuery && $(sectionSelect).hasClass('select2-hidden-accessible')) {
            $(sectionSelect).trigger('change.select2');
        }

        return fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Unable to load sections.');
                return response.json();
            })
            .then(data => fillSections(Array.isArray(data?.sections) ? data.sections : [], selectedSectionId))
            .catch(() => fillSections([], selectedSectionId));
    }

    async function loadSubjects(classId, selectedSubjectId = null) {
        resetOptions(subjectSelect, 'Select subject');
        if (!classId || !sectionSelect.value) return;

        const subjectsUrl = subjectSelect.dataset.subjectsUrl + '?class_id=' + encodeURIComponent(classId);
        const response = await fetch(subjectsUrl);
        if (!response.ok) throw new Error('Unable to load subjects.');
        fillSubjects(await response.json(), selectedSubjectId);
    }

    $(document).on('change', '#routine_class_id', function () {
        loadSections(this.value);
    });

    $(document).on('change', '#routine_section_id', function () {
        if (classSelect.value) {
            loadSubjects(classSelect.value).catch(console.error);
        }
    });

    if (classSelect.value) {
        loadSections(classSelect.value, @json(old('section_id')))
            .then(() => loadSubjects(classSelect.value, @json(old('subject_id'))))
            .catch(console.error);
    }
})();
</script>
@endsection
