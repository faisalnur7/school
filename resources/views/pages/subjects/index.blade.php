@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header flex justify-between items-center">
                        <h3 class="card-title font-bold text-white">Subjects</h3>
                        <div class="card-tools ml-auto">
                            <a href="{{ route('subjects.classwise') }}" class="btn btn-info btn-sm mr-2">
                                <i class="fas fa-sitemap"></i> Classwise View
                            </a>
                            <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Subject
                            </a>
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

@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
