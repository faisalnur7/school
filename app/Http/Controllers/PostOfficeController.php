<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Division;
use App\Models\PostOffice;
use App\Models\PoliceStation;
use Illuminate\Http\Request;

class PostOfficeController extends Controller
{
    public function index(Request $request)
    {
        // Start query with eager loading
        $query = PostOffice::with('policeStation.district.division')->latest();

        // Filter by Division
        if ($request->filled('division_id')) {
            $query->whereHas('policeStation.district.division', function ($q) use ($request) {
                $q->where('id', $request->division_id);
            });
        }

        // Filter by District
        if ($request->filled('district_id')) {
            $query->whereHas('policeStation.district', function ($q) use ($request) {
                $q->where('id', $request->district_id);
            });
        }

        // Filter by Police Station
        if ($request->filled('police_station_id')) {
            $query->where('police_station_id', $request->police_station_id);
        }

        // Filter by Name (English or Bangla)
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%')
                ->orWhere('bn_name', 'like', '%' . $request->name . '%');
            });
        }

        // Dynamic pagination
        $perPage = $request->input('per_page', 10);
        $postOffices = $query->paginate($perPage)->appends($request->query());

        // For dependent dropdowns
        $divisions = Division::orderBy('name')->get();
        $districts = District::when($request->division_id, fn($q) => $q->where('division_id', $request->division_id))->get();
        $stations  = PoliceStation::when($request->district_id, fn($q) => $q->where('district_id', $request->district_id))->get();

        return view('pages.post_office.index', compact('postOffices', 'divisions', 'districts', 'stations', 'perPage'));
    }





    public function create()
    {
        $policeStations = PoliceStation::all();
        return view('pages.post_office.create', compact('policeStations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'police_station_id' => 'required|exists:police_stations,id',
            'name'              => 'required|string|max:255',
            'bn_name'           => 'nullable|string|max:255',
            'url'               => 'nullable|string|max:255',
            'postcode'         => 'nullable|string|max:10',
            'status'            => 'required|boolean',
        ]);

        PostOffice::create($request->all());

        return redirect()->route('post-office.index')->with('success', 'Post Office created successfully.');
    }

    public function edit($id)
    {
        $postOffice = PostOffice::findOrFail($id);
        $policeStations = PoliceStation::all();
        return view('pages.post_office.edit', compact('postOffice', 'policeStations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'police_station_id' => 'required|exists:police_stations,id',
            'name'              => 'required|string|max:255',
            'bn_name'           => 'nullable|string|max:255',
            'url'               => 'nullable|string|max:255',
            'postcode'         => 'nullable|string|max:10',
            'status'            => 'required|boolean',
        ]);

        $postOffice = PostOffice::findOrFail($id);
        $postOffice->update($request->all());

        return redirect()->route('post-office.index')->with('success', 'Post Office updated successfully.');
    }

    public function destroy($id)
    {
        $postOffice = PostOffice::findOrFail($id);
        $postOffice->delete();

        return redirect()->route('post-office.index')->with('success', 'Post Office deleted successfully.');
    }
}
