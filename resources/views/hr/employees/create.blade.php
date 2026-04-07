@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Add Employee</h3>
            <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary btn-sm ml-auto"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('hr.employees._form')
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Employee</button>
            </form>
        </div>
    </div>
</div>
@endsection
