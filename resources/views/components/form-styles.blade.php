{{-- Shared Modern Form Styles --}}
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .form-control-sm,
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        background: #fff;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .form-control-sm:focus,
    .form-control:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 0.2rem rgba(148, 163, 184, 0.16);
    }

    .form-label,
    label {
        color: #334155;
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        display: block;
        font-weight: 600;
    }

    .btn-sm,
    .btn {
        font-weight: 600;
        border-radius: 0.5rem;
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        padding: 0.7rem 1rem;
        letter-spacing: 0.01em;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        transition: all 0.2s ease;
    }

    .btn-sm {
        min-height: 36px;
        padding: 0.55rem 0.85rem;
    }

    .btn-xs {
        min-height: 30px;
        padding: 0.35rem 0.65rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: 1px solid #1d4ed8;
        color: #fff;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        border-color: #1e40af;
        color: #fff;
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.22);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background-color: #fff;
        border: 1px solid #cbd5e1;
        color: #334155;
    }

    .btn-secondary:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
        box-shadow: 0 10px 18px rgba(148, 163, 184, 0.16);
        transform: translateY(-1px);
    }

    .btn-success {
        background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
        border: 1px solid #15803d;
        color: #fff;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #15803d 0%, #166534 100%);
        border-color: #166534;
        color: #fff;
        box-shadow: 0 12px 24px rgba(22, 163, 74, 0.2);
        transform: translateY(-1px);
    }

    .btn-info {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: 1px solid #0284c7;
        color: white;
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        border-color: #0369a1;
        box-shadow: 0 12px 24px rgba(14, 165, 233, 0.2);
        transform: translateY(-1px);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: 1px solid #d97706;
        color: #fff;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        border-color: #b45309;
        color: #fff;
        box-shadow: 0 12px 24px rgba(245, 158, 11, 0.22);
        transform: translateY(-1px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: 1px solid #dc2626;
        color: #fff;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        border-color: #b91c1c;
        color: #fff;
        box-shadow: 0 12px 24px rgba(239, 68, 68, 0.18);
        transform: translateY(-1px);
    }

    .btn-outline-primary,
    .btn-outline-secondary,
    .btn-outline-success,
    .btn-outline-danger,
    .btn-outline-warning {
        background: #fff;
    }

    .btn-outline-primary {
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
    }

    .btn-outline-primary:hover {
        background: #eff6ff;
        border-color: #60a5fa;
        color: #1e40af;
    }

    .btn-outline-secondary {
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .btn-outline-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .btn-outline-success {
        border: 1px solid #86efac;
        color: #15803d;
    }

    .btn-outline-success:hover {
        background: #f0fdf4;
        border-color: #4ade80;
        color: #166534;
    }

    .btn-outline-danger {
        border: 1px solid #fca5a5;
        color: #dc2626;
    }

    .btn-outline-danger:hover {
        background: #fef2f2;
        border-color: #f87171;
        color: #b91c1c;
    }

    .btn-outline-warning {
        border: 1px solid #fcd34d;
        color: #b45309;
    }

    .btn-outline-warning:hover {
        background: #fffbeb;
        border-color: #f59e0b;
        color: #92400e;
    }

    .btn:focus,
    .btn:active {
        box-shadow: 0 0 0 0.22rem rgba(148, 163, 184, 0.18);
    }

    .btn:disabled,
    .btn.disabled {
        opacity: 0.65;
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }

    .card {
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
    }

    .card:hover {
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1);
    }

    .card-header {
        border: none;
        padding: 1rem !important;
    }

    .card-body {
        padding: 1rem !important;
    }

    .card-footer {
        border: none;
        padding: 0.75rem 1rem !important;
        background-color: #f8fafc;
    }

    .alert {
        animation: slideDown 0.3s ease-out;
        border: none;
        border-radius: 0.375rem;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-group-text {
        border: 1px solid #cbd5e1;
        background-color: #f8f9fa;
        color: #475569;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        color: #dc3545;
        font-size: 0.75rem;
        margin-top: 0.15rem;
        display: block;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .text-danger {
        color: #dc3545;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .row.g-2 {
        margin-right: -0.5rem;
        margin-left: -0.5rem;
    }

    .row.g-2 > [class*="col-"] {
        padding-right: 0.5rem;
        padding-left: 0.5rem;
    }

    @media (max-width: 991.98px) {
        .card-header .d-flex,
        .card-footer .d-flex {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .card-header .btn,
        .card-footer .btn {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .card-header {
            padding: 0.75rem !important;
        }

        .card-footer {
            padding: 0.5rem 0.75rem !important;
        }

        .form-label,
        label {
            font-size: 0.75rem;
        }

        .btn-sm,
        .btn {
            padding: 0.55rem 0.75rem;
            font-size: 0.8rem;
            width: 100%;
            box-shadow: none;
        }

        .card-header .d-flex,
        .card-footer .d-flex {
            gap: 0.5rem;
        }

        .card-header .btn + .btn,
        .card-footer .btn + .btn {
            margin-left: 0 !important;
        }
    }

    html[data-theme='dark'] .bg-gradient-primary {
        background: linear-gradient(135deg, #1d4ed8 0%, #7c3aed 100%);
    }

    html[data-theme='dark'] .form-control-sm,
    html[data-theme='dark'] .form-control {
        background: #0f172a;
        border-color: rgba(148, 163, 184, 0.28);
        color: #e2e8f0;
    }

    html[data-theme='dark'] .form-control-sm:focus,
    html[data-theme='dark'] .form-control:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 0.2rem rgba(96, 165, 250, 0.18);
        background: #0f172a;
    }

    html[data-theme='dark'] .form-label,
    html[data-theme='dark'] label {
        color: #cbd5e1;
    }

    html[data-theme='dark'] .btn {
        box-shadow: 0 8px 18px rgba(2, 6, 23, 0.25);
    }

    html[data-theme='dark'] .btn-secondary,
    html[data-theme='dark'] .btn-outline-primary,
    html[data-theme='dark'] .btn-outline-secondary,
    html[data-theme='dark'] .btn-outline-success,
    html[data-theme='dark'] .btn-outline-danger,
    html[data-theme='dark'] .btn-outline-warning {
        background: #111827;
    }

    html[data-theme='dark'] .btn-secondary {
        border-color: rgba(148, 163, 184, 0.28);
        color: #e2e8f0;
    }

    html[data-theme='dark'] .btn-secondary:hover {
        background: #1e293b;
        border-color: rgba(148, 163, 184, 0.36);
        color: #f8fafc;
    }

    html[data-theme='dark'] .btn-outline-primary {
        border-color: rgba(96, 165, 250, 0.36);
        color: #93c5fd;
    }

    html[data-theme='dark'] .btn-outline-primary:hover {
        background: rgba(37, 99, 235, 0.16);
        border-color: #60a5fa;
        color: #dbeafe;
    }

    html[data-theme='dark'] .btn-outline-secondary {
        border-color: rgba(148, 163, 184, 0.28);
        color: #cbd5e1;
    }

    html[data-theme='dark'] .btn-outline-secondary:hover {
        background: #1e293b;
        border-color: rgba(148, 163, 184, 0.36);
        color: #f8fafc;
    }

    html[data-theme='dark'] .btn-outline-success {
        border-color: rgba(74, 222, 128, 0.28);
        color: #86efac;
    }

    html[data-theme='dark'] .btn-outline-success:hover {
        background: rgba(22, 163, 74, 0.16);
        border-color: rgba(74, 222, 128, 0.38);
        color: #bbf7d0;
    }

    html[data-theme='dark'] .btn-outline-danger {
        border-color: rgba(248, 113, 113, 0.28);
        color: #fca5a5;
    }

    html[data-theme='dark'] .btn-outline-danger:hover {
        background: rgba(220, 38, 38, 0.16);
        border-color: rgba(248, 113, 113, 0.38);
        color: #fecaca;
    }

    html[data-theme='dark'] .btn-outline-warning {
        border-color: rgba(251, 191, 36, 0.28);
        color: #fcd34d;
    }

    html[data-theme='dark'] .btn-outline-warning:hover {
        background: rgba(217, 119, 6, 0.16);
        border-color: rgba(251, 191, 36, 0.38);
        color: #fde68a;
    }

    html[data-theme='dark'] .card {
        background: #111827;
        box-shadow: 0 1px 4px rgba(2, 6, 23, 0.22);
        color: #e2e8f0;
    }

    html[data-theme='dark'] .card-header,
    html[data-theme='dark'] .card-footer {
        background-color: #111827;
        color: #e2e8f0;
    }

    html[data-theme='dark'] .card-footer {
        border-top: 1px solid rgba(148, 163, 184, 0.16);
    }

    html[data-theme='dark'] .input-group-text {
        background-color: #0f172a;
        border-color: rgba(148, 163, 184, 0.28);
        color: #cbd5e1;
    }

    html[data-theme='dark'] .invalid-feedback {
        color: #fca5a5;
    }

    html[data-theme='dark'] .text-danger {
        color: #fca5a5;
    }
</style>
