<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::latest()->get();
        return view('pages.groups.index', compact('groups'));
    }

    public function create()
    {
        $groups = Group::latest()->get();
        return view('pages.groups.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        Group::create([
            'name_en' => $request->name_en,
            'name_bn' => $request->name_bn,
            'status'  => 1,
        ]);

        return redirect()->route('groups.index')->with('success', 'Group created successfully');
    }

    public function edit($id)
    {
        $groups = Group::latest()->get();
        $group = Group::findOrFail($id);
        return view('pages.groups.edit', compact('group', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required',
            'name_bn' => 'required',
        ]);

        $group = Group::findOrFail($id);
        $group->update([
            'name_en' => $request->name_en,
            'name_bn' => $request->name_bn,
            'status'  => $request->has('status'),
        ]);

        return redirect()->route('groups.index')->with('success', 'Group updated successfully');
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

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully');
    }
}
