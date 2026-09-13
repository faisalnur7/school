<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = ClassSchedule::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->search . '%'))
            ->orderBy('sort_order')
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        return view('pages.class-schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('pages.class-schedules.create');
    }

    public function store(Request $request)
    {
        ClassSchedule::create($this->validateData($request));

        return redirect()->route('class-schedules.index')->with('success', 'Time schedule created successfully.');
    }

    public function edit(int $id)
    {
        $schedule = ClassSchedule::findOrFail($id);

        return view('pages.class-schedules.edit', compact('schedule'));
    }

    public function update(Request $request, int $id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $schedule->update($this->validateData($request, $schedule));

        return redirect()->route('class-schedules.index')->with('success', 'Time schedule updated successfully.');
    }

    public function destroy(int $id)
    {
        $schedule = ClassSchedule::findOrFail($id);

        if ($schedule->routines()->exists()) {
            return back()->withErrors(['schedule' => 'This time schedule is used by a class routine and cannot be deleted.']);
        }

        $schedule->delete();

        return redirect()->route('class-schedules.index')->with('success', 'Time schedule deleted successfully.');
    }

    private function validateData(Request $request, ?ClassSchedule $schedule = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'kind' => ['required', Rule::in(['teaching', 'assembly', 'tiffin', 'prayer'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $overlap = ClassSchedule::query()
            ->where('id', '<>', $schedule?->id ?? 0)
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($overlap) {
            abort(422, 'This time overlaps an existing schedule.');
        }

        if ($data['kind'] === 'teaching' && empty($data['is_active'])) {
            $data['is_active'] = false;
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        return $data;
    }
}
