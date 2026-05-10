<?php

namespace App\Http\Controllers;

class LocationHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-map',            'title' => 'Division',        'subtitle' => 'Manage divisions',        'route' => 'division.index',       'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-map-marked-alt', 'title' => 'District',        'subtitle' => 'Manage districts',        'route' => 'district.index',       'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-shield-alt',     'title' => 'Police Station',  'subtitle' => 'Manage police stations',  'route' => 'police-station.index', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-mail-bulk',      'title' => 'Post Office',     'subtitle' => 'Manage post offices',     'route' => 'post-office.index',    'from' => '#d97706', 'to' => '#b45309'],
        ];

        return view('pages.location.hub', compact('cards'));
    }
}
