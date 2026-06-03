@extends('layouts.master')
@section('title', 'Add Gallery Item')
@section('contents')
<div class="col-12">
    <form action="{{ route('website.gallery.store') }}" method="POST" enctype="multipart/form-data" class="card">
        @include('pages.website.gallery.form')
    </form>
</div>
@endsection
