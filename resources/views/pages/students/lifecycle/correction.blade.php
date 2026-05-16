@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="bg-gradient-to-br from-amber-600 to-amber-800 rounded-2xl p-8 mb-6 flex items-center gap-5">
        <i class="fas fa-edit text-white text-5xl opacity-80"></i>
        <div>
            <h3 class="text-white text-3xl font-bold m-0">Mid-Year Correction</h3>
            <p class="text-amber-200 text-sm mt-1 mb-0">Update class/section within the same session</p>
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
        <form method="GET" action="{{ route('students.correction') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Session</label>
                    <select name="academic_session_id" class="form-control form-control-sm">
                        <option value="">Select Session</option>
                        @foreach($sessions as $s)
                        <option value="{{ $s->id }}" @selected(request('academic_session_id') == $s->id)>{{ $s->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Class</label>
                    <select name="school_class_id" class="form-control form-control-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" @selected(request('school_class_id') == $c->id)>{{ $c->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg w-full">
                        <i class="fas fa-search mr-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <table class="table table-sm table-bordered mb-0">
            <thead class="bg-slate-100">
                <tr>
                    <th>CID</th><th>Name</th><th>Roll</th><th>Section</th><th>Group</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $rec)
                <tr>
                    <td>{{ $rec->student->student_cid }}</td>
                    <td>{{ $rec->student->full_name_en }}</td>
                    <td>{{ $rec->roll }}</td>
                    <td>{{ $rec->section->name_en ?? '—' }}</td>
                    <td>{{ $rec->group->name_en ?? '—' }}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning" onclick="toggleEdit({{ $rec->id }})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </td>
                </tr>
                <tr id="edit-{{ $rec->id }}" class="hidden bg-amber-50">
                    <td colspan="6">
                        <form method="POST" action="{{ route('students.correction.update', $rec->id) }}" class="p-3">
                            @csrf
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
                                <div>
                                    <label class="form-label text-xs font-medium">Class</label>
                                    <select name="school_class_id" class="form-control form-control-sm" required>
                                        @foreach($classes as $c)
                                        <option value="{{ $c->id }}" @selected($c->id == $rec->school_class_id)>{{ $c->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-xs font-medium">Section</label>
                                    <select name="section_id" class="form-control form-control-sm" required>
                                        @foreach($sections as $s)
                                        <option value="{{ $s->id }}" @selected($s->id == $rec->section_id)>{{ $s->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-xs font-medium">Group</label>
                                    <select name="group_id" class="form-control form-control-sm">
                                        <option value="">No Group</option>
                                        @foreach($groups as $g)
                                        <option value="{{ $g->id }}" @selected($g->id == $rec->group_id)>{{ $g->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label text-xs font-medium">Roll</label>
                                    <input type="text" name="roll" value="{{ $rec->roll }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-span-2 md:col-span-4 flex gap-2 mt-1">
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save mr-1"></i>Save</button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="toggleEdit({{ $rec->id }})">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif(request()->hasAny(['academic_session_id', 'school_class_id']))
    <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
        <i class="fas fa-users text-4xl mb-3 opacity-40"></i>
        <p>No students found for the selected filters.</p>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    row.classList.toggle('hidden');
}
</script>
@endsection
