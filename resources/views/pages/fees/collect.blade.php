@extends('layouts.master')

@section('contents')
<div class="container-fluid">

{{-- ================= SEARCH ================= --}}
@include('pages.fees.partials._student_filter_for_payment')


{{-- ================= STUDENT LIST ================= --}}
@if(isset($students) && $students->count() > 0 && !isset($student))

<div class="card mb-3">
    <div class="card-header">
        {{ __('Student List') }}
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Class') }}</th>
                    <th>{{ __('Section') }}</th>
                    <th>{{ __('Group') }}</th>
                    <th width="120">{{ __('Action') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($students as $std)
                <tr>
                    <td>{{ $std->student_cid }}</td>
                    <td>{{ $std->full_name_en }}</td>

                    <td>
                        {{ $std->academicInformations->first()->schoolClass->name_en ?? '' }}
                    </td>

                    <td>
                        {{ $std->academicInformations->first()->section->name_en ?? '' }}
                    </td>

                    <td>
                        {{ $std->academicInformations->first()->group->name_en ?? '' }}
                    </td>

                    <td>
                        <a href="{{ route('fees.collect_payment',['student_id' => $std->id]) }}"
                           class="btn btn-sm btn-dark">
                            Collect Fee
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endif

</div>
@endsection
@section('scripts')
    @include('scripts.common.load_academic_information')
@endsection
