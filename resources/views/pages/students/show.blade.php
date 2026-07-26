@extends('layouts.master')

@section('styles')
    <style>
        .student-profile-page {
            width: 100%;
        }

        .student-profile-shell {
            padding: 0.25rem 0 1.5rem;
        }

        .student-profile-hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.16), transparent 26%),
                linear-gradient(135deg, #0f172a 0%, #1e293b 55%, #334155 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.14);
        }

        .student-profile-identity {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .student-profile-copy {
            min-width: 0;
        }

        .student-profile-avatar {
            width: 180px;
            height: 240px;
            flex: 0 0 180px;
            border-radius: 22px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .student-profile-avatar--placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 3.25rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #334155, #475569);
        }

        .student-profile-name {
            margin: 0;
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .student-profile-subtitle {
            margin: 0.35rem 0 0;
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.95rem;
        }

        .student-profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.85rem;
        }

        .student-profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.38rem 0.7rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .student-profile-chip--light {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .student-profile-chip--success {
            background: rgba(34, 197, 94, 0.16);
            color: #dcfce7;
            border: 1px solid rgba(34, 197, 94, 0.26);
        }

        .student-profile-chip--muted {
            background: rgba(148, 163, 184, 0.14);
            color: #e2e8f0;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .student-profile-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.6rem;
        }

        .student-profile-actions .btn {
            min-height: 40px;
            border-radius: 12px;
            font-weight: 600;
        }

        .student-profile-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1rem;
        }

        .student-profile-stat {
            background: #fff;
            border: 1px solid #e7e5e4;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            padding: 1rem;
        }

        .student-profile-stat-label {
            margin: 0;
            font-size: 0.78rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .student-profile-stat-value {
            margin: 0.35rem 0 0;
            font-size: 1.35rem;
            line-height: 1.1;
            font-weight: 800;
            color: #111827;
        }

        .student-profile-stat-note {
            margin: 0.3rem 0 0;
            font-size: 0.88rem;
            color: #6b7280;
        }

        .student-profile-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1.35fr);
            gap: 1rem;
        }

        .student-profile-column {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            min-width: 0;
        }

        .student-profile-panel {
            background: #fff;
            border: 1px solid #e7e5e4;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .student-profile-panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 1rem 1rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .student-profile-panel-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 800;
            color: #111827;
        }

        .student-profile-panel-subtitle {
            margin: 0.25rem 0 0;
            font-size: 0.88rem;
            color: #6b7280;
        }

        .student-profile-panel-body {
            padding: 1rem;
        }

        .student-profile-info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .student-profile-info-item {
            padding: 0.85rem 0.9rem;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            background: #fcfcfd;
        }

        .student-profile-info-label {
            display: block;
            margin-bottom: 0.3rem;
            font-size: 0.76rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .student-profile-info-value {
            font-size: 0.96rem;
            font-weight: 600;
            color: #111827;
            word-break: break-word;
        }

        .student-profile-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .student-profile-badge--success {
            background: #dcfce7;
            color: #166534;
        }

        .student-profile-badge--secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .student-profile-badge--info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .student-profile-badge--warning {
            background: #fef3c7;
            color: #92400e;
        }

        .student-profile-badge--danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .student-profile-table-wrap {
            overflow-x: auto;
        }

        .student-profile-table {
            width: 100%;
            margin-bottom: 0;
        }

        .student-profile-table thead th {
            border-top: 0;
            border-bottom: 1px solid #e5e7eb;
            background: #f8fafc;
            color: #475569;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }

        .student-profile-table tbody td {
            vertical-align: top;
            padding: 0.9rem 0.75rem;
        }

        .student-profile-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .student-profile-tabs .nav-tabs {
            border-bottom: 1px solid #e5e7eb;
        }

        .student-profile-tabs .nav-link {
            border: 0;
            border-bottom: 2px solid transparent;
            color: #6b7280;
            font-weight: 700;
            border-radius: 0;
            padding-left: 0.2rem;
            padding-right: 0.2rem;
        }

        .student-profile-tabs .nav-link.active {
            color: #111827;
            border-bottom-color: #111827;
            background: transparent;
        }

        .student-profile-empty {
            padding: 1.2rem;
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            color: #6b7280;
            background: #fafafa;
            text-align: center;
        }

        .student-profile-section-stack {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .student-profile-link {
            color: inherit;
            text-decoration: none;
        }

        .student-profile-link:hover {
            color: inherit;
            text-decoration: none;
        }

        @media (max-width: 1199.98px) {
            .student-profile-grid,
            .student-profile-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .student-profile-hero,
            .student-profile-panel-header {
                flex-direction: column;
            }

            .student-profile-identity {
                align-items: flex-start;
            }

            .student-profile-actions {
                justify-content: flex-start;
            }

            .student-profile-grid,
            .student-profile-stats,
            .student-profile-info-grid {
                grid-template-columns: 1fr;
            }

            .student-profile-name {
                font-size: 1.6rem;
            }

            .student-profile-avatar {
                width: 140px;
                height: 186px;
                flex-basis: 140px;
            }
        }
    </style>
@endsection

@section('contents')
    @php
        $currentAcademicInfo = $currentAcademicInfo ?? $student->academicInformations->sortByDesc('id')->first();
        $academicCount = $student->academicInformations->count();
        $feeCount = $student->fees->count();
        $transportCount = $transports->count();
        $inventorySaleCount = $inventorySales->count();
        $paymentCount = $student->payments->count();

        $statusLabel = $student->status ? 'Active' : 'Inactive';
        $statusClass = $student->status ? 'student-profile-badge--success' : 'student-profile-badge--secondary';
        $avatarUrl = $student->photo_url ?? asset('assets/dist/img/avatar.png');
    @endphp

    <div class="student-profile-page">
        <div class="student-profile-shell container-fluid">
            <div class="student-profile-hero">
                <div class="student-profile-identity">
                    @if ($student->image && file_exists(public_path($student->image)))
                        <img src="{{ $avatarUrl }}" alt="{{ $student->full_name_en }}" class="student-profile-avatar">
                    @else
                        <div class="student-profile-avatar student-profile-avatar--placeholder">
                            {{ strtoupper(substr($student->full_name_en ?? 'S', 0, 1)) }}
                        </div>
                    @endif

                    <div class="student-profile-copy">
                        <h1 class="student-profile-name">{{ $student->full_name_en }}</h1>
                        @if ($student->full_name_bn)
                            <p class="student-profile-subtitle">{{ $student->full_name_bn }}</p>
                        @endif

                        <div class="student-profile-meta">
                            <span class="student-profile-chip student-profile-chip--light">
                                CID: {{ $student->student_cid ?? 'N/A' }}
                            </span>
                            <span class="student-profile-chip {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                            @if ($currentAcademicInfo)
                                <span class="student-profile-chip student-profile-chip--muted">
                                    {{ $currentAcademicInfo->academicSession->name_en ?? 'Session N/A' }}
                                </span>
                                <span class="student-profile-chip student-profile-chip--muted">
                                    {{ $currentAcademicInfo->schoolClass->name_en ?? 'Class N/A' }}
                                </span>
                                <span class="student-profile-chip student-profile-chip--muted">
                                    Roll: {{ $currentAcademicInfo->roll ?? 'N/A' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="student-profile-actions">
                    <a href="{{ route('students.index') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                    <a href="{{ route('students.pdf', $student->id) }}" class="btn btn-danger">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </a>
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary">
                        <i class="fas fa-pen mr-1"></i> Edit Profile
                    </a>
                </div>
            </div>

            <div class="student-profile-stats">
                <div class="student-profile-stat">
                    <p class="student-profile-stat-label">Academic Records</p>
                    <p class="student-profile-stat-value">{{ number_format($academicCount) }}</p>
                    <p class="student-profile-stat-note">Session and class history</p>
                </div>
                <div class="student-profile-stat">
                    <p class="student-profile-stat-label">Fees</p>
                    <p class="student-profile-stat-value">{{ number_format($feeCount) }}</p>
                    <p class="student-profile-stat-note">Assigned fee entries</p>
                </div>
                <div class="student-profile-stat">
                    <p class="student-profile-stat-label">Payments</p>
                    <p class="student-profile-stat-value">{{ number_format($paymentCount) }}</p>
                    <p class="student-profile-stat-note">Payment transactions</p>
                </div>
                <div class="student-profile-stat">
                    <p class="student-profile-stat-label">Transport</p>
                    <p class="student-profile-stat-value">{{ number_format($transportCount) }}</p>
                    <p class="student-profile-stat-note">Active transport records</p>
                </div>
                <div class="student-profile-stat">
                    <p class="student-profile-stat-label">Inventory Sales</p>
                    <p class="student-profile-stat-value">{{ number_format($inventorySaleCount) }}</p>
                    <p class="student-profile-stat-note">Purchased items from inventory</p>
                </div>
            </div>

            <div class="student-profile-grid">
                <div class="student-profile-column">
                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Basic Information</h2>
                                <p class="student-profile-panel-subtitle">Core identity and profile details</p>
                            </div>
                            <span class="student-profile-badge {{ $student->status ? 'student-profile-badge--success' : 'student-profile-badge--secondary' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="student-profile-panel-body">
                            <div class="student-profile-info-grid">
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Full Name</span>
                                    <div class="student-profile-info-value">{{ $student->full_name_en ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Bangla Name</span>
                                    <div class="student-profile-info-value">{{ $student->full_name_bn ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Gender</span>
                                    <div class="student-profile-info-value">{{ $student->gender_text ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Religion</span>
                                    <div class="student-profile-info-value">{{ $student->religion_text ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Date of Birth</span>
                                    <div class="student-profile-info-value">
                                        {{ $student->date_of_birth ? $student->date_of_birth->format('d M, Y') : 'N/A' }}
                                        @if ($student->date_of_birth)
                                            <span class="text-muted">({{ $student->date_of_birth->age }} years)</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Blood Group</span>
                                    <div class="student-profile-info-value">{{ $student->blood_group_text ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Birth Certificate</span>
                                    <div class="student-profile-info-value">{{ $student->birth_certificate_number ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">CID</span>
                                    <div class="student-profile-info-value">{{ $student->student_cid ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Parents and Guardian</h2>
                                <p class="student-profile-panel-subtitle">Family details and guardian contact</p>
                            </div>
                        </div>

                        <div class="student-profile-panel-body">
                            <div class="student-profile-section-stack">
                                <div class="student-profile-info-grid">
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Father Name</span>
                                        <div class="student-profile-info-value">{{ $student->father_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Father Profession</span>
                                        <div class="student-profile-info-value">{{ $student->fathersProfession?->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Father Phone</span>
                                        <div class="student-profile-info-value">{{ $student->father_phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Father Email</span>
                                        <div class="student-profile-info-value">{{ $student->father_email ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="student-profile-info-grid">
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Mother Name</span>
                                        <div class="student-profile-info-value">{{ $student->mother_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Mother Profession</span>
                                        <div class="student-profile-info-value">{{ $student->mothersProfession?->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Mother Phone</span>
                                        <div class="student-profile-info-value">{{ $student->mother_phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Mother Email</span>
                                        <div class="student-profile-info-value">{{ $student->mother_email ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="student-profile-info-grid">
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Name</span>
                                        <div class="student-profile-info-value">{{ $student->guardian_name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Relation</span>
                                        <div class="student-profile-info-value">{{ $student->guardian_relation ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Profession</span>
                                        <div class="student-profile-info-value">{{ $student->guardianProfession?->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Phone</span>
                                        <div class="student-profile-info-value">{{ $student->guardian_phone ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Email</span>
                                        <div class="student-profile-info-value">{{ $student->guardian_email ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Guardian Address</span>
                                        <div class="student-profile-info-value">{{ $student->guardian_address ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Contact and Address</h2>
                                <p class="student-profile-panel-subtitle">Current and permanent addresses</p>
                            </div>
                        </div>

                        <div class="student-profile-panel-body">
                            <div class="student-profile-info-grid">
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Present Address</span>
                                    <div class="student-profile-info-value">{{ $student->present_address ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Permanent Address</span>
                                    <div class="student-profile-info-value">{{ $student->permanent_address ?? 'N/A' }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Present Location</span>
                                    <div class="student-profile-info-value">
                                        {{ collect([
                                            $student->presentDivision?->name_en,
                                            $student->presentDistrict?->name_en,
                                            $student->presentPoliceStation?->name_en,
                                            $student->presentPostOffice?->name_en,
                                        ])->filter()->implode(', ') ?: 'N/A' }}
                                    </div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Permanent Location</span>
                                    <div class="student-profile-info-value">
                                        {{ collect([
                                            $student->permanentDivision?->name_en,
                                            $student->permanentDistrict?->name_en,
                                            $student->permanentPoliceStation?->name_en,
                                            $student->permanentPostOffice?->name_en,
                                        ])->filter()->implode(', ') ?: 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="student-profile-column">
                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Academic Information</h2>
                                <p class="student-profile-panel-subtitle">Current placement and academic history</p>
                            </div>
                            @if ($currentAcademicInfo)
                                <span class="student-profile-badge student-profile-badge--info">
                                    Current Record
                                </span>
                            @endif
                        </div>

                        <div class="student-profile-panel-body">
                            @if ($currentAcademicInfo)
                                <div class="student-profile-info-grid mb-3">
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Session</span>
                                        <div class="student-profile-info-value">{{ $currentAcademicInfo->academicSession->name_en ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Class</span>
                                        <div class="student-profile-info-value">{{ $currentAcademicInfo->schoolClass->name_en ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Section</span>
                                        <div class="student-profile-info-value">{{ $currentAcademicInfo->section->name_en ?? 'N/A' }}</div>
                                    </div>
                                    <div class="student-profile-info-item">
                                        <span class="student-profile-info-label">Group</span>
                                        <div class="student-profile-info-value">{{ $currentAcademicInfo->group->name_en ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @endif

                            <div class="student-profile-table-wrap">
                                <table class="table table-hover student-profile-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Session</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Group</th>
                                            <th>Roll</th>
                                            <th>Current</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($student->academicInformations->sortByDesc('id') as $info)
                                            <tr>
                                                <td>{{ $info->academicSession->name_en ?? 'N/A' }}</td>
                                                <td>{{ $info->schoolClass->name_en ?? 'N/A' }}</td>
                                                <td>{{ $info->section->name_en ?? 'N/A' }}</td>
                                                <td>{{ $info->group->name_en ?? 'N/A' }}</td>
                                                <td>{{ $info->roll ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($info->is_current)
                                                        <span class="student-profile-badge student-profile-badge--success">Yes</span>
                                                    @else
                                                        <span class="student-profile-badge student-profile-badge--secondary">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
                                                    <div class="student-profile-empty">No academic record found.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Fee Summary</h2>
                                <p class="student-profile-panel-subtitle">Billing overview and fee groups</p>
                            </div>
                            <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                                <a href="{{ route('fees.collect_payment', ['student_id' => $student->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Collect
                                </a>
                                <a href="{{ route('payments.index') }}?student_id={{ $student->id }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-receipt mr-1"></i> Payments
                                </a>
                            </div>
                        </div>

                        <div class="student-profile-panel-body">
                            <div class="student-profile-info-grid mb-3">
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Total Fees</span>
                                    <div class="student-profile-info-value">৳{{ number_format($totalAmount, 2) }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Total Paid</span>
                                    <div class="student-profile-info-value text-success">৳{{ number_format($totalPaid, 2) }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Total Due</span>
                                    <div class="student-profile-info-value text-danger">৳{{ number_format($totalDue, 2) }}</div>
                                </div>
                                <div class="student-profile-info-item">
                                    <span class="student-profile-info-label">Balance</span>
                                    <div class="student-profile-info-value {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $totalDue > 0 ? 'Due' : 'Clear' }}
                                    </div>
                                </div>
                            </div>

                            @php
                                $feeGroups = $regularFees->groupBy(fn ($fee) => $fee->feeSet->name ?? 'Unknown Fee Set');
                            @endphp

                            @if ($feeGroups->isNotEmpty())
                                <div class="student-profile-tabs">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach ($feeGroups as $group => $fees)
                                            <li class="nav-item" role="presentation">
                                                <a
                                                    class="nav-link {{ $loop->first ? 'active' : '' }}"
                                                    id="fee-tab-{{ Str::slug($group) }}"
                                                    data-toggle="tab"
                                                    href="#fee-content-{{ Str::slug($group) }}"
                                                    role="tab"
                                                >
                                                    {{ $group }}
                                                    <span class="badge badge-secondary ml-1">{{ $fees->count() }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <div class="tab-content pt-3">
                                        @foreach ($feeGroups as $group => $fees)
                                            <div
                                                class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                                id="fee-content-{{ Str::slug($group) }}"
                                                role="tabpanel"
                                            >
                                                <div class="student-profile-table-wrap">
                                                    <table class="table table-sm table-hover student-profile-table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Amount</th>
                                                                <th>Scholarship</th>
                                                                <th>Paid</th>
                                                                <th>Due</th>
                                                                <th>Due Date</th>
                                                                <th>Active</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($fees as $fee)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>৳{{ number_format($fee->amount, 2) }}</td>
                                                                    <td>৳{{ number_format($fee->scholarship_discount, 2) }}</td>
                                                                    <td class="text-success">৳{{ number_format($fee->paid_amount, 2) }}</td>
                                                                    <td class="text-danger">৳{{ number_format($fee->due_amount, 2) }}</td>
                                                                    <td>{{ optional($fee->due_date)->format('d M, Y') ?? 'N/A' }}</td>
                                                                    <td>
                                                                        @if ($fee->status !== 'paid')
                                                                            <form action="{{ route('fees.toggle-status', $fee->id) }}" method="POST">
                                                                                @csrf
                                                                                <div class="custom-control custom-switch">
                                                                                    <input
                                                                                        type="checkbox"
                                                                                        class="custom-control-input"
                                                                                        id="regularFeeSwitch{{ $fee->id }}"
                                                                                        onchange="this.form.submit()"
                                                                                        {{ $fee->is_active ? 'checked' : '' }}
                                                                                    >
                                                                                    <label class="custom-control-label" for="regularFeeSwitch{{ $fee->id }}"></label>
                                                                                </div>
                                                                            </form>
                                                                        @else
                                                                            <span class="student-profile-badge student-profile-badge--success">Locked</span>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        <span class="student-profile-badge {{ $fee->status === 'paid' ? 'student-profile-badge--success' : 'student-profile-badge--warning' }}">
                                                                            {{ ucfirst($fee->status) }}
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="student-profile-empty">No regular fee records found.</div>
                            @endif
                        </div>
                    </div>

                    <div class="student-profile-panel">
                        <div class="student-profile-panel-header">
                            <div>
                                <h2 class="student-profile-panel-title">Transport Assignments</h2>
                                <p class="student-profile-panel-subtitle">Transport fee records and assignment status</p>
                            </div>
                            @if ($transports->isNotEmpty() || $transportFeesFromBilling->isNotEmpty())
                                <a href="{{ route('transports.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus mr-1"></i> Assign Transport
                                </a>
                            @endif
                        </div>

                        <div class="student-profile-panel-body">
                            @if ($transports->isEmpty() && $transportFeesFromBilling->isEmpty())
                                <div class="student-profile-empty">
                                    No transport fee assignments found.
                                </div>
                            @else
                                @if ($transportFeesFromBilling->isNotEmpty())
                                    <div class="mb-3">
                                        <h3 class="student-profile-panel-title" style="font-size: 0.98rem;">Transport Fee Schedule</h3>
                                        <p class="student-profile-panel-subtitle">Billing-generated transport fee records</p>
                                        <div class="student-profile-table-wrap mt-3">
                                            <table class="table table-sm table-hover student-profile-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Due Date</th>
                                                        <th>Amount</th>
                                                        <th>Paid</th>
                                                        <th>Due</th>
                                                        <th>Status</th>
                                                        <th>Active</th>
                                                        <th>Fee Set</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transportFeesFromBilling as $fee)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ optional($fee->due_date)->format('d M, Y') ?? 'N/A' }}</td>
                                                            <td>৳{{ number_format($fee->amount, 2) }}</td>
                                                            <td class="text-success">৳{{ number_format($fee->paid_amount, 2) }}</td>
                                                            <td class="text-danger">৳{{ number_format($fee->due_amount, 2) }}</td>
                                                            <td>
                                                                <span class="student-profile-badge {{ $fee->status === 'paid' ? 'student-profile-badge--success' : 'student-profile-badge--warning' }}">
                                                                    {{ ucfirst($fee->status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if ($fee->status !== 'paid')
                                                                    <form action="{{ route('fees.toggle-status', $fee->id) }}" method="POST">
                                                                        @csrf
                                                                        <div class="custom-control custom-switch">
                                                                            <input
                                                                                type="checkbox"
                                                                                class="custom-control-input"
                                                                                id="feeSwitch{{ $fee->id }}"
                                                                                onchange="this.form.submit()"
                                                                                {{ $fee->is_active ? 'checked' : '' }}
                                                                            >
                                                                            <label class="custom-control-label" for="feeSwitch{{ $fee->id }}"></label>
                                                                        </div>
                                                                    </form>
                                                                @else
                                                                    <span class="student-profile-badge student-profile-badge--success">Paid</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ optional($fee->feeSet)->name ?? 'N/A' }}</td>
                                                            <td class="text-nowrap">
                                                                @if ($fee->status !== 'paid')
                                                                    <a href="{{ route('fees.edit', $fee->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form action="{{ route('fees.delete', $fee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this fee record?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class="text-muted">No actions</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @if ($transports->isNotEmpty())
                                    <div>
                                        <h3 class="student-profile-panel-title" style="font-size: 0.98rem;">Assigned Transport Records</h3>
                                        <p class="student-profile-panel-subtitle">Manual transport assignments</p>
                                        <div class="student-profile-table-wrap mt-3">
                                            <table class="table table-sm table-hover student-profile-table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Session</th>
                                                        <th>Category</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transports as $transport)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $transport->academicSession->name_en ?? 'N/A' }}</td>
                                                            <td>{{ $transport->feeCategory->name ?? 'N/A' }}</td>
                                                            <td>৳{{ number_format($transport->amount ?? 0, 2) }}</td>
                                                            <td>
                                                                <span class="student-profile-badge {{ ($transport->status ?? null) === 'active' ? 'student-profile-badge--success' : 'student-profile-badge--secondary' }}">
                                                                    {{ ucfirst($transport->status ?? 'inactive') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="student-profile-panel mt-4 mb-4">
            <div class="student-profile-panel-header">
                <div>
                    <h2 class="student-profile-panel-title">Inventory Purchases</h2>
                    <p class="student-profile-panel-subtitle">Student-linked inventory items with paid and due status</p>
                </div>
            </div>

            <div class="student-profile-panel-body">
                @if ($inventorySales->isEmpty())
                    <div class="student-profile-empty">
                        No inventory purchase records found for this student.
                    </div>
                @else
                    <div class="student-profile-table-wrap">
                        <table class="table table-sm table-hover student-profile-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Receipt</th>
                                    <th>Items</th>
                                    <th>Gross</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventorySales as $sale)
                                    @php
                                        $itemLabels = ($sale->items ?? collect())->map(function ($item) {
                                            $name = $item->inventoryItem?->name ?? 'Item';
                                            $category = $item->inventoryItem?->category?->name;

                                            return $name . ($category ? ' • ' . $category : '');
                                        })->unique()->values();
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="student-profile-info-value">{{ $sale->payment?->receipt_no ?? 'N/A' }}</div>
                                            <div class="text-muted small">{{ optional($sale->created_at)->format('d M, Y') ?? 'N/A' }}</div>
                                        </td>
                                        <td style="min-width: 240px;">
                                            <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                                                @foreach ($itemLabels->take(4) as $label)
                                                    <span class="student-profile-badge student-profile-badge--secondary">{{ $label }}</span>
                                                @endforeach
                                                @if ($itemLabels->count() > 4)
                                                    <span class="student-profile-badge student-profile-badge--info">+{{ $itemLabels->count() - 4 }} more</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                                        <td class="text-end text-success">৳{{ number_format($sale->paid_amount, 2) }}</td>
                                        <td class="text-end text-danger">৳{{ number_format($sale->due_amount, 2) }}</td>
                                        <td>
                                            <span class="student-profile-badge {{ $sale->due_amount > 0 ? 'student-profile-badge--warning' : 'student-profile-badge--success' }}">
                                                {{ $sale->due_amount > 0 ? 'Due' : 'Paid' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
