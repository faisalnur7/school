@extends('layouts.master')

@section('contents')
<div class="employee-show-page">
    @include('hr._alerts')

    <style>
        .employee-show-page {
            background: #f3f5fa;
            min-height: calc(100vh - 120px);
            padding: 1rem;
            color: #1f2937;
            width: 100%;
            max-width: 100%;
            flex: 0 0 100%;
            box-sizing: border-box;
        }
        .employee-show-shell {
            width: 100%;
            max-width: none;
            margin: 0 auto;
        }
        .employee-show-header,
        .employee-show-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, .05);
        }
        .employee-show-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }
        .employee-show-avatar {
            width: 72px;
            height: 72px;
            flex: 0 0 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eef2ff;
        }
        .employee-show-header-main {
            min-width: 0;
            flex: 1;
        }
        .employee-show-header-main h1 {
            margin: 0 0 .2rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
        }
        .employee-show-subtitle {
            color: #64748b;
            font-size: .9rem;
            margin-bottom: .45rem;
        }
        .employee-show-actions {
            display: flex;
            gap: .5rem;
            flex-wrap: wrap;
        }
        .employee-show-card {
            overflow: hidden;
            margin-bottom: 1rem;
        }
        .employee-show-card-header {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .8rem 1rem;
            border-bottom: 1px solid #edf0f5;
        }
        .employee-show-card-icon {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #fff;
            background: #4f46e5;
            font-size: .8rem;
        }
        .employee-show-card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #172033;
        }
        .employee-show-card-description {
            color: #94a3b8;
            font-size: .75rem;
        }
        .employee-show-card-body {
            padding: 1rem;
        }
        .employee-show-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem 1.25rem;
        }
        .employee-show-field-label {
            display: block;
            margin-bottom: .2rem;
            color: #64748b;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .employee-show-field-value {
            color: #1e293b;
            font-size: .9rem;
            min-height: 1.25rem;
            overflow-wrap: anywhere;
        }
        .employee-show-field-wide {
            grid-column: 1 / -1;
        }
        .employee-show-photo {
            width: 72px;
            height: 72px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .employee-show-session-list {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
        }
        .employee-show-session {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 999px;
            color: #4338ca;
            font-size: .75rem;
            padding: .2rem .55rem;
        }
        .employee-show-table-wrap {
            overflow-x: auto;
        }
        .employee-show-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 680px;
        }
        .employee-show-table th,
        .employee-show-table td {
            border-bottom: 1px solid #edf0f5;
            padding: .7rem .8rem;
            text-align: left;
            vertical-align: middle;
            font-size: .85rem;
        }
        .employee-show-table th {
            background: #f8fafc;
            color: #475569;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .employee-show-table tr:last-child td {
            border-bottom: 0;
        }
        @media (max-width: 767.98px) {
            .employee-show-page { padding: .65rem; }
            .employee-show-header { align-items: flex-start; flex-wrap: wrap; padding: .85rem; }
            .employee-show-actions { width: 100%; margin-left: 88px; }
            .employee-show-grid { grid-template-columns: 1fr; gap: .75rem; }
            .employee-show-field-wide { grid-column: auto; }
        }
    </style>

    <div class="employee-show-shell">
        <header class="employee-show-header">
            <img src="{{ $employee->photo_url }}" alt="{{ $employee->name }}" class="employee-show-avatar">
            <div class="employee-show-header-main">
                <h1>{{ $employee->name }}</h1>
                <div class="employee-show-subtitle">
                    {{ $employee->designation?->name ?? '—' }}
                    <span class="mx-1">|</span>
                    <span class="text-primary">{{ $employee->employee_id }}</span>
                </div>
                <span class="badge badge-{{ $employee->employee_type === 'teacher' ? 'primary' : 'info' }}">{{ ucfirst($employee->employee_type) }}</span>
                <span class="badge badge-{{ $employee->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($employee->status) }}</span>
            </div>
            <div class="employee-show-actions">
                <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-light btn-sm border">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </header>

        <section class="employee-show-card" aria-labelledby="employee-show-personal-heading">
            <div class="employee-show-card-header">
                <span class="employee-show-card-icon"><i class="fas fa-user"></i></span>
                <div>
                    <h2 class="employee-show-card-title" id="employee-show-personal-heading">Personal Information</h2>
                    <div class="employee-show-card-description">Basic identity and account details.</div>
                </div>
            </div>
            <div class="employee-show-card-body">
                <div class="employee-show-grid">
                    @foreach([
                        'Full Name' => $employee->name,
                        'Email' => $employee->user?->email,
                        'Gender' => $employee->gender ? ucfirst($employee->gender) : null,
                        'Date of Birth' => $employee->dob?->format('d M Y'),
                    ] as $label => $value)
                        <div>
                            <span class="employee-show-field-label">{{ $label }}</span>
                            <div class="employee-show-field-value">{{ $value ?: '—' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="employee-show-card" aria-labelledby="employee-show-employment-heading">
            <div class="employee-show-card-header">
                <span class="employee-show-card-icon"><i class="fas fa-briefcase"></i></span>
                <div>
                    <h2 class="employee-show-card-title" id="employee-show-employment-heading">Employment Information</h2>
                    <div class="employee-show-card-description">Role, reporting structure, payment, and profile photo.</div>
                </div>
            </div>
            <div class="employee-show-card-body">
                <div class="employee-show-grid">
                    @foreach([
                        'Employee Type' => $employee->employee_type ? ucfirst($employee->employee_type) : null,
                        'Designation' => $employee->designation?->name,
                        'Department' => is_string($employee->department) ? $employee->department : $employee->department?->name,
                        'Reporting Manager' => $employee->manager?->name,
                        'Joining Date' => $employee->joining_date?->format('d M Y'),
                        'Payment Method' => $employee->paymentInformation?->payment_method
                            ? ucwords(str_replace('_', ' ', $employee->paymentInformation->payment_method))
                            : 'Cash',
                    ] as $label => $value)
                        <div>
                            <span class="employee-show-field-label">{{ $label }}</span>
                            <div class="employee-show-field-value">{{ $value ?: '—' }}</div>
                        </div>
                    @endforeach

                    @if($employee->employee_type === 'teacher')
                        <div class="employee-show-field-wide">
                            <span class="employee-show-field-label">Academic Sessions</span>
                            <div class="employee-show-session-list">
                                @forelse($employee->academicSessions as $session)
                                    <span class="employee-show-session">{{ $session->name_en ?: $session->name_bn }}</span>
                                @empty
                                    <span class="employee-show-field-value">—</span>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <div>
                        <span class="employee-show-field-label">Profile Photo</span>
                        <img src="{{ $employee->photo_url }}" alt="{{ $employee->name }}" class="employee-show-photo">
                    </div>
                </div>
            </div>
        </section>

        <section class="employee-show-card" aria-labelledby="employee-show-contact-heading">
            <div class="employee-show-card-header">
                <span class="employee-show-card-icon"><i class="fas fa-address-card"></i></span>
                <div>
                    <h2 class="employee-show-card-title" id="employee-show-contact-heading">HR &amp; Contact Information</h2>
                    <div class="employee-show-card-description">Identification, contact details, and emergency contact.</div>
                </div>
            </div>
            <div class="employee-show-card-body">
                <div class="employee-show-grid">
                    @foreach([
                        'National ID' => $employee->national_id,
                        'Passport Number' => $employee->passport_number,
                        'Phone' => $employee->phone,
                        'Emergency Contact' => $employee->emergency_contact_name,
                        'Emergency Phone' => $employee->emergency_contact_phone,
                        'Relationship' => $employee->emergency_contact_relation,
                        'Address' => $employee->address,
                    ] as $label => $value)
                        <div class="{{ $label === 'Address' ? 'employee-show-field-wide' : '' }}">
                            <span class="employee-show-field-label">{{ $label }}</span>
                            <div class="employee-show-field-value">{{ $value ?: '—' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="employee-show-card" aria-labelledby="employee-show-qualifications-heading">
            <div class="employee-show-card-header">
                <span class="employee-show-card-icon"><i class="fas fa-graduation-cap"></i></span>
                <div>
                    <h2 class="employee-show-card-title" id="employee-show-qualifications-heading">Educational Qualifications</h2>
                    <div class="employee-show-card-description">Academic details saved from the employee form.</div>
                </div>
            </div>
            <div class="employee-show-table-wrap">
                <table class="employee-show-table">
                    <thead>
                        <tr>
                            <th>Qualification</th>
                            <th>Institution</th>
                            <th>Passing Year</th>
                            <th>Result / Grade</th>
                            <th>Certificate / Document</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->qualifications as $qualification)
                            <tr>
                                <td>{{ $qualification->qualification?->display_name ?? '—' }}</td>
                                <td>{{ $qualification->institution_name ?: '—' }}</td>
                                <td>{{ $qualification->passing_year ?: '—' }}</td>
                                <td>{{ $qualification->result ?: '—' }}</td>
                                <td>
                                    @if($qualification->document_url)
                                        <a href="{{ $qualification->document_url }}" target="_blank" rel="noopener">
                                            {{ $qualification->document_original_name ?: 'View document' }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No educational qualifications added.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @php($paymentInformation = $employee->paymentInformation)
        @if($paymentInformation && $paymentInformation->payment_method !== 'cash')
            <section class="employee-show-card" aria-labelledby="employee-show-payment-heading">
                <div class="employee-show-card-header">
                    <span class="employee-show-card-icon"><i class="fas fa-money-check-alt"></i></span>
                    <div>
                        <h2 class="employee-show-card-title" id="employee-show-payment-heading">Payment Details</h2>
                        <div class="employee-show-card-description">Additional details for the selected payment method.</div>
                    </div>
                </div>
                <div class="employee-show-card-body">
                    <div class="employee-show-grid">
                        @if(in_array($paymentInformation->payment_method, ['bank', 'bank_transfer'], true))
                            <div>
                                <span class="employee-show-field-label">Bank Name</span>
                                <div class="employee-show-field-value">{{ $paymentInformation->bank_name ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="employee-show-field-label">Account Number</span>
                                <div class="employee-show-field-value">{{ $paymentInformation->account_number ?: '—' }}</div>
                            </div>
                        @else
                            <div>
                                <span class="employee-show-field-label">Wallet Provider</span>
                                <div class="employee-show-field-value">{{ $paymentInformation->mobile_wallet_provider ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="employee-show-field-label">Wallet Number</span>
                                <div class="employee-show-field-value">{{ $paymentInformation->mobile_wallet_number ?: '—' }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
