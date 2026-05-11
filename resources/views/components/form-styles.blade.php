{{-- Shared Modern Form Styles --}}
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .form-control-sm,
    .form-control {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .form-control-sm:focus,
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.15);
    }

    .form-label,
    label {
        color: #2e3338;
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        display: block;
        font-weight: 600;
    }

    .btn-sm,
    .btn {
        font-weight: 600;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }

    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-info {
        background-color: #17a2b8;
        border: none;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .card {
        transition: all 0.2s ease;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
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
        background-color: #f8f9fa;
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
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
        color: #6c757d;
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
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
    }
</style>
