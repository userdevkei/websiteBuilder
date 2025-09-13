{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--    <head>--}}
{{--        <meta charset="utf-8" />--}}
{{--        <title>{{ get_option('site_title', 'LaraBuilder') }}</title>--}}
{{--        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">--}}
{{--        <meta http-equiv="X-UA-Compatible" content="IE=edge" />--}}
{{--        <meta name="csrf-token" content="{{ csrf_token() }}">--}}

{{--		<!-- App favicon -->--}}
{{--        <link rel="shortcut icon" href="{{ get_favicon() }}">--}}
{{--	   --}}
{{--        <!-- App css -->--}}
{{--        <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" media="all"  type="text/css" />--}}
{{--		<link href="{{ asset('backend/assets/css/fontawesome.min.css') }}" rel="stylesheet">--}}
{{--		<link href="{{ asset('backend/assets/css/themify-icons.css') }}" rel="stylesheet">--}}
{{--		<link href="{{ asset('backend/assets/css/styles.css') }}" rel="stylesheet" media="all" type="text/css" />--}}
{{--        <link rel="stylesheet" href="{{ mix('css/app.css') }}">--}}
{{--        <script src="{{ mix('js/app.js') }}" defer></script>--}}
{{--    </head>--}}

{{--	<body class="{{ _lang('SYS_DIRECTION_DIR') }}">--}}
{{--        <div class="page-wrapper">--}}
{{--            <!-- Page Content-->--}}
{{--            <div class="page-content pt-5">--}}
{{--                <div class="container">--}}
{{--                    <!-- Page-Title -->--}}
{{--					<div class="alert alert-success alert-dismissible" id="main_alert" role="alert">--}}
{{--						<button type="button" id="close_alert" class="close">--}}
{{--							<span aria-hidden="true"><i class="mdi mdi-close"></i></span>--}}
{{--						</button>--}}
{{--						<span class="msg"></span>--}}
{{--					</div>--}}
{{--                    --}}
{{--					@yield('content')--}}
{{--					--}}
{{--                </div><!-- container -->--}}

{{--                <footer class="footer text-center">--}}
{{--                    <span>&copy; {{ date('Y').' '.get_option('company_name') }}</span>--}}
{{--                </footer><!--end footer-->--}}
{{--            </div>--}}
{{--            <!-- end page content -->--}}
{{--        </div>--}}
{{--        <!-- end page-wrapper -->--}}


{{--        <!-- jQuery  -->--}}
{{--        <script src="{{ asset('backend/assets/js/vendor/jquery-2.2.4.min.js') }}"></script>--}}
{{--        <script src="{{ asset('backend/assets/js/popper.min.js') }}"></script>--}}
{{--		<script src="{{ asset('backend/assets/js/bootstrap.min.js') }}"></script>--}}
{{--        <script src="{{ asset('backend/assets/js/print.js') }}"></script>--}}
{{--		<script src="{{ asset('backend/plugins/toastr/toastr.js') }}"></script>--}}
{{--		<script src="{{ asset('backend/assets/js/public.js') }}"></script>--}}
{{--		--}}
{{--		<script type="text/javascript">		--}}
{{--		(function($){--}}
{{--			"use strict";	--}}

{{--			//Show Success Message--}}
{{--			@if(Session::has('success'))--}}
{{--		       $("#main_alert > span.msg").html(" {{ session('success') }} ");--}}
{{--			   $("#main_alert").addClass("alert-success").removeClass("alert-danger");--}}
{{--			   $("#main_alert").css('display','block');--}}
{{--			@endif--}}
{{--			--}}
{{--			//Show Single Error Message--}}
{{--			@if(Session::has('error'))--}}
{{--			   $("#main_alert > span.msg").html(" {{ session('error') }} ");--}}
{{--			   $("#main_alert").addClass("alert-danger").removeClass("alert-success");--}}
{{--			   $("#main_alert").css('display','block');--}}
{{--			@endif--}}
{{--			--}}
{{--			--}}
{{--			@php $i =0; @endphp--}}

{{--			@foreach ($errors->all() as $error)--}}
{{--			    @if ($loop->first)--}}
{{--					$("#main_alert > span.msg").html("<i class='typcn typcn-delete'></i> {{ $error }} ");--}}
{{--					$("#main_alert").addClass("alert-danger").removeClass("alert-success");--}}
{{--				@else--}}
{{--                    $("#main_alert > span.msg").append("<br><i class='typcn typcn-delete'></i> {{ $error }} ");					--}}
{{--				@endif--}}
{{--				--}}
{{--				@if ($loop->last)--}}
{{--					$("#main_alert").css('display','block');--}}
{{--				@endif--}}
{{--				--}}
{{--				@if(isset($errors->keys()[$i]))--}}
{{--					var name = "{{ $errors->keys()[$i] }}";--}}
{{--				--}}
{{--					$("input[name='" + name + "']").addClass('error');--}}
{{--					$("select[name='" + name + "'] + span").addClass('error');--}}
{{--				--}}
{{--					$("input[name='"+name+"'], select[name='"+name+"']").parent().append("<span class='v-error'>{{$error}}</span>");--}}
{{--				@endif--}}
{{--				@php $i++; @endphp--}}
{{--			--}}
{{--			@endforeach--}}
{{--			--}}
{{--        })(jQuery);--}}
{{--		--}}
{{--	 </script>--}}

{{--	 <!-- Custom JS -->--}}
{{--	 @yield('js-script')--}}
{{--	 --}}
{{--    </body>--}}
{{--</html>--}}

<html lang="{{ app()->getLocale() }}" @if(_lang('SYS_DIRECTION_DIR') == 'rtl')direction="rtl" dir="rtl"@endif>
<!-- begin::Head -->
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <title>{{ get_option('site_title', 'LaraBuilder') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ get_favicon() }}">

    <!-- Fonts -->
    <link href="//fonts.googleapis.com/css?family=Nunito|Roboto" rel="stylesheet">

    @php
        // Determine asset base path based on environment
        $assetBase = app()->environment('local') ? 'backend/assets' : 'public/backend/assets';
    @endphp

            <!-- Core CSS -->
    <link href="{{ asset($assetBase . '/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset($assetBase . '/css/fontawesome.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset($assetBase . '/css/themify-icons.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset($assetBase . '/css/styles.css') }}" rel="stylesheet" type="text/css"/>

    <!-- Mix (compiled assets) -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>

    @if (_lang('SYS_DIRECTION_DIR') == 'rtl')
        <!-- RTL Specific CSS -->
        <link href="{{ asset($assetBase . '/css/style.rtl.css') }}" rel="stylesheet" type="text/css"/>
        <link href="//fonts.googleapis.com/css?family=Cairo:300,400,600,700" rel="stylesheet">
    @endif

    <style>
        @media (max-width: 1024px) {
            .page-wrapper {
                padding: 10px;
            }
        }
    </style>

    {{-- Debug: Remove in production --}}
    @if(config('app.debug'))
        <style>
            .debug-info {
                position: fixed;
                top: 0;
                left: 0;
                background: rgba(255, 0, 0, 0.9);
                color: #fff;
                padding: 10px;
                font-size: 12px;
                z-index: 9999;
            }
        </style>
    @endif
</head>
<!-- end::Head -->
<body class="{{ _lang('SYS_DIRECTION_DIR') }}">
<div class="page-wrapper">
    <!-- Page Content-->
    <div class="page-content pt-5">
        <div class="container">
            <!-- Alerts -->
            <div class="alert alert-success alert-dismissible" id="main_alert" role="alert" style="display:none;">
                <button type="button" id="close_alert" class="close">
                    <span aria-hidden="true"><i class="mdi mdi-close"></i></span>
                </button>
                <span class="msg"></span>
            </div>

            <!-- Yield content -->
            @yield('content')
        </div><!-- container -->

        <footer class="footer text-center">
            <span>&copy; {{ date('Y').' '.get_option('company_name') }}</span>
        </footer>
    </div>
</div>

<!-- Vendor JS -->
<script src="{{ asset($assetBase . '/js/vendor/jquery-2.2.4.min.js') }}"></script>
<script src="{{ asset($assetBase . '/js/popper.min.js') }}"></script>
<script src="{{ asset($assetBase . '/js/bootstrap.min.js') }}"></script>
<script src="{{ asset($assetBase . '/js/print.js') }}"></script>
<script src="{{ asset('public/backend/plugins/toastr/toastr.js') }}"></script>
<script src="{{ asset($assetBase . '/js/public.js') }}"></script>

<script>
    (function ($) {
        "use strict";

        // Success flash
        @if(Session::has('success'))
        $("#main_alert > span.msg").html("{{ session('success') }}");
        $("#main_alert").addClass("alert-success").removeClass("alert-danger").show();
        @endif

        // Error flash
        @if(Session::has('error'))
        $("#main_alert > span.msg").html("{{ session('error') }}");
        $("#main_alert").addClass("alert-danger").removeClass("alert-success").show();
        @endif

        // Validation errors
        @php $i = 0; @endphp
        @foreach ($errors->all() as $error)
        @if ($loop->first)
        $("#main_alert > span.msg").html("<i class='typcn typcn-delete'></i> {{ $error }} ");
        $("#main_alert").addClass("alert-danger").removeClass("alert-success");
        @else
        $("#main_alert > span.msg").append("<br><i class='typcn typcn-delete'></i> {{ $error }} ");
        @endif
        @if ($loop->last)
        $("#main_alert").show();
        @endif

        @if(isset($errors->keys()[$i]))
        var name = "{{ $errors->keys()[$i] }}";
        $("input[name='" + name + "']").addClass('error');
        $("select[name='" + name + "'] + span").addClass('error');
        $("input[name='"+name+"'], select[name='"+name+"']")
            .parent().append("<span class='v-error'>{{$error}}</span>");
        @endif
        @php $i++; @endphp
        @endforeach
    })(jQuery);
</script>

@yield('js-script')

{{-- Debug Info --}}
@if(config('app.debug'))
    <div class="debug-info">
        <strong>Env:</strong> {{ app()->environment() }} <br>
        <strong>Asset Base:</strong> {{ $assetBase }}
    </div>
@endif
</body>
</html>
