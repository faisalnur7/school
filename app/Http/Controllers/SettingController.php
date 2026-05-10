<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function general()
    {
        return view('dashboard');
    }

    public function updateGeneral(Request $request)
    {
        return redirect()->back();
    }

    public function email()
    {
        return view('dashboard');
    }

    public function updateEmail(Request $request)
    {
        return redirect()->back();
    }

    public function payment()
    {
        return view('dashboard');
    }

    public function updatePayment(Request $request)
    {
        return redirect()->back();
    }

    public function backup()
    {
        return view('dashboard');
    }

    public function createBackup()
    {
        return redirect()->back();
    }

    public function downloadBackup()
    {
        return redirect()->back();
    }
}

