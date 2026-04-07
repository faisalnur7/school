<?php
namespace App\Http\Controllers;

use App\Models\IdCardTemplate;
use Illuminate\Http\Request;

class IdCardTemplateController extends Controller
{
    public function index()
    {
        $templates = IdCardTemplate::latest()->get();
        return view('pages.id-card-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('pages.id-card-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $data = $request->only(['name', 'orientation', 'front_bg_color', 'back_bg_color']);
        $data['design_front'] = $request->input('design_front') ? json_decode($request->input('design_front'), true) : [];
        $data['design_back']  = $request->input('design_back')  ? json_decode($request->input('design_back'),  true) : [];

        foreach (['front_bg_image' => 'front_bg_image', 'back_bg_image' => 'back_bg_image'] as $field => $col) {
            if ($request->hasFile($field)) {
                $file     = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/id_card_templates'), $filename);
                $data[$col] = 'uploads/id_card_templates/' . $filename;
            }
        }

        IdCardTemplate::create($data);
        return redirect()->route('id-card-templates.index')->with('success', 'Template saved successfully.');
    }

    public function edit(IdCardTemplate $idCardTemplate)
    {
        return view('pages.id-card-templates.edit', compact('idCardTemplate'));
    }

    public function update(Request $request, IdCardTemplate $idCardTemplate)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'orientation' => 'required|in:portrait,landscape',
        ]);

        $data = $request->only(['name', 'orientation', 'front_bg_color', 'back_bg_color']);
        $data['design_front'] = $request->input('design_front') ? json_decode($request->input('design_front'), true) : [];
        $data['design_back']  = $request->input('design_back')  ? json_decode($request->input('design_back'),  true) : [];

        foreach (['front_bg_image', 'back_bg_image'] as $field) {
            if ($request->hasFile($field)) {
                if ($idCardTemplate->$field && file_exists(public_path($idCardTemplate->$field))) {
                    unlink(public_path($idCardTemplate->$field));
                }
                $file     = $request->file($field);
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/id_card_templates'), $filename);
                $data[$field] = 'uploads/id_card_templates/' . $filename;
            }
        }

        $idCardTemplate->update($data);
        return redirect()->route('id-card-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(IdCardTemplate $idCardTemplate)
    {
        foreach (['background_image', 'front_bg_image', 'back_bg_image'] as $f) {
            if ($idCardTemplate->$f && file_exists(public_path($idCardTemplate->$f))) {
                unlink(public_path($idCardTemplate->$f));
            }
        }
        $idCardTemplate->delete();
        return redirect()->route('id-card-templates.index')->with('success', 'Template deleted.');
    }
}
