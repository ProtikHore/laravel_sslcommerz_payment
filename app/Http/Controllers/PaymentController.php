<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
    	return view('Payment.index');
    }

    public function save(Request $request)
    {
    	dd($request);
    }
}
