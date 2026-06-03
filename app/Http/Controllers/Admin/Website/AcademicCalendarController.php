<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\AcademicCalendar;
use Illuminate\Http\Request;

class AcademicCalendarController extends Controller
{
    public function index()
    {
        $items = AcademicCalendar::query()->orderBy('sort_order')->orderBy('start_date')->paginate(20);

        return view('pages.website.academic-calendar.index', compact('items'));
    }

    public function create()
    {
        return view('pages.website.academic-calendar.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        AcademicCalendar::create($data);

        return redirect()->route('website.academic-calendar.index')->with('success', 'Calendar item created.');
    }

    public function edit(AcademicCalendar $academicCalendar)
    {
        return view('pages.website.academic-calendar.edit', ['item' => $academicCalendar]);
    }

    public function update(Request $request, AcademicCalendar $academicCalendar)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = (bool) ($data['is_published'] ?? false);
        $academicCalendar->update($data);

        return redirect()->route('website.academic-calendar.index')->with('success', 'Calendar item updated.');
    }

    public function destroy(AcademicCalendar $academicCalendar)
    {
        $academicCalendar->delete();

        return redirect()->route('website.academic-calendar.index')->with('success', 'Calendar item deleted.');
    }
}
