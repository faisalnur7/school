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

    public function test_mobile_selection_rows_show_due_metadata_once_as_a_date(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString("const rightMeta = '';", $source);
        $this->assertStringContainsString(
            ".replace(/\\s+\\d{2}:\\d{2}(?::\\d{2})?$/, '')",
            $source
        );
        $this->assertStringContainsString('padding: 0.55rem 0.7rem;', $source);
        $this->assertStringContainsString('display: flex;', $source);
        $this->assertStringContainsString('flex-direction: column !important;', $source);
    }

    public function test_mobile_checkout_content_uses_the_full_available_width(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.mobile-checkout-sheet > .card {', $source);
        $this->assertStringContainsString('.fee-collect-page .mobile-checkout-sheet #cartItems', $source);
        $this->assertStringContainsString('max-width: none !important;', $source);
        $this->assertStringContainsString('width: 100% !important;', $source);
    }

    public function test_mobile_cart_rows_are_full_width(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.mobile-checkout-sheet #cartItems .cart-row {',
            $source
        );
        $this->assertStringContainsString('max-width: none !important;', $source);
    }

    public function test_mobile_cart_rows_hide_the_duplicate_bottom_total(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.mobile-checkout-sheet #cartItems .cart-row .cart-line-total {',
            $source
        );
        $this->assertStringContainsString('display: none !important;', $source);
    }

    public function test_mobile_inventory_rows_use_a_compact_two_row_layout(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('cart-row cart-row--inventory', $source);
        $this->assertStringContainsString('.cart-row--inventory .cart-row-main', $source);
        $this->assertStringContainsString('.cart-row--inventory .cart-control-group--paid', $source);
        $this->assertStringContainsString('display: flex !important;', $source);
        $this->assertStringContainsString('flex-wrap: nowrap;', $source);
    }

    public function test_mobile_inventory_rows_match_the_compact_green_item_card_design(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.mobile-checkout-sheet #cartItems \.cart-row--inventory \{[^}]*background: #f2fbf5 !important;[^}]*border: 1px solid #cfe7d8 !important;[^}]*border-radius: 12px !important;[^}]*padding: 8px 10px !important;/s',
            $source
        );
        $this->assertStringContainsString('gap: 8px;', $source);
        $this->assertStringContainsString('height: 30px;', $source);
        $this->assertStringContainsString('border-radius: 8px;', $source);
    }

    public function test_mobile_inventory_rows_match_the_dark_reference_theme(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/html\[data-theme=\x27dark\x27\] \.fee-collect-page \.mobile-checkout-sheet #cartItems \.cart-row--inventory \{[^}]*background: #14261d !important;[^}]*border-color: #25402f !important;/s',
            $source
        );
        $this->assertStringContainsString('background: #0f1a14 !important;', $source);
        $this->assertStringContainsString('color: #eef2f8 !important;', $source);
    }

    public function test_mobile_inventory_quantity_stepper_uses_balanced_cells(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.mobile-checkout-sheet #cartItems \.cart-row--inventory \.cart-qty-stepper-input \{[^}]*width: 28px !important;/s',
            $source
        );
    }

    public function test_inventory_rows_use_the_compact_card_layout_on_desktop_and_tablet(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.cart-row--inventory \{[^}]*display: flex !important;[^}]*flex-direction: column;[^}]*gap: 8px;[^}]*position: relative;/s',
            $source
        );
        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.cart-row--inventory \.cart-row-controls \{[^}]*display: flex !important;[^}]*flex-wrap: nowrap;[^}]*gap: 8px;/s',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .cart-row--inventory .cart-line-subtitle {',
            $source
        );
    }

    public function test_cart_remove_controls_are_borderless_and_shadowless(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.cart-row \.remove-btn,\s+\.fee-collect-page \.cart-row-remove \{[^}]*border: 0 !important;[^}]*box-shadow: none !important;[^}]*background: transparent !important;/s',
            $source
        );
    }

    public function test_mobile_inventory_rows_hide_the_unit_subtitle(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.cart-row--inventory .cart-line-subtitle {',
            $source
        );
        $this->assertStringContainsString('display: none !important;', $source);
    }

    public function test_transformed_date_inputs_do_not_duplicate_the_original_id(): void
    {
        $source = file_get_contents(resource_path('views/layouts/master.blade.php'));

        $this->assertStringContainsString("input.removeAttribute('id');", $source);
    }

    public function test_collapsed_mobile_checkout_hides_the_entire_payment_footer(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet .card-footer {' . PHP_EOL . '                display: none !important;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open .card-footer {' . PHP_EOL . '                position: static;',
            $source
        );
        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.mobile-checkout-sheet\.is-open \.card-footer \{[^}]*flex: 0 0 auto;[^}]*overflow: visible;/s',
            $source
        );
    }

    public function test_open_mobile_checkout_owns_the_scroll_and_keeps_summary_static(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open {' . PHP_EOL . '                transform: translateY(0);' . PHP_EOL . '                max-height: 90dvh;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open .card-header {' . PHP_EOL . '                position: sticky;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open .card-body {' . PHP_EOL . '                display: flex !important;' . PHP_EOL . '                flex: 1 1 auto;',
            $source
        );
    }

    public function test_open_mobile_checkout_gives_cart_items_the_flexible_height(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open .card-body {' . PHP_EOL .
            '                display: flex !important;' . PHP_EOL .
            '                flex: 1 1 auto;' . PHP_EOL .
            '                min-height: 0;' . PHP_EOL .
            '                max-height: none;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open #feeForm {' . PHP_EOL .
            '                flex: 1 1 auto;' . PHP_EOL .
            '                min-height: 0;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet.is-open .card-body .scroll-area {' . PHP_EOL .
            '                flex: 1 1 auto;' . PHP_EOL .
            '                min-height: 0;' . PHP_EOL .
            '                max-height: none !important;' . PHP_EOL .
            '                overflow-y: auto;',
            $source
        );
        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.mobile-checkout-sheet\.is-open \.card-footer \{[^}]*flex: 0 0 auto;/s',
            $source
        );
    }

    public function test_mobile_checkout_keeps_only_the_footer_collect_button(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertSame(0, substr_count($source, 'id="collectBtnMobile"'));
        $this->assertSame(1, substr_count($source, 'id="collectBtn"'));
    }

    public function test_mobile_checkout_summary_matches_the_compact_cart_sheet(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('class="mobile-cart-handle"', $source);
        $this->assertStringContainsString('data-collapsed-label="CART"', $source);
        $this->assertStringContainsString('class="mobile-cart-chevron"', $source);
        $this->assertStringContainsString('Collect payment', $source);
    }

    public function test_mobile_checkout_has_state_aware_total_due_and_item_badges(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('id="mobileCartEyebrow"', $source);
        $this->assertStringContainsString('id="mobileCartBadge"', $source);
        $this->assertStringContainsString('data-collapsed-label="CART"', $source);
        $this->assertStringContainsString('data-expanded-label="TOTAL DUE"', $source);
        $this->assertStringContainsString('.mobile-checkout-sheet.is-open .mobile-cart-badge', $source);
    }

    public function test_mobile_cart_chevron_is_a_clean_circular_button(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.mobile-checkout-sheet \.mobile-cart-chevron \{[^}]*width: 34px;[^}]*height: 34px;[^}]*border: 1px solid #dbe4ee !important;[^}]*background: #fff !important;[^}]*box-shadow: none !important;/s',
            $source
        );
        $this->assertStringContainsString('justify-content: center;', $source);
    }

    public function test_mobile_checkout_slider_has_bounded_expanded_height_and_fixed_cta(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            'max-height: 90dvh;',
            $source
        );
        $this->assertStringContainsString('.mobile-checkout-sheet.is-open .card-footer .collect-btn', $source);
        $this->assertStringContainsString('position: sticky;', $source);
        $this->assertStringContainsString('bottom: 0;', $source);
    }

    public function test_collapsed_mobile_slider_hides_the_form_below_the_summary(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .mobile-checkout-sheet:not(.is-open) #feeForm {',
            $source
        );
        $this->assertStringContainsString('display: none !important;', $source);
        $this->assertStringContainsString('env(safe-area-inset-bottom)', $source);
    }

    public function test_mobile_checkout_uses_compact_spacing_for_all_payment_sections(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('padding: 0.45rem 0.75rem 0.55rem;', $source);
        $this->assertStringContainsString('.mobile-checkout-sheet.is-open #feeForm .card-footer > .mb-3', $source);
        $this->assertStringContainsString('margin-bottom: 0.45rem !important;', $source);
        $this->assertStringContainsString('min-height: 46px;', $source);
    }

    public function test_mobile_discount_summary_has_dedicated_compact_components(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('mobile-subtotal-row', $source);
        $this->assertStringContainsString('mobile-discount-card', $source);
        $this->assertStringContainsString('.mobile-discount-card .discount-type-btn.active', $source);
    }

    public function test_mobile_checkout_header_uses_the_requested_compact_spacing(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            'style="border-color:#f1f5f9!important"',
            $source
        );
        $this->assertStringContainsString(
            'class="mobile-only mobile-cart-toggle-wrap mt-3"',
            $source
        );
        $this->assertStringContainsString(
            'style="padding: 0 12px !important;"',
            $source
        );
    }

    public function test_discount_controls_use_a_single_compact_horizontal_row(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('mobile-discount-head', $source);
        $this->assertStringContainsString('mobile-discount-controls', $source);
        $this->assertStringContainsString('.mobile-discount-controls #discountInput', $source);
    }

    public function test_discount_card_uses_the_compact_layout_on_desktop_and_tablet(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.discount-row \{[^}]*display: grid;[^}]*grid-template-columns: 1fr auto;[^}]*row-gap: 0\.35rem;/s',
            $source
        );
        $this->assertStringContainsString('.fee-collect-page .discount-row .mobile-discount-head {', $source);
        $this->assertStringContainsString('display: contents !important;', $source);
        $this->assertStringContainsString('.fee-collect-page .discount-row > .d-flex:last-child {', $source);
    }

    public function test_mobile_discount_card_aligns_applied_amount_under_the_controls(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('grid-template-columns: 1fr auto;', $source);
        $this->assertStringContainsString('.mobile-discount-card > .d-flex:last-child', $source);
        $this->assertStringContainsString('justify-content: space-between;', $source);
    }

    public function test_mobile_payment_summary_uses_compact_side_by_side_fields(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .mobile-checkout-sheet.is-open .payment-meta-row {', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $source);
        $this->assertStringContainsString('.fee-collect-page .mobile-checkout-sheet.is-open .mobile-payment-amount #paymentAmount {', $source);
        $this->assertStringContainsString('min-height: 34px !important;', $source);
        $this->assertStringContainsString('.fee-collect-page .mobile-checkout-sheet.is-open .mobile-discount-card .discount-amount-line {', $source);
        $this->assertStringContainsString('display: none !important;', $source);
    }

    public function test_payment_amount_and_date_share_a_row_on_desktop_and_tablet(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('<div class="payment-meta-row">', $source);
        $this->assertStringContainsString('.fee-collect-page .payment-meta-row {', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $source);
        $this->assertStringContainsString(
            '.fee-collect-page .payment-meta-row .mobile-payment-amount,' . PHP_EOL .
            '        .fee-collect-page .payment-meta-row > .mb-3 {',
            $source
        );
    }

    public function test_payment_amount_does_not_show_the_redundant_helper_sentence(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringNotContainsString(
            'Auto-calculated from the selected fee and inventory line payments.',
            $source
        );
    }

    public function test_category_panel_headers_keep_horizontal_padding(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .category-panel-card > .card-header {', $source);
        $this->assertStringContainsString('padding: 0.75rem 1rem !important;', $source);
        $this->assertStringContainsString('category-panel-card', $source);
        $this->assertStringNotContainsString('padding: 0 10px !important;margin: 0 !important;', $source);
    }

    public function test_mobile_payment_summary_has_prominent_total_and_payment_field(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('mobile-total-due-row', $source);
        $this->assertStringContainsString('mobile-payment-amount', $source);
        $this->assertStringContainsString('.mobile-total-due-row #totalAmount', $source);
    }

    public function test_mobile_discount_tabs_and_payment_controls_are_compact(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('height: 36px;', $source);
        $this->assertStringContainsString('min-height: 34px;', $source);
        $this->assertStringContainsString('font-size: 0.72rem;', $source);
        $this->assertStringContainsString('font-size: 1.8rem !important;', $source);
        $this->assertStringContainsString('min-height: 48px;', $source);
        $this->assertStringContainsString('role="tablist"', $source);
        $this->assertStringContainsString('role="tab" aria-selected="true"', $source);
    }

    public function test_mobile_checkout_collapsed_sheet_is_compact(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('--mobile-collapsed-sheet-height: 80px;', $source);
    }

    public function test_open_mobile_checkout_sheet_uses_ninety_percent_of_viewport(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('height: 90dvh;', $source);
        $this->assertStringContainsString('max-height: 90dvh;', $source);
    }

    public function test_mobile_checkout_has_a_compact_empty_cart_and_payment_summary(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('mobile-empty-cart', $source);
        $this->assertStringContainsString('max-height: none !important;', $source);
        $this->assertStringContainsString('PAYMENT SUMMARY', $source);
    }

    public function test_mobile_checkout_has_theme_tokens_for_light_and_dark_modes(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('--mobile-sheet-surface:', $source);
        $this->assertStringContainsString('--mobile-sheet-primary:', $source);
        $this->assertStringContainsString('html[data-theme=\'dark\'] .fee-collect-page .mobile-checkout-sheet', $source);
        $this->assertStringContainsString('background: var(--mobile-sheet-surface);', $source);
    }

    public function test_mobile_checkout_action_and_collapse_control_have_clear_accessibility_states(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('aria-label="Expand payment summary"', $source);
        $this->assertStringContainsString('background: #e2e8f0 !important;', $source);
        $this->assertStringContainsString('color: #475569 !important;', $source);
    }

    public function test_mobile_fee_selection_chips_render_as_a_vertical_list(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .mobile-chip-scroll {', $source);
        $this->assertStringContainsString('flex-direction: column;', $source);
        $this->assertStringContainsString('overflow-x: visible;', $source);
        $this->assertStringContainsString('.fee-collect-page .mobile-chip-scroll .mobile-select-chip {', $source);
        $this->assertStringContainsString('width: 100%;', $source);
        $this->assertStringContainsString("@media (max-width: 991.98px) {\n            .fee-collect-page .mobile-chip-scroll {", $source);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $source);
    }

    public function test_mobile_selection_items_are_stacked(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.mobile-selection-modal #mobileSelectionList {', $source);
        $this->assertStringContainsString('flex-direction: column !important;', $source);
        $this->assertStringContainsString('.mobile-selection-modal .mobile-selection-row {', $source);
        $this->assertStringContainsString('max-width: 100%;', $source);
        $this->assertStringContainsString("@media (max-width: 575.98px) {\n            .fee-collect-page .mobile-selection-modal #mobileSelectionList {", $source);
    }

    public function test_mobile_selection_modal_matches_the_compact_item_picker_structure(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('id="mobileSelectionCategory"', $source);
        $this->assertStringContainsString('class="selection-count"', $source);
        $this->assertStringContainsString('class="selection-total"', $source);
        $this->assertStringContainsString('.mobile-selection-modal .mobile-selection-mark.is-selected', $source);
        $this->assertStringContainsString('html[data-theme=\'dark\'] .fee-collect-page .mobile-selection-modal .modal-content', $source);
        $this->assertStringContainsString('.mobile-selection-modal .mobile-selection-footer {', $source);
    }

    public function test_results_hub_tools_follow_the_academic_workflow_sequence(): void
    {
        $controller = file_get_contents(base_path('app/Http/Controllers/ResultsHubController.php'));
        $switcher = file_get_contents(resource_path('views/components/hub-switcher.blade.php'));
        $sequence = ['All Exams', 'Exam Routine', 'Admit and Seat Cards', 'Tutorial Exam Report', 'Result Sheets', 'Terminal Report', 'Yearly Final Report'];

        $controllerPositions = array_map(fn ($label) => strpos($controller, "'title' => __('{$label}')"), $sequence);
        $switcherPositions = array_map(fn ($label) => strpos($switcher, "'label' => __('{$label}')"), $sequence);

        $this->assertNotContains(false, $controllerPositions);
        $this->assertNotContains(false, $switcherPositions);
        $sortedControllerPositions = $controllerPositions;
        $sortedSwitcherPositions = $switcherPositions;
        sort($sortedControllerPositions);
        sort($sortedSwitcherPositions);
        $this->assertSame($sortedControllerPositions, $controllerPositions);
        $this->assertSame($sortedSwitcherPositions, $switcherPositions);
    }

    public function test_module_navigation_has_an_accessible_topbar_collapse_control(): void
    {
        $topNav = file_get_contents(resource_path('views/layouts/partials/_top-nav.blade.php'));
        $switcher = file_get_contents(resource_path('views/components/hub-switcher.blade.php'));
        $master = file_get_contents(resource_path('views/layouts/master.blade.php'));

        $this->assertStringContainsString('data-hub-switcher-toggle', $topNav);
        $this->assertStringContainsString('aria-controls="hubSwitcher"', $topNav);
        $this->assertStringContainsString('aria-expanded="true"', $topNav);
        $this->assertStringContainsString("onclick=\"event.preventDefault(); var hidden = document.body.classList.toggle('hub-switcher-hidden');", $topNav);
        $this->assertStringContainsString("window.localStorage.setItem('school-hub-switcher-visible'", $topNav);
        $this->assertStringContainsString('document.body.classList.toggle', $master);
        $this->assertStringContainsString('hub-switcher-shell', $switcher);
        $this->assertStringContainsString('id="hubSwitcherShell"', $switcher);
        $this->assertStringContainsString('id="hubSwitcher"', $switcher);
        $this->assertStringNotContainsString('class="hub-switcher__toggle"', $switcher);
        $this->assertStringContainsString('hub-switcher-hidden', $switcher);
        $this->assertStringContainsString('max-height: 0;', $switcher);
        $this->assertStringContainsString('min-height:', $switcher);
        $this->assertStringContainsString('transition:', $switcher);
        $this->assertStringContainsString('data-hub-switcher-toggle', $master);
        $this->assertStringContainsString('aria-expanded', $master);
    }

    public function test_module_navigation_sits_against_the_global_topbar(): void
    {
        $switcher = file_get_contents(resource_path('views/components/hub-switcher.blade.php'));

        $this->assertStringContainsString('margin: 0 1rem 0;', $switcher);
        $this->assertStringContainsString('margin: 0 .65rem 0;', $switcher);
    }

    public function test_hub_card_browser_has_a_responsive_card_list_view(): void
    {
        $source = file_get_contents(resource_path('views/components/hub-card-browser.blade.php'));

        $this->assertStringContainsString("'card-list' => ['label' => __('Card List')", $source);
        $this->assertStringContainsString("'card-list'", $source);
        $this->assertStringContainsString('.hub-browser[data-view="card-list"] .hub-browser__grid', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(6, minmax(0, 1fr));', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(1, minmax(0, 1fr));', $source);
        $this->assertStringContainsString('@media (min-width: 576px) and (max-width: 1023.98px)', $source);
        $this->assertStringContainsString('@media (min-width: 1024px) and (max-width: 1439.98px)', $source);
    }

    public function test_card_list_is_the_default_hub_view(): void
    {
        $component = file_get_contents(resource_path('views/components/hub-card-browser.blade.php'));

        $this->assertStringContainsString("'defaultView' => 'card-list'", $component);
        $this->assertStringContainsString("'hub-view-mode-v2:' + baseKey", $component);

        foreach (glob(resource_path('views/pages/*/hub.blade.php')) as $hubView) {
            $source = file_get_contents($hubView);
            $this->assertStringContainsString('default-view="card-list"', $source, basename(dirname($hubView)));
        }

        $cmsHub = file_get_contents(resource_path('views/pages/website/cms/hub.blade.php'));
        $this->assertStringContainsString('default-view="card-list"', $cmsHub);
    }

    public function test_hub_view_modes_follow_the_standard_sequence(): void
    {
        $source = file_get_contents(resource_path('views/components/hub-card-browser.blade.php'));
        $modes = ["'card-list' =>", "'small' =>", "'medium' =>", "'large' =>"];
        $positions = array_map(fn ($mode) => strpos($source, $mode), $modes);

        $this->assertNotContains(false, $positions);
        $sortedPositions = $positions;
        sort($sortedPositions);
        $this->assertSame($sortedPositions, $positions);
        $this->assertStringNotContainsString("'list' =>", $source);
        $this->assertStringNotContainsString("'list'];", $source);
    }

    public function test_mobile_hub_view_switcher_is_icon_only_with_accessible_labels(): void
    {
        $source = file_get_contents(resource_path('views/components/hub-card-browser.blade.php'));

        $this->assertStringContainsString('.hub-browser__switcher-label {', $source);
        $this->assertStringContainsString('display: none;', $source);
        $this->assertStringContainsString('.hub-browser__button span {', $source);
        $this->assertStringContainsString('title="{{ $config[\'label\'] }}"', $source);
    }

    public function test_mobile_student_header_uses_compact_white_arrangement(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .mobile-topbar {', $source);
        $this->assertStringContainsString('background: #ffffff !important;', $source);
        $this->assertStringContainsString('.fee-collect-page .mobile-meta-row {', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(0, 1fr));', $source);
    }

    public function test_mobile_dark_theme_has_contrasting_surfaces(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-topbar", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-select-chip", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page #mainTabs", $source);
        $this->assertStringContainsString('color: #e2e8f0 !important;', $source);
    }

    public function test_mobile_dark_checkout_has_readable_payment_controls(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-checkout-sheet.is-open .mobile-payment-amount label", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-checkout-sheet.is-open .mobile-total-due-row > div > span:first-child", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-checkout-sheet .collect-btn", $source);
    }

    public function test_dark_theme_covers_selection_cart_and_history_surfaces(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-selection-modal .modal-header", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .mobile-selection-modal .mobile-selection-row", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .cart-line-title", $source);
        $this->assertStringContainsString("html[data-theme='dark'] .fee-collect-page .payment-history-table .payment-history-items .badge", $source);
    }

    public function test_theme_switching_uses_a_fade_with_reduced_motion_fallback(): void
    {
        $script = file_get_contents(resource_path('js/app.js'));
        $adminStyles = file_get_contents(resource_path('css/admin.css'));
        $appStyles = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('theme-fading', $script);
        $this->assertStringNotContainsString('theme-screen-wipe', $script);
        $this->assertStringNotContainsString('THEME_BUBBLE_COUNT', $script);
        $this->assertStringContainsString('THEME_FADE_DURATION = 260', $script);
        $this->assertStringContainsString('requestAnimationFrame', $script);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $script);

        foreach ([$adminStyles, $appStyles] as $styles) {
            $this->assertStringContainsString('theme-fading', $styles);
            $this->assertStringContainsString('transition: background-color 0.26s ease', $styles);
            $this->assertStringContainsString('prefers-reduced-motion: reduce', $styles);
        }
    }

    public function test_theme_toggle_is_icon_only_but_remains_accessible(): void
    {
        $toggle = file_get_contents(resource_path('views/layouts/partials/_theme-toggle.blade.php'));
        $styles = file_get_contents(resource_path('css/admin.css'));

        $this->assertStringContainsString('data-theme-toggle', $toggle);
        $this->assertStringContainsString('aria-label="Switch to dark mode"', $toggle);
        $this->assertStringNotContainsString('theme-toggle__label', $toggle);
        $this->assertStringContainsString('.theme-toggle__label {', $styles);
        $this->assertStringContainsString('display: none;', $styles);
        $this->assertStringContainsString('border-radius: 50%;', $styles);
        $this->assertStringContainsString('height: 38px;', $styles);
    }

    public function test_assigned_fees_mobile_layout_reduces_vertical_scrolling(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('assigned-fees-actions', $source);
        $this->assertStringContainsString('assigned-fees-bulk-actions', $source);
        $this->assertStringContainsString('assigned-fees-save', $source);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $source);
        $this->assertStringContainsString('position: sticky;', $source);
        $this->assertStringContainsString('Paid fees stay locked. Inactive fees are excluded from collection.', $source);
    }

    public function test_assigned_fees_mobile_actions_use_the_full_available_width(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('margin-left: 0 !important;', $source);
        $this->assertStringContainsString('.fee-collect-page .assigned-fees-actions {', $source);
        $this->assertStringContainsString('width: 100%;', $source);
        $this->assertStringContainsString('padding-left: 0 !important;', $source);
        $this->assertStringContainsString('padding-right: 0 !important;', $source);
    }

    public function test_assigned_fees_desktop_and_tablet_actions_stay_in_one_row(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.assigned-fees-actions \{[^}]*display: flex;[^}]*flex-wrap: nowrap;[^}]*align-items: center;/s',
            $source
        );
        $this->assertMatchesRegularExpression(
            '/\.fee-collect-page \.assigned-fees-bulk-actions \{[^}]*display: flex;[^}]*flex-wrap: nowrap;/s',
            $source
        );
    }

    public function test_assigned_fees_mobile_actions_share_one_button_size(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .assigned-fees-actions > .btn,', $source);
        $this->assertStringContainsString('.fee-collect-page .assigned-fees-bulk-actions > .btn {', $source);
        $this->assertStringContainsString('height: 42px;', $source);
        $this->assertStringContainsString('padding: 0.55rem 0.75rem !important;', $source);
        $this->assertStringContainsString('line-height: 1;', $source);
    }

    public function test_assigned_fees_mobile_actions_have_no_extra_top_margin(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .assigned-fees-actions {' . PHP_EOL .
            '                display: grid;' . PHP_EOL .
            '                grid-template-columns: 1fr;' . PHP_EOL .
            '                gap: 0.5rem;' . PHP_EOL .
            '                margin-top: 0 !important;',
            $source
        );
        $this->assertStringContainsString('margin-top: 0 !important;', $source);
    }

    public function test_assigned_fees_mobile_header_copy_does_not_shrink_to_character_width(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .assigned-fees-toolbar {' . PHP_EOL .
            '                width: 100%;' . PHP_EOL .
            '                display: block !important;',
            $source
        );
        $this->assertStringContainsString(
            '.fee-collect-page .assigned-fees-toolbar > .flex-grow-1 {' . PHP_EOL .
            '                width: 100%;',
            $source
        );
    }

    public function test_assigned_fees_bulk_buttons_have_no_top_margin(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .assigned-fees-bulk-actions > .btn {' . PHP_EOL .
            '                margin-top: 0 !important;',
            $source
        );
    }

    public function test_assigned_fees_mobile_content_has_no_horizontal_inset(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .assigned-fees-summary,', $source);
        $this->assertStringContainsString('.fee-collect-page .assigned-fees-table-shell {', $source);
        $this->assertStringContainsString('margin-left: 0 !important;', $source);
        $this->assertStringContainsString('margin-right: 0 !important;', $source);
        $this->assertStringContainsString('padding-left: 0 !important;', $source);
        $this->assertStringContainsString('padding-right: 0 !important;', $source);
    }

    public function test_assigned_fees_table_uses_compact_mobile_columns(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('table-layout: auto;', $source);
        $this->assertStringContainsString('font-size: 0.68rem;', $source);
        $this->assertStringContainsString('padding: 0.45rem 0.35rem !important;', $source);
        $this->assertStringContainsString('.assigned-fees-table th:nth-child(2)', $source);
        $this->assertStringContainsString('width: auto !important;', $source);
    }

    public function test_assigned_fees_table_hides_number_column_on_mobile(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString(
            '.fee-collect-page .assigned-fees-table th:first-child,' . PHP_EOL .
            '            .fee-collect-page .assigned-fees-table td:first-child {' . PHP_EOL .
            '                display: none !important;',
            $source
        );
    }

    public function test_assigned_fees_mobile_table_prioritizes_contained_columns(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page .assigned-fees-table th:nth-child(4),', $source);
        $this->assertStringContainsString('display: none !important;', $source);
    }

    public function test_assigned_fees_mobile_columns_use_content_sized_layout(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('table-layout: auto;', $source);
        $this->assertStringContainsString('width: max-content;', $source);
        $this->assertStringContainsString('min-width: 100%;', $source);
        $this->assertStringContainsString('width: auto !important;', $source);
    }

    public function test_assigned_fees_mobile_edit_action_is_a_compact_icon_button(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('assigned-fee-edit-label', $source);
        $this->assertStringContainsString('.assigned-fees-table .js-edit-assigned-fee {', $source);
        $this->assertStringContainsString('width: 32px;', $source);
        $this->assertStringContainsString('height: 32px;', $source);
        $this->assertStringContainsString('border-radius: 8px !important;', $source);
    }

    public function test_assigned_fees_table_is_responsive_only_on_mobile(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('assigned-fees-table-responsive', $source);
        $this->assertStringContainsString('overflow: visible;', $source);
        $this->assertStringContainsString('overflow-x: auto;', $source);
    }

    public function test_assigned_fees_mobile_table_keeps_all_columns_visible(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('th:nth-child(4)', $source);
        $this->assertStringContainsString('th:nth-child(5)', $source);
        $this->assertStringContainsString('th:nth-child(6)', $source);
        $this->assertStringContainsString('th:nth-child(7)', $source);
        $this->assertStringContainsString('display: table-cell !important;', $source);
    }

    public function test_payment_history_table_is_content_sized_and_scrollable_on_mobile(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('payment-history-table-responsive', $source);
        $this->assertStringContainsString('overflow-x: auto;', $source);
        $this->assertStringContainsString('.payment-history-table {', $source);
        $this->assertStringContainsString('table-layout: auto;', $source);
        $this->assertStringContainsString('width: max-content;', $source);
    }

    public function test_payment_history_header_does_not_render_totals_summary(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringNotContainsString('class="ms-auto history-summary"', $source);
    }

    public function test_assigned_fee_category_tabs_are_transparent_and_adjacent_on_mobile(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('background: #f1f5f9;', $source);
        $this->assertStringContainsString('background: #ffffff !important;', $source);
        $this->assertStringContainsString('gap: 0.25rem !important;', $source);
        $this->assertStringContainsString('border-radius: 8px !important;', $source);
    }

    public function test_main_mobile_tabs_use_the_same_segmented_control_design(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('.fee-collect-page #mainTabs {', $source);
        $this->assertStringContainsString('background: #f1f5f9;', $source);
        $this->assertStringContainsString('#mainTabs .nav-link.active', $source);
        $this->assertStringContainsString('background: #ffffff !important;', $source);
        $this->assertStringContainsString('gap: 0.25rem !important;', $source);
    }

    public function test_main_tabs_use_compact_labels(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('💳 Collect', $source);
        $this->assertStringContainsString('🧾 Fees', $source);
        $this->assertStringContainsString('🧾 History', $source);
        $this->assertStringNotContainsString('💳 Collect Payment', $source);
        $this->assertStringNotContainsString('🧾 Assigned Fees', $source);
        $this->assertStringNotContainsString('🧾 Payment History', $source);
    }

    public function test_assigned_fee_category_names_truncate_with_ellipsis(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString('text-overflow: ellipsis;', $source);
        $this->assertStringContainsString('overflow: hidden;', $source);
        $this->assertStringContainsString('.assigned-fees-tabs .nav-link > span:first-child', $source);
    }

    public function test_assigned_fee_category_labels_use_compact_first_words(): void
    {
        $source = file_get_contents(resource_path('views/pages/fees/collect_fee.blade.php'));

        $this->assertStringContainsString("preg_split('/\\s+/', trim(\$group['label']))", $source);
        $this->assertStringContainsString("mb_substr(\$categoryLabel, 0, 10) . '..'", $source);
        $this->assertStringContainsString('font-size: 0.64rem;', $source);
    }
}
