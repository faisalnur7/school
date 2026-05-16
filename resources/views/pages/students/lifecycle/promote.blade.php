@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-emerald-700 to-emerald-900 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-arrow-up text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Promote Students</h3>
            <p class="text-emerald-200 text-sm mt-1 mb-0">Promote or retain students to the next session</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="mb-0 list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.promote') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Source Session</label>
                    <select name="academic_session_id" class="form-control form-control-sm" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected(request('academic_session_id') == $s->id)>{{ $s->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Class</label>
                    <select name="school_class_id" class="form-control form-control-sm" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg w-full">
                        <i class="fas fa-search mr-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())
    <div class="bg-white rounded-2xl shadow p-5">
        <form method="POST" action="{{ route('students.promote.store') }}">
            @csrf
            <input type="hidden" name="source_session_id" value="{{ request('academic_session_id') }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5 p-4 bg-slate-50 rounded-xl">
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Target Session <span class="text-red-500">*</span></label>
                    <select name="target_session_id" class="form-control form-control-sm" required>
                        <option value="">Select Target Session</option>
                        @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected(old('target_session_id') == $s->id)>{{ $s->name_en }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-sm table-bordered">
                    <thead class="bg-slate-100">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>CID</th>
                            <th>Name</th>
                            <th>Roll</th>
                            <th>Section</th>
                            <th>Target Class</th>
                            <th>Target Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $i => $rec)
                        <tr>
                            <td><input type="checkbox" name="promotions[{{ $i }}][id]" value="{{ $rec->id }}" class="row-check"></td>
                            <td>{{ $rec->student->student_cid }}</td>
                            <td>{{ $rec->student->full_name_en }}</td>
                            <td>{{ $rec->roll }}</td>
                            <td>{{ $rec->section->name_en ?? '—' }}</td>
                            <td>
                                <select name="promotions[{{ $i }}][school_class_id]" class="form-control form-control-sm" required>
                                    @foreach($classes as $c)
                                    <option value="{{ $c->id }}" @selected($c->id == $rec->school_class_id)>{{ $c->name_en }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="promotions[{{ $i }}][section_id]" class="form-control form-control-sm" required>
                                    @foreach($sections as $s)
                                    <option value="{{ $s->id }}" @selected($s->id == $rec->section_id)>{{ $s->name_en }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex gap-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg font-medium">
                    <i class="fas fa-arrow-up mr-1"></i> Promote Selected
                </button>
            </div>
        </form>
    </div>
    @elseif(request()->hasAny(['academic_session_id', 'school_class_id']))
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-users text-4xl mb-3 opacity-40"></i>
        <p>No active students found for the selected filters.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
