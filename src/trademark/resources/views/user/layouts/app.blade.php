<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AMS オンライン出願サービス</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="max-filesize" content="3000000">

    <link href="{{ asset('common/css/contents.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{asset('common/css/scroll-hint.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('common/css/alert.css')}}" rel="stylesheet" type="text/css" />
    @yield('css')
    <script>
        const SET_SESSION_AJAX_URL = '{{ route('user.ajax.set-session') }}';
        const BASE_URL = '{{ config('app.url') }}/';
        const ASSET_URL = @json(trim(asset(''), '/'));
    </script>
    <script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/scroll-hint.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/js/loadingoverlay.min.js')}}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/bs3-modal.js') }}"></script>
    {{-- jquery-validation --}}
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-validation/jquery.validate.min.js')}}"></script>
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-validation/additional-methods.min.js')}}"></script>
    {{-- multiple-select --}}
    <link href="{{ asset('common/libs/multiple-select/multiple-select.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('common/libs/multiple-select/jquery.multiple.select.js') }}"></script>
    {{-- jquery-confirm --}}
    <link href="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{asset('common/libs/jquery-confirm/jquery-confirm.min.js')}}"></script>
    {{-- Common function --}}
    <script type="text/JavaScript" src="{{asset('common/js/functions.js')}}"></script>
    <style>
        #loading_image {
            background: url('{{ asset('/common/images/loading_image.gif') }}') no-repeat center center;
            background-size: 50px 50px;
        }
    </style>

    @yield('headerSection')
</head>

<body id="pagetop" class="user">
    <!-- wrapper -->
    <div id="wrapper">
        <!-- header -->
        @include('user.layouts.header')
        <!-- /header -->

        <!-- contents -->
        @yield('main-content')
    </div><!-- /wrapper -->

    @include('user.layouts.footer')

    <div class="pagetop"><a href="#pagetop" title="{{ __('labels.page_top_title') }}">{{ __('labels.page_top') }}</a></div>

    <script type="text/JavaScript" src="{{asset('common/js/common.js')}}"></script>
    @yield('footerSection')
    @yield('script')
    @yield('common-payer-info-script')
    @yield('modal')
</body>

</html>
