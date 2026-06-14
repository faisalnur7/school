@extends('layouts.master')

@section('contents')
<div class="container-fluid">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-teal-700 via-cyan-700 to-slate-900 p-8 mb-6 no-print">
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-72 h-72 rounded-full bg-cyan-400/20 blur-3xl"></div>
        <div class="relative z-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <div class="flex h-18 w-18 items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm">
                    <i class="fas fa-certificate text-white text-4xl"></i>
                </div>
                <div>
                    <h3 class="text-white text-3xl font-bold m-0">{{ $certificate->name }}</h3>
                    <p class="text-teal-100 text-base mt-1 mb-0">
                        {{ $certificate->description ?: 'Certificate preview' }}
                    </p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('students.certificates', ['search' => $student->student_cid]) }}"
                   class="inline-flex items-center rounded-lg bg-white/10 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-white/20">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Hub
                </a>
                <button type="button"
                        onclick="window.print()"
                        class="inline-flex items-center rounded-lg bg-rose-500 px-3 py-2 text-white text-xs font-semibold no-underline hover:bg-rose-600">
                    <i class="fas fa-print mr-2"></i> Print Preview
                </button>
            </div>
        </div>
    </div>

    <div class="bg-slate-100 rounded-3xl p-4 shadow-inner">
        <div class="mx-auto max-w-4xl bg-white border border-slate-300 shadow-xl px-8 py-10 md:px-14 md:py-12 certificate-sheet">
            <div class="text-center mb-10">
                <div class="inline-block border border-slate-400 px-8 py-2">
                    <div class="text-xl font-semibold tracking-wide text-slate-700 uppercase">
                        {{ $certificate->name }}
                    </div>
                </div>
            </div>

            <div class="certificate-body text-[15px] leading-9 text-slate-700">
                <div class="certificate-preview-text text-justify">
                    {!! $certificateTextHtml ?? '' !!}
                </div>
            </div>

            <div class="certificate-bottom flex items-end justify-between gap-8">
                <div class="max-w-xs text-sm text-slate-600">
                <div class="font-semibold text-slate-800 mb-2">Reason for Leaving School:</div>
                <div class="font-bold text-slate-700">{{ $leavingReason ?? 'No reason provided' }}</div>
            </div>

            <div class="text-right">
                <div class="mx-auto mb-3 w-44 border-t border-slate-400"></div>
                <div class="text-2xl font-bold text-slate-800">
                    {{ $setting->principal_designation ?: 'Principal' }}
                </div>
                <div class="text-sm text-slate-600">
                    @if(!empty($setting->principal_name))
                        ({{ $setting->principal_name }})
                    @endif
                </div>
                <div class="text-sm text-slate-600">
                    {{ $setting->principal_school_name ?: $setting->name }}
                </div>
                @if(!empty($setting->principal_phone))
                    <div class="text-sm text-slate-600">{{ $setting->principal_phone }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .certificate-preview-text strong {
        font-weight: 700;
        font-style: italic;
        color: #2f2f2f;
    }

    .certificate-preview-text {
        text-align: justify;
        text-justify: inter-word;
    }

    .certificate-preview-text p {
        text-align: justify;
        text-justify: inter-word;
        margin-bottom: 12px;
    }

    .certificate-body {
        width: 100%;
        max-width: none;
        padding-left: 0.25in;
        padding-right: 0.25in;
    }

    @page {
        margin: 2.5in 0.7in 0.7in;
    }

    @media print {
        .no-print { display: none !important; }
        html, body {
            background: #fff !important;
            width: 100% !important;
            height: auto !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        .container-fluid {
            padding: 0 !important;
            width: 100% !important;
        }
        .container-fluid > * {
            width: 100% !important;
        }
        .certificate-sheet {
            box-shadow: none !important;
            border: 0 !important;
            padding: 0 !important;
            margin: 0 auto !important;
            max-width: none !important;
            width: 100% !important;
        }
        .bg-slate-100 {
            background: transparent !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .certificate-preview-text {
            width: 100% !important;
            max-width: none !important;
            text-align: justify !important;
            text-justify: inter-word !important;
        }
        .certificate-body {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .certificate-preview-text p {
            text-align: justify !important;
            text-justify: inter-word !important;
            margin-bottom: 10px !important;
        }
        .certificate-bottom {
            margin-top: 76mm !important;
        }
    }
</style>
@endsection
