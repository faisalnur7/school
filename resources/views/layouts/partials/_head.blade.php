<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <title>@yield('title')</title> -->
    <title>@yield('title')</title>

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .content-wrapper {
            height: auto;
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