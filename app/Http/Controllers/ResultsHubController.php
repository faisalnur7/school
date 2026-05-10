<?php

namespace App\Http\Controllers;

class ResultsHubController extends Controller
{
    public function index()
    {
        $cards = [
            ['icon' => 'fa-file-alt',      'title' => 'All Exams',           'subtitle' => 'View & manage exams',          'route' => 'exams.index',           'from' => '#4f46e5', 'to' => '#7c3aed'],
            ['icon' => 'fa-plus-circle',   'title' => 'Create Exam',         'subtitle' => 'Create a new exam',            'route' => 'exams.create',          'from' => '#0891b2', 'to' => '#0e7490'],
            ['icon' => 'fa-book-reader',   'title' => 'Subject Assignment',  'subtitle' => 'Assign subjects to students',  'route' => 'student-subjects.index','from' => '#059669', 'to' => '#047857'],
        ];

        return view('pages.results.hub', compact('cards'));
    }
}
