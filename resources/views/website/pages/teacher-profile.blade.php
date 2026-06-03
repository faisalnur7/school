@extends('website.layouts.app')
@section('content')
<div class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-indigo-600">Faculty Profile</p>
    <h1 class="mt-2 text-3xl font-bold text-slate-900 lg:text-4xl">{{ $employee->name }}</h1>
</div>

<div class="rounded-3xl border border-white/50 bg-white/80 p-8 shadow-elevated backdrop-blur-sm">
    <div class="grid gap-8 lg:grid-cols-3">
        <div class="lg:col-span-1">
            <div class="relative inline-block">
                <img src="{{ $employee->photo_url }}" alt="{{ $employee->name }}" class="h-48 w-48 rounded-2xl object-cover shadow-lg">
            </div>
        </div>
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-slate-900">{{ $employee->name }}</h2>
            @php
                $desigObj = $employee->designation;
                $desigName = $desigObj && is_object($desigObj) ? $desigObj->name : 'Teacher';
                $deptObj = $employee->department;
                $deptName = $deptObj && is_object($deptObj) ? $deptObj->name : null;
            @endphp
            <p class="mt-2 text-lg font-medium text-indigo-600">{{ $desigName }}</p>
            @if($deptName)
                <p class="text-slate-600">{{ $deptName }}</p>
            @endif
            
            <div class="mt-6 space-y-3 text-sm">
                @if($employee->phone)
                    <p><span class="font-semibold text-slate-800">Phone:</span> {{ $employee->phone }}</p>
                @endif
                <p><span class="font-semibold text-slate-800">Employee ID:</span> {{ $employee->employee_id }}</p>
                <p><span class="font-semibold text-slate-800">Status:</span> <span class="capitalize">{{ $employee->status }}</span></p>
                @if($employee->joining_date)
                    <p><span class="font-semibold text-slate-800">Joined:</span> {{ $employee->joining_date->format('M d, Y') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection