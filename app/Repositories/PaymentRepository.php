<?php
namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository implements PaymentInterface {

	public function index() {
		return 'index';
	}

	public function save($request) {
		$post_data = array();
        $post_data['store_id'] = config('sslinfo.store_id');
        $post_data['store_passwd'] = config('sslinfo.store_password');
        $post_data['total_amount'] = $request->amount;
        $post_data['currency'] = $request->currency;
        $post_data['tran_id'] = uniqid();
        $post_data['success_url'] = url('payment/success');
        $post_data['fail_url'] = url('payment/fail');
        $post_data['cancel_url'] = url('payment/cancel');

        $post_data['cus_name'] = $request->get('name');
        $post_data['cus_email'] = $request->get('email');
        $post_data['cus_add1'] = $request->get('address');
        $post_data['cus_city'] = $request->get('city');
        $post_data['cus_state'] = $request->get('state');
        $post_data['cus_postcode'] = $request->get('zip');
        $post_data['cus_country'] = 'Bangladesh';
        $post_data['cus_phone'] = $request->get('mobile_no');

        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1 '] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Product";
        $post_data['product_category'] = "Service";
        $post_data['product_profile'] = "virtual-goods";

        $post_data['value_a'] = "ref001";
        $post_data['value_b '] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        $post_data['product_amount'] = "0";
        $post_data['vat'] = "0";
        $post_data['discount_amount'] = "0";
        $post_data['convenience_fee'] = "0";


        $payment = $request->except('_token');
    	$payment['status'] = 'Pending';
    	$payment['transaction_id'] = $post_data['tran_id'];
    	Payment::create($payment);


//        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
        $direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url );
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1 );
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


        $content = curl_exec($handle );

        //dd($content);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !( curl_errno($handle))) {
            curl_close( $handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close( $handle);
            echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
            exit;
        }
        $sslcz = json_decode($sslcommerzResponse, true );
        if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
            return redirect($sslcz['GatewayPageURL']);
        } else {
            echo "JSON Data parsing error!";
        }
	}

	public function success($request) {
		$tran_id = $request->input('tran_id');

    	$val_id=urlencode($_POST['val_id']);
        $store_id=urlencode(config('sslinfo.store_id'));
        $store_passwd=urlencode(config('sslinfo.store_password'));
        $requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$store_passwd."&v=1&format=json");

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $requested_url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

        $result = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if($code == 200 && !( curl_errno($handle)))
        {
        	$payment['transaction'] = $result;
        	$payment['status'] = 'Success';
            Payment::where('transaction_id', $tran_id)->update($payment);

            $paymentData = Payment::where('transaction_id', $tran_id)->first();
            $transactionData = json_decode($paymentData->transaction);

            return view('Payment.success', compact(
                'paymentData',
                'transactionData',
            ));

        } else {
            echo "Failed to connect with SSLCOMMERZ";
        }
	}

	public function cancel($request) {
		return 'cancel';
	}

	public function fail($request) {
		return 'fail';
	}
}