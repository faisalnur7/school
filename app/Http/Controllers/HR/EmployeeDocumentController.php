<?php
namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;

class EmployeeDocumentController extends Controller
{
    public function index(int $employeeId)
    {
        $employee  = Employee::findOrFail($employeeId);
        $documents = $employee->documents()->latest()->get();
        return view('hr.employees.partials.documents', compact('employee', 'documents'));
    }

    public function store(Request $request, int $employeeId)
    {
        $request->validate([
            'document_type' => 'required|in:nid,passport,certificate,contract,photo_id,other',
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/employees'), $filename);

        EmployeeDocument::create([
            'employee_id'   => $employeeId,
            'document_type' => $request->document_type,
            'file_path'     => 'uploads/employees/' . $filename,
            'original_name' => $originalName,
            'uploaded_at'   => now(),
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    public function destroy(EmployeeDocument $document)
    {
        if (file_exists(public_path($document->file_path))) {
            unlink(public_path($document->file_path));
        }
        $document->delete();
        return back()->with('success', 'Document deleted.');
    }
}
