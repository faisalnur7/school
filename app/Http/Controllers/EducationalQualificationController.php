<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationalQualificationRequest;
use App\Models\EducationalQualification;
use Illuminate\Http\Request;

class EducationalQualificationController extends Controller
{
    public function index(Request $request)
    {
        $qualifications = EducationalQualification::query()
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('abbreviation', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('educational-qualifications.index', compact('qualifications'));
    }

    public function create()
    {
        return view('educational-qualifications.create');
    }

    public function store(StoreEducationalQualificationRequest $request)
    {
        EducationalQualification::create($request->validated());

        return redirect()->route('educational-qualifications.index')->with('success', 'Qualification created.');
    }

    public function edit(EducationalQualification $educationalQualification)
    {
        return view('educational-qualifications.edit', ['qualification' => $educationalQualification]);
    }

    public function update(StoreEducationalQualificationRequest $request, EducationalQualification $educationalQualification)
    {
        $educationalQualification->update($request->validated());

        return redirect()->route('educational-qualifications.index')->with('success', 'Qualification updated.');
    }

    public function destroy(EducationalQualification $educationalQualification)
    {
        if (method_exists($educationalQualification, 'employees') && $educationalQualification->employees()->exists()) {
            return back()->with('error', 'Cannot delete — employees are linked to this qualification.');
        }

        $educationalQualification->delete();

        return redirect()->route('educational-qualifications.index')->with('success', 'Qualification deleted.');
    }
}
