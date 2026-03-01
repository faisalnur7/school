@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('pages.students.form', ['student' => $student])
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_location')
    @include('scripts.common.load_academic_information')
    @include('scripts.student.main_script')
@endsection
