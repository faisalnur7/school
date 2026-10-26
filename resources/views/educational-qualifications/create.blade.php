@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-primary text-white py-3"><div class="d-flex justify-content-between align-items-center"><h4 class="card-title mb-0 font-weight-bold text-white"><i class="fas fa-plus-circle mr-2"></i>Add Qualification</h4><a href="{{ route('educational-qualifications.index') }}" class="btn btn-light btn-sm"><i class="fas fa-arrow-left mr-1"></i>Back</a></div></div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('educational-qualifications.store') }}" method="POST">
                @csrf
                @include('educational-qualifications._form')
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
