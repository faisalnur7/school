<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Admission' }} · {{ \App\Models\SchoolSetting::current()->name ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body{font-family:'DM Sans',sans-serif}.mesh{background:radial-gradient(circle at 10% 10%,#ccfbf1 0,transparent 32%),radial-gradient(circle at 90% 0,#dbeafe 0,transparent 30%),#f8fafc}.public-site-main,.public-site-container{width:100%;max-width:90rem!important}</style>
    @yield('styles')
</head>
<body class="mesh min-h-screen text-slate-800">
<header class="public-site-header sticky top-0 z-10 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl"><div class="public-site-container mx-auto flex items-center justify-between px-5 py-4"><a href="{{ route('public.admission.form') }}" class="flex items-center gap-3"><img src="{{ \App\Models\SchoolSetting::current()->logo ? asset(\App\Models\SchoolSetting::current()->logo) : asset('assets/dist/img/AdminLTELogo.png') }}" class="h-11 w-11 rounded-xl object-cover" alt="Logo"><span class="font-extrabold text-slate-900">{{ \App\Models\SchoolSetting::current()->name ?? config('app.name') }}</span></a><nav class="flex gap-2 text-sm font-semibold"><a class="rounded-full px-4 py-2 {{ request()->routeIs('public.admission.form') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-white' }}" href="{{ route('public.admission.form') }}">New Admission</a><a class="rounded-full px-4 py-2 {{ request()->routeIs('public.admission.search') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-white' }}" href="{{ route('public.admission.search') }}">Search Application</a></nav></div></header>
<main class="public-site-main mx-auto max-w-6xl px-5 py-10">
@if(session('success'))
<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
@yield('content')
</main>
<footer class="public-site-footer public-site-container mx-auto px-5 pb-10 text-sm text-slate-500">Admissions office · {{ date('Y') }}</footer>
<script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker.min.js') }}"></script>
@yield('scripts')
</body>
</html>
