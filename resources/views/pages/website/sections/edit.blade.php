@extends('layouts.master')
@section('title', 'Edit Section')
@section('contents')
<div class="col-12">
    <form method="POST" action="{{ route('website.pages.sections.update', [$page, $section]) }}" class="card">
        @method('PUT')
        <div class="card-header"><h3 class="card-title">Edit Section</h3></div>
        @include('pages.website.sections.form')
    </form>
</div>
@endsection
