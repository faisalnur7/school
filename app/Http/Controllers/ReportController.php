<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function hub()
    {
        $cards = [
            [
                'icon'     => 'fa-money-check-alt',
                'title'    => 'Student Payment Report',
                'subtitle' => 'Fee & inventory payments by category',
                'route'    => 'reports.student-payment',
                'from'     => '#0ea5e9',
                'to'       => '#0369a1',
            ],
        ];

        return view('pages.reports.hub', compact('cards'));
    }

    public function index()
    {
        return view('dashboard');
    }

    public function student()
    {
        return view('dashboard');
    }
}

