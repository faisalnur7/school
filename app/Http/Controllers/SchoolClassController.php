<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index()
    {
        $data['classes'] = SchoolClass::latest()->get();
        $data['classes'] = SchoolClass::latest()->get();
        return view('pages.classes.index', $data);
    }

    public function create()
    {
        $data['classes'] = SchoolClass::latest()->get();
        return view('pages.classes.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|unique:school_classes,name_en',
            'name_bn' => 'required|unique:school_classes,name_bn',
        ]);

        $request->status = $request->status ?? 0;

        SchoolClass::create($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully');
    }

    public function edit($id)
    {
        $data['classes'] = SchoolClass::latest()->get();
        $data['class'] = SchoolClass::findOrFail($id);
        return view('pages.classes.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|unique:school_classes,name_en,' . $id,
            'name_bn' => 'required|unique:school_classes,name_bn,' . $id,
        ]);

        $class = SchoolClass::findOrFail($id);
        $class->status = $request->status ?? 0;
        $class->update($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy($id)
    {
        SchoolClass::findOrFail($id)->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully');
    }

    public function toggleStatus($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->status = ! $class->status;
        $class->save();

        return back();
    }
}
