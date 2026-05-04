@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Exam</h3>
                    <div class="card-tools">
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('exams.update', $exam) }}">
                    @csrf @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                        @endif

                        <div class="form-group">
                            <label>Exam Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $exam->name) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Exam Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" required>
                                        <option value="tutorial" {{ old('type', $exam->type) == 'tutorial' ? 'selected' : '' }}>Tutorial Exam</option>
                                        <option value="term"     {{ old('type', $exam->type) == 'term'     ? 'selected' : '' }}>Terminal Exam</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Academic Session <span class="text-danger">*</span></label>
                                    <select name="academic_session_id" class="form-control" required>
                                        @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('academic_session_id', $exam->academic_session_id) == $session->id ? 'selected' : '' }}>
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
                                    <input type="number" name="year" class="form-control" value="{{ old('year', $exam->year) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $exam->start_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ old('end_date', $exam->end_date?->format('Y-m-d')) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="draft"     {{ old('status', $exam->status) == 'draft'     ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $exam->status) == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i>Update Exam
                        </button>
                        <a href="{{ route('exams.show', $exam) }}" class="btn btn-secondary ml-2">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
