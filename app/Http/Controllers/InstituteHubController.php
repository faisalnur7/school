<?php

namespace App\Http\Controllers;

class InstituteHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-school',     'title' => 'School Settings',    'subtitle' => 'General school settings',    'route' => 'school-settings.index', 'permission' => 'view_card_school_settings', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-certificate','title' => 'Certificate Types',   'subtitle' => 'Manage certificate types and templates', 'route' => 'certificates.index', 'permission' => 'view_card_school_settings', 'from' => '#0f766e', 'to' => '#0d9488'],
            ['icon' => 'fa-signature',  'title' => 'Principal Signature', 'subtitle' => 'Edit principal designation and signature details', 'route' => 'school-settings.index', 'url' => route('school-settings.index') . '#principal-signature', 'permission' => 'view_card_school_settings', 'from' => '#be185d', 'to' => '#db2777'],
            // ['icon' => 'fa-id-card',    'title' => 'ID Card Templates',  'subtitle' => 'Manage ID card templates',   'route' => 'id-card-templates.index', 'permission' => 'view_card_id_card_templates', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-building',   'title' => 'Buildings',          'subtitle' => 'Manage school buildings',    'route' => 'buildings.index', 'permission' => 'view_card_buildings', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-door-open',  'title' => 'Rooms',              'subtitle' => 'Manage rooms',               'route' => 'rooms.index', 'permission' => 'view_card_rooms', 'from' => '#d97706', 'to' => '#b45309'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.institute.hub', compact('cards'));
    }
}
