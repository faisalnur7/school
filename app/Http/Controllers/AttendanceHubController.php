<?php

namespace App\Http\Controllers;

class AttendanceHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-clipboard-list',  'title' => __('Daily Attendance'),  'subtitle' => __('Mark & view daily attendance'),   'route' => 'attendance.index', 'permission' => 'view_card_daily_attendance', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-calendar-check',  'title' => __('Monthly Report'),    'subtitle' => __('View monthly attendance report'), 'route' => 'attendance.report.monthly', 'permission' => 'view_card_monthly_attendance_report', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-cog',             'title' => __('Settings'),          'subtitle' => __('Weekends & holiday settings'),    'route' => 'attendance.settings.index', 'permission' => 'view_card_attendance_settings', 'from' => '#6b7280', 'to' => '#4b5563'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.attendance.hub', compact('cards'));
    }
}
