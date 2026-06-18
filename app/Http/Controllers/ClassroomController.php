<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('name_en')
            ->paginate(20)
            ->withQueryString();

        return view('pages.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('pages.classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Classroom::create($validated);

        return redirect()->route('classrooms.index')->with('success', 'Classroom created successfully.');
    }

    public function edit(int $id)
    {
        $classroom = Classroom::findOrFail($id);

        return view('pages.classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, int $id)
    {
        $classroom = Classroom::findOrFail($id);
        $validated = $this->validateData($request);

        $classroom->update($validated);

        return redirect()->route('classrooms.index')->with('success', 'Classroom updated successfully.');
    }

    public function destroy(int $id)
    {
        $classroom = Classroom::findOrFail($id);
        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Classroom deleted successfully.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:255'],
            'name_bn' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
