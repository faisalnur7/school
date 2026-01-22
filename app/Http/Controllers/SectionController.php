<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $data['sections'] = Section::with('schoolClass')->latest()->get();
        return view('pages.sections..index', $data);
    }

    public function create()
    {
        $data['sections'] = Section::with('schoolClass')->latest()->get();
        $data['classes'] = SchoolClass::where('status', 1)->get();
        return view('pages.sections.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        Section::create($request->all());

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully');
    }

    public function edit($id)
    {
        $data['sections'] = Section::with('schoolClass')->latest()->get();
        $data['section'] = Section::findOrFail($id);
        $data['classes'] = SchoolClass::where('status', 1)->get();

        return view('pages.sections..edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        $section = Section::findOrFail($id);
        $section->update($request->all());

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully');
    }

    public function destroy($id)
    {
        Section::findOrFail($id)->delete();

        return redirect()->route('sections.index')
            ->with('success', 'Section deleted successfully');
    }

    public function toggleStatus($id)
    {
        $section = Section::findOrFail($id);
        $section->status = !$section->status;
        $section->save();

        return back()->with('success', 'Status updated.');
    }
}

