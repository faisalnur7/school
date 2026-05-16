@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                {{-- Override the form action to point to admission.store --}}
                @php
                    // Trick: the form.blade.php uses isset($student) to decide action.
                    // We leave $student unset so it renders as "Create Student" form,
                    // but we need to override the action. We do this via a wrapper that
                    // intercepts the form submit via JS and changes the action.
                    $admissionMode = true;
                @endphp
                @include('pages.students.form')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('scripts.common.load_location')
    @include('scripts.common.load_academic_information')
    @include('scripts.student.main_script')
    @include('components.form-styles')
    <script>
        // Override form action to admission store route
        document.querySelector('form[method="POST"]').action = "{{ route('students.admission.store') }}";
    </script>
@endsection
