<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\View\View;

class AuditTrailController extends Controller
{
    public function index(): View
    {
        $auditTrails = AuditTrail::query()
            ->latest('action_date')
            ->latest('action_time')
            ->latest('id')
            ->paginate(25);

        return view('pages.security.index', compact('auditTrails'));
    }
}
