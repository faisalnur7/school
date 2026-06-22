@extends('layouts.master')

@section('contents')
@php
    $errors = $errors ?? new \Illuminate\Support\MessageBag();
    $filters = $filters ?? [];
    $mode = old('promotion_mode', $filters['promotion_mode'] ?? request('promotion_mode', 'final_term_merit_list'));
    $sourceSession = old('source_session_id', $filters['source_session_id'] ?? request('source_session_id', request('academic_session_id')));
    $sourceClass = old('source_class_id', $filters['source_class_id'] ?? request('source_class_id', request('school_class_id')));
    $targetSession = old('target_session_id', $filters['target_session_id'] ?? request('target_session_id'));
    $targetClass = old('target_class_id', $filters['target_class_id'] ?? request('target_class_id'));
    $studentId = old('student_id', $filters['student_id'] ?? request('student_id', request('student_cid')));
    $failThreshold = old('fail_threshold', $filters['fail_threshold'] ?? request('fail_threshold', 1));

    $sourceReady = filled($sourceSession) && filled($sourceClass);
    $targetReady = filled($targetSession) && filled($targetClass);
    $classLookup = $classes->keyBy('id');
    $sessionLookup = $sessions->keyBy('id');
    $targetClassName = $targetReady ? ($classLookup->get((int) $targetClass)?->name_en ?? '—') : '—';
    $targetSessionName = $targetReady ? ($sessionLookup->get((int) $targetSession)?->name_en ?? '—') : '—';
