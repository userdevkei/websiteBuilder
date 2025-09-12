<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Stripe\Stripe;
use Stripe\Charge;
use Razorpay\Api\Api;
use App\PaymentHistory;
use App\EmailTemplate;
use App\Package;
use App\Company;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PremiumMembershipMail;
use App\Utilities\Overrider;
use Auth;

class MembershipController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_option('timezone','Asia/Dhaka'));	
    }

	/**
	* Show the membership extend form.
	*
	* @return \Illuminate\Http\Response
	*/
    public function my_subscription()
    {
    	$user = Auth::user();
		return view('membership.subscription_details', compact('user'));
    }

   /**
	* Show the membership extend form.
	*
	* @return \Illuminate\Http\Response
	*/
    public function extend()
    {
    	$user = Auth::user();
		return view('membership.extend', compact('user'));
    }
	
	public function pay(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'package' => 'required',
			'package_type' => 'required',
			'gateway' => 'required',
		]);
		if ($validator->fails()) {
			return redirect('membership/extend')->withErrors($validator)
												->withInput();				
		}
		
		
		$data = array();

		$package = Package::find($request->package);

		$data['title'] = "Buy {$package->package_name} Package";

		if($request->package_type == 'monthly'){
			$data['amount'] = $package->cost_per_month;
		    $data['custom'] = $request->package_type;
		}else{
			$data['amount'] = $package->cost_per_year;
		    $data['custom'] = $request->package_type;
		}

		
		//Create Pending Payment
		$payment = new PaymentHistory();
		$payment->company_id = company_id();
		$payment->title = $data['title'];
		$payment->method = "";
		$payment->currency = get_option('currency','USD');
		$payment->amount = $data['amount'];
		$payment->package_id = $package->id;
		$payment->package_type = $request->package_type;
		$payment->status = 'pending';
		$payment->save();
		
		$data['payment_id'] = $payment->id;
		
		if($request->gateway == "PayPal"){
			return view('membership.gateway.paypal',$data);
		}elseif($request->gateway == "Stripe"){

			Stripe::setApiKey(get_option('stripe_secret_key'));

			$session = \Stripe\Checkout\Session::create([
			  'payment_method_types' => ['card'],
			  'line_items' => [[
			    'price_data' => [
			      'product_data' => [
			          'name' => $data['title'],
			          'description' => $data['title'],
			       ],
			      'unit_amount' =>   round(convert_currency(get_option('currency','USD'), get_option('stripe_currency','USD'), ($data['amount'] * 100))),
			      'currency' 	=>   get_option('stripe_currency','USD'),
			    ],
			    'quantity' => 1,
			  ]],
			  'mode' => 'payment',
			  'success_url' => url('membership/stripe_payment/success/'.$payment->id),
			  'cancel_url' => url('membership/stripe_payment/cancel/'.$payment->id),
			]);

			$data['session_id'] = $session->id;
			session(['session_id' => $session->id]);

			return view('membership.gateway.stripe',$data);
		}elseif($request->gateway == "Razorpay"){
			$api = new Api(get_option('razorpay_key_id'), get_option('razorpay_secret_key'));

			$orderData = [
			    'receipt'         => $payment->id,
			    'amount'          => round(convert_currency(get_option('currency','USD'), 'INR',($data['amount'] * 100))),
			    'currency'        => 'INR',
			    'payment_capture' => 1 // auto capture
			];

			$razorpayOrder = $api->order->create($orderData);
			$razorpayOrderId = $razorpayOrder['id'];
			session(['razorpay_order_id' => $razorpayOrderId]);
			$data['amount'] = $orderData['amount'];
			$data['order_id'] = $razorpayOrderId;
			return view('membership.gateway.razorpay', $data);
		}else if($request->gateway == "Paystack"){
			return view('membership.gateway.paystack', $data);
		} else if($request->gateway == "Billplz"){
			return view('membership.gateway.billplz', $data);
		}else if($request->gateway == "paddle"){
			return view('membership.gateway.paddle', $data);
		}else if($request->gateway == "Flutterwave"){
            return view('membership.gateway.flutterwave', $data);
        }
    }
	
	//PayPal Payment Gateway
	public function paypal($action){
		if($action == "return"){
			
			return redirect('/dashboard')->with('paypal_success', _lang('Thank you, You have sucessfully extended your membership. Please wait until you get confrimation email if you still see your membership has expired.'));
		}else if($action == "cancel"){
			return redirect('membership/extend')->with('message', _lang('Payment Canceled !'));
		}
	}
	

	public function paypal_ipn(Request $request)
	{


		$payment_id = $request->item_number;
		//$amount = $request->mc_gross;
		$amount = convert_currency(get_option('paypal_currency','USD'), get_option('currency','USD'), $request->mc_gross);
		 
		$payment = PaymentHistory::find($payment_id);
		//$increment = $payment->extend;
		
		if( $amount >= $payment->amount){

			$company = Company::find($payment->company_id);

			if($payment->package_type == 'monthly'){
				$company->valid_to = date('Y-m-d', strtotime('+1 months'));
			}else{
				$company->valid_to = date('Y-m-d', strtotime('+1 year'));
			}

			$company->membership_type = 'member';
			$company->last_email = NULL;
			$company->package_id = $payment->package_id;

			 //Update Package Details
	        $package = $payment->package;

			if($company->package_type == 'Free') {
				$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
				$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
				$company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
				$company->package_type = $payment->package_type;
			  } else {
				$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
				$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
				$company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
				$company->package_type = $payment->package_type;
			  }

			$company->save();

			//Save payment History
			$payment->method = "PayPal";
			$payment->status = 'paid';
			$payment->save();
			
			
			//Replace paremeter
			$user = User::where('company_id',$company->id)
						->where('user_type','user')
						->first();

			$replace = array(
				'{name}'=>$user->name,
				'{email}'=>$user->email,
				'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
			);
			
			//Send email Confrimation
			Overrider::load("Settings");
			$template = EmailTemplate::where('name','premium_membership')->first();
			$template->body = process_string($replace,$template->body);

			try{
				Mail::to($user->email)->send(new PremiumMembershipMail($template));
			}catch (\Exception $e) {
				//Nothing
			}
			
        }		
    }
	
	//Stripe payment Gateway
	public function stripe_payment($action, $payment_id){

		if($action == 'cancel'){
			return redirect('/dashboard')->with('error', _lang('Payment Cancelled !'));
		}

		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		Stripe::setApiKey(get_option('stripe_secret_key'));

		$session = \Stripe\Checkout\Session::retrieve(session('session_id'));
		

        $payment = PaymentHistory::find($payment_id);

        if($session->amount_total != round(convert_currency(get_option('currency','USD'), get_option('stripe_currency','USD'), ($payment->amount * 100)))){
			return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
		}
		
		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
		if($company->package_type == 'Free') {
			$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
			$company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
			$company->package_type = $payment->package_type;
		  } else {
			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
			$company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
			$company->package_type = $payment->package_type;
		  }

		$company->save();
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "Stripe";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

		//Forget Session
		request()->session()->forget('session_id');

        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
	}


	//Razorpay payment Gateway
	public function razorpay_payment($payment_id){
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		$api = new Api(get_option('razorpay_key_id'), get_option('razorpay_secret_key'));

	    try{
	        $attributes = array(
	            'razorpay_order_id' 	=> session('razorpay_order_id'),
	            'razorpay_payment_id' 	=> $_POST['razorpay_payment_id'],
	            'razorpay_signature' 	=> $_POST['razorpay_signature']
	        );

	        $api->utility->verifyPaymentSignature($attributes);

	        $charge = $api->payment->fetch($_POST['razorpay_payment_id']);

	        $payment = PaymentHistory::find($payment_id);

	        if($charge->amount != round(convert_currency(get_option('currency','USD'), 'INR',($payment->amount * 100)))){
				return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
			}
		
			$company = Company::find($payment->company_id);

			if($payment->package_type == 'monthly'){
				$company->valid_to = date('Y-m-d', strtotime('+1 months'));
			}else{
				$company->valid_to = date('Y-m-d', strtotime('+1 year'));
			}

			$company->membership_type = 'member';
			$company->last_email = NULL;
			$company->package_id = $payment->package_id;

			//Update Package Details
	        $package = $payment->package;
			if($company->package_type == 'Free') {
				$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
				$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
				$company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
				$company->package_type = $payment->package_type;
			  } else {
				$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
				$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
				$company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
				$company->package_type = $payment->package_type;
			  }

			$company->save();
			
			session(['valid_to' => $company->valid_to]);

			//Save payment History
			$payment->method = "Rezorpay";
			$payment->status = 'paid';
			$payment->save();
			

			//Replace paremeter
	        $user = User::where('company_id',$company->id)
	                    ->where('user_type','user')
	                    ->first();
			$replace = array(
				'{name}' =>$user->name,
				'{email}' =>$user->email,
				'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
			);
			
			//Send email Confrimation
			Overrider::load("Settings");
			$template = EmailTemplate::where('name','premium_membership')->first();
			$template->body = process_string($replace,$template->body);

			try{
				Mail::to($user->email)->send(new PremiumMembershipMail($template));
			}catch (\Exception $e) {
				//Nothing
			}

			//Forget Session
			request()->session()->forget('razorpay_order_id');

	        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));

	    }catch(SignatureVerificationError $e){
	        $success = false;
	        $error = 'Razorpay Error : ' . $e->getMessage();
	        return redirect('/dashboard')->with('error', $error);
	    }	
        
	}

	//Paystack payment Gateway
	public function paystack_payment($payment_id, $reference){
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);
		
		$curl = curl_init();
  
		 curl_setopt_array($curl, array(
		    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		      "Authorization: Bearer " . get_option('paystack_secret_key'),
		      "Cache-Control: no-cache",
		    ),
		 ));
		  
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		  
		if ($err) {
		    return redirect('/dashboard')->with('error', $err);
		} 

		$charge = json_decode($response);

		if($charge->data->amount != round(convert_currency(get_option('currency','USD'), get_option('paystack_currency','GHS'),($payment->amount * 100)))){
			return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
		}
		
		
		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
        if($company->package_type == 'Free') {

			$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
//			$company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
			$company->package_type = $payment->package_type;
		  } else {
			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
//			$company->inventory_module = unserialize($company->inventory_module)[$company->package_type];
			$company->package_type = $payment->package_type;
		  }

		$company->save();
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "PayStack";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

        return redirect('/dashboard')->with('success', _lang('Thank you, You have successfully extended your membership.'));
	}

    public function flutterwave_payment($payment_id, $transaction_id){
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $payment = PaymentHistory::find($payment_id);

        if(!$payment) {
            return redirect('/dashboard')->with('error', _lang('Payment record not found!'));
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . get_option('FLUTTER_WAVE_SECRET_KEY'),
                "Content-Type: application/json",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return redirect('/dashboard')->with('error', $err);
        }

        $transaction = json_decode($response);

        // Check if verification was successful
        if (!$transaction || $transaction->status !== 'success') {
            return redirect('/dashboard')->with('error', _lang('Transaction verification failed!'));
        }

        $transaction_data = $transaction->data;

        // Verify transaction status
        if ($transaction_data->status !== 'successful') {
            return redirect('/dashboard')->with('error', _lang('Payment was not successful!'));
        }

        // Verify transaction reference matches
        if ($transaction_data->tx_ref != $payment_id) {
            return redirect('/dashboard')->with('error', _lang('Transaction reference mismatch!'));
        }

        // Verify amount (Flutterwave returns amount in the currency unit, not kobo/cents)
        $expected_amount = round(convert_currency(get_option('currency','USD'), get_option('flutter_currency','USD'), $payment->amount), 2);
        if ($transaction_data->amount != $expected_amount) {
            return redirect('/dashboard')->with('error', _lang('Amount mismatch! Illegal Operation!'));
        }

        // Verify currency
        if ($transaction_data->currency !== get_option('flutter_currency','USD')) {
            return redirect('/dashboard')->with('error', _lang('Currency mismatch!'));
        }

        // Check if payment has already been processed
        if ($payment->status === 'paid') {
            return redirect('/dashboard')->with('success', _lang('Payment has already been processed.'));
        }

        $company = Company::find($payment->company_id);

        if (!$company) {
            return redirect('/dashboard')->with('error', _lang('Company not found!'));
        }

        // Update company membership validity
        if($payment->package_type == 'monthly'){
            $company->valid_to = date('Y-m-d', strtotime('+1 months'));
        }else{
            $company->valid_to = date('Y-m-d', strtotime('+1 year'));
        }

        $company->membership_type = 'member';
        $company->last_email = NULL;
        $company->package_id = $payment->package_id;

        // Update Package Details
        $package = $payment->package;

        if(!$package) {
            return redirect('/dashboard')->with('error', _lang('Package not found!'));
        }

        if($company->package_type == 'Free') {
            $company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
            $company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
            // $company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
            $company->package_type = $payment->package_type;
        } else {
            $company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
            $company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
            // $company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
            $company->package_type = $payment->package_type;
        }

        $company->save();

        session(['valid_to' => $company->valid_to]);

        // Save payment History
        $payment->method = "Flutterwave";
        $payment->status = 'paid';
//        $payment->transaction_id = $transaction_data->id; // Store Flutterwave transaction ID
//        $payment->gateway_reference = $transaction_data->flw_ref; // Store Flutterwave reference
        $payment->save();

        // Get user for email
        $user = User::where('company_id', $company->id)
            ->where('user_type', 'user')
            ->first();

        if (!$user) {
            return redirect('/dashboard')->with('error', _lang('User not found!'));
        }

        // Replace parameters for email
        $replace = array(
            '{name}' => $user->name,
            '{email}' => $user->email,
            '{valid_to}' => date('d M,Y', strtotime($company->valid_to)),
//            '{transaction_id}' => $transaction_data->id,
//            '{amount}' => get_option('flutter_currency','USD') . ' ' . number_format($transaction_data->amount, 2),
//            '{payment_method}' => 'Flutterwave',
        );

        // Send email confirmation
        try {
            Overrider::load("Settings");
            $template = EmailTemplate::where('name', 'premium_membership')->first();

            if ($template) {
                $template->body = process_string($replace, $template->body);
                Mail::to($user->email)->send(new PremiumMembershipMail($template));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the payment process
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        return redirect('/dashboard')->with('success', _lang('Thank you, You have successfully extended your membership via Flutterwave.'));
    }
//	public function flutterwave_payment($payment_id, $reference){
//		@ini_set('max_execution_time', 0);
//		@set_time_limit(0);
//
//		$payment = PaymentHistory::find($payment_id);
//
//        // In your controller
//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => '',
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 0,
//            CURLOPT_FOLLOWLOCATION => true,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => 'POST',
//            CURLOPT_POSTFIELDS => json_encode([
//                'tx_ref' => $payment_id,
//                'amount' => $payment->amount,
//                'currency' => 'USD',
//                'redirect_url' => url('membership/flutterwave_callback'),
//                'customer' => [
//                    'email' => Auth::user()->email,
//                    'name' => Auth::user()->name,
//                ]
//            ]),
//            CURLOPT_HTTPHEADER => array(
//                'Authorization: Bearer ' . get_option('FLUTTER_WAVE_SECRET_KEY'),
//                'Content-Type: application/json'
//            ),
//        ));
//
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//        curl_close($curl);
//
//        if ($err) {
//            return redirect('/dashboard')->with('error', $err);
//        }
//
//       return $data = json_decode($response, true);
//        return redirect($data['data']['link']);
//
//
//
//		$curl = curl_init();
//
//		 curl_setopt_array($curl, array(
//		    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
//		    CURLOPT_RETURNTRANSFER => true,
//		    CURLOPT_ENCODING => "",
//		    CURLOPT_MAXREDIRS => 10,
//		    CURLOPT_TIMEOUT => 30,
//		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//		    CURLOPT_CUSTOMREQUEST => "GET",
//		    CURLOPT_HTTPHEADER => array(
//		      "Authorization: Bearer " . get_option('paystack_secret_key'),
//		      "Cache-Control: no-cache",
//		    ),
//		 ));
//
//		$response = curl_exec($curl);
//		$err = curl_error($curl);
//		curl_close($curl);
//
//		if ($err) {
//		    return redirect('/dashboard')->with('error', $err);
//		}
//
//		$charge = json_decode($response);
//
//		if($charge->data->amount != round(convert_currency(get_option('currency','USD'), get_option('paystack_currency','GHS'),($payment->amount * 100)))){
//			return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
//		}
//
//
//		$company = Company::find($payment->company_id);
//		if($payment->package_type == 'monthly'){
//			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
//		}else{
//			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
//		}
//		$company->membership_type = 'member';
//		$company->last_email = NULL;
//		$company->package_id = $payment->package_id;
//
//		//Update Package Details
//        $package = $payment->package;
//        if($company->package_type == 'Free') {
//
//			$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
//			$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
////			$company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
//			$company->package_type = $payment->package_type;
//		  } else {
//			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
//			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
////			$company->inventory_module = unserialize($company->inventory_module)[$company->package_type];
//			$company->package_type = $payment->package_type;
//		  }
//
//		$company->save();
//
//		session(['valid_to' => $company->valid_to]);
//
//		//Save payment History
//		$payment->method = "PayStack";
//		$payment->status = 'paid';
//		$payment->save();
//
//
//		//Replace paremeter
//        $user = User::where('company_id',$company->id)
//                    ->where('user_type','user')
//                    ->first();
//		$replace = array(
//			'{name}' =>$user->name,
//			'{email}' =>$user->email,
//			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
//		);
//
//		//Send email Confrimation
//		Overrider::load("Settings");
//		$template = EmailTemplate::where('name','premium_membership')->first();
//		$template->body = process_string($replace,$template->body);
//
//		try{
//			Mail::to($user->email)->send(new PremiumMembershipMail($template));
//		}catch (\Exception $e) {
//			//Nothing
//		}
//
//        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
//	}

	//Billplz payment Gateway
	public function billplz_payment($payment_id){
		
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);
		
		$url = 'https://www.billplz-sandbox.com/api/v2/bills';
		$ch = curl_init($url);
		$fields = array(
					'collection_id' => 'o2dvh5l6',
					'email' => Auth::user()->email,
					'name' => 'Johnathan',
					'amount' => convert_currency(get_option('currency','MYR'),get_option('billplz_currency','MYR'),$payment->amount*100) ,
					'mobile' => Auth::user()->phone,
					'callback_url' => url('membership/billplz_payment/'.$payment_id),
					'redirect_url' => url('membership/billplz_success/'.$payment_id),
					//if true, a SMS will be send to the mobile with a charge of 50 cents
					'deliver' => 'false'
				);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERPWD,  get_option('billplz_secret_key') );
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			//execute post
			$result = curl_exec($ch);


			if ($result === true) 
			{
				$info = curl_getinfo($ch);
				curl_close($ch);
				die('error occured during curl exec. Additioanl info: ' . var_export($info));
			}

			$err = curl_error($ch);
		//close connection
		curl_close($ch);
		  
		if ($err) {
		    return redirect('/dashboard')->with('error', $err);
		} 

		$charge = json_decode($result);

		return redirect($charge->url);

		
	}
	
	public function billplz_success(Request $request, $payment_id)
	{
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);

		if($request->billplz['paid'] != 'true') {
			return redirect('/dashboard')->with('error', _lang('Payment Not Paid !'));
		}

		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
        if($company->package_type == 'Free') {
			$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
			// $company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
			$company->package_type = $payment->package_type;
		  } else {
			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
			// $company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
			$company->package_type = $payment->package_type;
		  }

		$company->save();
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "PayStack";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
	}

	
	// paddle payment Gateway
	public function Paddle_payment($payment_id){

		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);

		$vendorId = get_option('PADDLE_VENDOR_ID');
		$vendorAuthCode = get_option('PADDLE_VENDOR_AUTH_CODE');
		$customer_country = $payment->currency;
		$name = Auth::user()->name ;
		$email = Auth::user()->email;
		$amount  = convert_currency(get_option('currency','USD'),get_option('paddle_currency','USD'),$payment->amount);
		$title	= $payment->title;
		// $custom_message =  'test';  
		$webhook_url =url('membership/Paddle_success/'.$payment_id);
		$return_url = url('membership/Paddle_payment/'.$payment_id);


		$data = [
			'vendor_id'=>$vendorId ,
			'vendor_auth_code'=>$vendorAuthCode,
			'title'=>$title ,
			'webhook_url'=> $webhook_url,
			'prices'=> ["$customer_country:$amount"] ,
			'return_url'=>$return_url,
			'customer_email'=>$email,
			'customer_country'=>$customer_country ,
		];
		$data = http_build_query($data);

		$curl = curl_init();
		
		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => [
			"Content-Type: application/x-www-form-urlencoded"
		  ],
		]);
		
		//execute post
		$result = curl_exec($curl);

		if ($result === true) 
		{
			$info = curl_getinfo($curl);
			curl_close($curl);
			die('error occured during curl exec. Additioanl info: ' . var_export($info));
		}

		$err = curl_error($curl);
		//close connection
		curl_close($curl);
		
		if ($err) {
			return redirect('/dashboard')->with('error', $err);
		} 

		$charge = json_decode($result);
 
		return redirect($charge->response->url);
	}
	

	// paddle payment Gateway Success
	public function Paddle_success(Request $request, $payment_id)
	{
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);

		if($request->billplz['paid'] != 'true') {
			return redirect('/dashboard')->with('error', _lang('Payment Not Paid !'));
		}

		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
        if($company->package_type == 'Free') {
			$company->websites_limit = unserialize($package->websites_limit)[$payment->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$payment->package_type];
			// $company->inventory_module = unserialize($package->inventory_module)[$payment->package_type];
			$company->package_type = $payment->package_type;
		  } else {
			$company->websites_limit = unserialize($package->websites_limit)[$company->package_type];
			$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
			// $company->inventory_module = unserialize($package->inventory_module)[$company->package_type];
			$company->package_type = $payment->package_type;
		  }

		$company->save();
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "paddle";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
	}
}
