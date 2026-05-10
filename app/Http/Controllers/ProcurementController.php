<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function orders()
    {
        return view('dashboard');
    }

    public function createOrder()
    {
        return view('dashboard');
    }

    public function storeOrder(Request $request)
    {
        return redirect()->back();
    }

    public function vendors()
    {
        return view('dashboard');
    }

    public function createVendor()
    {
        return view('dashboard');
    }

    public function storeVendor(Request $request)
    {
        return redirect()->back();
    }

    public function budget()
    {
        return view('dashboard');
    }

    public function allocation()
    {
        return view('dashboard');
    }
}

