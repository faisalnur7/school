@extends('layouts.master')

@section('contents')
    <div class="container-fluid">
        @include('pages.students.filter')
        @include('pages.students.table')
    </div>
@endsection

@section('scripts')
    @include('scripts.student.filter_scripts')
    @include('scripts.common.load_academic_information')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleAll = document.getElementById('pdf-columns-toggle-all');
            const checkboxes = Array.from(document.querySelectorAll('.pdf-column-checkbox'));

            if (!toggleAll || !checkboxes.length) {
                return;
            }

            const syncToggleState = () => {
                toggleAll.checked = checkboxes.every((checkbox) => checkbox.checked);
            };

            toggleAll.addEventListener('change', function () {
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = toggleAll.checked;
                });
            });

            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', syncToggleState);
            });

            syncToggleState();
        });
    </script>
@endsection
