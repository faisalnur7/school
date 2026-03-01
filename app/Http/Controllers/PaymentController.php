<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function index(){

    }
    
    public function receipt(Payment $payment){
        $payment->load(['items.fee.feeSet', 'student', 'collector']);
        return view('pages.payments.receipt', compact('payment'));
    }
}
