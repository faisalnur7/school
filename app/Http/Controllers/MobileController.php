<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function notifications()
    {
        return view('dashboard');
    }

    public function sendNotification(Request $request)
    {
        return redirect()->back();
    }

    public function offlineManagement()
    {
        return view('dashboard');
    }

    public function settings()
    {
        return view('dashboard');
    }

    public function updateSettings(Request $request)
    {
        return redirect()->back();
    }

    public function socialIntegration()
    {
        return view('dashboard');
    }
}

