<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LMSController extends Controller
{
    public function courses()
    {
        return view('dashboard');
    }

    public function createCourse()
    {
        return view('dashboard');
    }

    public function storeCourse(Request $request)
    {
        return redirect()->back();
    }

    public function assignments()
    {
        return view('dashboard');
    }

    public function createAssignment()
    {
        return view('dashboard');
    }

    public function storeAssignment(Request $request)
    {
        return redirect()->back();
    }

    public function digitalClassroom()
    {
        return view('dashboard');
    }

    public function videoConference()
    {
        return view('dashboard');
    }

    public function contentManagement()
    {
        return view('dashboard');
    }
}

