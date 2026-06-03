@extends('layouts.master')
@section('title', 'Create Website Page')
@section('contents')
<div class="col-12">
    <form action="{{ route('website.pages.store') }}" method="POST" class="card">
        <div class="card-header"><h3 class="card-title">Create Website Page</h3></div>
        @include('pages.website.pages.form')
    </form>
</div>
@endsection
