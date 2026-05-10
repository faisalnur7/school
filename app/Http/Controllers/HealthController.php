<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HealthController extends Controller
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

    public function edit($id)
    {
        return view('dashboard');
    }

    public function update(Request $request, $id)
    {
        return redirect()->back();
    }

    public function destroy($id)
    {
        return redirect()->back();
    }
}

