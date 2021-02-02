<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\PaymentInterface;

class PaymentController extends Controller
{
	protected $paymentRepo;
	public function __construct(PaymentInterface $paymentRepo) {
		$this->paymentRepo = $paymentRepo;
	}

    public function index()
    {
    	$data = $this->paymentRepo->index();
    	return view('Payment.index');
    }

    public function save(Request $request)
    {
    	return $this->paymentRepo->save($request);
    }

    public function success(Request $request)
    {
        return $this->paymentRepo->success($request);
    }

    public function cancel(Request $request)
    {
        $data = $this->paymentRepo->cancel($request);
        return view('Payment.cancel');
    }

    public function fail(Request $request)
    {
        $data = $this->paymentRepo->fail($request);
        return view('Payment.fail');
    }
}
