@extends('layouts.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-white text-lg">Edit Employee — {{ $employee->name }}</h3>
            <a href="{{ route('hr.employees.show', $employee) }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
            @include('hr._alerts')
            <form action="{{ route('hr.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                @include('hr.employees._form')
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Employee</button>
            </form>
        </div>
    </div>
</div>
@endsection
