<?php

namespace App\Http\Controllers;

class InstituteHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-school',     'title' => 'School Settings',    'subtitle' => 'General school settings',    'route' => 'school-settings.index', 'permission' => 'view_card_school_settings', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            // ['icon' => 'fa-id-card',    'title' => 'ID Card Templates',  'subtitle' => 'Manage ID card templates',   'route' => 'id-card-templates.index', 'permission' => 'view_card_id_card_templates', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-building',   'title' => 'Buildings',          'subtitle' => 'Manage school buildings',    'route' => 'buildings.index', 'permission' => 'view_card_buildings', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-door-open',  'title' => 'Rooms',              'subtitle' => 'Manage rooms',               'route' => 'rooms.index', 'permission' => 'view_card_rooms', 'from' => '#d97706', 'to' => '#b45309'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.institute.hub', compact('cards'));
    }
}
