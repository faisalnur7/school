<head>
    @php
        $schoolSetting = \App\Models\SchoolSetting::current();
        $favicon = !empty($schoolSetting->favicon)
            ? asset($schoolSetting->favicon)
            : (!empty($schoolSetting->logo) ? asset($schoolSetting->logo) : asset('assets/dist/img/AdminLTELogo.png'));
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- <title>@yield('title')</title> -->
    <title>@yield('title')</title>
    <link rel="icon" href="{{ $favicon }}">
    <link rel="shortcut icon" href="{{ $favicon }}">
    <script>
        (function() {
            try {
                var storedTheme = localStorage.getItem('school-theme');
                var preferredTheme = storedTheme || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.dataset.theme = preferredTheme;
                document.documentElement.classList.toggle('dark', preferredTheme === 'dark');
                document.documentElement.style.colorScheme = preferredTheme;
            } catch (error) {
                document.documentElement.dataset.theme = 'light';
            }
        })();
    </script>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <link rel="stylesheet" href="{{ asset('assets/css/backend_style.css') }}">
    <!-- Bootstrap Datepicker CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    <!-- Moment.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>

    @vite(['resources/css/admin.css'])

    <style>
        .content-wrapper {
            height: auto;
            margin-top: 0 !important;
        }

        /* Global unified form UI */
        form .form-control,
        form .form-select,
        form .custom-select,
        form textarea,
        form input[type="text"],
        form input[type="email"],
        form input[type="number"],
        form input[type="password"],
        form input[type="date"],
        form input[type="month"],
        form input[type="time"],
        form input[type="file"],
        form select {
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid #cbd5e1;
            background: #fff;
            min-height: 40px;
            font-size: 0.875rem;
            line-height: 1.4;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }

        form .form-control-sm,
        form .form-select-sm,
        form .custom-select-sm,
        form select.form-control-sm,
        form input.form-control-sm {
            min-height: 34px;
            font-size: 0.8rem;
            border-radius: 0.45rem;
        }

        form textarea.form-control,
        form textarea {
            min-height: 96px;
            resize: vertical;
        }

        form .form-control:focus,
        form .form-select:focus,
        form .custom-select:focus,
        form textarea:focus,
        form input:focus,
        form select:focus {
            border-color: #94a3b8;
            box-shadow: 0 0 0 0.2rem rgba(148, 163, 184, 0.16);
            background: #fff;
        }

        form label,
        form .form-label {
            color: #334155;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        form .input-group-text {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.82rem;
            font-weight: 600;
        }

        form .btn {
            min-height: 40px;
            border-radius: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.7rem 1rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
            transition: all 0.2s ease;
        }

        form .btn.btn-sm {
            min-height: 36px;
            padding: 0.55rem 0.85rem;
            border-radius: 0.55rem;
        }

        form .btn.btn-xs {
            min-height: 30px;
            padding: 0.35rem 0.65rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
        }

        form .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: 1px solid #1d4ed8;
            color: #fff;
        }

        form .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            border-color: #1e40af;
            color: #fff;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
            transform: translateY(-1px);
        }

        form .btn-secondary {
            border: 1px solid #cbd5e1;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            color: #334155;
        }

        form .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            box-shadow: 0 10px 18px rgba(148, 163, 184, 0.16);
            transform: translateY(-1px);
        }

        form .btn-success {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            border: 1px solid #15803d;
            color: #fff;
        }

        form .btn-success:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            border-color: #166534;
            color: #fff;
            box-shadow: 0 12px 24px rgba(22, 163, 74, 0.2);
            transform: translateY(-1px);
        }

        form .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: 1px solid #dc2626;
            color: #fff;
        }

        form .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border-color: #b91c1c;
            color: #fff;
            box-shadow: 0 12px 24px rgba(239, 68, 68, 0.18);
            transform: translateY(-1px);
        }

        form .btn-info {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: 1px solid #0284c7;
            color: #fff;
        }

        form .btn-info:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-color: #0369a1;
            color: #fff;
            box-shadow: 0 12px 24px rgba(14, 165, 233, 0.2);
            transform: translateY(-1px);
        }

        form .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: 1px solid #d97706;
            color: #fff;
        }

        form .btn-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            border-color: #b45309;
            color: #fff;
            box-shadow: 0 12px 24px rgba(245, 158, 11, 0.22);
            transform: translateY(-1px);
        }

        form .btn-outline-primary,
        form .btn-outline-secondary,
        form .btn-outline-success,
        form .btn-outline-danger,
        form .btn-outline-warning {
            background: #fff;
            backdrop-filter: blur(8px);
        }

        form .btn-outline-primary {
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        form .btn-outline-primary:hover {
            background: #eff6ff;
            border-color: #60a5fa;
            color: #1e40af;
        }

        form .btn-outline-secondary {
            border: 1px solid #cbd5e1;
            color: #475569;
        }

        form .btn-outline-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        form .btn-outline-success {
            border: 1px solid #86efac;
            color: #15803d;
        }

        form .btn-outline-success:hover {
            background: #f0fdf4;
            border-color: #4ade80;
            color: #166534;
        }

        form .btn-outline-danger {
            border: 1px solid #fca5a5;
            color: #dc2626;
        }

        form .btn-outline-danger:hover {
            background: #fef2f2;
            border-color: #f87171;
            color: #b91c1c;
        }

        form .btn-outline-warning {
            border: 1px solid #fcd34d;
            color: #b45309;
        }

        form .btn-outline-warning:hover {
            background: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }

        form .btn:focus,
        form .btn:active {
            box-shadow: 0 0 0 0.22rem rgba(148, 163, 184, 0.18);
        }

        form .btn:disabled,
        form .btn.disabled {
            opacity: 0.65;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        form .is-invalid,
        form .form-control.is-invalid,
        form .form-select.is-invalid {
            border-color: #dc3545 !important;
        }

        form .invalid-feedback {
            display: block;
            font-size: 0.75rem;
        }

        .content-wrapper .card form .card-body,
        .content-wrapper form.card .card-body {
            padding: 1rem;
        }

        .content-wrapper .card form .card-footer,
        .content-wrapper form.card .card-footer {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            justify-content: flex-start;
            padding: 1rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .content-wrapper .card form .card-footer .btn,
        .content-wrapper form.card .card-footer .btn {
            min-width: 120px;
        }

        .content-wrapper form .row {
            row-gap: 0.25rem;
        }

        .content-wrapper form .form-group,
        .content-wrapper form .col-md-1,
        .content-wrapper form .col-md-2,
        .content-wrapper form .col-md-3,
        .content-wrapper form .col-md-4,
        .content-wrapper form .col-md-5,
        .content-wrapper form .col-md-6,
        .content-wrapper form .col-md-7,
        .content-wrapper form .col-md-8,
        .content-wrapper form .col-md-9,
        .content-wrapper form .col-md-10,
        .content-wrapper form .col-md-11,
        .content-wrapper form .col-md-12,
        .content-wrapper form .col-lg-1,
        .content-wrapper form .col-lg-2,
        .content-wrapper form .col-lg-3,
        .content-wrapper form .col-lg-4,
        .content-wrapper form .col-lg-5,
        .content-wrapper form .col-lg-6,
        .content-wrapper form .col-lg-7,
        .content-wrapper form .col-lg-8,
        .content-wrapper form .col-lg-9,
        .content-wrapper form .col-lg-10,
        .content-wrapper form .col-lg-11,
        .content-wrapper form .col-lg-12 {
            margin-bottom: 0.35rem;
        }

        .content-wrapper form .btn-group,
        .content-wrapper form .d-flex {
            flex-wrap: wrap;
        }

        @media (max-width: 576px) {
            form .form-control,
            form .form-select,
            form .custom-select,
            form input,
            form select,
            form textarea {
                font-size: 0.82rem;
            }

            form .btn {
                width: 100%;
                box-shadow: none;
            }

            .content-wrapper .card form .card-footer,
            .content-wrapper form.card .card-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .content-wrapper .card form .card-footer .btn,
            .content-wrapper form.card .card-footer .btn {
                width: 100%;
                min-width: 0;
            }
        }

        .card-header {
            background: linear-gradient(90deg, #252f51, #212529) !important;
        }

        /* ── Select2 base ── */
        .select2-container { width: 100% !important; }

        /* Prevent Select2 from shifting body when dropdown opens */
        body { padding-right: 0 !important; overflow-x: hidden !important; }
        .content-wrapper { overflow-x: hidden; }

        /* Dropdown must not affect document flow */
        .select2-container--open .select2-dropdown {
            position: absolute;
            z-index: 99999;
        }

        /* Normal size (matches form-control ~38px) */
        .select2-container--default .select2-selection--single {
            background-color: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 6px;
            height: 38px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            transition: border-color .15s, box-shadow .15s;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937;
            font-size: .875rem;
            line-height: 38px;
            padding: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        /* Small size — matches form-control-sm (~28px) */
        .select2-container--default.select2-sm .select2-selection--single {
            height: 28px;
            border-radius: 4px;
            padding: 0 8px;
        }
        .select2-container--default.select2-sm .select2-selection--single .select2-selection__rendered {
            font-size: .8rem;
            line-height: 28px;
        }
        .select2-container--default.select2-sm .select2-selection--single .select2-selection__arrow {
            height: 28px;
            right: 6px;
        }
        .select2-container--default.select2-sm .select2-selection--single .select2-selection__arrow b {
            margin-top: -3px;
        }

        /* Focus / open state */
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,.12);
            background: #fff;
        }

        /* Dropdown */
        .select2-container--default .select2-dropdown {
            border: 1.5px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,.1);
            z-index: 99999;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #e5e7eb;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: .82rem;
            outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #667eea;
        }
        .select2-container--default .select2-results__option {
            padding: 6px 12px;
            font-size: .82rem;
            color: #374151;
        }
        .select2-container--default .select2-results__option--highlighted {
            background: #667eea;
            color: #fff;
        }
        .select2-container--default .select2-results__option--selected {
            background: #ede9fe;
            color: #5b21b6;
        }

        /* Modern multiple select skin */
        .select2-container--default .select2-selection--multiple {
            height: 40px;
            min-height: 40px;
            border: 1.5px solid #d6dee9;
            border-radius: 14px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 28px rgba(15, 23, 42, 0.06);
            padding: 0 0.55rem;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .select2-container--default .select2-selection--multiple:hover {
            border-color: #8aa0c4;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.12), 0 16px 30px rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: block;
            white-space: nowrap;
            overflow: hidden;
            padding: 0;
            margin: 0;
            width: 100%;
            line-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
            margin: 0.5rem 0.35rem 0.5rem 0;
            padding: 0.18rem 0.72rem;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(135deg, #667eea 0%, #4f46e5 100%);
            color: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            line-height: 1.2;
            box-shadow: 0 8px 18px rgba(79, 70, 229, 0.18);
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            order: 2;
            margin-left: 0.35rem;
            margin-right: 0;
            color: rgba(255, 255, 255, 0.88);
            font-weight: 700;
            border: 0;
            background: transparent;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #fff;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline {
            display: inline-block;
            margin: 0;
            vertical-align: middle;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
            margin: 0;
            padding: 0;
            min-width: 8rem;
            font-size: 0.875rem;
            color: #0f172a;
            height: 38px;
            line-height: 38px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .select2-container--default .select2-selection--multiple .select2-search__field::placeholder {
            color: #94a3b8;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            color: #94a3b8;
            margin-right: 0.2rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__clear:hover {
            color: #475569;
        }

        html[data-theme='dark'] .select2-container--default .select2-selection--multiple {
            border-color: #334155;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(15, 23, 42, 0.94));
            box-shadow: 0 1px 2px rgba(2, 6, 23, 0.25), 0 12px 28px rgba(2, 6, 23, 0.24);
        }

        html[data-theme='dark'] .select2-container--default .select2-selection--multiple:hover {
            border-color: #64748b;
        }

        html[data-theme='dark'] .select2-container--default.select2-container--focus .select2-selection--multiple,
        html[data-theme='dark'] .select2-container--default.select2-container--open .select2-selection--multiple {
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.18), 0 16px 30px rgba(2, 6, 23, 0.3);
            background: #0f172a;
        }

        html[data-theme='dark'] .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
            box-shadow: 0 8px 18px rgba(99, 102, 241, 0.24);
        }

        html[data-theme='dark'] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
        html[data-theme='dark'] .select2-container--default .select2-selection--multiple .select2-selection__placeholder,
        html[data-theme='dark'] .select2-container--default .select2-selection--multiple .select2-selection__clear {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
            color: #e2e8f0;
            background: transparent;
        }

        .select2-checkbox-option {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            width: 100%;
        }

        .select2-checkbox-option__box {
            width: 1rem;
            height: 1rem;
            border: 1.5px solid #94a3b8;
            border-radius: 0.28rem;
            background: #fff;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 1rem;
            font-size: 0.64rem;
            line-height: 1;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease;
        }

        .select2-checkbox-option__box.is-checked {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #4f46e5 100%);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.18);
        }

        .select2-results__option--highlighted .select2-checkbox-option__box {
            border-color: rgba(255, 255, 255, 0.88);
        }

        .select2-results__option--selected .select2-checkbox-option__box {
            border-color: #667eea;
        }

        .select2-checkbox-option__label {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        html[data-theme='dark'] .select2-checkbox-option__box {
            border-color: #64748b;
            background: #0f172a;
        }

        html[data-theme='dark'] .select2-checkbox-option__box.is-checked {
            border-color: #818cf8;
            background: linear-gradient(135deg, #818cf8 0%, #6366f1 100%);
        }

        .subject-class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.65rem 1rem;
            margin-top: 0.25rem;
        }

        .subject-class-item {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            margin: 0;
            padding: 0.35rem 0.25rem;
            cursor: pointer;
            user-select: none;
        }

        .subject-class-item input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .subject-class-item__icon {
            width: 1.05rem;
            height: 1.05rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            flex: 0 0 1.05rem;
            font-size: 0.62rem;
            line-height: 1;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.14);
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        .subject-class-item__label {
            color: #111827;
            font-size: 0.93rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .subject-class-item:hover .subject-class-item__icon {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.18);
        }

        .subject-class-item input:not(:checked) + .subject-class-item__icon {
            background: #d1d5db;
            color: transparent;
        }

        .subject-class-item input:focus-visible + .subject-class-item__icon {
            outline: 3px solid rgba(102, 126, 234, 0.18);
            outline-offset: 2px;
        }

        .subject-class-item input:not(:checked) ~ .subject-class-item__label {
            font-weight: 600;
            color: #1f2937;
        }

        html[data-theme='dark'] .subject-class-item__icon {
            background: #e2e8f0;
            color: #0f172a;
        }

        html[data-theme='dark'] .subject-class-item input:not(:checked) + .subject-class-item__icon {
            background: #475569;
            color: transparent;
        }

        html[data-theme='dark'] .subject-class-item__label {
            color: #e2e8f0;
        }

        html[data-theme='dark'] .subject-class-item input:not(:checked) ~ .subject-class-item__label {
            color: #cbd5e1;
        }

        html[data-theme='dark'] .subject-class-item:hover .subject-class-item__icon {
            box-shadow: 0 4px 10px rgba(2, 6, 23, 0.35);
        }
        /* Clear button */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 16px;
            color: #9ca3af;
            font-size: 1rem;
            line-height: 1;
        }

        .btn-shine {
            position: relative;
            overflow: hidden;
            z-index: 0;
        }

        .btn-shine::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: linear-gradient(120deg,
                    rgba(255, 255, 255, 0.1) 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0.1) 100%);
            transform: skewX(-25deg);
            z-index: 1;
        }

        .btn-shine:hover::before {
            animation: shine 0.75s ease-in-out forwards;
        }

        @keyframes shine {
            0% {
                left: -75%;
            }

            100% {
                left: 125%;
            }
        }

        /* Category items */
        .cat-item {
            cursor: pointer;
            transition: all .15s ease;
            border: 1.5px solid transparent;
        }

        .cat-item:hover {
            border-color: #6366f1;
            background: #eef2ff !important;
        }

        .cat-item.active {
            border-color: #6366f1 !important;
            background: #eef2ff !important;
        }

        .cat-item.active .cat-name {
            color: #4338ca !important;
            font-weight: 600;
        }

        .cat-item.active .cat-badge {
            background: #6366f1 !important;
            color: #fff !important;
        }

        /* Fee cards */
        .fee-card {
            cursor: pointer;
            transition: all .17s ease;
            border: 1.5px solid #e2e8f0;
        }

        .fee-card:hover:not(.in-cart) {
            border-color: #6366f1;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, .12);
        }

        .fee-card.in-cart {
            opacity: .4;
            pointer-events: none;
        }

        /* Cart rows */
        .cart-row {
            animation: slideIn .2s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(14px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Remove btn */
        .remove-btn {
            transition: all .14s ease;
        }

        .remove-btn:hover {
            background: #fee2e2 !important;
            color: #dc2626 !important;
            border-color: #fca5a5 !important;
        }

        /* Collect btn */
        .collect-btn {
            transition: all .18s ease;
            letter-spacing: .04em;
        }

        .collect-btn:not(:disabled):hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, .35);
        }

        .collect-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* Scroll areas */
        .scroll-area {
            max-height: 520px;
            overflow-y: auto;
        }

        .scroll-area::-webkit-scrollbar {
            width: 4px;
        }

        .scroll-area::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* Left accent bar */
        .accent-bar {
            border-left: 4px solid #6366f1 !important;
        }

        /* Discount section */
        .discount-row {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .discount-type-btn {
            font-size: 12px;
            padding: 3px 10px;
            border-radius: 6px;
            cursor: pointer;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-weight: 600;
            transition: all .14s;
        }

        .discount-type-btn.active {
            background: #6366f1;
            color: #fff;
            border-color: #6366f1;
        }

        #discountInput {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 14px;
            font-family: 'JetBrains Mono', monospace;
            width: 100%;
            outline: none;
            transition: border-color .14s;
        }

        #discountInput:focus {
            border-color: #6366f1;
        }

        .discount-amount-line {
            font-size: 13px;
            color: #16a34a;
            font-weight: 600;
        }

        .student-header-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e8eaf0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06), 0 1px 3px rgba(0, 0, 0, .04);
            overflow: hidden;
            position: relative;
        }

        .student-header-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(180deg, #6366f1, #8b5cf6);
            border-radius: 16px 0 0 16px;
        }

        .card-inner {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 24px 16px 28px;
            flex-wrap: wrap;
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid #e2e5ed;
            background: #f8f9fb;
            color: #555;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s ease;
            white-space: nowrap;
        }

        .back-btn:hover {
            background: #eef0f6;
            border-color: #c8ccda;
            color: #333;
        }

        /* Vertical Divider */
        .header-divider {
            width: 1px;
            height: 40px;
            background: #e4e7ef;
            flex-shrink: 0;
        }

        @media (max-width: 767px) {
            .header-divider {
                display: none;
            }
        }

        /* Student Identity */
        .student-identity {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-ring {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ede9fe, #c7d2fe);
            border: 2px solid #a5b4fc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .avatar-initials {
            font-size: 13px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: .03em;
            font-family: 'Courier New', monospace;
        }

        .label-tag {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            letter-spacing: .1em;
            color: #9ca3af;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .student-name {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            white-space: nowrap;
        }

        /* Meta Chips */
        .meta-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            padding: 8px 14px;
            background: #f4f5f8;
            border: 1px solid #e8eaf0;
            border-radius: 10px;
            min-width: 72px;
        }

        .chip-label {
            font-family: 'Courier New', monospace;
            font-size: 9.5px;
            letter-spacing: .09em;
            color: #9ca3af;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .chip-value {
            font-family: 'Courier New', monospace;
            font-size: 13.5px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: .01em;
        }
    </style>
    @yield('styles')
</head>
