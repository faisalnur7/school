<?php

namespace App\Http\Controllers;

class AssetsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-tags',           'title' => 'Categories',      'subtitle' => 'Manage asset categories',   'route' => 'asset-categories.index', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-boxes',          'title' => 'Assets List',     'subtitle' => 'View & manage assets',      'route' => 'assets.index',           'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-shopping-cart',  'title' => 'Purchases',       'subtitle' => 'Manage asset purchases',    'route' => 'asset-purchases.index',  'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-clipboard-list', 'title' => 'Issue Register',  'subtitle' => 'Track asset issues',        'route' => 'asset-issues.index',     'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-warehouse',      'title' => 'Asset Stock',     'subtitle' => 'View asset stock levels',   'route' => 'asset-issues.stock',     'from' => '#dc2626', 'to' => '#b91c1c'],
        ];

        return view('pages.assets.hub', compact('cards'));
    }
}
