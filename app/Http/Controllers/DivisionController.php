<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::latest()->get();
        return view('pages.division.div_list', compact('divisions'));
    }

    public function create()
    {
        return view('pages.division.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'bn_name'  => 'nullable|string|max:255',
            'status'   => 'required|boolean',
        ]);

        Division::create($request->all());

        return redirect()->route('division.index')->with('success', 'Division created successfully.');
    }

    public function edit($id)
    {
        $division = Division::findOrFail($id);
        return view('pages.division.edit', compact('division'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'bn_name'  => 'nullable|string|max:255',
            'status'   => 'required|boolean',
        ]);

        $division = Division::findOrFail($id);
        $division->update($request->all());

        return redirect()->route('division.index')->with('success', 'Division updated successfully.');
    }

    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        $division->delete();

        return redirect()->route('division.index')->with('success', 'Division deleted successfully.');
    }
}
