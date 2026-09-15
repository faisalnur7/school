@extends('layouts.master')

@section('contents')
    <div class="container-fluid groups-page">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @include('pages.groups.table')
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .groups-page .groups-action-btn {
        width: 2.15rem;
        height: 2.15rem;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin: 0 !important;
        border-radius: 0.55rem;
        color: #fff !important;
        font-size: 0.85rem;
        line-height: 1;
        text-decoration: none;
        box-shadow: none;
        cursor: pointer;
    }

    .groups-page .groups-action-form {
        display: inline-block;
        margin: 0 !important;
    }

    .groups-page .groups-action-edit {
        background: #334155 !important;
        border: 1px solid #475569 !important;
    }

    .groups-page .groups-action-delete {
        background: #ef3340 !important;
        border: 0 !important;
    }

    .groups-page .groups-action-delete:hover,
    .groups-page .groups-action-delete:focus {
        background: #dc2636 !important;
        color: #fff !important;
        transform: translateY(-1px);
    }
</style>
@endsection
