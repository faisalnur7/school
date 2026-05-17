<?php

namespace App\Http\Controllers;

class ShareholdersHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-list',       'title' => 'All Shareholders', 'subtitle' => 'View all shareholders',  'route' => 'shareholders.index', 'permission' => 'view_card_all_shareholders', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-user-plus',  'title' => 'Add Shareholder',  'subtitle' => 'Add a new shareholder',  'route' => 'shareholders.create', 'permission' => 'view_card_add_shareholder', 'from' => '#0891b2', 'to' => '#0e7490'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.shareholders.hub', compact('cards'));
    }
}
