<?php

namespace Tests\Feature;

use Tests\TestCase;

class DarkModeCoverageTest extends TestCase
{
    public function test_custom_authenticated_components_have_dark_mode_overrides(): void
    {
        $styles = file_get_contents(resource_path('css/admin.css'));

        foreach ([
            'permission-card-tile',
            'communications-page',
            'transactions-report',
            'product-form-page',
            'purchase-page',
            'certificate-editor-page',
            'yearly-template-page',
            'progress-template-preview',
            'transport-row-actions',
        ] as $component) {
            $this->assertMatchesRegularExpression(
                "/html\\[data-theme='dark'\\][^{]{0,300}\\.$component/",
                $styles,
                $component
            );
        }
    }
}
