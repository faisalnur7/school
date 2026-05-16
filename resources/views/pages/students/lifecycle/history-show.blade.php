@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-violet-700 to-violet-900 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-history text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">{{ $student->full_name_en }}</h3>
            <p class="text-violet-200 text-sm mt-1 mb-0">CID: {{ $student->student_cid }} &mdash; Academic History</p>
        </div>
    </div>

    <div class="mb-4">
        <a href="{{ route('students.history') }}" class="text-violet-600 hover:underline text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Search
        </a>
    </div>

    @if($records->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-slate-100">
                <tr>
                    <th>Session</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Group</th>
                    <th>Roll</th>
                    <th>Status</th>
                    <th>Promotion</th>
                    <th>Checkout Date</th>
                    <th>Current</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $rec)
                @php
                    $statusColors = [
                        'active'      => 'bg-green-100 text-green-700',
                        'graduated'   => 'bg-blue-100 text-blue-700',
                        'withdrawn'   => 'bg-yellow-100 text-yellow-700',
                        'transferred' => 'bg-orange-100 text-orange-700',
                        'expelled'    => 'bg-red-100 text-red-700',
                    ];
                    $promoColors = [
                        'new_admission' => 'bg-cyan-100 text-cyan-700',
                        'promoted'      => 'bg-emerald-100 text-emerald-700',
                        'retained'      => 'bg-amber-100 text-amber-700',
                    ];
                @endphp
                <tr class="{{ $rec->is_current ? 'bg-green-50' : '' }}">
                    <td class="font-medium">{{ $rec->academicSession->name_en ?? '—' }}</td>
                    <td>{{ $rec->schoolClass->name_en ?? '—' }}</td>
                    <td>{{ $rec->section->name_en ?? '—' }}</td>
                    <td>{{ $rec->group->name_en ?? '—' }}</td>
                    <td>{{ $rec->roll }}</td>
                    <td>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$rec->academic_status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($rec->academic_status) }}
                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $promoColors[$rec->promotion_status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ ucwords(str_replace('_', ' ', $rec->promotion_status)) }}
                        </span>
                    </td>
                    <td>{{ $rec->checkout_date ? $rec->checkout_date->format('d M Y') : '—' }}</td>
                    <td class="text-center">
                        @if($rec->is_current)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Current</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-folder-open text-4xl mb-3 opacity-40"></i>
        <p>No academic records found for this student.</p>
    </div>
    @endif
</div>
@endsection
