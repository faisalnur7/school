<?php

namespace App\Http\Controllers;

class AssetsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-tags',           'title' => 'Categories',      'subtitle' => 'Manage asset categories',   'route' => 'asset-categories.index', 'permission' => 'view_card_asset_categories', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-boxes',          'title' => 'Assets List',     'subtitle' => 'View & manage assets',      'route' => 'assets.index', 'permission' => 'view_card_assets_list', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-shopping-cart',  'title' => 'Purchases',       'subtitle' => 'Manage asset purchases',    'route' => 'asset-purchases.index', 'permission' => 'view_card_asset_purchases', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-clipboard-list', 'title' => 'Issue Register',  'subtitle' => 'Track asset issues',        'route' => 'asset-issues.index', 'permission' => 'view_card_asset_issue_register', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-warehouse',      'title' => 'Asset Stock',     'subtitle' => 'View asset stock levels',   'route' => 'asset-issues.stock', 'permission' => 'view_card_asset_stock', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-calendar-check',  'title' => 'Facility Bookings','subtitle' => 'Manage facility rentals',  'route' => 'facilities.bookings.index', 'permission' => 'view_card_facility_bookings', 'from' => '#0d9488', 'to' => '#0f766e'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.assets.hub', compact('cards'));
    }
}
