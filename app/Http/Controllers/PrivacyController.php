<?php

namespace App\Http\Controllers;

class PrivacyController extends Controller
{
    public function privacySettings()
    {
        return view('dashboard');
    }

    public function dataProtection()
    {
        return view('dashboard');
    }

    public function gdprCompliance()
    {
        return view('dashboard');
    }
}

