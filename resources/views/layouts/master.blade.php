@include('layouts.partials._head')

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('layouts.partials._top-nav')
        @include('layouts.partials._side-nav')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            @include('layouts.partials._header')
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">

                    <!-- Main row -->
                    <div class="row">
                        @yield('contents')
                    </div>
                    <!-- /.row (main row) -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @include('layouts.partials._footer')


    </div>
    <!-- ./wrapper -->

    @include('layouts.partials._scripts')

    @yield('scripts')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (required for bootstrap-datepicker) -->
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}

    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>


    <script>
        $(function() {

            // Datepicker init
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Select2 init (exclude opt-out)
            $('select:not(.no-select2)').each(function() {
                const $select = $(this);
                $select.select2({
                    width: '100%',
                    allowClear: true,
                    dropdownParent: $select.closest('.modal').length ? $select.closest('.modal') : $select.parent(),
                    placeholder: $select.find('option[value=""]').text() || 'Select...'
                });
            });

        });
    </script>

    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 2500,
            extendedTimeOut: 1000,
            showDuration: 300,
            hideDuration: 300,
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
        };

        $(document).on('click', '.filter_button', function() {
            ($('#filterCollapse').hasClass('hidden')) ? $('#filterCollapse').removeClass('hidden'): $(
                '#filterCollapse').addClass('hidden');
        })

        // Replace all date inputs with DD/MM/YYYY text inputs backed by hidden YYYY-MM-DD fields
        document.querySelectorAll('input[type="date"]').forEach(function(input) {
            var val = input.value;
            var name = input.name;
            var required = input.required;
            var cls = input.className;
            var id = input.id;

            // Hidden field holds the real YYYY-MM-DD value for form submission
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name;
            hidden.value = val;

            // Visible text input shows DD/MM/YYYY
            var visible = document.createElement('input');
            visible.type = 'text';
            visible.className = cls;
            visible.placeholder = 'DD/MM/YYYY';
            visible.autocomplete = 'off';
            if (id) visible.id = id;
            if (required) visible.required = true;
            visible.value = val ? moment(val, 'YYYY-MM-DD').format('DD/MM/YYYY') : '';

            // Remove name from original so it doesn't submit
            input.removeAttribute('name');
            input.removeAttribute('required');
            input.style.display = 'none';

            input.parentNode.insertBefore(hidden, input);
            input.parentNode.insertBefore(visible, input);

            // Init bootstrap datepicker on visible input
            $(visible).datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            }).on('changeDate', function(e) {
                hidden.value = moment(e.date).format('YYYY-MM-DD');
            });

            // Allow manual typing: sync to hidden on blur
            visible.addEventListener('blur', function() {
                var m = moment(this.value, 'DD/MM/YYYY', true);
                hidden.value = m.isValid() ? m.format('YYYY-MM-DD') : '';
            });
        });
    </script>
</body>

</html>
