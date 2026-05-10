<?php

namespace App\Http\Controllers;

class ShareholdersHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-list',       'title' => 'All Shareholders', 'subtitle' => 'View all shareholders',  'route' => 'shareholders.index',  'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-user-plus',  'title' => 'Add Shareholder',  'subtitle' => 'Add a new shareholder',  'route' => 'shareholders.create', 'from' => '#0891b2', 'to' => '#0e7490'],
        ];

        return view('pages.shareholders.hub', compact('cards'));
    }
}
