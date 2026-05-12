<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Http\Requests\StoreSchoolSettingRequest;

class SchoolSettingController extends Controller
{
    public function index()
    {
        $setting = SchoolSetting::current();
        $classes = SchoolClass::get();
        return view('pages.school-settings.index', compact('setting', 'classes'));
    }

    public function update(StoreSchoolSettingRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('logo')) {
            $image    = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/school_settings'), $filename);
            $validated['logo'] = 'uploads/school_settings/' . $filename;
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('letter_head')) {
            $file     = $request->file('letter_head');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/school_settings'), $filename);
            $validated['letter_head'] = 'uploads/school_settings/' . $filename;
        } else {
            unset($validated['letter_head']);
        }

        if ($request->hasFile('whatsapp_qr')) {
            $image    = $request->file('whatsapp_qr');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/school_settings'), $filename);
            $validated['whatsapp_qr'] = 'uploads/school_settings/' . $filename;
        } else {
            unset($validated['whatsapp_qr']);
        }

        $setting = SchoolSetting::firstOrNew(['id' => 1]);
        $setting->fill($validated);
        $setting->save();

        return redirect()->route('school-settings.index')->with('success', 'School settings saved successfully.');
    }
}
