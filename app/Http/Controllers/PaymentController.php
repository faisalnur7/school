<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\AcademicSession;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Group;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $data['payments'] = $query->latest()->get();
        $data['from']     = $request->from;
        $data['to']       = $request->to;

        return view('pages.payments.index', $data);
    }
    
    public function receipt(Payment $payment){
        $payment->load(['items.fee.feeSet', 'student', 'collector']);
        return view('pages.payments.receipt', compact('payment'));
    }
}
