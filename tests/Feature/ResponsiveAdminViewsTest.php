<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResponsiveAdminViewsTest extends TestCase
{
    public function test_legacy_admin_tables_have_mobile_horizontal_scrolling(): void
    {
        $views = [
            'account-groups/index.blade.php',
            'accounting-periods/index.blade.php',
            'accounts-list/index.blade.php',
            'assets/assets/index.blade.php',
            'budget-heads/index.blade.php',
            'events/index.blade.php',
            'notices/index.blade.php',
            'professions/index.blade.php',
        ];

        foreach ($views as $view) {
            $source = file_get_contents(resource_path('views/pages/'.$view));

            $this->assertStringContainsString(
                'table-responsive',
                $source,
                $view.' should wrap its table for mobile scrolling.'
            );
        }
    }

    public function test_legacy_table_action_rows_can_wrap_on_small_screens(): void
    {
        foreach (['events/index.blade.php', 'notices/index.blade.php'] as $view) {
            $source = file_get_contents(resource_path('views/pages/'.$view));

            $this->assertStringContainsString(
                '@media (max-width: 767.98px)',
                $source,
                $view.' should define a small-screen layout.'
            );
        }
    }
}
