<?php

namespace App\Http\Controllers;

class LocationHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-map',            'title' => __('Division'),        'subtitle' => __('Manage divisions'),        'route' => 'division.index', 'permission' => 'view_card_divisions', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-map-marked-alt', 'title' => __('District'),        'subtitle' => __('Manage districts'),        'route' => 'district.index', 'permission' => 'view_card_districts', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-shield-alt',     'title' => __('Police Station'),  'subtitle' => __('Manage police stations'),  'route' => 'police-station.index', 'permission' => 'view_card_police_stations', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-mail-bulk',      'title' => __('Post Office'),     'subtitle' => __('Manage post offices'),     'route' => 'post-office.index', 'permission' => 'view_card_post_offices', 'from' => '#d97706', 'to' => '#b45309'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.location.hub', compact('cards'));
    }
}
