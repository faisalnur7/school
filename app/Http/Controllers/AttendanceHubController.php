<?php

namespace App\Http\Controllers;

class AttendanceHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-clipboard-list',  'title' => 'Daily Attendance',  'subtitle' => 'Mark & view daily attendance',   'route' => 'attendance.index',          'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-calendar-check',  'title' => 'Monthly Report',    'subtitle' => 'View monthly attendance report', 'route' => 'attendance.report.monthly', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-cog',             'title' => 'Settings',          'subtitle' => 'Weekends & holiday settings',    'route' => 'attendance.settings.index', 'from' => '#6b7280', 'to' => '#4b5563'],
        ];

        return view('pages.attendance.hub', compact('cards'));
    }
}
