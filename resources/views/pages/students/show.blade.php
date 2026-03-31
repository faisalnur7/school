@extends('layouts.master')

@section('contents')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="font-bold text-lg">👤 Student Profile</h4>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">← Back to Students</a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center flex flex-col justify-center items-center">
                        @php
                            $avatarUrl = $student->image
                                ? asset($student->image)
                                : (strtolower($student->gender ?? '') === 'female'
                                    ? asset('assets/dist/img/avatar4.png')
                                    : asset('assets/dist/img/avatar.png'));
                        @endphp
                        <img src="{{ $avatarUrl }}" class="rounded-circle mb-3" alt="Student" width="120"
                            height="120">
                        <h5>{{ $student->full_name_en }}</h5>
                        <p class="text-muted mb-1">{{ $student->full_name_bn }}</p>
                        <p class="mb-0">Status: <span
                                class="badge bg-{{ $student->status ? 'success' : 'secondary' }}">{{ $student->status ? 'Active' : 'Inactive' }}</span>
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-primary text-white">Basic Information</div>
                    <div class="card-body">
                        <p><strong>Gender:</strong> {{ $student->gender_text }}</p>
                        <p><strong>Date of Birth:</strong>
                            {{ optional($student->date_of_birth)->format('d M, Y') ?? 'N/A' }}</p>
                        <p><strong>Blood Group:</strong> {{ $student->blood_group_text }}</p>
                        <p><strong>Religion:</strong> {{ $student->religion_text }}</p>
                        <p><strong>Birth Cert No:</strong> {{ $student->birth_certificate_number ?? '—' }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-secondary text-white">Parents Information</div>
                    <div class="card-body">
                        <p><strong>Father Name:</strong> {{ $student->father_name ?? '—' }}</p>
                        <p><strong>Father Occupation:</strong> {{ $student->father_occupation ?? '—' }}</p>
                        <p><strong>Father Phone:</strong> {{ $student->father_phone ?? '—' }}</p>
                        <p><strong>Father Email:</strong> {{ $student->father_email ?? '—' }}</p>
                        <hr>
                        <p><strong>Mother Name:</strong> {{ $student->mother_name ?? '—' }}</p>
                        <p><strong>Mother Occupation:</strong> {{ $student->mother_occupation ?? '—' }}</p>
                        <p><strong>Mother Phone:</strong> {{ $student->mother_phone ?? '—' }}</p>
                        <p><strong>Mother Email:</strong> {{ $student->mother_email ?? '—' }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-secondary text-white">Contact Information</div>
                    <div class="card-body">
                        <p><strong>Permanent Address:</strong> {{ $student->permanent_address ?? '—' }}</p>
                        <p><strong>Present Address:</strong> {{ $student->present_address ?? '—' }}</p>
                        <p><strong>Guardian Phone:</strong> {{ $student->guardian_phone ?? '—' }}</p>
                        <p><strong>Guardian Email:</strong> {{ $student->guardian_email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white">Academic Data</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Session</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Group</th>
                                        <th>Roll</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($student->academicInformations as $info)
                                        <tr>
                                            <td>{{ $info->academicSession->name_en ?? '—' }}</td>
                                            <td>{{ $info->schoolClass->name_en ?? '—' }}</td>
                                            <td>{{ $info->section->name_en ?? '—' }}</td>
                                            <td>{{ $info->group->name_en ?? '—' }}</td>
                                            <td>{{ $info->roll ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No academic record found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header flex justify-between align-items-center bg-success text-white">
                        <span>Fee Summary</span>
                        <div class="ml-auto">
                            <a href="{{ route('fees.collect_payment', $student->id) }}"
                                class="btn btn-sm btn-light text-dark me-2">
                                🧾 Collect Payment
                            </a>
                            <a href="{{ route('payments.index') }}?student_id={{ $student->id }}"
                                class="btn btn-sm btn-light text-dark">
                                📜 Payment History
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">Total Fees:
                                    <strong>{{ number_format($totalAmount, 2) }}</strong></div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">Total Paid:
                                    <strong>{{ number_format($totalPaid, 2) }}</strong></div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">Total Due:
                                    <strong>{{ number_format($totalDue, 2) }}</strong></div>
                            </div>
                        </div>

                        @php
                            $feeGroups = $regularFees->groupBy(fn($fee) => $fee->feeSet->name ?? 'Unknown');
                        @endphp

                        <ul class="nav nav-tabs mt-3" role="tablist">
                            @foreach ($feeGroups as $group => $fees)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="tab-{{ Str::slug($group) }}" data-bs-toggle="tab"
                                        data-bs-target="#content-{{ Str::slug($group) }}" type="button" role="tab"
                                        aria-controls="content-{{ Str::slug($group) }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $group }} <span class="badge bg-secondary">{{ $fees->count() }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content border border-top-0 p-3">
                            @foreach ($feeGroups as $group => $fees)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="content-{{ Str::slug($group) }}" role="tabpanel"
                                    aria-labelledby="tab-{{ Str::slug($group) }}">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Amount</th>
                                                    <th>Scholarship</th>
                                                    <th>Paid</th>
                                                    <th>Due</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($fees as $fee)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ number_format($fee->amount, 2) }}</td>
                                                        <td>{{ number_format($fee->scholarship_discount, 2) }}</td>
                                                        <td>{{ number_format($fee->paid_amount, 2) }}</td>
                                                        <td>{{ number_format($fee->due_amount, 2) }}</td>
                                                        <td>{{ optional($fee->due_date)->format('d M, Y') }}</td>
                                                        <td><span
                                                                class="badge bg-{{ $fee->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($fee->status) }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <span class="fw-bold">Group Totals:</span>
                                        <span class="ms-2">Total Amount:
                                            ৳{{ number_format($fees->sum('amount'), 2) }}</span>
                                        <span class="ms-2">Paid:
                                            ৳{{ number_format($fees->sum('paid_amount'), 2) }}</span>
                                        <span class="ms-2">Due: ৳{{ number_format($fees->sum('due_amount'), 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header text-white font-bold">Assigned Transport Fees</div>
                    <div class="card-body">

                        @if ($transports->isEmpty() && $transportFeesFromBilling->isEmpty())
                            <p>No transport fee assignments found. <a href="{{ route('transports.create') }}"
                                    class="btn btn-sm btn-primary">Assign transport fee</a></p>
                        @else
                            @if ($transportFeesFromBilling->isNotEmpty())
                                <h6 class="mt-3">Transport Fee Schedule (12 months)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-3">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Due Date</th>
                                                <th>Amount</th>
                                                <th>Paid</th>
                                                <th>Due</th>
                                                <th>Status</th>
                                                <th>Active/Inactive</th>
                                                <th>Fee Set</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($transportFeesFromBilling as $fee)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ optional($fee->due_date)->format('d M, Y') }}</td>
                                                    <td>{{ number_format($fee->amount, 2) }}</td>
                                                    <td>{{ number_format($fee->paid_amount, 2) }}</td>
                                                    <td>{{ number_format($fee->due_amount, 2) }}</td>
                                                    <td><span
                                                            class="badge bg-{{ $fee->status == 'paid' ? 'success' : 'warning' }}">{{ ucfirst($fee->status) }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($fee->status !== 'paid')
                                                            <form action="{{ route('fees.toggle-status', $fee->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" class="custom-control-input"
                                                                        id="feeSwitch{{ $fee->id }}"
                                                                        onchange="this.form.submit()"
                                                                        {{ $fee->is_active ? 'checked' : '' }}>
                                                                    <label class="custom-control-label"
                                                                        for="feeSwitch{{ $fee->id }}"></label>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <span class="text-muted">Paid</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($fee->feeSet)->name ?? '—' }}</td>
                                                    <td class="text-end">
                                                        @if ($fee->status !== 'paid')
                                                            <a href="{{ route('fees.edit', $fee->id) }}"
                                                                class="btn btn-sm btn-info" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <form action="{{ route('fees.delete', $fee->id) }}"
                                                                method="POST" class="d-inline"
                                                                onsubmit="return confirm('Delete this fee record?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    title="Delete">
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
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
