<?php

namespace App\Http\Controllers;

class UsersHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-users',      'title' => 'Users',       'subtitle' => 'Manage system users',       'route' => 'users.index', 'permission' => 'view_card_users', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-user-tag',   'title' => 'Roles',       'subtitle' => 'Manage user roles',         'route' => 'roles.index', 'permission' => 'view_card_roles', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-key',        'title' => 'Permissions',  'subtitle' => 'Manage permissions',       'route' => 'permissions.index', 'permission' => 'view_card_permissions', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-shield-alt', 'title' => 'Audit Trail',  'subtitle' => 'Review security activity',  'route' => 'audit-trails.index', 'permission' => 'view_audit_trail', 'from' => '#dc2626', 'to' => '#b91c1c'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.users.hub', compact('cards'));
    }
}
