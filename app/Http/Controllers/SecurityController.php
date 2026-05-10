<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function accessControl()
    {
        return view('dashboard');
    }

    public function createAccessControl()
    {
        return view('dashboard');
    }

    public function storeAccessControl(Request $request)
    {
        return redirect()->back();
    }

    public function auditTrails()
    {
        return view('dashboard');
    }

    public function emergencyProtocols()
    {
        return view('dashboard');
    }

    public function emergencyResponse()
    {
        return view('dashboard');
    }

    public function cctvIntegration()
    {
        return view('dashboard');
    }
}

