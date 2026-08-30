@extends('admissions.public.layout')

@section('styles')
<style>
    @media print {
        .public-site-header, .public-site-footer, .public-search-tools, .public-print-button,
        .public-payment-form, .public-admit-card-note { display: none !important; }
        .public-site-main { max-width: none !important; padding: 0 !important; }
        .public-print-sheet { border: 0 !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
        .public-print-section { break-inside: avoid; }
    }
</style>
@endsection

@section('content')
@php($data = $application?->applicant_data ?? [])
@php($guardianType = (int) ($data['guardian_type'] ?? $application?->guardian_type ?? 1))
@php($guardianLabel = [1 => 'Father', 2 => 'Mother', 3 => 'Other'][$guardianType] ?? 'Father')
<div class="public-site-container mx-auto w-full">
    @if(!empty($confirmation))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-900">
            <p class="font-bold"><i class="fas fa-check-circle mr-2"></i>Application confirmed successfully</p>
            <p class="mt-1 text-sm">Your application has been saved. Use the buttons below to print or export the submitted admission form.</p>
        </div>
    @endif
    <div class="public-search-tools mb-8">
        <p class="text-sm font-bold uppercase tracking-[.2em] text-teal-700">Applicant portal</p>
        <h1 class="mt-2 text-4xl font-extrabold text-slate-950">Search your application</h1>
        <p class="mt-3 text-slate-600">Enter the application number and any phone number submitted with the application.</p>
        <form class="mt-6 grid gap-3 rounded-3xl border border-white bg-white/90 p-3 shadow-xl sm:grid-cols-[1fr_1.3fr_auto]">
            <input name="application_number" required placeholder="Application number e.g. 0472" value="{{ $searchTerm ?? '' }}" class="min-w-0 rounded-2xl border-0 bg-slate-50 px-5 py-4">
            <input name="phone" required placeholder="Father, mother, guardian, or contact phone" value="{{ $phone ?? '' }}" class="min-w-0 rounded-2xl border-0 bg-slate-50 px-5 py-4">
            <button class="rounded-2xl bg-slate-950 px-6 py-4 font-bold text-white">Search</button>
        </form>
        @if(($searchErrors ?? null)?->any())
            <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">
                <ul class="list-disc pl-5">@foreach($searchErrors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
    </div>

    @if(($searchTerm ?? '') !== '' && !$application)
        <div class="public-search-tools rounded-2xl bg-rose-50 p-5 text-rose-800">No application matched both the application number and phone number.</div>
    @endif

    @if($application)
        <div class="public-print-sheet rounded-3xl border border-white bg-white/90 p-7 shadow-xl">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 pb-5">
                <div>
                    <p class="text-sm text-slate-500">Admission application</p>
                    <h2 class="text-2xl font-extrabold text-slate-950">{{ $application->application_number }}</h2>
                    <p class="mt-1 text-slate-600">{{ $data['full_name_en'] ?? $application->full_name_en ?? '-' }} · {{ $application->schoolClass?->name_en ?? '-' }}</p>
                </div>
                @if($application->image || !empty($data['image']))
                    <img src="{{ asset($application->image ?? $data['image']) }}" alt="Student photo" class="h-20 w-16 rounded-lg object-cover">
                @endif
                <div class="flex items-center gap-3">
                    <span class="rounded-full {{ $application->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} px-4 py-2 text-sm font-bold">Payment: {{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}</span>
                    <div class="public-print-button flex gap-2">
                        <button type="button" onclick="window.print()" class="rounded-xl bg-slate-950 px-4 py-2 font-bold text-white">Print Application</button>
                        <a href="{{ route('public.admission.application-pdf', $application) }}" class="rounded-xl bg-teal-700 px-4 py-2 font-bold text-white">Export as PDF</a>
                        @if($application->admitCard && $application->payment_status === 'paid')
                            <a class="rounded-xl bg-cyan-700 px-4 py-2 font-bold text-white" href="{{ route('public.admission.admit-card', $application) }}">Download Admit Card</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Admission exam</span><b>{{ $application->exam?->name ?? '-' }}</b></div>
                <div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Exam date</span><b>{{ $application->exam?->exam_date?->format('d M Y') ?? '-' }}</b></div>
                <div class="rounded-2xl bg-slate-50 p-4"><span class="block text-xs text-slate-500">Application status</span><b>{{ ucfirst(str_replace('_', ' ', $application->application_status ?: $application->status)) }}</b></div>
            </div>

            <div class="public-print-section mt-7">
                <h3 class="border-b border-slate-200 pb-2 text-lg font-extrabold">Basic information</h3>
                <div class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                    <div><span class="text-xs text-slate-500">Full name</span><p class="font-semibold">{{ $data['full_name_en'] ?? $application->full_name_en ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Name in Bangla</span><p class="font-semibold">{{ $data['full_name_bn'] ?? $application->full_name_bn ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Date of birth</span><p class="font-semibold">{{ $data['date_of_birth'] ?? $application->date_of_birth?->format('d/m/Y') ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Birth certificate number</span><p class="font-semibold">{{ $data['birth_certificate_number'] ?? $application->birth_certificate_number ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Applied class</span><p class="font-semibold">{{ $application->schoolClass?->name_en ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Gender</span><p class="font-semibold">{{ $data['gender'] ?? $application->gender ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Religion</span><p class="font-semibold">{{ $data['religion'] ?? $application->religion ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Blood group</span><p class="font-semibold">{{ $data['blood_group'] ?? $application->blood_group ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Disability status</span><p class="font-semibold">{{ !empty($data['disable']) || $application->disable ? 'Disabled' : 'Not disabled' }}</p></div>
                </div>
            </div>

            <div class="public-print-section mt-7">
                <h3 class="border-b border-slate-200 pb-2 text-lg font-extrabold">Parents and contact</h3>
                <div class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                    <div><span class="text-xs text-slate-500">Father</span><p class="font-semibold">{{ $data['father_name'] ?? $application->father_name ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Father phone</span><p class="font-semibold">{{ $data['father_phone'] ?? $application->father_phone ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Mother</span><p class="font-semibold">{{ $data['mother_name'] ?? $application->mother_name ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Mother phone</span><p class="font-semibold">{{ $data['mother_phone'] ?? $application->mother_phone ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Father email</span><p class="font-semibold">{{ $data['father_email'] ?? $application->father_email ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Mother email</span><p class="font-semibold">{{ $data['mother_email'] ?? $application->mother_email ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Father NID</span><p class="font-semibold">{{ $data['father_nid_number'] ?? $application->father_nid_number ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Mother NID</span><p class="font-semibold">{{ $data['mother_nid_number'] ?? $application->mother_nid_number ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Family annual income</span><p class="font-semibold">{{ $data['annual_income'] ?? $application->annual_income ?? '-' }}</p></div>
                </div>
            </div>

            <div class="public-print-section mt-7">
                <h3 class="border-b border-slate-200 pb-2 text-lg font-extrabold">Guardian information · {{ $guardianLabel }}</h3>
                <div class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                    @if($guardianType === 1 || $guardianType === 2)
                        @php($prefix = $guardianType === 1 ? 'father' : 'mother')
                        <div><span class="text-xs text-slate-500">{{ $guardianLabel }} name</span><p class="font-semibold">{{ $data[$prefix . '_name'] ?? $application->{$prefix . '_name'} ?? '-' }}</p></div>
                        <div><span class="text-xs text-slate-500">{{ $guardianLabel }} phone</span><p class="font-semibold">{{ $data[$prefix . '_phone'] ?? $application->{$prefix . '_phone'} ?? '-' }}</p></div>
                        <div><span class="text-xs text-slate-500">{{ $guardianLabel }} email</span><p class="font-semibold">{{ $data[$prefix . '_email'] ?? $application->{$prefix . '_email'} ?? '-' }}</p></div>
                    @else
                        <div><span class="text-xs text-slate-500">Guardian name</span><p class="font-semibold">{{ $data['guardian_name'] ?? $application->guardian_name ?? '-' }}</p></div>
                        <div><span class="text-xs text-slate-500">Relationship</span><p class="font-semibold">{{ $data['guardian_relation'] ?? $application->guardian_relation ?? '-' }}</p></div>
                        <div><span class="text-xs text-slate-500">Guardian phone</span><p class="font-semibold">{{ $data['guardian_phone'] ?? $application->guardian_phone ?? '-' }}</p></div>
                        <div><span class="text-xs text-slate-500">Guardian email</span><p class="font-semibold">{{ $data['guardian_email'] ?? $application->guardian_email ?? '-' }}</p></div>
                        <div class="sm:col-span-2"><span class="text-xs text-slate-500">Guardian address</span><p class="font-semibold">{{ $data['guardian_address'] ?? $application->guardian_address ?? '-' }}</p></div>
                    @endif
                </div>
            </div>

            <div class="public-print-section mt-7">
                <h3 class="border-b border-slate-200 pb-2 text-lg font-extrabold">Address and previous school</h3>
                <div class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                    <div><span class="text-xs text-slate-500">Present address</span><p class="font-semibold">{{ $data['present_address'] ?? $application->present_address ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Permanent address</span><p class="font-semibold">{{ $data['permanent_address'] ?? $application->permanent_address ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Previous school</span><p class="font-semibold">{{ $data['previous_school'] ?? $application->previous_school ?? '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Previous class</span><p class="font-semibold">{{ $data['previous_class_appeared'] ?? $application->previous_class_appeared ?? '-' }}</p></div>
                </div>
            </div>

            <div class="public-print-section mt-7 rounded-2xl bg-slate-50 p-5">
                <h3 class="font-extrabold">Payment status</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    <div><span class="text-xs text-slate-500">Status</span><p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $application->payment_status)) }}</p></div>
                    <div><span class="text-xs text-slate-500">Amount</span><p class="font-semibold">{{ $application->payment?->amount !== null ? number_format((float) $application->payment->amount, 2) : '-' }}</p></div>
                    <div><span class="text-xs text-slate-500">Reference</span><p class="font-semibold">{{ $application->payment?->payment_reference ?? '-' }}</p></div>
                </div>
            </div>

            @if(!$application->admitCard || $application->payment_status !== 'paid')
                <p class="public-admit-card-note mt-6 text-sm text-slate-500">Your admit card will appear here after payment is verified and the card is generated.</p>
            @endif
        </div>
    @endif
</div>
@endsection
