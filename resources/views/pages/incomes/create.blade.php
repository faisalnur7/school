@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.incomes.partials.form-styles')
    @include('components.dropzone-attachment-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
@endsection

@section('contents')
    @include('pages.incomes.partials.form', [
        'formAction' => route('incomes.store'),
        'formMethod' => 'POST',
        'pageTitle' => 'Record Income',
        'pageIcon' => 'fa-plus-circle',
        'submitLabel' => 'Create Income',
        'submitIcon' => 'fa-save',
        'backRoute' => route('incomes.index'),
        'accountGroups' => $accountGroups,
    ])
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    @include('components.dropzone-attachment-script')
@endsection