@endphp

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
            <ul class="mb-0 list-disc pl-4">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow p-5 mb-5">
        <form method="GET" action="{{ route('students.promote') }}" id="promotion-filter-form">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Source Session <span class="text-red-500">*</span></label>
                    <select name="source_session_id" class="form-control form-control-sm" required>
                        <option value="">Select Session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @selected((string) $sourceSession === (string) $session->id)>{{ $session->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Source Class <span class="text-red-500">*</span></label>
                    <select name="source_class_id" class="form-control form-control-sm" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected((string) $sourceClass === (string) $class->id)>{{ $class->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Target Session</label>
                    <select name="target_session_id" class="form-control form-control-sm">
                        <option value="">Select Target Session</option>
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" @selected((string) $targetSession === (string) $session->id)>{{ $session->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Target Class</label>
                    <select name="target_class_id" class="form-control form-control-sm">
                        <option value="">Select Target Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" @selected((string) $targetClass === (string) $class->id)>{{ $class->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Student ID</label>
                    <input type="text" name="student_id" value="{{ $studentId }}" class="form-control form-control-sm" placeholder="CID or numeric ID">
                </div>

                <div>
                    <label class="form-label text-sm font-medium text-slate-600">Promotion Mode</label>
                    <select name="promotion_mode" class="form-control form-control-sm" id="promotion_mode">
                        @foreach($promotionModes as $value => $label)
                            <option value="{{ $value }}" @selected((string) $mode === (string) $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="fail-threshold-wrap" class="{{ $mode === 'n_subjects_fail' ? '' : 'hidden' }}">
                    <label class="form-label text-sm font-medium text-slate-600">Fail Threshold</label>
                    <input type="number" min="1" step="1" name="fail_threshold" value="{{ $failThreshold }}" class="form-control form-control-sm">
                    <small class="text-slate-500">Example: 2 means failed in 2 or more subjects.</small>
                </div>

                <div class="md:col-span-3 flex flex-wrap gap-3 items-center justify-between">
                    <p class="text-slate-500 text-sm mb-0">
                        Load the cohort from the selected source session/class. Target filters are required before saving promotions.
                    </p>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-search mr-1"></i> Load Students
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($students->isNotEmpty())
        <div class="bg-white rounded-2xl shadow p-5">
            <form method="POST" action="{{ route('students.promote.store') }}" id="promotion-store-form">
                @csrf

                <input type="hidden" name="source_session_id" value="{{ $sourceSession }}">
                <input type="hidden" name="source_class_id" value="{{ $sourceClass }}">
                <input type="hidden" name="target_session_id" value="{{ $targetSession }}">
                <input type="hidden" name="target_class_id" value="{{ $targetClass }}">
                <input type="hidden" name="student_id" value="{{ $studentId }}">
                <input type="hidden" name="promotion_mode" value="{{ $mode }}">
                <input type="hidden" name="fail_threshold" value="{{ $failThreshold }}">

                <div class="bg-slate-50 rounded-xl p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-sm">
                        <div><span class="text-slate-500">Source Session:</span> <strong>{{ $sessionLookup->get((int) $sourceSession)?->name_en ?? '—' }}</strong></div>
                        <div><span class="text-slate-500">Source Class:</span> <strong>{{ $classLookup->get((int) $sourceClass)?->name_en ?? '—' }}</strong></div>
                        <div><span class="text-slate-500">Target Session:</span> <strong>{{ $targetSessionName }}</strong></div>
                        <div><span class="text-slate-500">Target Class:</span> <strong>{{ $targetClassName }}</strong></div>
                    </div>
                    <p class="text-xs text-slate-500 mt-3 mb-0">
                        Merit and fail modes auto-fill target rolls in display order. Custom mode lets you edit target rolls manually.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="text-center" style="width: 48px"><input type="checkbox" id="selectAll"></th>
                                <th>CID</th>
                                <th>Name</th>
                                <th>Source Roll</th>
                                <th>Merit Rank</th>
                                <th>Fail Count</th>
                                <th>Source Status</th>
                                <th>Section</th>
                                <th>Group</th>
                                <th>Target Class</th>
                                <th>Target Roll</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $row)
                                @php
                                    $student = data_get($row, 'student');
                                    $academicInfo = data_get($row, 'academic_info');
                                    $targetRoll = data_get($row, 'target_roll');
                                    $isCustomMode = $mode === 'custom';
                                    $hasTarget = filled($targetSession) && filled($targetClass);
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="promotions[{{ $index }}][selected]" value="1" class="row-check" checked>
                                        <input type="hidden" name="promotions[{{ $index }}][source_academic_information_id]" value="{{ data_get($academicInfo, 'id') }}">
                                        <input type="hidden" name="promotions[{{ $index }}][student_id]" value="{{ data_get($student, 'id') }}">
                                        <input type="hidden" name="promotions[{{ $index }}][target_section_id]" value="{{ data_get($row, 'target_section_id') }}">
                                        <input type="hidden" name="promotions[{{ $index }}][target_group_id]" value="{{ data_get($row, 'target_group_id') }}">
                                        <input type="hidden" name="promotions[{{ $index }}][target_roll]" value="{{ $targetRoll }}">
                                    </td>
                                    <td>{{ data_get($student, 'student_cid', '—') }}</td>
                                    <td>{{ data_get($student, 'full_name_en', '—') }}</td>
                                    <td>{{ data_get($academicInfo, 'roll', '—') }}</td>
                                    <td>{{ data_get($row, 'source_rank', '—') }}</td>
                                    <td>{{ data_get($row, 'failed_subjects', 0) }}</td>
                                    <td>{{ data_get($row, 'source_fail_status', '—') }}</td>
                                    <td>{{ data_get($academicInfo, 'section.name_en', '—') }}</td>
                                    <td>{{ data_get($academicInfo, 'group.name_en', '—') }}</td>
                                    <td>{{ $targetClassName }}</td>
                                    <td style="min-width: 160px">
                                        @if($hasTarget)
                                            @if($isCustomMode)
                                                <input type="number"
                                                       min="1"
                                                       step="1"
                                                       name="promotions[{{ $index }}][target_roll]"
                                                       value="{{ old('promotions.' . $index . '.target_roll', $targetRoll) }}"
                                                       class="form-control form-control-sm">
                                            @else
                                                <input type="number"
                                                       min="1"
                                                       step="1"
                                                       name="promotions[{{ $index }}][target_roll]"
                                                       value="{{ old('promotions.' . $index . '.target_roll', $targetRoll) }}"
                                                       class="form-control form-control-sm bg-slate-100"
                                                       readonly>
                                            @endif
                                        @else
                                            <input type="text"
                                                   class="form-control form-control-sm bg-slate-100"
                                                   value="Select target session/class"
                                                   readonly>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-3 items-center justify-between">
                    <div class="text-sm text-slate-500">
                        <span class="font-medium">{{ $students->count() }}</span> student(s) loaded.
                    </div>
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-lg font-medium {{ $targetReady ? '' : 'opacity-60 cursor-not-allowed' }}"
                            @disabled(! $targetReady)>
                        <i class="fas fa-arrow-up mr-1"></i> Promote Selected
                    </button>
                </div>
            </form>
        </div>
    @elseif($sourceReady)
        <div class="bg-white rounded-2xl shadow p-8 text-center text-slate-400">
            <i class="fas fa-users text-4xl mb-3 opacity-40"></i>
            <p>No active students found for the selected source filters.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
    const selectAll = document.getElementById('selectAll');
    const mode = document.getElementById('promotion_mode');
    const thresholdWrap = document.getElementById('fail-threshold-wrap');

    selectAll?.addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    });

    const toggleThreshold = () => {
        if (!mode || !thresholdWrap) return;
        thresholdWrap.classList.toggle('hidden', mode.value !== 'n_subjects_fail');
    };

    mode?.addEventListener('change', toggleThreshold);
    toggleThreshold();
})();
</script>
@endsection
