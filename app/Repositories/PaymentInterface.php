<?php
namespace App\Repositories;

interface PaymentInterface {
	public function index();
	public function save($request);
	public function success($request);
	public function cancel($request);
	public function fail($request);
}