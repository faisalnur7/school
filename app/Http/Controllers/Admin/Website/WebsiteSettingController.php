<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    public function edit()
    {
        $keys = [
            'school_name',
            'tagline',
            'contact_email',
            'contact_phone',
            'address',
            'footer_about',
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'social_linkedin',
        ];
        $settings = WebsiteSetting::query()->whereIn('key', $keys)->pluck('value', 'key');

        return view('pages.website.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'footer_about' => ['nullable', 'string', 'max:1000'],
            'social_facebook' => ['nullable', 'url', 'max:500'],
            'social_instagram' => ['nullable', 'url', 'max:500'],
            'social_youtube' => ['nullable', 'url', 'max:500'],
            'social_linkedin' => ['nullable', 'url', 'max:500'],
        ]);

        foreach ($data as $key => $value) {
            WebsiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('website.settings.edit')->with('success', 'Website settings updated.');
    }
}
