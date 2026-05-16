@extends('layouts.master')

@section('contents')
<div class="container-fluid px-3 py-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0 font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>Create Role
                </h4>
                <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.store') }}" id="modernForm">
            @csrf

            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mb-2 py-2" role="alert">
                        <i class="fas fa-exclamation-circle mr-1"></i><strong>Errors:</strong>
                        <ul class="mb-0 mt-1 ml-4 small">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required placeholder="e.g. Teacher, Accountant">
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-2">
                            <label class="small mb-1">Description</label>
                            <input type="text" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                                value="{{ old('description') }}" placeholder="Optional description">
                            @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <label class="small mb-2 font-weight-bold">Permissions</label>
                    @foreach($permissions as $category => $perms)
                        <div class="card border mb-2">
                            <div class="card-header py-1 px-2 bg-light d-flex justify-content-between align-items-center">
                                <span class="small font-weight-bold">{{ $category ?? 'General' }}</span>
                                <label class="mb-0 small text-muted" style="cursor:pointer">
                                    <input type="checkbox" class="category-toggle mr-1"
                                        data-category="cat_{{ Str::slug($category ?? 'general') }}">
                                    Select All
                                </label>
                            </div>
                            <div class="card-body py-2 px-2">
                                <div class="row">
                                    @foreach($perms as $permission)
                                        <div class="col-md-3 col-sm-4 col-6 mb-1">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input perm-check cat_{{ Str::slug($category ?? 'general') }}"
                                                    id="perm_{{ $permission->id }}"
                                                    name="permissions[]" value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label small" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer bg-light border-top py-2 px-3">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
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
    $(function () {
        if ($('.is-invalid').length > 0) {
            $('html, body').animate({ scrollTop: $('.is-invalid').first().offset().top - 50 }, 300);
        }
        $('.category-toggle').on('change', function () {
            const cat = $(this).data('category');
            $('.' + cat).prop('checked', $(this).is(':checked'));
        });
        $('.perm-check').on('change', function () {
            const cat = $(this).attr('class').match(/cat_[\w-]+/)[0];
            const total = $('.' + cat).length;
            const checked = $('.' + cat + ':checked').length;
            $('[data-category="' + cat + '"]').prop('checked', total === checked);
        });
    });
</script>
@endsection