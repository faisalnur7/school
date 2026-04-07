@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Edit Department</h3>
            <a href="{{ route('hr.departments.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('hr.departments.update', $department) }}" method="POST">
                @csrf @method('PUT')
                @include('hr.departments._form')
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </form>
        </div>
    </div>
</div>
@endsection