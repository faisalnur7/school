@extends('layouts.master')
@section('title', 'Add Notice')
@section('contents')
<div class="col-12">
    <form action="{{ route('notice.store') }}" method="POST" class="card">
        @include('pages.notices.form')
    </form>
</div>
@endsection
