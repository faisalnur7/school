<?php

namespace App\Http\Controllers;

class InventoryController extends Controller
{
    public function hub()
    {
        abort_if(!auth()->user()?->hasAnyPermission([
            'view_inventory',
            'manage_inventory_categories',
            'manage_inventory_products',
            'manage_inventory_suppliers',
            'manage_inventory_purchases',
            'view_inventory_reports',
        ]), 403);

        $cards = [
            ['icon' => 'fa-tags',          'title' => 'Categories',        'subtitle' => 'Manage inventory categories', 'route' => 'inventory.categories.index', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-boxes',         'title' => 'Products',          'subtitle' => 'Manage products & stock',     'route' => 'inventory.products.index',   'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-truck',         'title' => 'Suppliers',         'subtitle' => 'Manage suppliers',            'route' => 'inventory.suppliers.index',  'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-shopping-cart', 'title' => 'Purchases',         'subtitle' => 'Record new purchases',        'route' => 'inventory.purchases.index',  'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-chart-bar',     'title' => 'Stock Report',      'subtitle' => 'View stock summary',          'route' => 'inventory.reports.stock',    'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-exclamation',   'title' => 'Low Stock Products','subtitle' => 'Items below minimum stock',   'route' => 'inventory.reports.lowStock', 'from' => '#7c3aed', 'to' => '#6d28d9'],
        ];

        return view('pages.inventory.hub', compact('cards'));
    }
}

