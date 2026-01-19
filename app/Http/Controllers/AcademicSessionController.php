<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['sessions'] = AcademicSession::latest()->get();
        return view('pages.sessions.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['sessions'] = AcademicSession::latest()->get();
        return view('pages.sessions.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_bn' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'status'  => 'nullable|boolean',
        ]);

        AcademicSession::create([
            'name_bn' => $request->name_bn,
            'name_en' => $request->name_en,
            'status'  => $request->status ?? 1,
        ]);

        return redirect()
            ->route('sessions.index')
            ->with('success', 'Academic Session created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['sessions'] = AcademicSession::latest()->get();
        $data['session'] = AcademicSession::findOrFail($id);
        return view('pages.sessions.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name_en' => 'required|unique:academic_sessions,name_en,' . $id,
            'name_bn' => 'required|unique:academic_sessions,name_bn,' . $id,
        ]);

        $session = AcademicSession::findOrFail($id);
        $session->name_en = $request->name_en;
        $session->name_bn = $request->name_bn;
        $session->status  = $request->has('status');
        $session->save();

        return redirect()->route('sessions.index')
            ->with('success', 'Academic session updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        AcademicSession::findOrFail($id)->delete();

        return redirect()
            ->route('sessions.index')
            ->with('success', 'Academic Session deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $session = AcademicSession::findOrFail($id);
        $session->status = !$session->status;
        $session->save();

        return back()->with('success', 'Status updated.');
    }

}
