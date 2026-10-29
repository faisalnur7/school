<?php

namespace App\Services;

use App\Models\MobileAppFeature;
use App\Models\MobileSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MobileAppConfigurationService
{
    public function current(): array
    {
        $settings = MobileSetting::current();
        $features = MobileAppFeature::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (MobileAppFeature $feature) => $this->featurePayload($feature))
            ->values()
            ->all();

        return [
            'is_active' => (bool) $settings->is_active,
            'maintenance_message' => $settings->maintenance_message,
            'features' => $features,
        ];
    }

    public function feature(string $key): ?MobileAppFeature
    {
        return MobileAppFeature::where('key', $key)->first();
    }

    public function isActive(): bool
    {
        return (bool) MobileSetting::current()->is_active;
    }

    public function isFeatureEnabled(string $key): bool
    {
        $feature = $this->feature($key);

        return $feature?->is_enabled === true;
    }

    public function update(array $settings, array $features, int $actorId): void
    {
        DB::transaction(function () use ($settings, $features, $actorId): void {
            $mobileSettings = MobileSetting::current();
            $mobileSettings->fill([
                'is_active' => (bool) ($settings['is_active'] ?? $mobileSettings->is_active),
                'maintenance_message' => blank($settings['maintenance_message'] ?? null)
                    ? null
                    : trim((string) $settings['maintenance_message']),
                'updated_by' => $actorId,
            ])->save();

            $knownFeatures = MobileAppFeature::query()->get()->keyBy('key');
            $unknownKeys = collect($features)->pluck('key')->diff($knownFeatures->keys());
            if ($unknownKeys->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'features' => 'Unknown mobile app feature: ' . $unknownKeys->first(),
                ]);
            }

            foreach ($features as $featureInput) {
                $feature = $knownFeatures->get($featureInput['key']);
                $feature->fill(array_filter([
                    'label' => $featureInput['label'] ?? null,
                    'icon' => $featureInput['icon'] ?? null,
                    'is_enabled' => array_key_exists('is_enabled', $featureInput)
                        ? (bool) $featureInput['is_enabled']
                        : null,
                    'sort_order' => array_key_exists('sort_order', $featureInput)
                        ? (int) $featureInput['sort_order']
                        : null,
                    'updated_by' => $actorId,
                ], fn ($value) => $value !== null));

                if ($feature->key === 'dashboard') {
                    $feature->is_enabled = true;
                }

                $feature->save();
            }

            MobileAppFeature::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->each(function (MobileAppFeature $feature, int $index) use ($actorId): void {
                    $feature->forceFill([
                        'sort_order' => ($index + 1) * 10,
                        'updated_by' => $actorId,
                    ])->save();
                });
        });
    }

    private function featurePayload(MobileAppFeature $feature): array
    {
        return [
            'key' => $feature->key,
            'label' => $feature->label,
            'icon' => $feature->icon,
            'route' => $feature->flutter_route,
            'api_capability' => $feature->api_capability ?? [],
            'is_enabled' => $feature->key === 'dashboard' || (bool) $feature->is_enabled,
            'sort_order' => (int) $feature->sort_order,
        ];
    }
}
