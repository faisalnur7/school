@extends('layouts.master')

@section('styles')
    @include('components.form-styles')
    @include('pages.shareholder-transactions.partials.form-styles')
@endsection

@section('contents')
    @include('pages.shareholder-transactions.partials.form', [
        'formAction' => route('shareholder-transactions.store'),
        'formMethod' => 'POST',
        'pageTitle' => 'Add Capital Transaction',
        'pageIcon' => 'fa-plus-circle',
        'submitLabel' => 'Create Transaction',
        'submitIcon' => 'fa-save',
        'backRoute' => route('shareholder-transactions.index'),
    ])
@endsection

@section('scripts')
    @include('pages.shareholder-transactions.partials.scripts')
@endsection
