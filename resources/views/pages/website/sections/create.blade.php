@extends('layouts.master')
@section('title', 'Create Section')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('website.pages.sections.store', $page) }}" class="card">
        <div class="card-header"><h3 class="card-title">Create Section</h3></div>
        @include('pages.website.sections.form')
    </form>
</div>
@endsection
