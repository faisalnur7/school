@extends('admissions.public.layout')

@section('content')
@php
    $data = $draft->applicant_data;
    $guardianType = (int) ($data['guardian_type'] ?? 1);
    $guardianLabel = [1 => 'Father', 2 => 'Mother', 3 => 'Other'][$guardianType] ?? 'Father';
    $gender = \App\Models\Student::GENDERS[(int) ($data['gender'] ?? 0)] ?? '-';
    $religion = \App\Models\Student::RELIGIONS[(int) ($data['religion'] ?? 0)] ?? '-';
    $bloodGroup = \App\Models\Student::BLOOD_GROUPS[(int) ($data['blood_group'] ?? 0)] ?? '-';
    $value = fn (string $key) => $data[$key] ?? '-';
@endphp
<div class="mx-auto w-full max-w-6xl">
    <div class="mb-6"><p class="text-sm font-bold uppercase tracking-[.2em] text-teal-700">Review before submission</p><h1 class="mt-2 text-3xl font-extrabold text-slate-950 sm:text-4xl">Check your application</h1><p class="mt-2 text-slate-600">Review every detail carefully. Nothing is saved to the admission record until you confirm.</p></div>
    <div class="rounded-3xl border border-white bg-white/90 p-5 shadow-xl sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5 border-b border-slate-200 pb-6"><div><p class="text-xs font-bold uppercase tracking-widest text-teal-700">{{ $draft->exam?->name }}</p><h2 class="mt-2 text-2xl font-extrabold text-slate-950">{{ $value('full_name_en') }}</h2><p class="mt-1 text-slate-600">{{ $draft->schoolClass?->name_en ?? '-' }} · {{ $draft->exam?->academicSession?->name_en ?? '-' }}</p></div>@if($draft->image_path)<img src="{{ asset($draft->image_path) }}" alt="Student photo" class="h-24 w-20 rounded-xl object-cover ring-4 ring-teal-50">@endif</div>
        <div class="mt-6 grid gap-3 sm:grid-cols-3"><div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Exam date</span><b>{{ $draft->exam?->exam_date?->format('d M Y') ?? '-' }}</b></div><div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Reporting time</span><b>{{ $draft->exam?->reporting_time ?? '-' }}</b></div><div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Admission form fee</span><b>৳ {{ number_format((float) $draft->exam?->form_fee, 2) }}</b></div></div>

        @foreach([
            ['Basic information', [['Full name', $value('full_name_en')], ['Name in Bangla', $value('full_name_bn')], ['Date of birth', $value('date_of_birth')], ['Birth certificate number', $value('birth_certificate_number')], ['Gender', $gender], ['Religion', $religion], ['Blood group', $bloodGroup], ['Disability status', !empty($data['disable']) ? 'Disabled' : 'Not disabled']]],
            ['Parents information', [['Father name', $value('father_name')], ['Father phone', $value('father_phone')], ['Father email', $value('father_email')], ['Father NID', $value('father_nid_number')], ['Mother name', $value('mother_name')], ['Mother phone', $value('mother_phone')], ['Mother email', $value('mother_email')], ['Mother NID', $value('mother_nid_number')], ['Family annual income', $value('annual_income')]]],
            ['Guardian information · ' . $guardianLabel, $guardianType === 3 ? [['Guardian name', $value('guardian_name')], ['Relationship', $value('guardian_relation')], ['Guardian phone', $value('guardian_phone')], ['Guardian email', $value('guardian_email')], ['Guardian address', $value('guardian_address')]] : [['Selected guardian', $guardianLabel], ['Guardian phone', $guardianLabel === 'Father' ? $value('father_phone') : $value('mother_phone')]]],
            ['Address and previous school', [['Present address', $value('present_address')], ['Permanent address', $value('permanent_address')], ['Previous school', $value('previous_school')], ['Previous class', $value('previous_class_appeared')], ['TC number', $value('tc_number')]]],
        ] as [$title, $fields])
            <section class="mt-7"><h3 class="border-b border-slate-200 pb-2 text-lg font-extrabold text-slate-950">{{ $title }}</h3><div class="mt-4 grid gap-x-8 gap-y-4 sm:grid-cols-2">@foreach($fields as [$label, $fieldValue])<div><span class="text-xs text-slate-500">{{ $label }}</span><p class="mt-1 font-semibold text-slate-800">{{ $fieldValue }}</p></div>@endforeach</div></section>
        @endforeach

        <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><strong>Important:</strong> Confirming will create your admission application and a pending payment record. Your application number will be generated after confirmation.</div>
        <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5"><a href="{{ route('public.admission.form', ['draft' => $token]) }}" class="rounded-xl border border-slate-300 px-5 py-3 font-bold text-slate-700">Edit application</a><form method="POST" action="{{ route('public.admission.confirm', $token) }}">@csrf<button class="rounded-xl bg-teal-700 px-6 py-3 font-bold text-white shadow-lg shadow-teal-700/20">Confirm Application <span class="ml-1">→</span></button></form></div>
    </div>
</div>
@endsection
