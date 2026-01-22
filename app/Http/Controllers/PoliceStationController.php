<?php

namespace App\Http\Controllers;

use App\Models\PoliceStation;
use App\Models\District;
use App\Models\Division;
use Illuminate\Http\Request;

class PoliceStationController extends Controller
{
    public function index(Request $request){
        $divisions = Division::all();

        $districts = District::when($request->division_id, function ($query) use ($request) {
            $query->where('division_id', $request->division_id);
        })->get();

        $stationsQuery = PoliceStation::with('district.division')
            ->when($request->district_id, function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
            })
            ->when($request->division_id, function ($query) use ($request) {
                // Filter police stations by division via nested relationship
                $query->whereHas('district.division', function ($q) use ($request) {
                    $q->where('id', $request->division_id);
                });
            })
            ->when($request->filled('name'), function ($query) use ($request) {
                $name = $request->name;
                $query->where(function ($q) use ($name) {
                    $q->where('name', 'like', "%{$name}%")
                    ->orWhere('bn_name', 'like', "%{$name}%");
                });
            })
            ->latest();

        // Use dynamic per_page (optional: fallback to 10)
        $perPage = $request->input('per_page', 10);

        $stations = $stationsQuery->paginate($perPage)->appends($request->query());

        return view('pages.police_station.index', compact('stations', 'divisions', 'districts', 'perPage'));
    }




    public function create()
    {
        $districts = District::all();
        return view('pages.police_station.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name'        => 'required|string|max:255',
            'bn_name'     => 'nullable|string|max:255',
            'url'         => 'nullable|string|max:255',
            'status'      => 'required|boolean',
        ]);

        PoliceStation::create($request->all());

        return redirect()->route('police-station.index')->with('success', 'Police Station created successfully.');
    }

    public function edit($id)
    {
        $station = PoliceStation::findOrFail($id);
        $districts = District::all();
        return view('pages.police_station.edit', compact('station', 'districts'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name'        => 'required|string|max:255',
            'bn_name'     => 'nullable|string|max:255',
            'url'         => 'nullable|string|max:255',
            'status'      => 'required|boolean',
        ]);

        $station = PoliceStation::findOrFail($id);
        $station->update($request->all());

        return redirect()->route('police-station.index')->with('success', 'Police Station updated successfully.');
    }

    public function destroy($id)
    {
        $station = PoliceStation::findOrFail($id);
        $station->delete();

        return redirect()->route('police-station.index')->with('success', 'Police Station deleted successfully.');
    }
}
