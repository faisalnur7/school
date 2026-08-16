@extends('layouts.master')

@section('styles')
    <style>
        .fee-collect-page {
            color: #334155;
        }

        .fee-collect-page .card {
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .fee-collect-page .card .card-header {
            background: #fff !important;
            border-bottom: 1px solid #eef2f7 !important;
            padding: 1rem 1.25rem !important;
        }

        .fee-collect-page .card .card-header .fw-bold,
        .fee-collect-page .card .card-header .text-white,
        .fee-collect-page .card .card-header strong,
        .fee-collect-page .card .card-header span:not(.badge),
        .fee-collect-page .card .card-header div {
            color: #334155 !important;
        }

        .fee-collect-page .card .card-body {
            padding: 1rem !important;
            background: #fff;
        }

        .fee-collect-page .card form .card-footer,
        .fee-collect-page #feeForm .card-footer {
            display: block !important;
            padding: 1.25rem !important;
            background: #fff !important;
            border-top: 1px solid #eef2f7 !important;
        }

        .fee-collect-page .card form .card-footer .btn,
        .fee-collect-page #feeForm .card-footer .btn {
            min-width: 0 !important;
        }

        .fee-collect-page .panel-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b !important;
        }

        .fee-collect-page .collect-btn {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            text-align: center;
        }

        .fee-collect-page #mainTabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .fee-collect-page #mainTabs::-webkit-scrollbar {
            display: none;
        }

        .fee-collect-page #mainTabs .nav-item {
            flex: 0 0 auto;
        }

        .fee-collect-page #mainTabs .nav-link {
            white-space: nowrap;
        }

        .fee-collect-page .discount-row {
            margin-bottom: 1rem;
        }

        .fee-collect-page .discount-type-btn {
            min-width: 44px;
        }

        .fee-collect-page .cat-badge.badge,
        .fee-collect-page .inv-cat-item > .badge,
        .fee-collect-page #cartBadge {
            min-width: 2.1rem;
            padding: 0.45rem 0.7rem !important;
            font-size: 0.95rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
        }

        .fee-collect-page .inv-due-card > .badge {
            min-width: auto;
            padding: 0.28rem 0.55rem !important;
            font-size: 0.8rem !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            white-space: nowrap;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .fee-collect-page .cat-item,
        .fee-collect-page .inv-cat-item,
        .fee-collect-page .inv-due-card,
        .fee-collect-page .fee-card,
        .fee-collect-page .inv-item-card,
        .fee-collect-page .cart-row {
            border-radius: 14px !important;
        }

        .fee-collect-page .inv-item-card--out {
            opacity: .58;
            background: #f8fafc !important;
            border-color: #dbe4ee !important;
        }

        .fee-collect-page .inv-item-card--out:hover {
            transform: none !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
        }

        .fee-collect-page .scroll-area {
            padding-right: 0.15rem;
        }

        .fee-collect-page .cart-row {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "main remove"
                "controls controls";
            gap: 0.3rem 0.55rem;
            padding: 0.6rem 0.65rem !important;
            align-items: start;
            align-content: start;
        }

        .fee-collect-page .cart-row > .flex-grow-1,
        .fee-collect-page .cart-row-main {
            grid-area: main;
            min-width: 0;
        }

        .fee-collect-page .cart-row > .text-end,
        .fee-collect-page .cart-row-controls {
            grid-area: controls;
            width: 100%;
            min-width: 0 !important;
            text-align: left !important;
        }

        .fee-collect-page .cart-row-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.35rem 0.5rem;
        }

        .fee-collect-page .cart-row .remove-btn,
        .fee-collect-page .cart-row-remove {
            grid-area: remove;
            align-self: start;
            justify-self: end;
            width: 30px;
            height: 30px;
            padding: 0 !important;
            line-height: 1;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            margin-left: 0 !important;
        }

        .fee-collect-page .cart-line-title {
            display: block;
            font-size: 13px;
            line-height: 1.25;
            font-weight: 600;
            color: #111827;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .fee-collect-page .cart-line-subtitle {
            display: block;
            margin-top: 0.15rem;
            font-size: 11px;
            line-height: 1.2;
            color: #64748b;
        }

        .fee-collect-page .cart-line-total {
            font-size: 13px;
            white-space: nowrap;
        }

        .fee-collect-page .cart-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.4rem 0.6rem;
        }

        .fee-collect-page #cartItems {
            gap: 0.5rem !important;
        }

        .fee-collect-page .cart-control-group {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            min-width: 0;
            flex: 1 1 120px;
        }

        .fee-collect-page .cart-control-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
        }

        .fee-collect-page .cart-control-group .input-group {
            flex: 1 1 auto;
            min-width: 0;
        }

        .fee-collect-page .cart-control-group .form-control,
        .fee-collect-page .cart-control-group .input-group-text {
            padding-top: 0.28rem;
            padding-bottom: 0.28rem;
            font-size: 0.8rem;
        }

        .fee-collect-page .cart-control-group .form-control {
            min-width: 0;
        }

        .fee-collect-page .cart-control-group--qty .form-control {
            max-width: 54px;
            text-align: center;
        }

        .fee-collect-page .cart-qty-stepper {
            display: inline-flex;
            align-items: stretch;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .fee-collect-page .cart-qty-stepper-btn {
            width: 28px;
            border: 0;
            background: #f8fafc;
            color: #475569;
            font-size: 14px;
            font-weight: 700;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .fee-collect-page .cart-qty-stepper-btn:hover:not(:disabled) {
            background: #eef2ff;
            color: #4338ca;
        }

        .fee-collect-page .cart-qty-stepper-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .fee-collect-page .cart-qty-stepper-input {
            width: 38px !important;
            border: 0 !important;
            border-left: 1px solid #cbd5e1 !important;
            border-right: 1px solid #cbd5e1 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            text-align: center;
            padding-left: 0.15rem !important;
            padding-right: 0.15rem !important;
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .fee-collect-page .cart-qty-stepper-input::-webkit-outer-spin-button,
        .fee-collect-page .cart-qty-stepper-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .fee-collect-page .cart-control-group--paid .input-group {
            max-width: 128px;
        }

        .fee-collect-page .cart-row-controls .cart-line-total {
            margin-left: auto;
            padding-left: 0.25rem;
        }

        .fee-collect-page .history-summary {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.25rem 0.35rem;
        }

        .fee-collect-page .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 0;
            border-radius: 0;
            margin: 0;
        }

        .fee-collect-page .table-responsive > .table {
            min-width: 1050px;
        }

        .fee-collect-page .table th,
        .fee-collect-page .table td {
            white-space: nowrap;
        }

        .fee-collect-page .payment-history-table thead th {
            white-space: nowrap;
            vertical-align: middle;
        }

        .fee-collect-page .payment-history-table tbody td {
            vertical-align: middle;
        }

        .fee-collect-page .payment-history-table td.payment-history-items {
            white-space: normal !important;
            min-width: 320px;
            max-width: 520px;
        }

        .fee-collect-page .payment-history-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem 0.4rem;
            align-items: center;
        }

        .fee-collect-page .payment-history-tag {
            display: inline-flex;
            align-items: center;
            max-width: 100%;
            white-space: nowrap;
            line-height: 1.1;
            padding: 0.38rem 0.58rem;
        }

        .fee-collect-page .payment-history-table td.payment-history-method,
        .fee-collect-page .payment-history-table td.payment-history-gross,
        .fee-collect-page .payment-history-table td.payment-history-scholarship,
        .fee-collect-page .payment-history-table td.payment-history-discount,
        .fee-collect-page .payment-history-table td.payment-history-paid,
        .fee-collect-page .payment-history-table td.payment-history-collector,
        .fee-collect-page .payment-history-table td.payment-history-actions {
            white-space: nowrap;
        }

        .fee-collect-page .payment-history-table td.payment-history-actions {
            min-width: 180px;
        }

        .fee-collect-page .payment-history-table .payment-history-action-btn {
            white-space: nowrap;
        }

        .fee-collect-page .mobile-collect-shell,
        .fee-collect-page .mobile-cart-backdrop {
            display: none;
        }

        .fee-collect-page .mobile-select-chip,
        .fee-collect-page .mobile-summary-card,
        .fee-collect-page .mobile-topbar,
        .fee-collect-page .mobile-selection-row,
        .fee-collect-page .mobile-selection-footer,
        .fee-collect-page .mobile-cart-toggle,
        .fee-collect-page .mobile-cart-summary {
            border-radius: 18px;
        }

        .fee-collect-page .min-w-0 {
            min-width: 0 !important;
        }

        .fee-collect-page .mobile-selection-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 0.9rem 1rem;
            text-align: left;
            transition: border-color 0.15s ease, background-color 0.15s ease, transform 0.15s ease;
        }

        .fee-collect-page .mobile-selection-row:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .fee-collect-page .mobile-selection-row.is-selected {
            border-color: #4338ca;
            background: #eef2ff;
            box-shadow: inset 0 0 0 1px rgba(67, 56, 202, 0.08);
        }

        .fee-collect-page .mobile-selection-row.is-added {
            border-style: dashed;
            opacity: 0.8;
        }

        .fee-collect-page .mobile-selection-row .mobile-selection-mark {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 4.5rem;
            padding: 0.35rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #eff6ff;
            color: #4338ca;
        }

        .fee-collect-page .mobile-selection-row.is-added .mobile-selection-mark {
            background: #ecfdf5;
            color: #059669;
        }

        .fee-collect-page .mobile-selection-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.25;
            margin-bottom: 0.15rem;
        }

        .fee-collect-page .mobile-selection-subtitle {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.25;
        }

        .fee-collect-page .mobile-selection-price {
            font-size: 0.9rem;
            font-weight: 700;
            color: #4338ca;
            white-space: nowrap;
        }

        .fee-collect-page .mobile-select-chip {
            flex: 0 0 auto;
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 0.1rem;
            min-width: 130px;
            border: 1px solid #dbe4ee;
            background: #fff;
            color: #0f172a;
            padding: 0.72rem 0.85rem;
            text-align: left;
            scroll-snap-align: start;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .fee-collect-page .mobile-select-chip strong {
            font-size: 0.92rem;
            line-height: 1.15;
        }

        .fee-collect-page .mobile-select-chip small {
            color: #64748b;
            font-size: 0.74rem;
        }

        .fee-collect-page .mobile-select-chip.is-active {
            border-color: #4338ca;
            background: #eef2ff;
            color: #312e81;
        }

        .fee-collect-page .mobile-summary-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
            padding: 0.95rem 1rem;
        }

        .fee-collect-page .mobile-topbar {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(10px);
        }

        .fee-collect-page .mobile-topbar .mobile-topbar-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .fee-collect-page .mobile-topbar .mobile-topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 0 0 auto;
        }

        .fee-collect-page .mobile-topbar .mobile-icon-btn {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid #dbe4ee;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
        }

        .fee-collect-page .mobile-topbar .student-name {
            font-size: 1rem;
            line-height: 1.2;
        }

        .fee-collect-page .mobile-topbar .label-tag {
            margin-bottom: 0.1rem;
            font-size: 0.7rem;
        }

        .fee-collect-page .mobile-meta-row {
            display: flex;
            gap: 0.45rem;
            overflow-x: auto;
            padding-bottom: 0.1rem;
            scroll-snap-type: x proximity;
            scrollbar-width: none;
        }

        .fee-collect-page .mobile-meta-row::-webkit-scrollbar {
            display: none;
        }

        .fee-collect-page .mobile-meta-chip {
            flex: 0 0 auto;
            white-space: nowrap;
            scroll-snap-align: start;
            border: 1px solid #dbe4ee;
            background: #fff;
            color: #475569;
            padding: 0.45rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .fee-collect-page .mobile-switch-row {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .fee-collect-page .mobile-switch-row .form-control,
        .fee-collect-page .mobile-switch-row .btn {
            min-height: 44px;
        }

        .fee-collect-page .mobile-switch-row .form-control {
            border-radius: 14px;
            font-size: 16px;
        }

        .fee-collect-page .mobile-switch-row .btn {
            border-radius: 14px;
            min-width: 88px;
        }

        .fee-collect-page .mobile-chip-scroll {
            display: flex;
            align-items: stretch;
            gap: 0.6rem;
            overflow-x: auto;
            padding-bottom: 0.15rem;
            scroll-snap-type: x proximity;
            scrollbar-width: none;
        }

        .fee-collect-page .mobile-chip-scroll::-webkit-scrollbar {
            display: none;
        }

        .fee-collect-page .mobile-chip-scroll::after {
            content: '';
            flex: 0 0 8px;
        }

        .fee-collect-page .mobile-summary-line {
            font-size: 0.86rem;
            color: #475569;
            line-height: 1.35;
        }

        .fee-collect-page .mobile-summary-line strong {
            color: #0f172a;
        }

        .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle {
            width: 100%;
            border: 0;
            background: linear-gradient(135deg, #ffffff 0%, #f3f4f6 100%);
            color: #0f172a;
            padding: 0.9rem 1rem;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle strong {
            font-size: 1rem;
            line-height: 1.2;
        }

        .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle small {
            color: #475569;
            font-size: 0.74rem;
        }

        .fee-collect-page .mobile-cart-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            color: #0f172a;
        }

        .fee-collect-page .mobile-cart-summary .mobile-cart-count {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .fee-collect-page .mobile-cart-summary .mobile-cart-total {
            font-size: 1.05rem;
            font-weight: 800;
            white-space: nowrap;
            color: #111827;
        }

        .fee-collect-page .mobile-collect-action {
            width: 100%;
            min-height: 48px;
            border-radius: 14px;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .fee-collect-page .mobile-selection-modal .modal-dialog {
            margin: 0;
            max-width: none;
            width: 100%;
            height: 100%;
        }

        .fee-collect-page .mobile-selection-modal .modal-content {
            min-height: 100vh;
            border: 0;
            border-radius: 0;
            background: #f8fafc;
        }

        .fee-collect-page .mobile-selection-modal .modal-header,
        .fee-collect-page .mobile-selection-modal .modal-footer {
            background: #fff;
            border-color: #e2e8f0;
        }

        .fee-collect-page .mobile-selection-modal .modal-body {
            padding: 1rem;
            overflow-y: auto;
        }

        .fee-collect-page .mobile-selection-modal .mobile-selection-search {
            border-radius: 14px;
            min-height: 44px;
            font-size: 16px;
        }

        .fee-collect-page .mobile-selection-modal .mobile-selection-footer {
            position: sticky;
            bottom: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding: 0.9rem 1rem;
        }

        .fee-collect-page .mobile-selection-modal .mobile-selection-footer .selection-meta {
            font-size: 0.82rem;
            color: #475569;
            flex: 1 1 140px;
        }

        .fee-collect-page .mobile-selection-modal .mobile-selection-footer .selection-action {
            min-height: 48px;
            border-radius: 14px;
            min-width: 160px;
        }

        .fee-collect-page .mobile-cart-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1040;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(1px);
        }

        .fee-collect-page .mobile-cart-backdrop.is-visible {
            display: block;
        }

        body.mobile-cart-open {
            overflow: hidden;
        }

        .fee-collect-page .mobile-quick-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        @media (min-width: 1024px) {
            .fee-collect-page .mobile-collect-shell,
            .fee-collect-page .mobile-quick-actions,
            .fee-collect-page .mobile-cart-backdrop {
                display: none !important;
            }

            .fee-collect-page .mobile-checkout-sheet {
                position: static !important;
                inset: auto !important;
                left: auto !important;
                right: auto !important;
                bottom: auto !important;
                transform: none !important;
                transition: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
                max-height: none !important;
                width: auto !important;
                z-index: auto !important;
            }

            .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle {
                display: none !important;
            }

            .fee-collect-page .mobile-cart-toggle-wrap,
            .fee-collect-page #collectBtnMobile {
                display: none !important;
            }

            .fee-collect-page .mobile-checkout-sheet .card-body,
            .fee-collect-page .mobile-checkout-sheet .card-footer {
                display: block !important;
                max-height: none !important;
            }
        }

        @media (max-width: 991.98px) {
            .fee-collect-page .payment-history-table td.payment-history-items {
                min-width: 260px;
                max-width: 360px;
            }

            .fee-collect-page .payment-history-table td.payment-history-actions {
                min-width: 150px;
            }
        }

        @media (max-width: 991.98px) {
            .fee-collect-page {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .fee-collect-page .student-header-card .card-inner {
                padding: 1rem;
                gap: 0.75rem;
                align-items: flex-start;
            }

            .fee-collect-page .student-identity {
                width: 100%;
            }

            .fee-collect-page .student-name {
                white-space: normal;
            }

            .fee-collect-page .meta-chips {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .fee-collect-page .chip {
                min-width: 0;
                width: 100%;
            }

            .fee-collect-page .chip--switch {
                grid-column: 1 / -1;
            }

            .fee-collect-page .chip--switch .d-flex {
                flex-direction: column;
                align-items: stretch !important;
            }

            .fee-collect-page .chip--switch .form-control,
            .fee-collect-page .chip--switch .btn {
                width: 100%;
            }

            .fee-collect-page .scroll-area {
                max-height: none !important;
            }

            .fee-collect-page .history-summary {
                width: 100%;
                justify-content: flex-start;
                margin-left: 0 !important;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 1023.98px) {
            .fee-collect-page {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                padding-bottom: 6.5rem;
            }

            .fee-collect-page .student-header-card {
                display: none !important;
            }

            .fee-collect-page .desktop-collect-layout > .col-12.col-lg-3.col-xl-2,
            .fee-collect-page .desktop-collect-layout > .col-12.col-lg-5.col-xl-6 {
                display: none !important;
            }

            .fee-collect-page .mobile-collect-shell {
                display: block;
                margin-bottom: 0.9rem;
            }

            .fee-collect-page #mainTabs {
                position: sticky;
                top: var(--mobile-tabs-top, 0px);
                z-index: 1035;
                margin-bottom: 0.85rem !important;
                background: rgba(248, 250, 252, 0.95);
                backdrop-filter: blur(8px);
                padding-top: 0.35rem;
                padding-bottom: 0.35rem;
                margin-left: -0.5rem;
                margin-right: -0.5rem;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .fee-collect-page #mainTabs .nav-link {
                border-radius: 999px;
            }

            .fee-collect-page .mobile-topbar {
                display: block;
                position: sticky;
                top: 0;
                z-index: 1040;
                margin-bottom: 0.75rem;
            }

            .fee-collect-page .mobile-summary-card {
                margin-top: 0.75rem;
            }

            .fee-collect-page .mobile-checkout-sheet {
                position: fixed;
                left: 0;
                right: 0;
                bottom: 0;
                z-index: 1045;
                transform: translateY(calc(100% - 148px));
                transition: transform 0.24s ease, box-shadow 0.24s ease;
                box-shadow: 0 -12px 32px rgba(15, 23, 42, 0.22);
                border-radius: 22px 22px 0 0;
                overflow: hidden;
                max-height: calc(100dvh - 148px);
            }

            .fee-collect-page .mobile-checkout-sheet.is-open {
                transform: translateY(0);
                max-height: calc(100dvh - 0.5rem);
            }

            .fee-collect-page .mobile-checkout-sheet .card-header {
                padding: 0 !important;
                border: 0 !important;
                background: transparent !important;
            }

            .fee-collect-page .mobile-checkout-sheet .card-header > .d-flex.align-items-center.gap-2 {
                display: none !important;
            }

            .fee-collect-page .mobile-checkout-sheet .card-body {
                max-height: calc(100dvh - 210px);
                overflow-y: auto;
                padding-bottom: 1rem !important;
            }

            .fee-collect-page .mobile-checkout-sheet .card-footer {
                position: sticky;
                bottom: 0;
                z-index: 2;
                padding: 0.8rem 0.9rem !important;
            }

            .fee-collect-page .mobile-checkout-sheet .card-body,
            .fee-collect-page .mobile-checkout-sheet .card-footer {
                display: none;
            }

            .fee-collect-page .mobile-checkout-sheet.is-open .card-body,
            .fee-collect-page .mobile-checkout-sheet.is-open .card-footer {
                display: block;
            }

            .fee-collect-page .mobile-checkout-sheet.is-open .mobile-cart-toggle {
                border-radius: 22px 22px 0 0;
            }

            .fee-collect-page .mobile-checkout-sheet.is-open .mobile-cart-toggle .mobile-cart-chevron {
                transform: rotate(180deg);
            }

            .fee-collect-page .mobile-cart-backdrop.is-visible {
                display: block;
            }

            .fee-collect-page .mobile-topbar .student-name {
                font-size: 0.95rem;
            }

            .fee-collect-page .mobile-selection-modal .modal-dialog {
                margin: 0;
            }
        }

        @media (max-width: 575.98px) {
            .fee-collect-page {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }

            .fee-collect-page .student-header-card .card-inner {
                padding: 0.85rem;
            }

            .fee-collect-page .meta-chips {
                grid-template-columns: 1fr;
            }

            .fee-collect-page .back-btn {
                width: 100%;
                justify-content: center;
            }

            .fee-collect-page #mainTabs .nav-link {
                padding: 0.65rem 0.9rem;
                font-size: 0.95rem;
            }

            .fee-collect-page .card .card-header,
            .fee-collect-page .card .card-body,
            .fee-collect-page .card form .card-footer {
                padding-left: 0.9rem !important;
                padding-right: 0.9rem !important;
            }

            .fee-collect-page .cart-row {
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 0.28rem 0.45rem;
                padding: 0.55rem 0.6rem !important;
            }

            .fee-collect-page #cartItems {
                gap: 0.4rem !important;
            }

            .fee-collect-page .cart-controls {
                justify-content: flex-start;
            }

            .fee-collect-page .cart-row-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .fee-collect-page .cart-control-group {
                flex: 1 1 100%;
                width: 100%;
            }

            .fee-collect-page .cart-control-group--qty {
                width: auto;
                flex: 0 0 auto;
            }

            .fee-collect-page .cart-control-group--paid .input-group {
                max-width: 100%;
            }

            .fee-collect-page .cart-row-controls .cart-line-total {
                margin-left: 0;
                align-self: flex-end;
            }

            .fee-collect-page .history-summary {
                font-size: 0.8rem;
            }

            .fee-collect-page .mobile-topbar .mobile-topbar-row {
                align-items: flex-start;
            }

            .fee-collect-page .mobile-select-chip {
                min-width: 118px;
                padding: 0.68rem 0.78rem;
            }

            .fee-collect-page .mobile-selection-modal .modal-body {
                padding: 0.9rem;
            }

            .fee-collect-page .mobile-selection-modal .mobile-selection-footer {
                padding: 0.8rem 0.9rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1023.98px) {
            .fee-collect-page .mobile-selection-modal .modal-dialog {
                width: min(92vw, 860px);
                margin: 1rem auto;
                height: auto;
            }

            .fee-collect-page .mobile-selection-modal .modal-content {
                min-height: auto;
                border-radius: 24px;
                overflow: hidden;
            }

            .fee-collect-page .mobile-checkout-sheet {
                width: min(100%, 860px);
                left: 50%;
                right: auto;
                transform: translateX(-50%) translateY(calc(100% - 148px));
            }

            .fee-collect-page .mobile-checkout-sheet.is-open {
                transform: translateX(-50%) translateY(0);
            }
        }

        @media (max-width: 1023.98px) {
            .fee-collect-page .student-search-trigger {
                min-width: 0;
            }
        }

        .fee-collect-page .mobile-only {
            display: none;
        }

        @media (max-width: 1023.98px) {
            .fee-collect-page .mobile-only {
                display: block;
            }
        }

        .fee-collect-page .student-search-trigger {
            border-radius: 12px;
            min-width: 118px;
        }

        .fee-collect-page .student-search-modal .modal-header,
        .fee-collect-page .student-search-modal .modal-footer {
            border-color: #eef2f7;
        }

        .fee-collect-page .student-search-results {
            min-height: 120px;
        }

        .fee-collect-page .student-search-table thead th {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            border-top: 0;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.55rem 0.75rem;
            white-space: nowrap;
        }

        .fee-collect-page .student-search-table tbody td {
            padding: 0.45rem 0.75rem;
            font-size: 0.9rem;
            color: #0f172a;
            white-space: nowrap;
        }

        .fee-collect-page .student-search-row {
            cursor: pointer;
            transition: background-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .fee-collect-page .student-search-row:hover,
        .fee-collect-page .student-search-row:focus {
            background: #eff6ff;
            box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.18);
            outline: none;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-content {
            background: #111827;
            color: #e2e8f0;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-header,
        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-footer {
            border-color: rgba(148, 163, 184, 0.18);
        }

        html[data-theme='dark'] .fee-collect-page .student-search-table thead th {
            border-bottom-color: rgba(148, 163, 184, 0.18);
            color: #cbd5e1;
            background: rgba(15, 23, 42, 0.96);
        }

        html[data-theme='dark'] .fee-collect-page .student-search-table tbody td {
            color: #e2e8f0;
            border-color: rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .fee-collect-page .student-search-row:hover,
        html[data-theme='dark'] .fee-collect-page .student-search-row:focus {
            background: rgba(30, 41, 59, 0.95);
        }

        html[data-theme='dark'] .fee-collect-page {
            color: #e2e8f0;
            background: #0b1020;
        }

        html[data-theme='dark'] .fee-collect-page .student-header-card,
        html[data-theme='dark'] .fee-collect-page .card,
        html[data-theme='dark'] .fee-collect-page .modal-content {
            background: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
            box-shadow: 0 12px 28px rgba(2, 6, 23, 0.38) !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-header-card .card-inner,
        html[data-theme='dark'] .fee-collect-page .card .card-body,
        html[data-theme='dark'] .fee-collect-page .card form .card-footer,
        html[data-theme='dark'] .fee-collect-page #feeForm .card-footer {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .card .card-header,
        html[data-theme='dark'] .fee-collect-page .card-header.bg-white,
        html[data-theme='dark'] .fee-collect-page .card-footer.bg-white,
        html[data-theme='dark'] .fee-collect-page .fee-card.bg-white {
            background: #0b1120 !important;
            color: #e2e8f0 !important;
            border-color: rgba(148, 163, 184, 0.14) !important;
        }

        html[data-theme='dark'] .fee-collect-page .card form .card-footer *,
        html[data-theme='dark'] .fee-collect-page #feeForm .card-footer * {
            border-color: rgba(148, 163, 184, 0.14) !important;
        }

        html[data-theme='dark'] .fee-collect-page .discount-row,
        html[data-theme='dark'] .fee-collect-page #discountSection {
            background: #0f172a !important;
            border: 1px solid rgba(148, 163, 184, 0.18) !important;
            border-radius: 14px !important;
            padding: 0.85rem !important;
        }

        html[data-theme='dark'] .fee-collect-page #discountSection {
            box-shadow: inset 0 0 0 1px rgba(30, 41, 59, 0.55) !important;
        }

        html[data-theme='dark'] .fee-collect-page .discount-row .d-flex.align-items-center.gap-2.mb-2 {
            margin-bottom: 0.55rem !important;
        }

        html[data-theme='dark'] .fee-collect-page .discount-type-btn {
            background: #111827 !important;
            color: #cbd5e1 !important;
            border: 1px solid rgba(148, 163, 184, 0.24) !important;
        }

        html[data-theme='dark'] .fee-collect-page .discount-type-btn.active {
            background: #6366f1 !important;
            color: #ffffff !important;
            border-color: #6366f1 !important;
        }

        html[data-theme='dark'] .fee-collect-page #subtotalAmount,
        html[data-theme='dark'] .fee-collect-page #totalAmount,
        html[data-theme='dark'] .fee-collect-page .discount-amount-line {
            color: #cbd5e1 !important;
        }

        html[data-theme='dark'] .fee-collect-page #subtotalAmount,
        html[data-theme='dark'] .fee-collect-page #discountInput,
        html[data-theme='dark'] .fee-collect-page #paymentAmount,
        html[data-theme='dark'] .fee-collect-page #descriptionInput {
            background: #0b1120 !important;
            border-color: rgba(148, 163, 184, 0.24) !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page #discountInput {
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03) !important;
        }

        html[data-theme='dark'] .fee-collect-page #paymentAmount::placeholder,
        html[data-theme='dark'] .fee-collect-page #discountInput::placeholder,
        html[data-theme='dark'] .fee-collect-page #descriptionInput::placeholder {
            color: #94a3b8 !important;
        }

        html[data-theme='dark'] .fee-collect-page .discount-row .form-label,
        html[data-theme='dark'] .fee-collect-page .discount-row .text-muted,
        html[data-theme='dark'] .fee-collect-page .discount-row .mono.text-muted,
        html[data-theme='dark'] .fee-collect-page .discount-row small {
            color: #94a3b8 !important;
        }

        html[data-theme='dark'] .fee-collect-page hr {
            border-top-color: rgba(148, 163, 184, 0.22) !important;
        }

        html[data-theme='dark'] .fee-collect-page .card-footer .d-flex.justify-content-between.align-items-baseline {
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .card-footer .d-flex.justify-content-between.align-items-center.mt-2 {
            color: #94a3b8 !important;
        }

        html[data-theme='dark'] .fee-collect-page .card-footer .collect-btn {
            background: linear-gradient(135deg, #6366f1, #4338ca) !important;
            color: #ffffff !important;
            border: 0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-header-card .card-inner {
            border-bottom: 1px solid rgba(148, 163, 184, 0.14);
        }

        html[data-theme='dark'] .fee-collect-page .back-btn,
        html[data-theme='dark'] .fee-collect-page .btn-outline-primary,
        html[data-theme='dark'] .fee-collect-page .btn-outline-secondary {
            background: #111827 !important;
            border-color: rgba(99, 102, 241, 0.55) !important;
            color: #e2e8f0 !important;
            box-shadow: none !important;
        }

        html[data-theme='dark'] .fee-collect-page .back-btn:hover,
        html[data-theme='dark'] .fee-collect-page .btn-outline-primary:hover,
        html[data-theme='dark'] .fee-collect-page .btn-outline-secondary:hover {
            background: #1e293b !important;
            color: #f8fafc !important;
        }

        html[data-theme='dark'] .fee-collect-page .label-tag,
        html[data-theme='dark'] .fee-collect-page .student-name,
        html[data-theme='dark'] .fee-collect-page .panel-eyebrow,
        html[data-theme='dark'] .fee-collect-page .cart-control-label,
        html[data-theme='dark'] .fee-collect-page .cart-line-subtitle,
        html[data-theme='dark'] .fee-collect-page .text-muted,
        html[data-theme='dark'] .fee-collect-page small,
        html[data-theme='dark'] .fee-collect-page .history-summary,
        html[data-theme='dark'] .fee-collect-page .student-search-modal .text-muted {
            color: #cbd5e1 !important;
        }

        html[data-theme='dark'] .fee-collect-page .meta-chips .chip,
        html[data-theme='dark'] .fee-collect-page .chip--switch,
        html[data-theme='dark'] .fee-collect-page .student-search-table thead th,
        html[data-theme='dark'] .fee-collect-page .student-search-table tbody td {
            background: #111827 !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .meta-chips .chip-value,
        html[data-theme='dark'] .fee-collect-page .chip--switch .form-control,
        html[data-theme='dark'] .fee-collect-page .chip--switch .form-control::placeholder {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        html[data-theme='dark'] .fee-collect-page .chip,
        html[data-theme='dark'] .fee-collect-page .chip--switch,
        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-header,
        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-footer,
        html[data-theme='dark'] .fee-collect-page .nav-tabs {
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .fee-collect-page .chip,
        html[data-theme='dark'] .fee-collect-page .student-search-table,
        html[data-theme='dark'] .fee-collect-page .payment-history-table,
        html[data-theme='dark'] .fee-collect-page .table-responsive,
        html[data-theme='dark'] .fee-collect-page .cart-qty-stepper,
        html[data-theme='dark'] .fee-collect-page .input-group,
        html[data-theme='dark'] .fee-collect-page .form-control,
        html[data-theme='dark'] .fee-collect-page textarea.form-control,
        html[data-theme='dark'] .fee-collect-page select.form-control {
            background: #111827 !important;
            color: #e2e8f0 !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }

        html[data-theme='dark'] .fee-collect-page .form-control::placeholder,
        html[data-theme='dark'] .fee-collect-page textarea.form-control::placeholder {
            color: #94a3b8 !important;
        }

        html[data-theme='dark'] .fee-collect-page .input-group-text,
        html[data-theme='dark'] .fee-collect-page .cart-qty-stepper-btn {
            background: #0b1120 !important;
            color: #cbd5e1 !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }

        html[data-theme='dark'] .fee-collect-page .nav-tabs {
            border-bottom-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .fee-collect-page .nav-tabs .nav-link {
            background: #0f172a !important;
            color: #93c5fd !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .fee-collect-page .nav-tabs .nav-link.active {
            background: #f8fafc !important;
            color: #111827 !important;
            border-color: rgba(248, 250, 252, 0.65) !important;
        }

        html[data-theme='dark'] .fee-collect-page .fee-card,
        html[data-theme='dark'] .fee-collect-page .inv-cat-item,
        html[data-theme='dark'] .fee-collect-page .inv-due-card,
        html[data-theme='dark'] .fee-collect-page .inv-item-card,
        html[data-theme='dark'] .fee-collect-page .cat-item,
        html[data-theme='dark'] .fee-collect-page .cart-row,
        html[data-theme='dark'] .fee-collect-page .student-search-row {
            background: #111827 !important;
            color: #e2e8f0 !important;
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .fee-collect-page .fee-card:hover,
        html[data-theme='dark'] .fee-collect-page .inv-cat-item:hover,
        html[data-theme='dark'] .fee-collect-page .inv-due-card:hover,
        html[data-theme='dark'] .fee-collect-page .inv-item-card:hover,
        html[data-theme='dark'] .fee-collect-page .cat-item:hover,
        html[data-theme='dark'] .fee-collect-page .cart-row:hover,
        html[data-theme='dark'] .fee-collect-page .student-search-row:hover {
            background: #1e293b !important;
        }

        html[data-theme='dark'] .fee-collect-page .inv-item-card--out {
            background: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.14) !important;
            opacity: 0.55;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-modal .table-responsive,
        html[data-theme='dark'] .fee-collect-page .student-search-modal .student-search-table {
            background: #0f172a !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-table thead th {
            background: #0b1120 !important;
            color: #cbd5e1 !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-table tbody td {
            background: #111827 !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-row:hover,
        html[data-theme='dark'] .fee-collect-page .student-search-row:focus {
            background: #1e293b !important;
            box-shadow: inset 0 0 0 1px rgba(96, 165, 250, 0.35) !important;
        }

        html[data-theme='dark'] .fee-collect-page .text-dark {
            color: #f8fafc !important;
        }

        html[data-theme='dark'] .fee-collect-page .badge {
            border-color: rgba(148, 163, 184, 0.18) !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-trigger {
            background: #111827 !important;
            color: #e2e8f0 !important;
            border-color: rgba(99, 102, 241, 0.55) !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-content {
            background: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html[data-theme='dark'] .fee-collect-page .student-search-modal .modal-body {
            background: #0f172a !important;
        }

        html[data-theme='dark'] .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle {
            background: linear-gradient(135deg, #111827 0%, #0f172a 100%);
            color: #ffffff;
        }

        html[data-theme='dark'] .fee-collect-page .mobile-checkout-sheet .mobile-cart-toggle small {
            color: rgba(255, 255, 255, 0.72);
        }

        html[data-theme='dark'] .fee-collect-page .mobile-cart-summary {
            color: #ffffff;
        }

        html[data-theme='dark'] .fee-collect-page .mobile-cart-summary .mobile-cart-total {
            color: #ffffff;
        }

        html[data-theme='dark'] .fee-collect-page .mobile-cart-summary .mobile-cart-count {
            color: rgba(255, 255, 255, 0.82);
        }

        html[data-theme='dark'] .fee-collect-page .mobile-collect-action {
            background: linear-gradient(135deg, #6366f1, #4338ca) !important;
            color: #ffffff !important;
            border-color: transparent !important;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid py-3 px-3 py-lg-4 px-lg-4 fee-collect-page">
        @php
            $feeSets = $pendingFees->groupBy('fee_set_id');
            $mobileDueCount = isset($inventoryDueItems) ? $inventoryDueItems->flatten(1)->count() : 0;
        @endphp

        {{-- ── Student Banner ── --}}
        <div class="student-header-card mb-4">
                <div class="card-inner">
                    <button type="button" class="btn btn-outline-primary btn-sm student-search-trigger mr-2" data-toggle="modal" data-target="#studentSearchModal">
                        Search Student
                    </button>
                    <a href="{{ url()->previous() }}" class="back-btn">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Back
                </a>
                <div class="header-divider"></div>
                <div class="student-identity">
                    <div class="avatar-ring">
                        <span class="avatar-initials">{{ $student?->full_name_en ? strtoupper(substr($student->full_name_en, 0, 2)) : '—' }}</span>
                    </div>
                    <div>
                        <p class="label-tag">STUDENT</p>
                        <h5 class="student-name">{{ $student->full_name_en ?? 'Student not selected' }}</h5>
                    </div>
                </div>
                @php $info = $student?->academicInformations?->last(); @endphp
                <div class="meta-chips ms-auto">
                    <div class="chip">
                        <span class="chip-label">ID</span>
                        <span class="chip-value">{{ $student->student_cid ?? '—' }}</span>
                    </div>
                    <div class="chip chip--switch" style="min-width:220px">
                        <span class="chip-label">STUDENT ID</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <input type="text" id="studentCidSwitch" class="form-control form-control-sm"
                                value="{{ $student->student_cid ?? '' }}" placeholder="Enter Student ID" autocomplete="off"
                                style="border-radius:12px;border:1px solid #c7d2fe" />
                            <button type="button" id="studentCidSwitchBtn" class="btn btn-sm"
                                style="border-radius:12px;background:#4338ca;color:#fff">
                                Switch
                            </button>
                        </div>
                    </div>
                    <div class="chip">
                        <span class="chip-label">CLASS</span>
                        <span class="chip-value">{{ $info?->schoolClass?->name_en ?? '—' }}</span>
                    </div>
                    <div class="chip">
                        <span class="chip-label">SECTION</span>
                        <span class="chip-value">{{ $info?->section?->name_en ?? '—' }}</span>
                    </div>
                    <div class="chip">
                        <span class="chip-label">GROUP</span>
                        <span class="chip-value">{{ $info?->group?->name_en ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mobile-collect-shell mobile-only">
            <div class="mobile-topbar card p-3 mb-3">
                <div class="mobile-topbar-row">
                    <a href="{{ url()->previous() }}" class="mobile-icon-btn" aria-label="Back">
                        <svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <button type="button" class="mobile-icon-btn student-search-trigger" data-toggle="modal" data-target="#studentSearchModal" aria-label="Search Student">
                        <i class="fas fa-search" aria-hidden="true"></i>
                    </button>
                    <div class="flex-grow-1 min-w-0">
                        <p class="label-tag">STUDENT</p>
                        <h5 class="student-name mb-0 text-truncate">{{ $student->full_name_en ?? 'Student not selected' }}</h5>
                    </div>
                    <div class="avatar-ring flex-shrink-0">
                        <span class="avatar-initials">{{ $student?->full_name_en ? strtoupper(substr($student->full_name_en, 0, 2)) : '—' }}</span>
                    </div>
                </div>
                @php $info = $student?->academicInformations?->last(); @endphp
                <div class="mobile-meta-row mt-3">
                    <span class="mobile-meta-chip">ID {{ $student->student_cid ?? '—' }}</span>
                    <span class="mobile-meta-chip">Class {{ $info?->schoolClass?->name_en ?? '—' }}</span>
                    <span class="mobile-meta-chip">Section {{ $info?->section?->name_en ?? '—' }}</span>
                    <span class="mobile-meta-chip">Group {{ $info?->group?->name_en ?? 'N/A' }}</span>
                </div>
                <div class="mobile-switch-row mt-3">
                    <input type="text" id="studentCidSwitchMobile" class="form-control student-cid-switch-input"
                        value="{{ $student->student_cid ?? '' }}" placeholder="Enter Student ID" autocomplete="off" />
                    <button type="button" id="studentCidSwitchBtnMobile" class="btn btn-primary">
                        Switch
                    </button>
                </div>
            </div>

        </div>

        <div class="modal fade student-search-modal" id="studentSearchModal" tabindex="-1" role="dialog" aria-labelledby="studentSearchModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="studentSearchModalLabel">Search Student</h5>
                            <small class="text-muted">Filter students and pick one to collect payment.</small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="studentSearchForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-2">
                                    <label class="form-label mono text-muted fw-semibold" style="font-size:11px;letter-spacing:.08em">STUDENT ID</label>
                                    <input type="text" name="student_id" class="form-control" placeholder="Enter Student ID">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mono text-muted fw-semibold" style="font-size:11px;letter-spacing:.08em">SESSION</label>
                                    <select name="academic_session_id" class="form-control">
                                        <option value="">All</option>
                                        @foreach($sessions ?? collect() as $session)
                                            <option value="{{ $session->id }}">{{ $session->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mono text-muted fw-semibold" style="font-size:11px;letter-spacing:.08em">CLASS</label>
                                    <select name="school_class_id" id="studentSearchClass" class="form-control">
                                        <option value="">All</option>
                                        @foreach($classes ?? collect() as $class)
                                            <option value="{{ $class->id }}">{{ $class->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mono text-muted fw-semibold" style="font-size:11px;letter-spacing:.08em">SECTION</label>
                                    <select name="section_id" id="studentSearchSection" class="form-control" disabled>
                                        <option value="">All</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label mono text-muted fw-semibold" style="font-size:11px;letter-spacing:.08em">GROUP</label>
                                    <select name="group_id" class="form-control">
                                        <option value="">All</option>
                                        @foreach($groups ?? collect() as $group)
                                            <option value="{{ $group->id }}">{{ $group->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-dark w-100" title="Search" aria-label="Search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="student-search-results mt-4" id="studentSearchResults">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <p class="mb-0">Use the filters above to search for a student.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade mobile-selection-modal" id="mobileSelectionModal" tabindex="-1" role="dialog" aria-labelledby="mobileSelectionTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="min-w-0">
                            <p class="panel-eyebrow mb-1">Select Items</p>
                            <h5 class="modal-title mb-0 text-truncate" id="mobileSelectionTitle">Select Items</h5>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="search" id="mobileSelectionSearch" class="form-control mobile-selection-search" placeholder="Search items">
                        </div>
                        <div id="mobileSelectionList" class="d-flex flex-column gap-2"></div>
                    </div>
                    <div class="modal-footer mobile-selection-footer">
                        <div class="selection-meta" id="mobileSelectionMeta">0 selected · BDT 0.00</div>
                        <button type="button" class="btn btn-primary selection-action" id="mobileSelectionAddBtn" disabled>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>


        {{-- ── Tabs ── --}}
        <ul class="nav nav-tabs mb-4" id="mainTabs">
            <li class="nav-item">
                <a class="nav-link active fw-semibold" href="#tabCollect">
                    💳 Collect Payment
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" href="#tabAssignedFees">
                    🧾 Assigned Fees
                    <span class="badge rounded-pill ms-1"
                        style="font-size:10px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                        {{ isset($assignedFees) ? $assignedFees->count() : 0 }}
                    </span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-semibold" href="#tabHistory">
                    🧾 Payment History
                    <span class="badge rounded-pill ms-1"
                        style="font-size:10px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                        {{ $payments->count() }}
                    </span>
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ══════════════════════════════════════
                 TAB 1 — COLLECT PAYMENT
            ══════════════════════════════════════ --}}
            <div class="tab-pane fade show active" id="tabCollect">
                <div class="mobile-quick-actions mobile-only mb-3">
                    <div class="card mobile-summary-card mb-3">
                        <div class="mobile-chip-scroll">
                            @foreach ($feeSets as $feeSetId => $fees)
                                <button type="button" class="mobile-select-chip"
                                    data-selection-kind="fee"
                                    data-selection-key="{{ $feeSetId }}"
                                    data-selection-title="{{ $fees->first()->feeSet->name }}"
                                    aria-label="Open {{ $fees->first()->feeSet->name }}">
                                    <strong>{{ $fees->first()->feeSet->name }}</strong>
                                    <small>{{ $fees->count() }} fee{{ $fees->count() === 1 ? '' : 's' }}</small>
                                </button>
                            @endforeach
                            @foreach($inventoryCategories as $cat)
                                <button type="button" class="mobile-select-chip"
                                    data-selection-kind="inventory"
                                    data-selection-key="{{ $cat->id }}"
                                    data-selection-title="{{ $cat->name }}"
                                    aria-label="Open {{ $cat->name }}">
                                    <strong>{{ $cat->name }}</strong>
                                    <small>{{ $cat->items->count() }} item{{ $cat->items->count() === 1 ? '' : 's' }}</small>
                                </button>
                            @endforeach
                            @if($mobileDueCount > 0)
                                <button type="button" class="mobile-select-chip"
                                    data-selection-kind="due"
                                    data-selection-key="all"
                                    data-selection-title="Inventory Dues"
                                    aria-label="Open Inventory Dues">
                                    <strong>Inventory Dues</strong>
                                    <small>{{ $mobileDueCount }} due{{ $mobileDueCount === 1 ? '' : 's' }}</small>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="card mobile-summary-card">
                        <div class="mobile-summary-line" id="mobileSummaryText">
                            Tap a category to add fees, inventory items, or dues.
                        </div>
                    </div>
                </div>

                <div class="desktop-collect-layout row g-4">

                    {{-- LEFT: Categories --}}
                    <div class="col-12 col-lg-3 col-xl-2">

                        {{-- Fee Categories --}}
                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5">🗂️</span>
                                    <span class="panel-eyebrow">Fee Categories</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="d-flex flex-column gap-2" id="categoryList">
                                    @php $feeSets = $pendingFees->groupBy('fee_set_id'); @endphp
                                    @foreach ($feeSets as $feeSetId => $fees)
                                        <div class="cat-item d-flex justify-content-between align-items-center rounded-3 px-3 py-2"
                                            style="background:#f8fafc;cursor:pointer" data-cat="{{ $feeSetId }}">
                                            <span class="cat-name text-secondary fw-medium" style="font-size:13px">
                                                {{ $fees->first()->feeSet->name }}
                                            </span>
                                            <span class="cat-badge badge rounded-pill mono"
                                                style="font-size:11px;background:#e2e8f0;color:#64748b">
                                                {{ $fees->count() }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Inventory Categories --}}
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5">📦</span>
                                    <span class="panel-eyebrow">Inventory</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                @if($inventoryCategories->isNotEmpty())
                                    <div class="d-flex flex-column gap-2" id="invCategoryList">
                                        @foreach($inventoryCategories as $cat)
                                            <div class="inv-cat-item d-flex justify-content-between align-items-center rounded-3 px-3 py-2"
                                                style="background:#f8fafc;cursor:pointer" data-inv-cat="{{ $cat->id }}">
                                                <span class="text-secondary fw-medium" style="font-size:13px">
                                                    {{ $cat->name }}
                                                </span>
                                                <span class="badge rounded-pill mono"
                                                    style="font-size:11px;background:#e2e8f0;color:#64748b">
                                                    {{ $cat->items->count() }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0" style="font-size:12px">No inventory items available.</p>
                                @endif
                            </div>
                        </div>

                        {{-- Inventory Dues --}}
                        @if(!empty($inventoryDueItems) && $inventoryDueItems->isNotEmpty())
                            <div class="card border-0 shadow-sm rounded-4 mt-3">
                                <div class="card-header bg-white border-bottom py-3 px-4"
                                    style="border-color:#f1f5f9!important">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fs-5">⏳</span>
                                        <span class="panel-eyebrow">Inventory Dues</span>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column gap-2" id="inventoryDueList">
                                        @foreach($inventoryDueItems as $dueGroup)
                                            @php
                                                $firstDue = $dueGroup->first();
                                                $dueCategory = $firstDue?->inventoryItem?->category;
                                                $dueCategoryName = $dueCategory?->name ?? 'Inventory';
                                            @endphp
                                            <div class="small text-uppercase fw-bold text-muted mt-1 mb-1" style="font-size:10px;letter-spacing:.08em">
                                                {{ $dueCategoryName }}
                                            </div>
                                            @foreach($dueGroup as $dueItem)
                                                @php
                                                    $dueAmount = (float) ($dueItem->due_amount ?? max(0, $dueItem->subtotal - ($dueItem->paid_amount ?? 0)));
                                                @endphp
                                                <div class="inv-due-card d-flex justify-content-between align-items-center rounded-3 px-3 py-2"
                                                    style="background:#fff7ed;cursor:pointer;border:1.5px solid #fed7aa"
                                                    data-due-id="{{ $dueItem->id }}"
                                                    data-due-name="{{ $dueItem->inventoryItem->name ?? 'Inventory Item' }}"
                                                    data-due-category="{{ $dueCategoryName }}"
                                                    data-due-amount="{{ $dueAmount }}">
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <div class="fw-semibold text-dark text-truncate" style="font-size:13px">
                                                            {{ $dueItem->inventoryItem->name ?? 'Inventory Item' }}
                                                        </div>
                                                        <div class="text-muted mono" style="font-size:10px">
                                                            Due: {{ number_format($dueAmount, 2) }} BDT
                                                        </div>
                                                    </div>
                                                    <span class="badge rounded-pill" style="font-size:10px;background:#ffedd5;color:#c2410c;border:1px solid #fdba74">
                                                        Due
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>{{-- /col LEFT --}}

                    {{-- MIDDLE: Pending Fees / Items --}}
                    <div class="col-12 col-lg-5 col-xl-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5" id="middlePanelIcon">📋</span>
                                    <span class="panel-eyebrow" id="middlePanelTitle">Pending Fees</span>
                                </div>
                            </div>
                            <div class="card-body p-3">

                                <div id="emptySelect" class="text-center py-5 text-muted">
                                    <div style="font-size:44px;opacity:.2">👈</div>
                                    <p class="mt-3 mb-0 fw-medium" style="font-size:14px">
                                        Select a category on the left<br>to view pending fees or items
                                    </p>
                                </div>

                                <div class="scroll-area d-none" id="feesContainer">

                                    {{-- Fee Cards --}}
                                    @foreach ($pendingFees as $fee)
                                        @php
                                            $feeSetName = $fee->feeSet->frequency == 'monthly'
                                                ? Carbon\Carbon::parse($fee->due_date)->format('F - Y')
                                                : $fee->feeSet->name;
                                        @endphp
                                        <div class="fee-card bg-white rounded-4 p-3 mb-3"
                                            data-cat="{{ $fee->fee_set_id }}"
                                            data-id="{{ $fee->id }}"
                                            data-amount="{{ $fee->calculated_net_amount }}"
                                            data-gross="{{ $fee->amount }}"
                                            data-discount="{{ $fee->total_scholarship_discount ?? 0 }}"
                                            data-discount-label="{{ $fee->discount_label ?? 'Fee Discount' }}"
                                            data-name="{{ $feeSetName }}"
                                            data-items='@json($fee->feeSet->items->map(fn($i) => ['category' => $i->category->name, 'amount' => $i->amount]))'
                                            style="display:none;border:1.5px solid #e2e8f0;cursor:pointer">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <p class="fw-semibold text-dark mb-1" style="font-size:15px">{{ $feeSetName }}</p>
                                                    <p class="mono text-muted mb-0" style="font-size:12px">Due: {{ $fee->due_date }}</p>
                                                    @if (!empty($fee->category_discounts))
                                                        @foreach ($fee->category_discounts as $catDiscount)
                                                            <p class="mono mb-0 mt-1" style="font-size:11px;color:#059669">
                                                                <span class="badge rounded-pill"
                                                                    style="background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;font-size:10px">
                                                                    🎓 {{ $catDiscount['category'] }}: -৳{{ number_format($catDiscount['discount'], 2) }}
                                                                </span>
                                                            </p>
                                                        @endforeach
                                                    @endif
                                                    @if (!empty($fee->category_transports))
                                                        @foreach ($fee->category_transports as $catTransport)
                                                            <p class="mono mb-0 mt-1" style="font-size:11px;color:#4338ca">
                                                                <span class="badge rounded-pill"
                                                                    style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:10px">
                                                                    🚌 {{ $catTransport['category'] }}: +৳{{ number_format($catTransport['amount'], 2) }}
                                                                </span>
                                                            </p>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="text-end ms-3">
                                                    @if (!empty($fee->category_discounts) || !empty($fee->category_transports))
                                                        <p class="mono text-muted mb-0" style="font-size:12px;text-decoration:line-through">
                                                            {{ number_format($fee->amount, 2) }}
                                                        </p>
                                                    @endif
                                                    <p class="mono fw-bold mb-1" style="font-size:19px;color:#4338ca">
                                                        {{ number_format($fee->calculated_net_amount, 2) }}
                                                    </p>
                                                    <span class="badge rounded-pill"
                                                        style="font-size:10px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                                                        + Add
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Inventory Item Cards --}}
                                    @foreach($inventoryCategories as $cat)
                                    @foreach($cat->items as $invItem)
                                        @php $isMadeToOrder = ($invItem->stock_type ?? 'stocked') === 'made_to_order'; @endphp
                                            <div class="inv-item-card align-items-center gap-2 rounded-3 px-3 py-2 mb-2 {{ ($invItem->current_stock > 0 || $isMadeToOrder) ? '' : 'inv-item-card--out' }}"
                                                data-inv-cat="{{ $cat->id }}"
                                                data-inv-id="{{ $invItem->id }}"
                                                data-name="{{ $invItem->name }}"
                                                data-price="{{ $invItem->selling_price }}"
                                                data-flexible-price="{{ $invItem->is_flexible_price ? 1 : 0 }}"
                                                data-stock-type="{{ $isMadeToOrder ? 'made_to_order' : 'stocked' }}"
                                                data-stock="{{ $invItem->current_stock }}"
                                                data-unit="{{ $invItem->unit }}"
                                                aria-disabled="{{ ($invItem->current_stock > 0 || $isMadeToOrder) ? 'false' : 'true' }}"
                                                style="display:none;border:1.5px solid #d1fae5;cursor:{{ ($invItem->current_stock > 0 || $isMadeToOrder) ? 'pointer' : 'not-allowed' }};background:#f0fdf4">
                                                {{-- Icon --}}
                                                <div class="d-flex align-items-center justify-content-center flex-shrink-0 rounded-3"
                                                    style="width:34px;height:34px;background:#dcfce7">
                                                    <i class="fas fa-box" style="font-size:13px;color:#16a34a"></i>
                                                </div>
                                                {{-- Name + meta --}}
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <p class="fw-semibold text-dark mb-0 text-truncate" style="font-size:13px">{{ $invItem->name }}</p>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        @if($isMadeToOrder)
                                                            <span class="badge rounded-pill" style="font-size:10px;background:#fef3c7;color:#92400e;border:1px solid #fde68a">Made to order</span>
                                                        @elseif($invItem->current_stock > 0)
                                                            <span class="badge rounded-pill" style="font-size:10px;background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0">{{ $invItem->current_stock }} left</span>
                                                        @else
                                                            <span class="badge rounded-pill" style="font-size:10px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca">Out of stock</span>
                                                        @endif
                                                        @if($invItem->unit)
                                                            <span class="mono text-muted" style="font-size:10px">{{ $invItem->unit }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                {{-- Price + stock --}}
                                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                                    <span class="mono fw-bold" style="font-size:14px;color:#059669">{{ number_format($invItem->selling_price, 2) }}</span>
                                                    @if($invItem->is_flexible_price)
                                                        <span class="badge rounded-pill" style="font-size:10px;background:#fef3c7;color:#92400e;border:1px solid #fde68a">Flexible</span>
                                                    @endif
                                                    @if($isMadeToOrder)
                                                        <span class="badge rounded-pill" style="font-size:10px;background:#ede9fe;color:#6d28d9;border:1px solid #ddd6fe">MTO</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach

                                </div>{{-- /#feesContainer --}}
                            </div>{{-- /card-body --}}
                        </div>{{-- /card --}}
                    </div>{{-- /col MIDDLE --}}

                    {{-- RIGHT: Cart --}}
                    <div class="col-12 col-lg-4 col-xl-4 mobile-checkout-sheet">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 px-4"
                                style="border-color:#f1f5f9!important">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-5">🧾</span>
                                    <span class="panel-eyebrow">Selected Items</span>
                                    <span id="cartBadge" class="mono ms-auto badge rounded-pill"
                                        style="font-size:11px;background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe">
                                        0 items
                                    </span>
                                </div>
                                <div class="mobile-only mobile-cart-toggle-wrap mt-3">
                                    <button type="button" class="mobile-cart-toggle" id="mobileCartToggle">
                                        <div class="mobile-cart-summary">
                                            <div class="min-w-0">
                                                <strong id="mobileCartTotal">BDT 0.00</strong>
                                                <small class="d-block text-truncate" id="mobileCartCount">No items added yet</small>
                                            </div>
                                            <span class="mobile-cart-chevron" aria-hidden="true">⌃</span>
                                        </div>
                                    </button>
                                    <button type="button" class="btn btn-primary mobile-collect-action collect-btn mobile-only" id="collectBtnMobile" disabled>
                                        ✓ &nbsp;COLLECT PAYMENT
                                    </button>
                                </div>
                            </div>
                            <form id="feeForm">
                                @csrf
                                <div class="card-body p-3">
                                    <div class="scroll-area" style="max-height:280px">
                                        <div id="cartEmpty" class="text-center py-5 text-muted">
                                            <div style="font-size:40px;opacity:.18">🛒</div>
                                            <p class="mt-3 mb-0 fw-medium" style="font-size:13px">
                                                No items added yet.<br>Click a fee, inventory item, or due item to add it.
                                            </p>
                                        </div>
                                        <div id="cartItems" class="d-flex flex-column gap-2"></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top p-4" style="border-color:#f1f5f9!important">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <span class="mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.08em">SUBTOTAL</span>
                                        <span class="mono fw-semibold" id="subtotalAmount"
                                            style="font-size:15px;color:#64748b">0.00</span>
                                    </div>
                                    <div class="discount-row mb-2" id="discountSection">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="mono text-muted fw-semibold"
                                                style="font-size:11px;letter-spacing:.08em">DISCOUNT</span>
                                            <div class="ms-auto d-flex gap-1">
                                                <button type="button" class="discount-type-btn active"
                                                    id="btnFlat">BDT</button>
                                                <button type="button" class="discount-type-btn"
                                                    id="btnPercent">%</button>
                                            </div>
                                        </div>
                                        <input type="number" id="discountInput" name="discount" min="0"
                                            step="0.01" placeholder="0.00" value="0" autocomplete="off">
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-muted" style="font-size:11px">Discount applied:</span>
                                            <span class="mono discount-amount-line" id="discountLine">- 0.00 BDT</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="discount_type" id="discountTypeHidden" value="flat">
                                    <input type="hidden" name="discount_amount" id="discountAmountHidden"
                                        value="0">
                                    <hr class="my-2" style="border-color:#f1f5f9">
                                    <div class="d-flex justify-content-between align-items-baseline mb-3">
                                        <span class="mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.09em">TOTAL DUE</span>
                                        <div>
                                            <span class="mono text-muted me-1" style="font-size:13px">BDT</span>
                                            <span class="mono fw-bold" id="totalAmount"
                                                style="font-size:28px;color:#4338ca">0.00</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="paymentAmount" class="form-label mono text-muted fw-semibold"
                                            style="font-size:11px;letter-spacing:.08em">PAYMENT AMOUNT</label>
                                         <input type="number" id="paymentAmount" name="payment_amount" min="0" step="0.01"
                                             placeholder="Enter payment amount" class="form-control" readonly
                                             style="font-size:16px;border-radius:8px;border:1px solid #e2e8f0;background:#f8fafc">
                                         <small class="text-muted" style="font-size:11px">Auto-calculated from the selected fee and inventory line payments.</small>
                                    </div>

                                    <div class="mb-3">
                                        <textarea name="description" id="descriptionInput" class="form-control" rows="2"
                                            placeholder="Add payment note or description" style="font-size:13px;border-radius:8px;border:1px solid #e2e8f0">{{ old('description') }}</textarea>
                                    </div>

                                    <button type="button" class="collect-btn btn w-100 fw-bold text-white rounded-3 py-3"
                                        id="collectBtn" disabled
                                        style="background:linear-gradient(135deg,#6366f1,#4338ca);font-size:14px">
                                        ✓ &nbsp;COLLECT PAYMENT
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="mobile-cart-backdrop" id="mobileCartBackdrop"></div>
            </div>

            {{-- ══════════════════════════════════════
                 TAB 2 — ASSIGNED FEES
            ══════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tabAssignedFees">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="fs-5">🧾</span>
                            <span class="panel-eyebrow">Assigned Fees</span>
                            <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-outline-success js-set-all-fees" data-state="1">
                                    Activate All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-set-all-fees" data-state="0">
                                    Deactivate All
                                </button>
                                <button type="submit" class="btn btn-sm btn-primary" form="feeActivationForm">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                        <div class="small text-muted mt-2">
                            Paid fees stay locked. Inactive fees will not appear as dues in Collect Payment.
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if (($assignedFees ?? collect())->isEmpty())
                            <div class="p-4 text-center text-muted">
                                No assigned fees found for this student.
                            </div>
                        @else
                            <form id="feeActivationForm" action="{{ route('fees.bulk-toggle-status') }}" method="POST">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:64px;">#</th>
                                                <th>Fee Set</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Amount</th>
                                                <th class="text-end">Paid</th>
                                                <th class="text-end">Due</th>
                                                <th>Status</th>
                                                <th>Active</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($assignedFees as $fee)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $fee->feeSet->name ?? 'N/A' }}</div>
                                                        <div class="small text-muted">{{ $fee->remarks ?? 'Fee assigned to this student' }}</div>
                                                    </td>
                                                    <td>{{ optional($fee->due_date)->format('d M, Y') ?? 'N/A' }}</td>
                                                    <td class="text-end">৳{{ number_format($fee->amount ?? 0, 2) }}</td>
                                                    <td class="text-end text-success">৳{{ number_format($fee->paid_amount ?? 0, 2) }}</td>
                                                    <td class="text-end text-danger">
                                                        ৳{{ number_format($fee->due_amount ?? max(0, (float) ($fee->amount ?? 0) - (float) ($fee->paid_amount ?? 0)), 2) }}
                                                    </td>
                                                    <td>
                                                        <span class="student-profile-badge {{ $fee->status === 'paid' ? 'student-profile-badge--success' : 'student-profile-badge--warning' }}">
                                                            {{ ucfirst($fee->status ?? 'pending') }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($fee->status === 'paid')
                                                            <span class="student-profile-badge student-profile-badge--success">Locked</span>
                                                        @else
                                                            <div class="custom-control custom-switch">
                                                                <input
                                                                    type="checkbox"
                                                                    class="custom-control-input js-fee-active-toggle"
                                                                    id="assignedFeeSwitch{{ $fee->id }}"
                                                                    name="active_fee_ids[]"
                                                                    value="{{ $fee->id }}"
                                                                    {{ $fee->is_active ? 'checked' : '' }}
                                                                >
                                                                <label class="custom-control-label" for="assignedFeeSwitch{{ $fee->id }}"></label>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>


            {{-- ══════════════════════════════════════
                 TAB 2 — PAYMENT HISTORY
            ══════════════════════════════════════ --}}
            <div class="tab-pane fade" id="tabHistory">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-color:#f1f5f9!important">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">📑</span>
                            <span class="panel-eyebrow">Payment History</span>
                            <span class="ms-auto history-summary" style="font-size:12px;color:#64748b">
                                Total Paid:
                                <strong style="color:#111827">
                                    BDT {{ number_format($payments->sum('amount'), 2) }}
                                </strong>
                                &nbsp;|&nbsp; Scholarship:
                                <strong style="color:#111827">
                                    BDT {{ number_format($payments->sum(fn ($payment) => $payment->scholarship_received_amount), 2) }}
                                </strong>
                                &nbsp;|&nbsp; Free Studentship:
                                <strong style="color:#111827">
                                    BDT {{ number_format($payments->sum(fn ($payment) => $payment->free_studentship_received_amount), 2) }}
                                </strong>
                                &nbsp;|&nbsp; Discount:
                                <strong style="color:#111827">
                                    BDT {{ number_format($payments->sum('discount_amount'), 2) }}
                                </strong>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 payment-history-table">
                                <thead style="background:#f8fafc;font-size:11px;letter-spacing:.07em">
                                    <tr>
                                        <th class="mono px-4 py-3 text-muted">#</th>
                                        <th class="mono px-4 py-3 text-muted">RECEIPT NO</th>
                                        <th class="mono px-4 py-3 text-muted">DATE</th>
                                        <th class="mono px-4 py-3 text-muted">ITEMS</th>
                                        <th class="mono px-4 py-3 text-muted">GROSS</th>
                                        <th class="mono px-4 py-3 text-muted">SCHOLARSHIP</th>
                                        <th class="mono px-4 py-3 text-muted">FREE STUDENTSHIP</th>
                                        <th class="mono px-4 py-3 text-muted">DISCOUNT</th>
                                        <th class="mono px-4 py-3 text-muted">PAID</th>
                                        <th class="mono px-4 py-3 text-muted">COLLECTED BY</th>
                                        <th class="mono px-4 py-3 text-muted text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                        <tr>
                                            <td class="px-4 py-3 text-muted mono" style="font-size:13px">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <code
                                                    style="font-size:12px;background:#f1f5f9;padding:3px 8px;border-radius:6px;color:#4338ca">
                                                    {{ $payment->receipt_no }}
                                                </code>
                                            </td>
                                            <td class="px-4 py-3 mono text-muted" style="font-size:13px">
                                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                            </td>
                                            <td class="px-4 py-3 payment-history-items" style="font-size:13px">
                                                <div class="payment-history-tags">
                                                    @foreach ($payment->items as $item)
                                                        <span class="badge rounded-pill payment-history-tag"
                                                            style="font-size:10px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0">
                                                            {{ $item->fee->feeSet->name ?? 'Fee' }}
                                                        </span>
                                                    @endforeach
                                                    @if ($payment->inventorySale?->items?->isNotEmpty())
                                                        @foreach ($payment->inventorySale->items as $saleItem)
                                                            <span class="badge rounded-pill payment-history-tag"
                                                                style="font-size:10px;background:#ecfdf5;color:#059669;border:1px solid #a7f3d0">
                                                                {{ $saleItem->inventoryItem->name ?? 'Inventory' }}
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-4 py-3 payment-history-gross">
                                                <span class="mono fw-bold" style="font-size:15px;color:#64748b">
                                                    {{ number_format($payment->calculated_gross_amount ?: $payment->gross_amount ?: $payment->calculated_amount, 2) }}
                                                </span>
                                                <span class="text-muted mono" style="font-size:11px"> BDT</span>
                                            </td>
                                            <td class="px-4 py-3 payment-history-scholarship">
                                                @if ($payment->scholarship_received_amount > 0)
                                                    <span class="mono fw-bold" style="font-size:13px;color:#059669">
                                                        -{{ number_format($payment->scholarship_received_amount, 2) }}
                                                    </span>
                                                    <span class="text-muted mono" style="font-size:10px"> BDT</span>
                                                @else
                                                    <span class="text-muted mono" style="font-size:12px">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 payment-history-free-studentship">
                                                @if ($payment->free_studentship_received_amount > 0)
                                                    <span class="mono fw-bold" style="font-size:13px;color:#059669">
                                                        -{{ number_format($payment->free_studentship_received_amount, 2) }}
                                                    </span>
                                                    <span class="text-muted mono" style="font-size:10px"> BDT</span>
                                                @else
                                                    <span class="text-muted mono" style="font-size:12px">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 payment-history-discount">
                                                @if ($payment->discount_amount > 0)
                                                    <span class="mono fw-bold" style="font-size:13px;color:#b45309">
                                                        -{{ number_format($payment->discount_amount, 2) }}
                                                    </span>
                                                    <span class="text-muted mono" style="font-size:10px">
                                                        {{ $payment->discount_type === 'percent' ? '%' : 'BDT' }}
                                                    </span>
                                                @else
                                                    <span class="text-muted mono" style="font-size:12px">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 payment-history-paid">
                                                <span class="mono fw-bold" style="font-size:15px;color:#4338ca">
                                                    {{ number_format($payment->calculated_amount, 2) }}
                                                </span>
                                                <span class="text-muted mono" style="font-size:11px"> BDT</span>
                                            </td>
                                            <td class="px-4 py-3 text-muted payment-history-collector" style="font-size:13px">
                                                {{ $payment->collector->name ?? '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-center payment-history-actions">
                                                <div class="d-flex gap-1 justify-content-center align-items-center flex-wrap">
                                                    <form action="{{ route('payments.destroy', $payment->id) }}"
                                                        method="POST"
                                                        class="d-inline js-payment-delete-form"
                                                        data-receipt-no="{{ $payment->receipt_no }}"
                                                        data-student-name="{{ $student->full_name_en ?? '' }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center payment-history-action-btn"
                                                            title="Delete payment"
                                                            aria-label="Delete payment"
                                                            style="width:32px;height:32px;padding:0">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M3 6h18" />
                                                                <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" />
                                                                <path d="M10 11v6" />
                                                                <path d="M14 11v6" />
                                                                <path d="M6 6l1 14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-14" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <a href="{{ route('payments.receipt', $payment->id) }}" target="_blank"
                                                        class="btn btn-sm btn-secondary d-inline-flex align-items-center justify-content-center payment-history-action-btn"
                                                        title="Print Receipt"
                                                        aria-label="Print Receipt"
                                                        style="width:32px;height:32px;padding:0">
                                                        <svg width="13" height="13" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2.2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="6 9 6 2 18 2 18 9" />
                                                            <path
                                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                                            <rect x="6" y="14" width="12" height="8" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-5">
                                                <div style="font-size:36px;opacity:.2">🧾</div>
                                                <p class="mt-2 mb-0" style="font-size:14px">No payments recorded yet</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(function() {
            let grossSubtotal = 0;
            let cartIds = new Set();       // fee IDs
            let cartInvIds = new Set();    // inventory item IDs (for dedup)
            let cartDueIds = new Set();    // inventory sale item IDs (for dues)
            let discountType = 'flat';
            let cartData = [];
            let invItemIndex = 0;          // running index for items[] hidden inputs
            let dueItemIndex = 0;          // running index for inventory_dues[] hidden inputs

            const $feeCards = $('.fee-card');
            const $catItems = $('.cat-item');
            const $cartItemsEl = $('#cartItems');
            const $cartEmpty = $('#cartEmpty');
            const $emptySelect = $('#emptySelect');
            const $feesContainer = $('#feesContainer');
            const $subtotalEl = $('#subtotalAmount');
            const $totalEl = $('#totalAmount');
            const $badgeEl = $('#cartBadge');
            const $collectBtns = $('.collect-btn');
            const $discountInput = $('#discountInput');
            const $discountLine = $('#discountLine');
            const $inventoryDueCards = $('.inv-due-card');
            const $studentSearchForm = $('#studentSearchForm');
            const $studentSearchResults = $('#studentSearchResults');
            const $studentSearchClass = $('#studentSearchClass');
            const $studentSearchSection = $('#studentSearchSection');
            const $studentCidInputs = $('#studentCidSwitch, #studentCidSwitchMobile');
            const $studentCidBtns = $('#studentCidSwitchBtn, #studentCidSwitchBtnMobile');
            const $mobileCheckoutSheet = $('.mobile-checkout-sheet');
            const $mobileCartToggle = $('#mobileCartToggle');
            const $mobileCartBackdrop = $('#mobileCartBackdrop');
            const $mobileCartTotal = $('#mobileCartTotal');
            const $mobileCartCount = $('#mobileCartCount');
            const $mobileSummaryText = $('#mobileSummaryText');
            const $mobileSelectionModal = $('#mobileSelectionModal');
            const $mobileSelectionTitle = $('#mobileSelectionTitle');
            const $mobileSelectionSearch = $('#mobileSelectionSearch');
            const $mobileSelectionList = $('#mobileSelectionList');
            const $mobileSelectionMeta = $('#mobileSelectionMeta');
            const $mobileSelectionAddBtn = $('#mobileSelectionAddBtn');
            const isMobileViewport = () => window.matchMedia('(max-width: 1023.98px)').matches;

            const currentCid = @json($student?->student_cid ?? '');
            const $studentCidInput = $studentCidInputs;
            const $studentCidBtn = $studentCidBtns;

            function loadStudentSearchResults(query = null) {
                if (!$studentSearchResults.length) return;

                $studentSearchResults.html(`
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status" aria-hidden="true"></div>
                        <p class="mb-0">Searching students...</p>
                    </div>
                `);

                $.get('{{ route('fees.search-student') }}', query || ($studentSearchForm.length ? $studentSearchForm.serialize() : {}))
                    .done(function (html) {
                        $studentSearchResults.html(html);
                    })
                    .fail(function () {
                        $studentSearchResults.html(`
                            <div class="text-center text-danger py-4">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <p class="mb-0">Unable to load students. Please try again.</p>
                            </div>
                        `);
                    });
            }

            function selectSearchStudent(url) {
                if (!url) return;
                window.location.href = url;
            }

            function resetStudentSearchSection() {
                if (!$studentSearchSection.length) return;

                $studentSearchSection.html('<option value="">All</option>');
                $studentSearchSection.prop('disabled', true);
            }

            function loadStudentSearchSections(classId) {
                if (!$studentSearchSection.length) return;

                if (!classId) {
                    resetStudentSearchSection();
                    return;
                }

                $studentSearchSection.prop('disabled', true).html('<option value="">Loading...</option>');

                fetch(`{{ route('load_section_groups') }}?school_class_id=${encodeURIComponent(classId)}`)
                    .then((response) => {
                        if (!response.ok) throw new Error('Failed to load sections');
                        return response.json();
                    })
                    .then((data) => {
                        const sections = Array.isArray(data?.sections) ? data.sections : [];
                        let html = '<option value="">All</option>';

                        sections.forEach((section) => {
                            html += `<option value="${section.id}">${section.name_en}</option>`;
                        });

                        $studentSearchSection.html(html).prop('disabled', false);
                    })
                    .catch(() => {
                        resetStudentSearchSection();
                    });
            }

            function showSwitchError(title, message, icon) {
                Swal.fire({
                    icon: icon || 'error',
                    title: title,
                    text: message,
                    confirmButtonColor: '#4338ca',
                });
            }

            function attemptStudentSwitch() {
                const cid = ($studentCidInputs.filter(':visible').val() || $studentCidInputs.first().val() || '').trim();

                if (cid === '' || cid.length < 1) {
                    showSwitchError('Invalid CID', 'Please enter a valid student CID.', 'warning');
                    return;
                }

                if (cid === String(currentCid)) {
                    return;
                }

                $studentCidBtns.prop('disabled', true).text('...');

                Swal.fire({
                    title: 'Searching...',
                    text: 'Looking up student by CID',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading(),
                });

                $.ajax({
                    url: '{{ route('fees.switch_student') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: $('input[name="_token"]').first().val(),
                        student_cid: cid,
                    },
                    success: function(res) {
                        if (!res?.success || !res?.redirect_url) {
                            Swal.close();
                            showSwitchError('Student Not Found', 'No student found with CID: ' + cid +
                                '. Please check and try again.', 'error');
                            return;
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Student Found',
                            text: 'Switching to student ' + (res.student_name || '') + '...',
                            timer: 900,
                            showConfirmButton: false,
                        }).then(() => {
                            window.location.href = res.redirect_url;
                        });
                    },
                    error: function(xhr) {
                        Swal.close();

                        const msg = xhr.responseJSON?.message;

                        if (xhr.status === 422) {
                            showSwitchError('Invalid CID', msg || 'Please enter a valid student CID.',
                                'warning');
                            return;
                        }

                        if (xhr.status === 404) {
                            showSwitchError('Student Not Found', msg || ('No student found with CID: ' +
                                cid + '. Please check and try again.'), 'error');
                            return;
                        }

                        showSwitchError('Error', msg || 'Something went wrong. Please try again.',
                            'error');
                    },
                    complete: function() {
                        $studentCidBtns.prop('disabled', false).text('Switch');
                    }
                });
            }

            $studentCidBtns.on('click', attemptStudentSwitch);
            $studentCidInputs.on('input', function() {
                const value = $(this).val();
                $studentCidInputs.not(this).val(value);
            });
            $studentCidInputs.on('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    attemptStudentSwitch();
                }
            });
            $studentCidInputs.on('blur', function() {
                attemptStudentSwitch();
            });

            const mobileSelectionState = {
                kind: null,
                key: null,
                title: '',
                items: [],
            };
            const mobileSelectionSelected = new Set();

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function mobileSelectionSourceSelector(item) {
                if (item.kind === 'fee') {
                    return `.fee-card[data-id="${item.id}"]`;
                }

                if (item.kind === 'inventory') {
                    return `.inv-item-card[data-inv-id="${item.id}"]`;
                }

                return `.inv-due-card[data-due-id="${item.id}"]`;
            }

            function buildMobileSelectionItems(kind, key) {
                if (kind === 'fee') {
                    return $feeCards.filter(function() {
                        return String($(this).data('cat')) === String(key);
                    }).map(function() {
                        const $card = $(this);
                        const id = String($card.data('id'));
                        return {
                            kind: 'fee',
                            id,
                            key: `fee_${id}`,
                            label: String($card.data('name') || 'Fee'),
                            subtitle: String($card.find('.mono.text-muted').first().text() || '').trim() || 'Fee item',
                            amount: parseFloat($card.data('amount')) || 0,
                            added: $card.hasClass('in-cart'),
                            disabled: false,
                        };
                    }).get();
                }

                if (kind === 'inventory') {
                    return $('.inv-item-card').filter(function() {
                        return String($(this).data('inv-cat')) === String(key);
                    }).map(function() {
                        const $card = $(this);
                        const id = String($card.data('inv-id'));
                        const stock = parseInt($card.data('stock')) || 0;
                        const unit = String($card.data('unit') || '').trim();
                        return {
                            kind: 'inventory',
                            id,
                            key: `inv_${id}`,
                            label: String($card.data('name') || 'Inventory Item'),
                            subtitle: String($card.data('stock-type')) === 'made_to_order'
                                ? `Made to order${unit ? ` · ${unit}` : ''}`
                                : (stock > 0 ? `${stock} left${unit ? ` · ${unit}` : ''}` : 'Out of stock'),
                            amount: parseFloat($card.data('price')) || 0,
                            added: $card.hasClass('in-cart'),
                            disabled: String($card.data('stock-type')) !== 'made_to_order' && stock <= 0,
                        };
                    }).get();
                }

                return $inventoryDueCards.map(function() {
                    const $card = $(this);
                    const id = String($card.data('due-id'));
                    return {
                        kind: 'due',
                        id,
                        key: `due_${id}`,
                        label: String($card.data('due-name') || 'Inventory Due'),
                        subtitle: String($card.data('due-category') || 'Inventory'),
                        amount: parseFloat($card.data('due-amount')) || 0,
                        added: $card.hasClass('in-cart'),
                        disabled: false,
                    };
                }).get();
            }

            function updateMobileSelectionMeta() {
                if (!$mobileSelectionMeta.length) return;

                const selectedItems = mobileSelectionState.items.filter((item) => mobileSelectionSelected.has(item.key) && !item.added && !item.disabled);
                const selectedSubtotal = selectedItems.reduce((sum, item) => sum + (parseFloat(item.amount) || 0), 0);
                $mobileSelectionMeta.text(`${selectedItems.length} selected · BDT ${selectedSubtotal.toFixed(2)}`);
                $mobileSelectionAddBtn.prop('disabled', selectedItems.length === 0);
            }

            function renderMobileSelectionList(filterText = '') {
                if (!$mobileSelectionList.length) return;

                const needle = String(filterText || '').trim().toLowerCase();
                const visibleItems = mobileSelectionState.items.filter((item) => {
                    if (!needle) return true;
                    return [item.label, item.subtitle, item.amount.toFixed(2)]
                        .join(' ')
                        .toLowerCase()
                        .includes(needle);
                });

                if (!visibleItems.length) {
                    $mobileSelectionList.html(`
                        <div class="text-center text-muted py-5">
                            <div style="font-size:36px;opacity:.18">🔎</div>
                            <p class="mt-2 mb-0">No items matched your search.</p>
                        </div>
                    `);
                    updateMobileSelectionMeta();
                    return;
                }

                const html = visibleItems.map((item) => {
                    const isSelected = mobileSelectionSelected.has(item.key) && !item.added && !item.disabled;
                    const sourceSelector = mobileSelectionSourceSelector(item);
                    const statusLabel = item.added ? 'Added' : (item.disabled ? 'Out of stock' : (isSelected ? 'Selected' : 'Add'));
                    const rightMeta = item.kind === 'inventory' && item.subtitle
                        ? `<small class="d-block mt-1 text-muted" style="font-size:10px">${escapeHtml(item.subtitle)}</small>`
                        : `<small class="d-block mt-1 text-muted" style="font-size:10px">${escapeHtml(item.subtitle || '')}</small>`;

                    return `
                        <button type="button"
                            class="mobile-selection-row ${isSelected ? 'is-selected' : ''} ${item.added ? 'is-added' : ''}"
                            data-selection-key="${escapeHtml(item.key)}"
                            data-source-selector="${escapeHtml(sourceSelector)}"
                            data-selection-amount="${escapeHtml(item.amount.toFixed(2))}"
                            ${item.added || item.disabled ? 'disabled' : ''}
                            aria-pressed="${isSelected ? 'true' : 'false'}">
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="min-w-0">
                                        <div class="mobile-selection-title text-truncate">${escapeHtml(item.label)}</div>
                                        <div class="mobile-selection-subtitle text-truncate">${escapeHtml(item.kind === 'fee' ? item.subtitle : item.subtitle)}</div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="mobile-selection-price">BDT ${item.amount.toFixed(2)}</div>
                                        ${rightMeta}
                                    </div>
                                </div>
                            </div>
                            <span class="mobile-selection-mark">${escapeHtml(statusLabel)}</span>
                        </button>
                    `;
                }).join('');

                $mobileSelectionList.html(html);
                updateMobileSelectionMeta();
            }

            function openMobileSelection(kind, key, title) {
                mobileSelectionState.kind = kind;
                mobileSelectionState.key = key;
                mobileSelectionState.title = title;
                mobileSelectionState.items = buildMobileSelectionItems(kind, key);
                mobileSelectionSelected.clear();

                if ($mobileSelectionTitle.length) {
                    $mobileSelectionTitle.text(title);
                }

                if ($mobileSelectionSearch.length) {
                    $mobileSelectionSearch.val('');
                }

                renderMobileSelectionList('');

                if ($mobileSelectionModal.length) {
                    if (isMobileViewport()) {
                        toggleMobileCart(false);
                    }

                    $mobileSelectionModal.modal('show');
                }
            }

            function closeMobileSelection() {
                mobileSelectionSelected.clear();
                mobileSelectionState.kind = null;
                mobileSelectionState.key = null;
                mobileSelectionState.title = '';
                mobileSelectionState.items = [];
                if ($mobileSelectionSearch.length) {
                    $mobileSelectionSearch.val('');
                }
                if ($mobileSelectionList.length) {
                    $mobileSelectionList.empty();
                }
            }

            function toggleMobileCart(forceOpen = null) {
                if (!$mobileCheckoutSheet.length || !isMobileViewport()) return;

                const open = forceOpen === null ? !$mobileCheckoutSheet.hasClass('is-open') : !!forceOpen;
                $mobileCheckoutSheet.toggleClass('is-open', open);
                $mobileCartBackdrop.toggleClass('is-visible', open);
                $('body').toggleClass('mobile-cart-open', open);
            }

            function syncMobileCartState(countText, totalText, summaryText) {
                if ($mobileCartCount.length) {
                    $mobileCartCount.text(countText);
                }
                if ($mobileCartTotal.length) {
                    $mobileCartTotal.text(totalText);
                }
                if ($mobileSummaryText.length) {
                    $mobileSummaryText.text(summaryText);
                }
            }

            function syncMobileStickyOffsets() {
                if (!isMobileViewport()) {
                    document.documentElement.style.removeProperty('--mobile-tabs-top');
                    return;
                }

                const $topbar = $('.mobile-topbar:visible').first();
                const topbarHeight = Math.ceil(($topbar.outerHeight(true) || 0));
                document.documentElement.style.setProperty('--mobile-tabs-top', `${topbarHeight}px`);
            }

            $(document).on('click', '.mobile-select-chip', function() {
                const kind = $(this).data('selection-kind');
                const key = $(this).data('selection-key');
                const title = $(this).data('selection-title') || 'Select Items';

                $('.mobile-select-chip').removeClass('is-active');
                $(this).addClass('is-active');
                openMobileSelection(kind, key, title);
            });

            $mobileSelectionList.on('click', '.mobile-selection-row', function() {
                if ($(this).prop('disabled')) return;

                const key = String($(this).data('selection-key') || '');
                if (!key) return;

                if (mobileSelectionSelected.has(key)) {
                    mobileSelectionSelected.delete(key);
                } else {
                    mobileSelectionSelected.add(key);
                }

                renderMobileSelectionList($mobileSelectionSearch.val() || '');
            });

            $mobileSelectionSearch.on('input', function() {
                renderMobileSelectionList($(this).val() || '');
            });

            $mobileSelectionAddBtn.on('click', function() {
                const selectedItems = mobileSelectionState.items.filter((item) => mobileSelectionSelected.has(item.key) && !item.added && !item.disabled);

                if (!selectedItems.length) {
                    return;
                }

                selectedItems.forEach((item) => {
                    const $source = $(mobileSelectionSourceSelector(item));
                    if ($source.length) {
                        $source.trigger('click');
                    }
                });

                $mobileSelectionModal.modal('hide');
            });

            $mobileSelectionModal.on('hidden.bs.modal', function() {
                $('.mobile-select-chip').removeClass('is-active');
                closeMobileSelection();
            });

            $mobileCartToggle.on('click', function() {
                toggleMobileCart();
            });

            $mobileCartBackdrop.on('click', function() {
                toggleMobileCart(false);
            });

            $(window).on('resize', function() {
                if (!isMobileViewport()) {
                    toggleMobileCart(false);
                    $mobileCartBackdrop.removeClass('is-visible');
                }
                syncMobileStickyOffsets();
            });

            /* ── Category click (fees) ── */
            $catItems.on('click', function() {
                $catItems.removeClass('active');
                $('.inv-cat-item').removeClass('active');
                $(this).addClass('active');
                const sel = $(this).data('cat');
                $emptySelect.hide();
                $feesContainer.removeClass('d-none');
                $('#middlePanelIcon').text('📋');
                $('#middlePanelTitle').text('PENDING FEES').css('color', '#4338ca');
                $('.inv-item-card').hide();
                $feeCards.each(function() {
                    $(this).data('cat') == sel ? $(this).show() : $(this).hide();
                });
            });

            /* ── Inventory category click ── */
            $(document).on('click', '.inv-cat-item', function() {
                $('.inv-cat-item').removeClass('active');
                $catItems.removeClass('active');
                $(this).addClass('active');
                const sel = $(this).data('inv-cat');
                $emptySelect.hide();
                $feesContainer.removeClass('d-none');
                $('#middlePanelIcon').text('📦');
                $('#middlePanelTitle').text('INVENTORY ITEMS').css('color', '#059669');
                $feeCards.hide();
                $('.inv-item-card').each(function() {
                    $(this).data('inv-cat') == sel ? $(this).css('display','flex') : $(this).hide();
                });
            });

            /* ── Inventory due item click ── */
            $(document).on('click', '.inv-due-card', function() {
                const dueId = $(this).data('due-id');
                if (cartDueIds.has(dueId)) return;

                const dueName = $(this).data('due-name');
                const dueCategory = $(this).data('due-category') || 'Inventory';
                const dueAmount = parseFloat($(this).data('due-amount')) || 0;

                cartDueIds.add(dueId);
                cartData.push({ cartKey: 'due_' + dueId, dueId, dueName, dueCategory, dueAmount, type: 'due' });
                $(this).addClass('in-cart');
                $cartEmpty.hide();

                const idx = dueItemIndex++;
                const html = `
                    <div class="cart-row rounded-3 px-3 py-2"
                         id="cart-due_${dueId}"
                         data-unit-price="${dueAmount}"
                         data-subtotal="${dueAmount}"
                         style="background:#fff7ed;border:1.5px solid #fed7aa">
                        <input type="hidden" name="inventory_dues[${idx}][inventory_sale_item_id]" value="${dueId}">
                        <input type="hidden" name="inventory_dues[${idx}][paid_amount]" value="${dueAmount.toFixed(2)}" class="due-paid-hidden">
                        <div class="cart-row-main" style="line-height:1.35">
                            <span class="cart-line-title">${dueName}</span>
                            <span class="cart-line-subtitle">${dueCategory}</span>
                        </div>
                        <div class="cart-row-controls">
                            <div class="cart-control-group cart-control-group--paid">
                                <span class="cart-control-label">Paid</span>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text" style="background:#fff;border-color:#fed7aa">BDT</span>
                                    <input type="number"
                                           class="form-control form-control-sm due-paid-input"
                                           value="${dueAmount.toFixed(2)}"
                                           min="0" step="0.01"
                                           max="${dueAmount}"
                                           style="border-color:#fed7aa">
                                </div>
                            </div>
                            <span class="mono fw-bold due-line-amount cart-line-total" style="color:#c2410c">${dueAmount.toFixed(2)}</span>
                        </div>
                        <button type="button"
                                class="remove-btn cart-row-remove btn btn-light btn-sm border rounded-2 px-2 py-1"
                                data-id="due_${dueId}" data-due-id="${dueId}" data-amount="${dueAmount}" data-type="due"
                                style="font-size:13px;line-height:1">✕</button>
                    </div>`;
                $cartItemsEl.append(html);
                updateUI();
            });

            /* ── Discount type toggle ── */
            $('#btnFlat').on('click', function() {
                discountType = 'flat';
                $(this).addClass('active');
                $('#btnPercent').removeClass('active');
                $('#discountTypeHidden').val('flat');
                $discountInput.attr('placeholder', '0.00');
                updateUI();
            });

            $('#btnPercent').on('click', function() {
                discountType = 'percent';
                $(this).addClass('active');
                $('#btnFlat').removeClass('active');
                $('#discountTypeHidden').val('percent');
                $discountInput.attr('placeholder', '0');
                updateUI();
            });

            $discountInput.on('input', updateUI);

            /* ── Add fee on card click ── */
            $feeCards.on('click', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));
                let gross = parseFloat($(this).data('gross') || $(this).data('amount'));
                let discount = parseFloat($(this).data('discount') || 0);
                let discountLabel = $(this).data('discount-label') || 'Fee Discount';
                let name = $(this).data('name');
                let items = $(this).data('items') || [];
                if (cartIds.has(id)) return;
                cartIds.add(id);
                cartData.push({ id, cartKey: id, name, gross, discount, items });
                $(this).addClass('in-cart');
                $cartEmpty.hide();

                let discountHtml = discount > 0 ?
                    `<span class="mono" style="font-size:11px;color:#059669">${discountLabel}: -${discount.toFixed(2)}</span><br>` :
                    '';
                let grossHtml = discount > 0 ?
                    `<span class="mono text-muted" style="font-size:11px;text-decoration:line-through">${gross.toFixed(2)}</span><br>` :
                    '';

                let itemsBreakdown = '';
                if (items.length > 0) {
                    itemsBreakdown = '<div class="mt-1 ps-2" style="font-size:11px;color:#64748b;">';
                    items.forEach(function(item) {
                        itemsBreakdown += '<div class="d-flex justify-content-between"><span>• ' +
                            item.category + '</span><span>' + parseFloat(item.amount).toFixed(2) +
                            '</span></div>';
                    });
                    itemsBreakdown += '</div>';
                }

                let html = `
                     <div class="cart-row rounded-3 px-3 py-2"
                          id="cart-${id}"
                          style="background:#f8fafc;border:1.5px solid #e2e8f0">
                         <input type="hidden" name="fees[${id}][fee_id]" value="${id}">
                         <input type="hidden" name="fees[${id}][amount]" class="fee-paid-hidden" value="${amount.toFixed(2)}">
                         <div class="cart-row-main" style="line-height:1.35">
                             <span class="cart-line-title">${name}</span>
                             ${discountHtml}
                             ${itemsBreakdown}
                         </div>
                         <div class="cart-row-controls">
                             <div class="cart-control-group cart-control-group--paid">
                                 <span class="cart-control-label">Paid</span>
                                 <div class="input-group input-group-sm">
                                     <span class="input-group-text" style="background:#fff;border-color:#e2e8f0">BDT</span>
                                     <input type="number"
                                            class="form-control form-control-sm fee-paid-input"
                                            value="${amount.toFixed(2)}"
                                            min="0" step="0.01"
                                            style="border-color:#e2e8f0">
                                 </div>
                             </div>
                             ${grossHtml}
                             <span class="mono fw-bold gross-line-amount cart-line-total" style="color:#4338ca">${amount.toFixed(2)}</span>
                         </div>
                         <button type="button"
                                 class="remove-btn cart-row-remove btn btn-light btn-sm border rounded-2 px-2 py-1"
                                 data-id="${id}" data-amount="${amount}" data-type="fee"
                                 style="font-size:13px;line-height:1">✕</button>
                     </div>`;
                $cartItemsEl.append(html);
                updateUI();
            });

            /* ── Add inventory item on card click ── */
            $(document).on('click', '.inv-item-card', function(e) {
                if ($(e.target).is('input')) return;
                let invId = $(this).data('inv-id');
                let stock = parseInt($(this).data('stock')) || 0;
                let isMadeToOrder = String($(this).data('stock-type')) === 'made_to_order';
                if (!isMadeToOrder && stock <= 0) return;
                if (cartInvIds.has(invId)) return;
                let name      = $(this).data('name');
                let unitPrice = parseFloat($(this).data('price'));
                let isFlexible = parseInt($(this).data('flexible-price')) === 1;
                let unit      = $(this).data('unit') || '';
                let qty       = 1;
                let itemSubtotal = unitPrice * qty;
                let cartKey   = 'inv_' + invId;

                cartInvIds.add(invId);
                cartData.push({ cartKey, invId, name, qty, unitPrice, itemSubtotal, isFlexible, isMadeToOrder, type: 'item' });
                $(this).addClass('in-cart');
                $cartEmpty.hide();

                let idx = invItemIndex++;
                let html = `
                    <div class="cart-row rounded-3 px-3 py-2"
                         id="cart-${cartKey}"
                         data-unit-price="${unitPrice}"
                         data-flexible-price="${isFlexible ? 1 : 0}"
                         data-stock-type="${isMadeToOrder ? 'made_to_order' : 'stocked'}"
                         data-subtotal="${itemSubtotal}"
                         style="background:#f0fdf4;border:1.5px solid #bbf7d0">
                        <input type="hidden" name="items[${idx}][inventory_item_id]" value="${invId}">
                        <input type="hidden" name="items[${idx}][quantity]" value="${qty}" class="inv-qty-hidden">
                        <input type="hidden" name="items[${idx}][unit_price]" value="${unitPrice.toFixed(2)}" class="inv-unit-price-hidden">
                        <input type="hidden" name="items[${idx}][paid_amount]" value="${itemSubtotal.toFixed(2)}" class="inv-paid-hidden">
                        <div class="cart-row-main" style="line-height:1.35">
                            <span class="cart-line-title">${name}</span>
                            ${unit ? '<span class="cart-line-subtitle">' + unit + '</span>' : ''}
                        </div>
                        <div class="cart-row-controls">
                            <div class="cart-control-group cart-control-group--unit">
                                <span class="cart-control-label">Unit</span>
                                ${
                                    isFlexible
                                        ? `<div class="input-group input-group-sm">
                                               <input type="number" class="cart-inv-unit-price form-control form-control-sm"
                                                   value="${unitPrice.toFixed(2)}" min="0" step="0.01"
                                                   style="border-color:#fde68a">
                                           </div>`
                                        : `<span class="mono fw-semibold" style="font-size:12px;color:#065f46">${unitPrice.toFixed(2)}</span>`
                                }
                            </div>
                            <div class="cart-control-group cart-control-group--qty">
                                <div class="cart-qty-stepper">
                                    <button type="button" class="cart-qty-stepper-btn cart-qty-decrease" aria-label="Decrease quantity" ${qty <= 1 ? 'disabled' : ''}>−</button>
                                    <input type="number" inputmode="numeric" step="1" class="cart-inv-qty form-control form-control-sm cart-qty-stepper-input"
                                        value="${qty}" min="1"
                                        ${isMadeToOrder ? '' : `max="${stock}"`}
                                        data-max-stock="${isMadeToOrder ? '' : stock}"
                                        data-unlimited-stock="${isMadeToOrder ? 1 : 0}">
                                    <button type="button" class="cart-qty-stepper-btn cart-qty-increase" aria-label="Increase quantity" ${!isMadeToOrder && qty >= stock ? 'disabled' : ''}>+</button>
                                </div>
                            </div>
                            <div class="cart-control-group cart-control-group--paid">
                                <span class="cart-control-label">Paid</span>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="cart-inv-paid form-control form-control-sm"
                                        value="${itemSubtotal.toFixed(2)}" min="0" step="0.01"
                                        max="${itemSubtotal}"
                                        style="border-color:#bbf7d0">
                                </div>
                            </div>
                            <span class="mono fw-bold inv-subtotal-display cart-line-total" style="color:#16a34a">${itemSubtotal.toFixed(2)}</span>
                        </div>
                        <button type="button"
                                class="remove-btn cart-row-remove btn btn-light btn-sm border rounded-2 px-2 py-1"
                                data-id="${cartKey}" data-inv-id="${invId}" data-amount="${itemSubtotal}" data-type="item"
                                style="font-size:13px;line-height:1">✕</button>
                    </div>`;
                $cartItemsEl.append(html);
                updateUI();
            });

            /* ── Remove from cart ── */
            $cartItemsEl.on('click', '.remove-btn', function() {
                let id = $(this).data('id');
                let amount = parseFloat($(this).data('amount'));
                let type = $(this).data('type') || 'fee';
                if (type === 'item') {
                    let invId = $(this).data('inv-id');
                    cartInvIds.delete(invId);
                    $(`.inv-item-card[data-inv-id="${invId}"]`).removeClass('in-cart');
                } else if (type === 'due') {
                    let dueId = $(this).data('due-id');
                    cartDueIds.delete(dueId);
                    $(`.inv-due-card[data-due-id="${dueId}"]`).removeClass('in-cart');
                } else {
                    cartIds.delete(id);
                    $(`.fee-card[data-id="${id}"]`).removeClass('in-cart');
                }
                $('#cart-' + id).remove();
                if (cartIds.size === 0 && cartInvIds.size === 0 && cartDueIds.size === 0) $cartEmpty.show();
                updateUI();
            });

            /* ── Inventory item quantity change in cart ── */
            function syncInventoryRowQuantity($row, newQty) {
                let $qtyInput = $row.find('.cart-inv-qty');
                let unlimited = parseInt($qtyInput.data('unlimited-stock')) === 1;
                let maxStock = unlimited ? Number.MAX_SAFE_INTEGER : (parseInt($qtyInput.data('max-stock')) || parseInt($qtyInput.attr('max')) || 1);
                newQty = Math.max(1, Math.min(maxStock, parseInt(newQty) || 1));

                let unitPrice = parseFloat($row.find('input[name^="items"][name$="[unit_price]"]').val()) || parseFloat($row.data('unit-price'));
                let oldSubtotal = parseFloat($row.data('subtotal'));
                let oldPaid = parseFloat($row.find('input[name^="items"][name$="[paid_amount]"]').val()) || 0;
                let newSubtotal = unitPrice * newQty;

                $qtyInput.val(newQty);
                $row.find('.cart-qty-decrease').prop('disabled', newQty <= 1);
                $row.find('.cart-qty-increase').prop('disabled', !unlimited && newQty >= maxStock);
                $row.data('subtotal', newSubtotal);
                $row.find('.inv-subtotal-display').text(newSubtotal.toFixed(2));
                $row.find('input[name^="items"][name$="[quantity]"]').val(newQty);
                $row.find('input[name^="items"][name$="[unit_price]"]').val(unitPrice.toFixed(2));
                $row.find('input[name^="items"][name$="[paid_amount]"]').attr('max', newSubtotal);

                let currentPaid = newSubtotal;
                if (oldSubtotal > 0) {
                    currentPaid = roundTo2((oldPaid / oldSubtotal) * newSubtotal);
                }
                currentPaid = Math.max(0, Math.min(currentPaid, newSubtotal));

                $row.find('.cart-inv-paid').val(currentPaid.toFixed(2));
                $row.find('input[name^="items"][name$="[paid_amount]"]').val(currentPaid.toFixed(2));
                updateUI();
            }

            function syncInventoryRowUnitPrice($row, newUnitPrice) {
                let $unitPriceInput = $row.find('.cart-inv-unit-price');
                let unlimited = parseInt($row.find('.cart-inv-qty').data('unlimited-stock')) === 1;
                let maxStock = unlimited ? Number.MAX_SAFE_INTEGER : (parseInt($row.find('.cart-inv-qty').data('max-stock')) || 1);
                newUnitPrice = Math.max(0, parseFloat(newUnitPrice) || 0);

                let oldSubtotal = parseFloat($row.data('subtotal'));
                let oldPaid = parseFloat($row.find('input[name^="items"][name$="[paid_amount]"]').val()) || 0;
                let qty = parseInt($row.find('.cart-inv-qty').val()) || 1;
                qty = Math.max(1, Math.min(maxStock, qty));
                let newSubtotal = newUnitPrice * qty;

                $unitPriceInput.val(newUnitPrice.toFixed(2));
                $row.data('unit-price', newUnitPrice);
                $row.data('subtotal', newSubtotal);
                $row.find('.inv-subtotal-display').text(newSubtotal.toFixed(2));
                $row.find('input[name^="items"][name$="[unit_price]"]').val(newUnitPrice.toFixed(2));
                $row.find('input[name^="items"][name$="[paid_amount]"]').attr('max', newSubtotal);

                let currentPaid = newSubtotal;
                if (oldSubtotal > 0) {
                    currentPaid = roundTo2((oldPaid / oldSubtotal) * newSubtotal);
                }
                currentPaid = Math.max(0, Math.min(currentPaid, newSubtotal));

                $row.find('.cart-inv-paid').val(currentPaid.toFixed(2));
                $row.find('input[name^="items"][name$="[paid_amount]"]').val(currentPaid.toFixed(2));
                updateUI();
            }

            $cartItemsEl.on('click', '.cart-qty-decrease', function() {
                let $row = $(this).closest('.cart-row');
                let currentQty = parseInt($row.find('.cart-inv-qty').val()) || 1;
                syncInventoryRowQuantity($row, currentQty - 1);
            });

            $cartItemsEl.on('click', '.cart-qty-increase', function() {
                let $row = $(this).closest('.cart-row');
                let currentQty = parseInt($row.find('.cart-inv-qty').val()) || 1;
                syncInventoryRowQuantity($row, currentQty + 1);
            });

            $cartItemsEl.on('input', '.cart-inv-qty', function() {
                let $row = $(this).closest('.cart-row');
                syncInventoryRowQuantity($row, $(this).val());
            });

            $cartItemsEl.on('input', '.cart-inv-unit-price', function() {
                let $row = $(this).closest('.cart-row');
                syncInventoryRowUnitPrice($row, $(this).val());
            });

            $cartItemsEl.on('input', '.fee-paid-input', function() {
                let $row = $(this).closest('.cart-row');
                let maxDue = parseFloat($row.find('.gross-line-amount').text()) || 0;
                let paid = Math.max(0, parseFloat($(this).val()) || 0);
                if (paid > maxDue) {
                    paid = maxDue;
                    $(this).val(paid.toFixed(2));
                }
                $row.find('input.fee-paid-hidden').val(paid.toFixed(2));
                updateUI();
            });

            $cartItemsEl.on('input', '.cart-inv-paid', function() {
                let $row = $(this).closest('.cart-row');
                let maxDue = parseFloat($row.find('.inv-subtotal-display').text()) || 0;
                let paid = Math.max(0, parseFloat($(this).val()) || 0);
                if (paid > maxDue) {
                    paid = maxDue;
                    $(this).val(paid.toFixed(2));
                }
                $row.find('input[name^="items"][name$="[paid_amount]"]').val(paid.toFixed(2));
                updateUI();
            });

            $cartItemsEl.on('input', '.due-paid-input', function() {
                let $row = $(this).closest('.cart-row');
                let maxDue = parseFloat($row.find('.due-line-amount').text()) || 0;
                let paid = Math.max(0, parseFloat($(this).val()) || 0);
                if (paid > maxDue) {
                    paid = maxDue;
                    $(this).val(paid.toFixed(2));
                }
                $row.find('input[name^="inventory_dues"][name$="[paid_amount]"]').val(paid.toFixed(2));
                updateUI();
            });

            /* ── Compute discount ── */
            function computeDiscount() {
                let raw = Math.max(0, parseFloat($discountInput.val()) || 0);
                if (discountType === 'flat') return Math.min(raw, grossSubtotal);
                if (discountType === 'percent') return (grossSubtotal * Math.min(raw, 100)) / 100;
                return 0;
            }

            function roundTo2(value) {
                return Math.round((parseFloat(value) || 0) * 100) / 100;
            }

            /* ── Update all UI ── */
            function updateUI() {
                let grossTotal = 0;
                let paidTotal = 0;

                $cartItemsEl.find('.cart-row').each(function() {
                    const $row = $(this);
                    if ($row.find('input.fee-paid-hidden').length) {
                        const gross = parseFloat($row.find('.gross-line-amount').text()) || 0;
                        const paid = parseFloat($row.find('input.fee-paid-hidden').val()) || 0;
                        grossTotal += gross;
                        paidTotal += paid;
                    }

                    if ($row.find('input[name^="items"][name$="[inventory_item_id]"]').length) {
                        const gross = parseFloat($row.data('subtotal')) || 0;
                        const paid = parseFloat($row.find('input[name^="items"][name$="[paid_amount]"]').val()) || 0;
                        grossTotal += gross;
                        paidTotal += paid;
                    }

                    if ($row.find('input[name^="inventory_dues["]').length) {
                        const gross = parseFloat($row.data('subtotal')) || 0;
                        const paid = parseFloat($row.find('input[name^="inventory_dues["][name$="[paid_amount]"]').val()) || 0;
                        grossTotal += gross;
                        paidTotal += paid;
                    }
                });

                grossSubtotal = grossTotal;
                const discountAmt = computeDiscount();
                $subtotalEl.text(grossTotal.toFixed(2));
                $totalEl.text(paidTotal.toFixed(2));
                $discountLine.text(`- ${discountAmt.toFixed(2)} BDT`);
                $('#discountAmountHidden').val(discountAmt.toFixed(2));
                let totalCount = cartIds.size + cartInvIds.size;
                totalCount += cartDueIds.size;
                $badgeEl.text(totalCount + (totalCount === 1 ? ' item' : ' items'));
                $collectBtns.prop('disabled', totalCount === 0 || paidTotal <= 0);
                $('#paymentAmount').val(paidTotal.toFixed(2));

                const cartCountText = totalCount === 0
                    ? 'No items added yet'
                    : `${totalCount} item${totalCount === 1 ? '' : 's'} added`;
                const cartTotalText = `BDT ${paidTotal.toFixed(2)}`;
                const summaryText = totalCount === 0
                    ? 'Tap a category to add fees, inventory items, or dues.'
                    : `${totalCount} item${totalCount === 1 ? '' : 's'} selected · ${cartTotalText} ready to collect`;

                syncMobileCartState(cartCountText, cartTotalText, summaryText);

                if (isMobileViewport() && totalCount === 0) {
                    toggleMobileCart(false);
                }
            }



            /* ── AJAX Collect ── */
            $('.collect-btn').on('click', function() {

                if (cartIds.size === 0 && cartInvIds.size === 0 && cartDueIds.size === 0) return;

                const $btn = $(this);
                $btn.prop('disabled', true).html('⏳ &nbsp;Processing...');

                let feesPayload = [];
                let itemsPayload = [];
                let inventoryDuesPayload = [];
                $('#cartItems input[name^="fees["], #cartItems input[name^="items["]').each(function() {
                    let name = $(this).attr('name');
                    let match = name.match(/(fees|items)\[(\d+)\]\[(.+)\]/);
                    if (!match) return;
                    let collection = match[1], idx = match[2], field = match[3];
                    if (collection === 'fees') {
                        if (!feesPayload[idx]) feesPayload[idx] = {};
                        feesPayload[idx][field] = $(this).val();
                    } else {
                        if (!itemsPayload[idx]) itemsPayload[idx] = {};
                        itemsPayload[idx][field] = $(this).val();
                    }
                });
                $('#cartItems input[name^="inventory_dues["]').each(function() {
                    let name = $(this).attr('name');
                    let match = name.match(/inventory_dues\[(\d+)\]\[(.+)\]/);
                    if (!match) return;
                    let idx = match[1], field = match[2];
                    if (!inventoryDuesPayload[idx]) inventoryDuesPayload[idx] = {};
                    inventoryDuesPayload[idx][field] = $(this).val();
                });
                feesPayload = feesPayload.filter(Boolean);
                itemsPayload = itemsPayload.filter(Boolean);
                inventoryDuesPayload = inventoryDuesPayload.filter(Boolean);

                // Build payload
                const payload = {
                    _token: $('input[name="_token"]').first().val(),
                    fees: feesPayload,
                    items: itemsPayload,
                    inventory_dues: inventoryDuesPayload,
                    student_id: @json($student?->id),
                    payment_amount: $('#paymentAmount').val() ?? $totalEl.text(),
                    discount: $('#discountInput').val() || 0,
                    discount_type: $('#discountTypeHidden').val(),
                    discount_amount: $('#discountAmountHidden').val(),
                    description: $('#descriptionInput').val() || '',
                };

                $.ajax({
                    url: '{{ route('fees.pay') }}',
                    method: 'POST',
                    data: payload,
                    dataType: 'json',

                    success: function(res) {

                        // ── Show toast ──
                        $('#toastReceiptNo').text('Receipt: ' + res.receipt_no);
                        const $toast = $('#paymentToast');
                        $toast.css('display', 'flex').hide().fadeIn(250);
                        setTimeout(function() {
                            $toast.fadeOut(400);
                        }, 3500);

                        // ── Reset cart ──
                        cartIds.clear();
                        cartInvIds.clear();
                        invItemIndex = 0;
                        grossSubtotal = 0;
                        cartData = [];
                        $('#cartItems').empty();
                        $('#cartEmpty').show();
                        $('#discountInput').val(0);
                        $('#descriptionInput').val('');
                        $btn.prop('disabled', true).html('✓ &nbsp;COLLECT PAYMENT');
                        updateUI();
                        toggleMobileCart(false);

                        // ── Open receipt in new tab & auto-print ──
                        const receiptUrl = '{{ url('payments') }}/' + res.payment_id +
                            '/receipt';
                        const win = window.open(receiptUrl, '_blank');

                        // Fire print once the new tab has loaded
                        if (win) {
                            win.addEventListener('load', function() {
                                setTimeout(function() {
                                    win.focus();
                                    win.print();
                                }, 600); // small delay for fonts/styles to settle
                            });
                        }

                        // Refresh the collection page so the updated payment history
                        // and fee status are visible immediately after payment.
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    },

                    error: function(xhr) {
                        const msg = xhr.responseJSON?.message ??
                            'Something went wrong. Please try again.';

                        // ── Error toast ──
                        const $toast = $('#paymentToast');
                        $toast.css('background', '#dc2626');
                        $('#paymentToast span:first').text('✕');
                        $toast.find('.fw-800, [style*="font-weight:800"]').text(
                            'PAYMENT FAILED');
                        $('#toastReceiptNo').text(msg);
                        $toast.css('display', 'flex').hide().fadeIn(250);
                        setTimeout(function() {
                            $toast.fadeOut(400, function() {
                                // Reset toast to success style for next time
                                $toast.css('background', '#111');
                                $('#paymentToast span:first').text('✓');
                                $toast.find('[style*="font-weight:800"]').text(
                                    'PAYMENT COLLECTED');
                            });
                        }, 4000);

                        $btn.prop('disabled', false).html('✓ &nbsp;COLLECT PAYMENT');
                    }
                });
            });

            $(document).on('click', '.js-set-all-fees', function() {
                const shouldActivate = String($(this).data('state')) === '1';
                $('#feeActivationForm .js-fee-active-toggle').not(':disabled').prop('checked', shouldActivate);
            });

            $(document).on('submit', '.js-payment-delete-form', function(event) {
                event.preventDefault();

                const form = this;
                const receiptNo = $(form).data('receipt-no') || 'this payment';

                if (typeof Swal === 'undefined') {
                    form.submit();
                    return;
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Delete payment?',
                    text: `This will permanently delete payment ${receiptNo} and its related accounting and inventory records.`,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Tab switching without Bootstrap
            $('#mainTabs a').on('click', function(e) {
                e.preventDefault();
                $('#mainTabs a').removeClass('active');
                $('div.tab-pane').removeClass('active show');
                $(this).addClass('active');
                $($(this).attr('href')).addClass('active show');
            });

            if ($studentSearchForm.length) {
                $studentSearchForm.on('submit', function(e) {
                    e.preventDefault();
                    loadStudentSearchResults();
                });
            }

            $studentSearchResults.on('click', '.student-search-row', function () {
                selectSearchStudent($(this).data('url'));
            });

            $studentSearchResults.on('keydown', '.student-search-row', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectSearchStudent($(this).data('url'));
                }
            });

            if ($studentSearchClass.length) {
                $studentSearchClass.on('change', function () {
                    loadStudentSearchSections(this.value);
                });
            }

            $('#studentSearchModal').on('shown.bs.modal', function() {
                if ($studentSearchForm.length) {
                    $studentSearchForm[0].reset();
                }
                resetStudentSearchSection();
                $studentSearchResults.html(`
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p class="mb-0">Use the filters above to search for a student.</p>
                    </div>
                `);
                $(this).find('input[name="student_id"]').trigger('focus');
            });

            // Activate tab based on URL hash (e.g., /fees/collect_payment/1#tabHistory)
            $(function() {
                const h = window.location.hash;
                if (!h) return;
                setTimeout(function() {
                    const $link = $('#mainTabs a[href="' + h + '"]');
                    if ($link.length) $link.trigger('click');
                }, 100);
            });

            updateUI();
            syncMobileStickyOffsets();
        });
    </script>
@endsection
