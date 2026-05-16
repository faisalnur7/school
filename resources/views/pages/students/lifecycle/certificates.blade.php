@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-teal-700 to-teal-900 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-certificate text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Certificates</h3>
            <p class="text-teal-200 text-sm mt-1 mb-0">Generate Transfer Certificate & Testimonial</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.certificates') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="form-label text-sm font-medium text-slate-600">Search Student by Name or CID</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="Enter name or student ID...">
                </div>
                <div>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg w-full font-medium">
                        <i class="fas fa-search mr-1"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($students) && $students->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-slate-100">
                <tr>
                    <th>CID</th>
                    <th>Name</th>
                    <th>Transfer Certificate</th>
                    <th>Testimonial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td class="font-mono text-sm">{{ $student->student_cid }}</td>
                    <td class="font-medium">{{ $student->full_name_en }}</td>
                    <td>
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('students.tc', [$student, 'style' => 'classic']) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-scroll mr-1"></i> Classic
                            </a>
                            <a href="{{ route('students.tc', [$student, 'style' => 'modern']) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-alt mr-1"></i> Modern
                            </a>
                            <a href="{{ route('students.tc.pdf', $student) }}"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('students.testimonial', [$student, 'style' => 'classic']) }}" target="_blank"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-scroll mr-1"></i> Classic
                            </a>
                            <a href="{{ route('students.testimonial', [$student, 'style' => 'modern']) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-file-alt mr-1"></i> Modern
                            </a>
                            <a href="{{ route('students.testimonial.pdf', $student) }}"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                        </div>
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
    @else
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-certificate text-5xl mb-3 opacity-20"></i>
        <p class="text-lg font-medium">Search for a student to generate certificates</p>
        <p class="text-sm">Enter a student name or CID above</p>
    </div>
    @endif
</div>
@endsection
