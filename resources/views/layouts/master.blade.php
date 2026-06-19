<!DOCTYPE html>
<html lang="en">

@include('layouts.partials._head')

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        @include('layouts.partials._top-nav')
        @include('layouts.partials._side-nav')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            @include('layouts.partials._header')
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid m-1">

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

    <!-- Bootstrap Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    @yield('scripts')

    <style>
        @media (max-width: 991.98px) {
            #mainSidebar {
                position: fixed !important;
                inset: 0 auto 0 0;
                width: 260px;
                max-width: 85%;
                transform: translateX(-100%);
                transition: transform .25s ease-in-out;
                z-index: 1070;
                box-shadow: 2px 0 16px rgba(0, 0, 0, .18);
                background-color: #ffffff;
            }

            #mainSidebar.mobile-open {
                transform: translateX(0);
            }

            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background-color: rgba(0, 0, 0, .45);
                z-index: 1060;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity .25s ease-in-out, visibility .25s ease-in-out;
            }

            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }
    </style>

<script>
        $(function() {
            var sidebar = $('#mainSidebar');
            var overlay = $('#sidebarOverlay');

            function closeSidebar() {
                sidebar.removeClass('mobile-open');
                overlay.removeClass('active');
                $('body').removeClass('sidebar-open sidebar-collapse');
                $('#sidebar-overlay').remove();
            }

            function openSidebar() {
                sidebar.addClass('mobile-open');
                overlay.addClass('active');
                $('body').addClass('sidebar-open');
                $('#sidebar-overlay').remove();
            }

            // Ensure sidebar starts in correct state based on screen size
            if ($(window).width() >= 992) {
                closeSidebar();
            }
            
            // Mobile sidebar toggle
            function toggleSidebar() {
                if ($(window).width() >= 992) {
                    return;
                }

                if (sidebar.hasClass('mobile-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            // Toggle sidebar when button is clicked
            $(document).on('click', '[data-widget="pushmenu"]', function(e) {
                if ($(window).width() < 992) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    toggleSidebar();
                    return false;
                }
            });
            
            // Close sidebar when overlay is clicked
            $(document).on('click', '#sidebarOverlay', function(e) {
                if ($(window).width() < 992) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    closeSidebar();
                    return false;
                }
            });
            
            // Close sidebar when nav link is clicked (mobile)
            $(document).on('click', '.nav-link-modern', function() {
                if ($(window).width() < 992 && sidebar.hasClass('mobile-open')) {
                    closeSidebar();
                }
            });

            // Handle window resize - reset mobile state on desktop
            $(window).on('resize', function() {
                if ($(window).width() >= 992) {
                    closeSidebar();
                }
            }).trigger('resize'); // Trigger on load to set initial state

            // Datepicker init
            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });

            // Select2 init
            function initSelect2(context) {
                $('select:not(.no-select2)', context).each(function() {
                    const $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) return;
                    const isSm = $select.hasClass('form-control-sm') || $select.hasClass('select2-sm');
                    const $modal = $select.closest('.modal');
                    $select.select2({
                        width: '100%',
                        allowClear: true,
                        dropdownParent: $modal.length ? $modal : $(document.body),
                        placeholder: $select.find('option[value=""]').first().text() || 'Select...'
                    });
                    if (isSm) $select.data('select2').$container.addClass('select2-sm');
                });
            }

            // Refresh a Select2 select after its options have been replaced
            function refreshSelect2($select) {
                if (!($select instanceof $)) $select = $($select);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.trigger('change.select2');
                } else {
                    initSelect2($select.parent());
                }
            }
            window.refreshSelect2 = refreshSelect2;

            // Init on page load
            initSelect2(document);

            // Re-init when modals open
            $(document).on('shown.bs.modal', '.modal', function() {
                initSelect2(this);
            });

            // Re-init after AJAX-loaded HTML is injected into the DOM
            window.reinitSelect2 = function(context) {
                initSelect2(context || document);
            };

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
                todayHighlight: true,
                orientation: 'bottom'
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
