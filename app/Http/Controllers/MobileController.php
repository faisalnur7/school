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
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        return redirect()->route('platform-admins.mobile-app.edit');
    }

    public function updateSettings(Request $request)
    {
        abort_unless(auth()->user()?->isPlatformAdmin(), 403);

        return redirect()->route('platform-admins.mobile-app.edit');
    }

    public function socialIntegration()
    {
        return view('dashboard');
    }
}

