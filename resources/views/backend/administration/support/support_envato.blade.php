@extends('layouts.app')

@section('content')
<div class="card mb-5 mb-xl-10">

    <div class="wrapper-settings">
        <div class="mx-auto mb-5 col-lg-12">

            <div class="card mb-5">
                <div class="card-body" style="margin: 28px;font-weight: bold;font-family: sans-serif;font-size: 16px;" >
                    <div class="message  message--warning">
                        <p>
                            {{_lang('You Can Get Support Through CodeCanyon By')}} <a target="_blank" href="https://support.spotlayer.com"  style="color:#007bff;"> {{_lang('Click Here')}} </a> {{_lang('If You Have Purchased 6 Months Of Support as a Customer  You Can Submit Support Ticket Or Simply Upgrade Your Support By Purchasing Another 6 Months, So That You Can Enjoy Instant Answers and Solutions')}}
                                <br><br><br>{{_lang('If you encounter any problem, you should create a ticket first by writing the full details of the problem you are facing, in addition to taking a screenshot of the error that appears on your screen. Our technical support team will provide you with the necessary assistance as soon as possible')}}.
                        </p>
                    </div>
                   
                    <div class="message  message--warning">
                        <p>
                            {{_lang('If You Have Any Questions, Please Do Not Hesitate To Contact Us Via Email At')}}
                            <a style="color:#007bff;" href="mailto:envato.spotlayer@gmail.com">envato.spotlayer@gmail.com</a>
                            {{_lang('and We Will Be Happy To Assist You. Please Note That We May Have Response Times Of Up To Two Business Days')}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection