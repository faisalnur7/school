@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Edit ID Card Template</h3>
            <a href="{{ route('id-card-templates.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('id-card-templates.update', $idCardTemplate) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('pages.id-card-templates._form', ['template' => $idCardTemplate])
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save"></i> Update Template
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
