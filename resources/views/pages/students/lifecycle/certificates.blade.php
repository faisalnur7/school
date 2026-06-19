@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-700 via-cyan-700 to-slate-900 p-8 mb-6">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-18 w-18 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                    <i class="fas fa-certificate text-white text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-white text-3xl font-bold m-0">Certificate Hub</h3>
                    <p class="text-teal-100 text-base mt-1 mb-0">
                        Search a student, then choose the certificate type you want to generate.
                    </p>
                </div>
            </div>
            <div class="text-teal-100 text-sm">
                Active certificate types and templates are managed separately.
                <div class="mt-3">
                    <a href="{{ route('certificates.index') }}"
                       class="inline-flex items-center rounded-lg bg-white/10 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-white/20">
                        <i class="fas fa-cog mr-2"></i> Manage certificate types
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.certificates') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="form-label text-sm font-medium text-slate-600">Search student by name or CID</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control form-control-sm" placeholder="Enter name or student ID...">
                </div>
                <div>
                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg w-full font-medium" title="Search" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($students) && $students->isNotEmpty())
    <div class="card shadow overflow-hidden">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3">
            <div>
                <h3 class="card-title mb-0 text-white text-lg">Search results</h3>
                <p class="mb-0 text-white-50 small">Select the certificate type you want to generate for the student.</p>
            </div>
        </div>
        <div class="card-body px-0 pb-4 pt-0">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-slate-100">
                    <tr>
                        <th>CID</th>
                        <th>Name</th>
                        <th class="text-center">Certificate Types</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td class="font-mono text-sm">{{ $student->student_cid }}</td>
                        <td class="font-medium">{{ $student->full_name_en }}</td>
                        <td>
                            <div class="flex flex-wrap gap-2">
                                @foreach($certificates as $certificate)
                                    <a href="{{ route('students.certificate.preview', ['student' => $student, 'certificate' => $certificate]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-signature mr-1"></i> {{ $certificate->name }}
                                    </a>
                                    <a href="{{ route('students.certificate.pdf', ['student' => $student, 'certificate' => $certificate]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                    </a>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
    @elseif(request()->filled('search'))
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-search text-5xl mb-3 opacity-20"></i>
        <p class="text-lg font-medium">No matching students found</p>
        <p class="text-sm mb-0">Try a different name or CID.</p>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-certificate text-5xl mb-3 opacity-20"></i>
        <p class="text-lg font-medium">Search for a student to generate certificates</p>
        <p class="text-sm mb-0">Enter a student name or CID above.</p>
    </div>
    @endif
</div>
@endsection
