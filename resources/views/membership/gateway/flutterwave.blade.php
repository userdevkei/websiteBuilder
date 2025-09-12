@extends('layouts.login')

@section('content')
	<!-- begin:: Page -->
	<div class="kt-grid kt-grid--ver kt-grid--root">
		<div class="kt-grid kt-grid--hor kt-grid--root  kt-login kt-login--v1" id="kt_login">
			<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--desktop kt-grid--ver-desktop kt-grid--hor-tablet-and-mobile">
				<!--begin::Aside-->
				<div class="kt-grid__item kt-grid__item--order-tablet-and-mobile-2 kt-grid kt-grid--hor kt-login__aside">
					<div class="kt-grid__item">
						<a href="{{url('/')}}" class="kt-login__logo">
           				<img alt="Logo" src="{{ Auth::user()->company_id != '' ? get_company_logo() : get_logo() }}"/>
						</a>
					</div>
					<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver">
						<div class="kt-grid__item kt-grid__item--middle">
							<h3 class="kt-login__title">{{ _lang('Membership Payment') }}!</h3>
							<h4 class="kt-login__subtitle">{{ _lang('Upgrade your membership to use more features') }}.</h4>
						</div>
					</div>
					<div class="kt-grid__item">
						<div class="kt-login__info">
							<div class="kt-login__copyright">
								&copy; {{ date('Y').' '.get_option('company_name') }}
							</div>
						</div>
					</div>
				</div>
				<!--begin::Aside-->

				<!--begin::Content-->
				<div class="kt-grid__item kt-grid__item--fluid  kt-grid__item--order-tablet-and-mobile-1  kt-login__wrapper">

					<!--begin::Body-->
					<div class="kt-login__body">

						<!--begin::Signin-->
						<div class="kt-login__form">	
							
						<div class="card card-signin my-5">			
							<div class="card-header text-center">
							{{ _lang('Extend Membership') }}
							</div>

							<div class="card-body" id="extend_membership">
								
                <style>
                .stripe-button-el{width: 100% !important;}
                </style>
                {{--<h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place(convert_currency(get_option('currency','USD'),get_option('flutter_currency','USD'), $amount), currency(get_option('flutter_currency','GHS'))) }}</h5>
                  <br>
                <button type="button" class="btn btn-primary btn-block" onclick="payWithFlutterWave()"> {{ _lang('Pay Now') }}</button>
                
                <script src="https://js.paystack.co/v1/inline.js"></script> --}}
                                <!-- HTML Section -->
                                <h5 class="text-center">{{ _lang('Payable Amount') }} : {{ g_decimal_place(convert_currency(get_option('currency','USD'),get_option('flutter_currency','USD'), $amount), currency(get_option('flutter_currency','GHS'))) }}</h5>
                                <br>
                                <button type="button" class="btn btn-primary btn-block" onclick="payWithFlutterWave()"> {{ _lang('Pay Now') }}</button>

                                <!-- Flutterwave Inline Script -->
                                <script src="https://checkout.flutterwave.com/v3.js"></script>

							</div>
						</div>

						</div>
						<!--end::Signin-->
					</div>
					<!--end::Body-->
				</div>
				<!--end::Content-->
			</div>
		</div>
	</div>
@endsection

@section('js-script')
<script type="text/javascript">

function payWithFlutterWave() {
    FlutterwaveCheckout({
        public_key: "{{ get_option('FLUTTER_WAVE_PUBLIC_KEY') }}",
        tx_ref: "{{ $payment_id }}",
        amount: {{ round(convert_currency(get_option('currency','USD'), get_option('flutter_currency','NGN'), $amount)) }},
        currency: "{{ get_option('flutter_currency','NGN') }}",
        payment_options: "card,mobilemoney,ussd,banktransfer",
        customer: {
            email: "{{ Auth::user()->email }}",
            name: "{{ Auth::user()->name }}",
            phone_number: "{{ Auth::user()->phone ?? '' }}",
        },
        customizations: {
            title: "{{ config('app.name') }}",
            description: "{{ _lang('Membership Payment') }}",
            logo: "{{ Auth::user()->company_id != '' ? get_company_logo() : get_logo() }}",
        },
        callback: function (data) {
            console.log(data);
            window.location = "{{ url('membership/flutterwave_payment/'.$payment_id) }}/" + data.transaction_id;
        },
        onclose: function () {
            console.log("Payment modal closed");
        },
        // 👉 add this
        payment_plan: null, // optional
        redirect_url: undefined // keep undefined to prevent full-page redirect
    });

}

</script>
@endsection