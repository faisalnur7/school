@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-violet-700 to-violet-900 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-history text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Academic History</h3>
            <p class="text-violet-200 text-sm mt-1 mb-0">View full session history per student</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.history') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="form-label text-sm font-medium text-slate-600">Search by Name or Student ID</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Enter name or CID...">
                </div>
                <div>
                    <button type="submit" class="bg-violet-600 hover:bg-violet-700 text-white px-4 py-2 rounded-lg w-full" title="Search" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-slate-100">
                <tr><th>CID</th><th>Name</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ $student->student_cid }}</td>
                    <td>{{ $student->full_name_en }}</td>
                    <td>
                        <a href="{{ route('students.history.show', $student->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye mr-1"></i> View History
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(request()->filled('search'))
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-search text-4xl mb-3 opacity-40"></i>
        <p>No students found matching "{{ request('search') }}".</p>
    </div>
    @endif
</div>
@endsection
