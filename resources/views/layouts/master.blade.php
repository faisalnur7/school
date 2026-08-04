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
            @if (!empty($hubRoute) && !empty($routeName ?? null) && ($routeName ?? null) !== $hubRoute)
                <div class="container-fluid px-3 pt-2">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route($hubRoute) }}" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Hub
                        </a>
                    </div>
                </div>
            @endif
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

        html[data-theme='dark'] #mainSidebar {
            background-color: #0f172a;
            box-shadow: 2px 0 16px rgba(2, 6, 23, 0.35);
        }

        html[data-theme='dark'] #mainSidebar .brand-link-modern,
        html[data-theme='dark'] #mainSidebar .sidebar-modern,
        html[data-theme='dark'] #mainSidebar .user-panel-modern {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        html[data-theme='dark'] #mainSidebar .nav-link-modern {
            color: #cbd5e1;
        }

        html[data-theme='dark'] #mainSidebar .nav-link-modern:hover,
        html[data-theme='dark'] #mainSidebar .nav-link-modern.active {
            background-color: rgba(37, 99, 235, 0.16);
            color: #f8fafc;
        }

        html[data-theme='dark'] .sidebar-overlay {
            background-color: rgba(2, 6, 23, 0.62);
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
            $('.datepicker, [datepicker]').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            });

            // Select2 init
            function initSelect2(context) {
                function formatCheckboxOption(data) {
                    if (!data.id) {
                        return data.text;
                    }

                    const isSelected = data.element ? $(data.element).prop('selected') : false;
                    return $('<span class="select2-checkbox-option"></span>')
                        .append(
                            $('<span class="select2-checkbox-option__box"></span>')
                                .toggleClass('is-checked', isSelected)
                                .html(isSelected ? '<i class="fas fa-check"></i>' : '')
                        )
                        .append($('<span class="select2-checkbox-option__label"></span>').text(data.text));
                }

                function getSelect2SearchText(data) {
                    if (!data) {
                        return '';
                    }

                    if (data.element) {
                        const elementSearchText = data.element.getAttribute('data-search-text') || data.element.dataset.searchText;
                        if (elementSearchText) {
                            return String(elementSearchText).toLowerCase();
                        }
                    }

                    return String(data.text || '').toLowerCase();
                }

                function matchSelect2Search(params, data) {
                    const term = String(params.term || '').trim().toLowerCase();
                    if (!term) {
                        return data;
                    }

                    if (data.children && data.children.length) {
                        const matches = [];
                        data.children.forEach(function(child) {
                            const match = matchSelect2Search(params, child);
                            if (match) {
                                matches.push(match);
                            }
                        });

                        if (matches.length) {
                            return $.extend({}, data, { children: matches });
                        }

                        return null;
                    }

                    return getSelect2SearchText(data).includes(term) ? data : null;
                }

                $('select:not(.no-select2)', context).each(function() {
                    const $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) return;
                    const isMultiple = $select.prop('multiple');
                    const hasEmptyOption = $select.find('option[value=""]').length > 0;
                    const isSm = $select.hasClass('form-control-sm') || $select.hasClass('select2-sm');
                    const $modal = $select.closest('.modal');
                    const useCheckboxes = isMultiple && ($select.data('select2Checkboxes') !== undefined || $select.hasClass('select2-checkboxes'));
                    const placeholder = $select.data('placeholder') || (hasEmptyOption
                        ? $select.find('option[value=""]').first().text()
                        : (isMultiple ? 'Search and select options' : 'Select...'));
                    const select2Options = {
                        width: '100%',
                        allowClear: !isMultiple && hasEmptyOption,
                        closeOnSelect: !isMultiple,
                        dropdownParent: $modal.length ? $modal : $(document.body),
                        placeholder: placeholder
                    };
                    if (useCheckboxes) {
                        select2Options.closeOnSelect = false;
                        select2Options.templateResult = formatCheckboxOption;
                    }
                    select2Options.matcher = matchSelect2Search;
                    if ($select.data('select2ContainerClass')) {
                        select2Options.containerCssClass = $select.data('select2ContainerClass');
                    }
                    if ($select.data('select2DropdownClass')) {
                        select2Options.dropdownCssClass = $select.data('select2DropdownClass');
                    }
                    $select.select2(select2Options);
                    if (isSm) $select.data('select2').$container.addClass('select2-sm');

                    if (isMultiple) {
                        const $container = $select.data('select2').$container;
                        $container.off('click.select2ChoiceToggle').on('click.select2ChoiceToggle', '.select2-selection__choice', function(event) {
                            if ($(event.target).closest('.select2-selection__choice__remove').length) {
                                return;
                            }

                            event.preventDefault();
                            event.stopPropagation();
                            $(this).find('.select2-selection__choice__remove').first().trigger('click');
                        });
                    }
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
