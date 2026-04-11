<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        $professions = Profession::latest()->paginate(20);
        return view('pages.professions.index', compact('professions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100|unique:professions,name',
            'bn_name' => 'required|string|max:100',
        ]);

        Profession::create($request->only('name', 'bn_name'));

        return back()->with('success', 'Profession created.');
    }

    public function update(Request $request, Profession $profession)
    {
        $request->validate([
            'name'    => 'required|string|max:100|unique:professions,name,' . $profession->id,
            'bn_name' => 'required|string|max:100',
        ]);

        $profession->update($request->only('name', 'bn_name'));

        return back()->with('success', 'Profession updated.');
    }

    public function destroy(Profession $profession)
    {
        $profession->delete();
        return back()->with('success', 'Profession deleted.');
    }
}
