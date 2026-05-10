<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function create()
    {
        return view('dashboard');
    }

    public function store(Request $request)
    {
        return redirect()->back();
    }
}

