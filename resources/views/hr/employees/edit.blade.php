@extends('layouts.master')
@section('contents')
<div class="employee-form-page">
    <div class="employee-form-wrap">
        <div class="employee-form-page__header">
            <div>
                <h1>Edit Employee</h1>
                <p>Update {{ $employee->name }}’s staff profile. Fields marked <span class="employee-form-required">*</span> are required.</p>
            </div>
            <a href="{{ route('hr.employees.index') }}" class="employee-form-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back</a>
        </div>
        @include('hr._alerts')
        <form action="{{ route('hr.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('hr.employees._form')
            <div class="employee-form-actions">
                <span>Review all sections before saving.</span>
                <div><a href="{{ route('hr.employees.show', $employee) }}" class="employee-form-cancel">Cancel</a><button type="submit" class="employee-form-save"><i class="fas fa-save mr-1" aria-hidden="true"></i> Update Employee</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
