<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\WeekendSetting;
use Illuminate\Http\Request;

class AttendanceSettingsController extends Controller
{
    public function index()
    {
        $weekendSetting = WeekendSetting::current();
        $holidays = Holiday::orderBy('date')->get();

        return view('pages.attendance.settings', compact('weekendSetting', 'holidays'));
    }

    public function saveWeekends(Request $request)
    {
        $request->validate([
            'weekend_days'   => ['nullable', 'array'],
            'weekend_days.*' => ['integer', 'between:0,6'],
        ]);

        $days = $request->input('weekend_days', []);

        $setting = WeekendSetting::first();
        if ($setting) {
            $setting->update(['weekend_days' => $days]);
        } else {
            WeekendSetting::create(['weekend_days' => $days]);
        }

        return back()->with('success', 'Weekend settings saved.');
    }

    public function storeHoliday(Request $request)
    {
        $request->validate([
            'date_start'  => ['required', 'date'],
            'date_end'    => ['required', 'date', 'gte:date_start'],
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $start = \Carbon\Carbon::parse($request->date_start);
        $end   = \Carbon\Carbon::parse($request->date_end);

        $inserted = 0;
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            Holiday::firstOrCreate(
                ['date' => $d->toDateString()],
                ['title' => $request->title, 'description' => $request->description]
            );
            $inserted++;
        }

        $label = $inserted === 1
            ? $start->format('d M Y')
            : $start->format('d M Y') . ' – ' . $end->format('d M Y');

        return back()->with('success', "Holiday(s) added: {$label}.");
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Holiday deleted.');
    }
}
