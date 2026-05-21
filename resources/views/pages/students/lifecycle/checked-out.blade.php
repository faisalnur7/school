@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-rose-700 to-rose-900 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-user-times text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Checked Out Students</h3>
            <p class="text-rose-200 text-sm mt-1 mb-0">Students who are transferred, graduated, withdrawn, or expelled</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-4 mb-4">
        <form method="GET" action="{{ route('students.checked-out') }}" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label text-sm font-medium text-slate-600">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                    placeholder="Name or CID">
            </div>
            <div class="mb-1">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-search mr-1"></i> Search
                </button>
                <a href="{{ route('students.checked-out') }}" class="btn btn-sm btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Checkout Type</th>
                        <th>Student Details</th>
                        <th>Academic Information</th>
                        <th>Checkout Date</th>
                        <th>Academic Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $i => $rec)
                    <tr>
                        <td>{{ $records->firstItem() + $i }}</td>
                        <td>
                            <span class="badge badge-danger text-uppercase">{{ $rec->academic_status }}</span>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $rec->student->full_name_en ?? $rec->student->full_name_bn ?? '—' }}</div>
                            <div class="text-muted small">CID: {{ $rec->student->student_cid ?? '—' }}</div>
                            <div class="text-muted small">Phone: {{ $rec->student->guardian_phone ?? $rec->student->father_phone ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="small">Session: {{ $rec->academicSession->name_en ?? '—' }}</div>
                            <div class="small">Class: {{ $rec->schoolClass->name_en ?? '—' }}</div>
                            <div class="small">Section: {{ $rec->section->name_en ?? '—' }}</div>
                            <div class="small">Group: {{ $rec->group->name_en ?? '—' }}</div>
                            <div class="small">Roll: {{ $rec->roll ?? '—' }}</div>
                        </td>
                        <td>{{ $rec->checkout_date ? $rec->checkout_date->format('d M Y') : '—' }}</td>
                        <td><span class="badge badge-secondary">{{ $rec->academic_status }}</span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('students.tc', [$rec->student_id, 'style' => 'standard']) }}"
                                    class="btn btn-xs btn-primary" target="_blank">
                                    <i class="fas fa-file-alt mr-1"></i> TC
                                </a>
                                <a href="{{ route('students.testimonial', [$rec->student_id, 'style' => 'standard']) }}"
                                    class="btn btn-xs btn-info" target="_blank">
                                    <i class="fas fa-certificate mr-1"></i> Testimonial
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No checked out students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="p-3">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
