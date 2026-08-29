@extends('layouts.master')

@section('contents')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-1">{{ $exam ? 'Edit' : 'Create' }} Admission Exam</h2>
                    <p class="text-muted">Configure the exam and the minimum total marks for every class.</p>

                    <form method="POST" action="{{ $exam ? route('admissions.exams.update', $exam) : route('admissions.exams.store') }}">
                        @csrf
                        @if($exam) @method('PUT') @endif

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Exam name</label>
                                <input class="form-control" name="name" required value="{{ old('name', $exam?->name) }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Academic session</label>
                                <select class="form-control" name="academic_session_id" required>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" @selected(old('academic_session_id', $exam?->academic_session_id) == $session->id)>{{ $session->name_en }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Exam date</label>
                                <input type="date" class="form-control" name="exam_date" required value="{{ old('exam_date', $exam?->exam_date?->format('Y-m-d')) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Admission form fee</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                                    <input type="number" min="0.01" step="0.01" class="form-control" name="form_fee" required value="{{ old('form_fee', $exam?->form_fee) }}">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Venue</label>
                                <input class="form-control" name="venue" value="{{ old('venue', $exam?->venue) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Reporting time</label>
                                <input class="form-control" name="reporting_time" value="{{ old('reporting_time', $exam?->reporting_time) }}" placeholder="08:30 AM">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Instructions</label>
                            <textarea id="admissionInstructions" class="form-control" name="instructions" rows="8">{{ old('instructions', $exam?->instructions) }}</textarea>
                        </div>

                        <h5 class="mt-4">Classwise pass marks</h5>
                        <div class="row">
                            @foreach($classes as $class)
                                @php $currentMark = $exam?->classSettings?->firstWhere('school_class_id', $class->id)?->pass_mark; @endphp
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ $class->name_en }}</label>
                                        <input type="number" min="0" step="0.01" class="form-control" name="pass_marks[{{ $class->id }}]" required value="{{ old('pass_marks.' . $class->id, $currentMark) }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="custom-control custom-switch mb-4">
                            <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" @checked(old('status', $exam?->status ?? true))>
                            <label class="custom-control-label" for="status">Make this the active exam</label>
                        </div>

                        <button class="btn btn-primary">{{ $exam ? 'Update' : 'Create' }} Exam</button>
                        <a class="btn btn-light" href="{{ route('admissions.exams') }}">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    #admissionInstructions + .note-editor .note-editable ul {
        list-style-type: disc !important;
        padding-left: 2rem !important;
    }

    #admissionInstructions + .note-editor .note-editable ol {
        list-style-type: decimal !important;
        padding-left: 2rem !important;
    }

    #admissionInstructions + .note-editor .note-editable li {
        display: list-item !important;
    }
</style>
<script>
    $(document).ready(function () {
        var instructionsEditor = $('#admissionInstructions');

        if (!instructionsEditor.length || typeof $.fn.summernote !== 'function' || instructionsEditor.next('.note-editor').length) {
            return;
        }

        instructionsEditor.summernote({
            height: 220,
            minHeight: 180,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endsection
