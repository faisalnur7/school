<?php

namespace App\Http\Controllers;

class UsersHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-users',      'title' => 'Users',       'subtitle' => 'Manage system users',       'route' => 'users.index',       'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-user-tag',   'title' => 'Roles',       'subtitle' => 'Manage user roles',         'route' => 'roles.index',       'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-key',        'title' => 'Permissions',  'subtitle' => 'Manage permissions',       'route' => 'permissions.index', 'from' => '#059669', 'to' => '#047857'],
        ];

        return view('pages.users.hub', compact('cards'));
    }
}
