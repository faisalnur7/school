@extends('layouts.master')

@section('contents')
    <div class="container-fluid subjects-page">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="card-title font-bold text-white">Subjects</h3>
                        <div class="card-tools ml-auto">
                            <a href="{{ route('subjects.classwise') }}" class="btn btn-info btn-sm mr-2">
                                <i class="fas fa-sitemap"></i> Classwise View
                            </a>
                            @if(auth()->user()?->hasPermission('create_subjects'))
                                <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Subject
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @include('pages.subjects.filter')
                        @include('pages.subjects.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    html[data-theme='dark'] .subjects-page .card,
    html.dark .subjects-page .card,
    html[data-theme='dark'] .subjects-page .card-body,
    html.dark .subjects-page .card-body {
        background: #111827 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .subjects-page .card-header,
    html.dark .subjects-page .card-header {
        background: linear-gradient(135deg, #172554 0%, #111827 100%) !important;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .subjects-page form,
    html.dark .subjects-page form {
        background: #111827 !important;
    }

    html[data-theme='dark'] .subjects-page .form-control,
    html.dark .subjects-page .form-control {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .subjects-page .form-control::placeholder,
    html.dark .subjects-page .form-control::placeholder {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .subjects-page .form-control option,
    html.dark .subjects-page .form-control option {
        background: #0f172a;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .subjects-page .table,
    html.dark .subjects-page .table {
        --bs-table-bg: #111827;
        --bs-table-color: #e2e8f0;
        --bs-table-striped-bg: #172033;
        --bs-table-striped-color: #e2e8f0;
        --bs-table-hover-bg: #1e293b;
        --bs-table-hover-color: #f8fafc;
        border-color: #334155 !important;
    }

    html[data-theme='dark'] .subjects-page .table thead,
    html.dark .subjects-page .table thead,
    html[data-theme='dark'] .subjects-page .table thead th,
    html.dark .subjects-page .table thead th {
        background: #1e293b !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .subjects-page .table td,
    html.dark .subjects-page .table td {
        background-color: transparent !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html[data-theme='dark'] .subjects-page .text-muted,
    html.dark .subjects-page .text-muted {
        color: #94a3b8 !important;
    }

    html[data-theme='dark'] .subjects-page .btn-secondary,
    html.dark .subjects-page .btn-secondary {
        background: #111827 !important;
        border-color: #475569 !important;
        color: #cbd5e1 !important;
    }
</style>
@endsection

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
