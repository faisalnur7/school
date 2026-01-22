<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\SchoolClass;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('schoolClass')->latest()->get();
        $classes = SchoolClass::where('status', 1)->get();

        return view('pages.groups.index', compact('groups', 'classes'));
    }

    public function create()
    {
        $groups = Group::with('schoolClass')->latest()->get();
        $classes = SchoolClass::where('status', 1)->get();
        return view('pages.groups.create', compact('classes','groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        Group::create([
            'school_class_id' => $request->school_class_id,
            'name_en' => $request->name_en,
            'name_bn' => $request->name_bn,
            'status'  => 1,
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group created successfully');
    }

    public function edit($id)
    {
        $groups = Group::with('schoolClass')->latest()->get();
        $group = Group::findOrFail($id);
        $classes = SchoolClass::where('status', 1)->get();

        return view('pages.groups.edit', compact('group', 'classes','groups'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        $group = Group::findOrFail($id);
        $group->update([
            'school_class_id' => $request->school_class_id,
            'name_en' => $request->name_en,
            'name_bn' => $request->name_bn,
            'status'  => $request->has('status'),
        ]);

        return redirect()->route('groups.index')
            ->with('success', 'Group updated successfully');
    }

    public function toggleStatus($id)
    {
        $group = Group::findOrFail($id);
        $group->status = !$group->status;
        $group->save();

        return back();
    }

    public function destroy($id)
    {
        Group::findOrFail($id)->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully');
    }
}
