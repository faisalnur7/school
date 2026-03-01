@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @include('pages.students.filter')
        @include('pages.students.table')
    </div>
@endsection

@section('scripts')
    @include('scripts.student.filter_scripts')
    @include('scripts.common.load_academic_information')
@endsection
