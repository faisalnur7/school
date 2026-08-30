@extends('admissions.public.layout')

@section('styles')
    @include('components.form-styles')
    @include('pages.students._modern-styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/dropzone/min/dropzone.min.css') }}">
    <style>
        .public-site-main { max-width: 90rem !important; }
        .public-site-container { max-width: 90rem !important; }
        .public-admission-page { width: 100%; }
        .public-admission-intro { position: relative; overflow: hidden; margin-bottom: 1rem; padding: 1.35rem 1.6rem; border-radius: 1.25rem; color: #fff; background: linear-gradient(120deg, #071b2d 0%, #0f766e 62%, #0891b2 100%); box-shadow: 0 18px 45px rgba(15, 23, 42, .14); }
        .public-admission-intro::after { content: ''; position: absolute; width: 15rem; height: 15rem; right: -4rem; top: -8rem; border: 1px solid rgba(255,255,255,.18); border-radius: 50%; box-shadow: 0 0 0 2rem rgba(255,255,255,.04), 0 0 0 4rem rgba(255,255,255,.03); }
        .public-admission-intro h1 { margin: 0; font-size: clamp(1.65rem, 3vw, 2.35rem); font-weight: 800; letter-spacing: -.045em; }
        .public-admission-intro p { max-width: 52rem; margin: .35rem 0 0; color: rgba(255,255,255,.78); font-size: .86rem; line-height: 1.5; }
        .public-admission-intro > div { position: relative; z-index: 1; }
        .public-admission-page .student-form-page { padding: 0; background: transparent; }
        .public-admission-page .student-form-shell { width: 100%; border-radius: 1.25rem; box-shadow: 0 18px 45px rgba(15, 23, 42, .08); }
        .public-admission-page .student-form-header { padding: .7rem 1rem; background: linear-gradient(120deg, #0f172a, #155e75); }
        .public-admission-page .student-form-title { font-size: .95rem; }
        .public-admission-page .student-form-subtitle { font-size: .72rem; }
        .public-admission-page .student-form-header-actions { display: none; }
        .public-admission-page #student_cid, .public-admission-page label[for="student_cid"], .public-admission-page #roll, .public-admission-page label[for="roll"] { display: none; }
        .public-admission-page #student_cid, .public-admission-page #roll { visibility: hidden; }
        .public-admission-page .student-form-body { padding: .65rem; }
        .public-admission-page .student-section { margin-bottom: .5rem; border-radius: .8rem; box-shadow: 0 5px 16px rgba(15, 23, 42, .035); }
        .public-admission-page .student-section__head { padding: .6rem .75rem; }
        .public-admission-page .student-section > summary { cursor: default; pointer-events: none; }
        .public-admission-page .student-section__chevron { display: none; }
        .public-admission-page .student-section__head h5 { font-size: .82rem; }
        .public-admission-page .student-section__head p { font-size: .68rem; }
        .public-admission-page .student-section__body { max-width: none; padding: .6rem .75rem .7rem; }
        .public-admission-page .student-field-grid { gap: .55rem; }
        .public-admission-page .student-basic-fields .student-field-grid { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
        .public-admission-page .public-applied-class-field { grid-column: 1 / -1; }
        .public-admission-page .student-basic-layout { grid-template-columns: minmax(0, 1fr) minmax(230px, 285px); gap: .65rem; }
        .public-admission-page .student-basic-fields, .public-admission-page .student-basic-media__card { padding: .65rem; border-radius: .8rem; }
        .public-admission-page .student-basic-media { align-self: start; }
        .public-admission-page .student-basic-media__card { top: .65rem; height: auto; min-height: 0; display: flex; flex-direction: column; box-sizing: border-box; }
        .public-admission-page .student-form-page input[type="text"], .public-admission-page .student-form-page input[type="number"], .public-admission-page .student-form-page select, .public-admission-page .student-form-page textarea { padding: .64rem .72rem; border-radius: .7rem; font-size: .84rem; }
        .public-admission-page .student-form-page input[type="text"], .public-admission-page .student-form-page input[type="number"], .public-admission-page .student-form-page select { min-height: 38px; }
        .public-admission-page .student-form-page textarea { min-height: 68px; }
        .public-admission-page .student-form-page .student-floating-input { padding-top: .85rem; padding-bottom: .45rem; }
        .public-admission-page .student-form-page select { padding-top: .85rem; padding-bottom: .45rem; }
        .public-admission-page .student-form-page .student-floating-label { font-size: .66rem; }
        .public-admission-page .student-form-page .student-floating-label,
        .public-admission-page .student-form-page .student-floating-input:focus + .student-floating-label,
        .public-admission-page .student-form-page .student-floating-input:not(:placeholder-shown) + .student-floating-label,
        .public-admission-page .student-form-page .student-floating-input[value]:not([value=""]) + .student-floating-label { color: #1d4ed8 !important; }
        .public-admission-page .student-form-page .public-parent-phone-note { color: inherit; font-size: inherit; font-weight: inherit; }
        .public-admission-page .student-section__chevron { width: 1.35rem; height: 1.35rem; font-size: .68rem; }
        .public-admission-page .student-image-dropzone { flex: 1; min-height: 0 !important; display: flex; align-items: center; justify-content: center; }
        .public-admission-page input.datepicker { color: #0f172a; }
        .public-admission-page input.datepicker::placeholder { color: #64748b; opacity: 1; }
        .public-admission-page .datepicker-dropdown { z-index: 2000 !important; }
        .public-admission-page .student-form-actions a { display: none; }
        .public-admission-page .student-form-actions { position: sticky; bottom: 0; z-index: 5; margin: .25rem -.65rem -.65rem; padding: .7rem .65rem; background: rgba(255,255,255,.94); backdrop-filter: blur(10px); }
        .public-admission-page .student-form-actions button { min-width: 13rem; border: 0; border-radius: .7rem; padding: .7rem 1.25rem; background: linear-gradient(135deg, #059669, #0f766e); box-shadow: 0 8px 18px rgba(5, 150, 105, .2); font-weight: 800; }
        @media (max-width: 767.98px) { .public-site-main { padding: 1rem .75rem !important; } .public-admission-intro { padding: 1.1rem 1rem; } .public-admission-page .student-basic-layout { grid-template-columns: 1fr; } .public-admission-page .student-basic-fields .student-field-grid { grid-template-columns: 1fr !important; } .public-admission-page .student-basic-media__card { height: auto; } .public-admission-page .student-image-dropzone { min-height: 220px !important; } .public-admission-page .student-form-actions button { width: 100%; } }
    </style>
@endsection

@section('content')
<div class="public-admission-page">
    <section class="public-admission-intro">
        <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 1rem;">
            <div>
                <div class="text-uppercase" style="font-size:.75rem;letter-spacing:.2em;color:#99f6e4;font-weight:800;">{{ $exam ? 'Admissions ' . $exam->academicSession?->name_en : 'Admissions' }}</div>
                <h1>{{ $exam?->name ?? 'New Admission' }}</h1>
                <p>Complete the application carefully. A sequential four-digit application number will be generated after confirmation for tracking payment and results.</p>
            </div>
            @if($exam)
                <div class="rounded-3xl border border-white/20 bg-white/10 px-5 py-4 backdrop-blur-sm">
                    <div class="small text-teal-100">Exam date</div>
                    <div class="font-weight-bold">{{ $exam->exam_date?->format('d M Y') }}</div>
                    <div class="small text-teal-100 mt-1">{{ $exam->venue ?: 'Venue will be announced' }}</div>
                </div>
            @endif
        </div>
    </section>

    @if($exam)
        @php
            $admissionMode = true;
            $publicAdmissionMode = true;
            $draftData = $draft?->applicant_data ?? [];
        @endphp
        @include('pages.students.form')
    @else
        <div class="rounded-3xl border border-warning bg-white p-5 text-warning">There is no active admission exam at the moment.</div>
    @endif
</div>
@endsection

@section('scripts')
    @include('scripts.common.load_location')
    @include('scripts.common.load_academic_information')
    @include('scripts.student.main_script')
    <script src="{{ asset('assets/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>if (window.Dropzone) Dropzone.autoDiscover = false;</script>
    <script>
        $(document).ready(function () {
            const form = document.querySelector('.public-admission-page form');
            if (!form) return;
            form.action = @json(route('public.admission.store'));
            $('.public-admission-page .datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom'
            }).on('focus click', function () {
                $(this).datepicker('show');
            });
            const sameAddressCheckbox = form.querySelector('#same_address');
            const permanentAddressSection = form.querySelector('#permanent_address_section');
            const setPermanentAddressReadOnly = (isReadOnly) => {
                if (!permanentAddressSection) return;
                permanentAddressSection.querySelectorAll('select').forEach(select => {
                    select.setAttribute('aria-readonly', isReadOnly ? 'true' : 'false');
                    select.tabIndex = isReadOnly ? -1 : 0;
                });
                const address = permanentAddressSection.querySelector('textarea[name="permanent_address"]');
                if (address) address.readOnly = isReadOnly;
            };
            if (sameAddressCheckbox) {
                sameAddressCheckbox.addEventListener('change', function () {
                    setPermanentAddressReadOnly(this.checked);
                });
                setPermanentAddressReadOnly(sameAddressCheckbox.checked);
            }

            const guardianTypeInputs = form.querySelectorAll('input[name="guardian_type"]');
            const guardianInfoFields = form.querySelector('#guardianInfoFields');
            const syncGuardianValidation = () => {
                const isOtherGuardian = form.querySelector('input[name="guardian_type"]:checked')?.value === '3';
                ['guardian_name', 'guardian_relation', 'guardian_phone'].forEach((name) => {
                    const input = form.querySelector(`[name="${name}"]`);
                    if (input) input.required = isOtherGuardian;
                });
                if (guardianInfoFields) guardianInfoFields.classList.toggle('guardian-validation-active', isOtherGuardian);
            };
            guardianTypeInputs.forEach((input) => input.addEventListener('change', syncGuardianValidation));
            syncGuardianValidation();

            const dropzoneElement = form.querySelector('#studentImageDropzone');
            const imageInput = form.querySelector('#studentImageInput');
            const basicFields = form.querySelector('.student-basic-fields');
            const imageCard = form.querySelector('.student-basic-media__card');
            const syncImageCardHeight = () => {
                if (!basicFields || !imageCard) return;
                if (window.matchMedia('(max-width: 767.98px)').matches) {
                    imageCard.style.height = '';
                    return;
                }
                imageCard.style.height = `${basicFields.getBoundingClientRect().height}px`;
            };
            syncImageCardHeight();
            window.addEventListener('resize', syncImageCardHeight);
            if (dropzoneElement && imageInput && typeof Dropzone !== 'undefined') {
                const imageDropzone = new Dropzone(dropzoneElement, {
                    url: form.action,
                    clickable: false,
                    autoProcessQueue: false,
                    maxFiles: 1,
                    maxFilesize: 0.2,
                    acceptedFiles: 'image/*',
                    previewsContainer: dropzoneElement,
                    previewTemplate: '<div class="dz-preview dz-file-preview"><div class="dz-details"><div class="dz-filename"><span data-dz-name></span></div><div class="dz-size" data-dz-size></div></div><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div>'
                });

                dropzoneElement.addEventListener('click', () => imageInput.click());
                imageDropzone.on('addedfile', function (file) {
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    imageInput.files = transfer.files;
                });
                imageInput.addEventListener('change', function () {
                    imageDropzone.removeAllFiles(true);
                    if (imageInput.files[0]) imageDropzone.addFile(imageInput.files[0]);
                });
            }
            const submit = form.querySelector('.student-form-actions button');
            if (submit) submit.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Application';
        });
    </script>
@endsection
