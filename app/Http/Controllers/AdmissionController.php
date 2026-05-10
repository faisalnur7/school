<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdmissionController extends Controller
{
    public function applications()
    {
        return view('dashboard');
    }

    public function createApplication()
    {
        return view('dashboard');
    }

    public function storeApplication(Request $request)
    {
        return redirect()->back();
    }

    public function processTracking()
    {
        return view('dashboard');
    }

    public function documentVerification()
    {
        return view('dashboard');
    }

    public function interviewScheduling()
    {
        return view('dashboard');
    }

    public function onlinePortal()
    {
        return view('dashboard');
    }
}

