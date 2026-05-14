@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold">
                    <i class="fas fa-edit mr-2"></i>Edit Fee Category
                </h4>
                <a href="{{ route('fee-categories.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('fee-categories.update', $feeCategory->id) }}" id="modernForm">
            @csrf
            @method('PUT')

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
                            <label>Name (English)</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ $feeCategory->name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Name (Bangla)</label>
                            <input type="text" name="bn_name" class="form-control"
                                   value="{{ $feeCategory->bn_name }}" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $feeCategory->description }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Applicable For</label>
                            <select name="student_type" class="form-control" required>
                                <option value="both" {{ $feeCategory->student_type === 'both' ? 'selected' : '' }}>All Students</option>
                                <option value="new" {{ $feeCategory->student_type === 'new' ? 'selected' : '' }}>New Students Only</option>
                                <option value="old" {{ $feeCategory->student_type === 'old' ? 'selected' : '' }}>Returning Students Only</option>
                            </select>
                        </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('fee-categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save mr-1"></i>Update
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
    $(function () {
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 50
            }, 300);
        }
    });
</script>
@endsection