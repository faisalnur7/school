<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Division;
use App\Models\PoliceStation;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function index(Request $request)
    {
        $query = District::with('division')->latest();

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // Paginate and retain filters in query string
        $perPage = $request->input('per_page', 10);
        $districts = $query->paginate($perPage)->appends($request->query());

        $divisions = Division::all();

        return view('pages.district.dis_index', compact('districts', 'divisions', 'perPage'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('pages.district.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required|string|max:255',
            'bn_name'     => 'nullable|string|max:255',
            'lat'         => 'nullable|numeric',
            'lon'         => 'nullable|numeric',
            'url'         => 'nullable|string|max:255',
            'status'      => 'required|boolean',
        ]);

        District::create($request->all());

        return redirect()->route('district.index')->with('success', 'District created successfully.');
    }

    public function edit($id)
    {
        $district = District::findOrFail($id);
        $divisions = Division::all();
        return view('pages.district.edit', compact('district', 'divisions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required|string|max:255',
            'bn_name'     => 'nullable|string|max:255',
            'lat'         => 'nullable|numeric',
            'lon'         => 'nullable|numeric',
            'url'         => 'nullable|string|max:255',
            'status'      => 'required|boolean',
        ]);

        $district = District::findOrFail($id);
        $district->update($request->all());

        return redirect()->route('district.index')->with('success', 'District updated successfully.');
    }

    public function destroy($id)
    {
        $district = District::findOrFail($id);
        $district->delete();

        return redirect()->route('district.index')->with('success', 'District deleted successfully.');
    }

    public function getDistricts(Request $request, $division_id){
         $districts = District::where('division_id', $division_id)->get();
         return response()->json($districts);
    }

    public function getStations($district_id){
        $stations = PoliceStation::where('district_id', $district_id)->get();
        return response()->json($stations);
    }
}
