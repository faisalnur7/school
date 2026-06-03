@extends('layouts.master')
@section('title', 'Edit Website Page')
@section('contents')
<div class="col-12">
    <form action="{{ route('website.pages.update', $page) }}" method="POST" class="card">
        @method('PUT')
        <div class="card-header"><h3 class="card-title">Edit Website Page</h3></div>
        @include('pages.website.pages.form')
    </form>
</div>
@endsection
