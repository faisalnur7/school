@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Form
                </h4>
                <a href="{{ route('exams.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('exams.store') }}" id="modernForm">
            @csrf

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-3" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

<div class="form-group">
                            <label>Exam Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                placeholder="e.g. First Terminal Exam 2025" required>
                        </div>

                        <div class="row">
                            <input type="hidden" name="type" id="exam_type" value="{{ old('type', 'tutorial') }}">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Exam Category <span class="text-danger">*</span></label>
                                    <select name="exam_category" class="form-control" required>
                                        <option value="tutorial" {{ old('exam_category', old('type') === 'term' ? 'terminal' : old('type')) == 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                                        <option value="terminal" {{ old('exam_category', old('type') === 'term' ? 'terminal' : old('type')) == 'terminal' ? 'selected' : '' }}>Terminal Exam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pair Index <span class="text-danger">*</span></label>
                                    <select name="pair_no" class="form-control" required>
                                        <option value="1" {{ old('pair_no') == 1 ? 'selected' : '' }}>Pair 1</option>
                                        <option value="2" {{ old('pair_no') == 2 ? 'selected' : '' }}>Pair 2</option>
                                        <option value="3" {{ old('pair_no') == 3 ? 'selected' : '' }}>Pair 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Pair Weight % <span class="text-danger">*</span></label>
                                    <input type="number" name="pair_weight_percent" class="form-control" min="0" max="100"
                                        value="{{ old('pair_weight_percent', 20) }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" class="form-control" required>
                                        <option value="">Select Session</option>
                                        @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                            {{ $session->name_en ?? $session->name_bn }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Year <span class="text-danger">*</span></label>
                                    <input type="number" name="year" class="form-control"
                                        value="{{ old('year', date('Y')) }}" min="2000" max="2100" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="draft"     {{ old('status', 'draft') == 'draft'     ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status')           == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('exams.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Create
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
@include('components.form-styles')
@endsection

@section('scripts')
<script>
    function syncExamType() {
        var category = $('select[name="exam_category"]').val();
        $('#exam_type').val(category === 'terminal' ? 'term' : 'tutorial');
    }

    $(function () {
        syncExamType();
        $('select[name="exam_category"]').on('change', syncExamType);

        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection