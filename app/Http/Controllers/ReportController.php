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
                'permission' => 'view_card_reports_student_payment',
                'from'     => '#0ea5e9',
                'to'       => '#0369a1',
            ],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

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
