<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $buildings = Building::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->withCount('rooms')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('pages.settings.buildings.index', compact('buildings'));
    }

    public function create()
    {
        return view('pages.settings.buildings.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Building::create($validated);

        return redirect()->route('buildings.index')->with('success', 'Building created successfully.');
    }

    public function edit(Building $building)
    {
        return view('pages.settings.buildings.edit', compact('building'));
    }

    public function update(Request $request, Building $building)
    {
        $validated = $this->validateData($request, $building->id);

        $building->update($validated);

        return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');
    }

    public function destroy(Building $building)
    {
        if ($building->rooms()->exists()) {
            return back()->with('error', 'Cannot delete this building because rooms are linked to it.');
        }

        $building->delete();

        return redirect()->route('buildings.index')->with('success', 'Building deleted successfully.');
    }

    private function validateData(Request $request, ?int $buildingId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:buildings,code,' . $buildingId,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
