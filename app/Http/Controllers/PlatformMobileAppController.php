<?php

namespace App\Http\Controllers;

use App\Models\MobileAppFeature;
use App\Models\MobileSetting;
use App\Services\MobileAppConfigurationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformMobileAppController extends Controller
{
    public function edit(): View
    {
        return view('pages.platform-admins.mobile-app', [
            'settings' => MobileSetting::current(),
            'features' => MobileAppFeature::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, MobileAppConfigurationService $configuration): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'features' => ['nullable', 'array'],
            'features.*.is_enabled' => ['nullable', 'boolean'],
            'features.*.sort_order' => ['nullable', 'integer', 'min:1'],
            'features.*.label' => ['nullable', 'string', 'max:255'],
            'features.*.icon' => ['nullable', 'string', 'max:100'],
        ]);

        $features = collect($validated['features'] ?? [])
            ->map(fn (array $feature, string $key): array => array_merge(['key' => $key], $feature))
            ->values()
            ->all();

        $configuration->update([
            'is_active' => $request->boolean('is_active'),
            'maintenance_message' => $validated['maintenance_message'] ?? null,
        ], $features, (int) $request->user()->id);

        return redirect()
            ->route('platform-admins.mobile-app.edit')
            ->with('success', 'Mobile app settings updated successfully.');
    }
}
