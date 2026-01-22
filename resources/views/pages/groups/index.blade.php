@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @include('pages.groups.table')
            </div>
        </div>
    </div>
@endsection
