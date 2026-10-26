@extends('layouts.master')
@section('contents')
<div class="employee-form-page">
    <div class="employee-form-wrap">
        <div class="employee-form-page__header">
            <div>
                <h1>Add Employee</h1>
                <p>Create a new staff profile. Fields marked <span class="employee-form-required">*</span> are required.</p>
            </div>
            <a href="{{ route('hr.employees.index') }}" class="employee-form-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Back</a>
        </div>
        @include('hr._alerts')
        <form action="{{ route('hr.employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('hr.employees._form')
            <div class="employee-form-actions">
                <span>Review all sections before saving.</span>
                <div><a href="{{ route('hr.employees.index') }}" class="employee-form-cancel">Cancel</a><button type="submit" class="employee-form-save"><i class="fas fa-save mr-1" aria-hidden="true"></i> Save Employee</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
