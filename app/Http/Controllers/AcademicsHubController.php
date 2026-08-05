<?php

namespace App\Http\Controllers;

class AcademicsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-calendar-alt', 'title' => 'Sessions',      'subtitle' => 'Manage academic sessions',      'route' => 'sessions.index', 'permission' => 'view_card_sessions', 'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-chalkboard',   'title' => 'Classes',       'subtitle' => 'Manage school classes',         'route' => 'classes.index',  'permission' => 'view_card_classes', 'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-layer-group',  'title' => 'Sections',      'subtitle' => 'Manage class sections',         'route' => 'sections.index', 'permission' => 'view_card_sections', 'from' => '#059669', 'to' => '#047857'],
            ['icon' => 'fa-object-group', 'title' => 'Groups',        'subtitle' => 'Manage student groups',         'route' => 'groups.index',   'permission' => 'view_card_groups', 'from' => '#d97706', 'to' => '#b45309'],
            ['icon' => 'fa-book-open',    'title' => 'Subjects',      'subtitle' => 'Manage subjects & assignments', 'route' => 'subjects.index', 'permission' => 'view_card_subjects', 'from' => '#dc2626', 'to' => '#b91c1c'],
            ['icon' => 'fa-door-open',    'title' => 'Class Rooms',   'subtitle' => 'Manage classrooms',             'route' => 'classrooms.index', 'permission' => 'view_card_class_rooms', 'from' => '#7c3aed', 'to' => '#6d28d9'],
            ['icon' => 'fa-clock',        'title' => 'Class Routine', 'subtitle' => 'Manage class routines',         'route' => 'routines.index', 'permission' => 'view_card_class_routine', 'from' => '#0f766e', 'to' => '#0d9488'],
        ];
        $cards = array_values(array_filter($cards, fn ($card) => auth()->user()?->hasPermission($card['permission'])));

        return view('pages.academics.hub', compact('cards'));
    }
}
