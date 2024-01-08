<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if (View::hasSection('title'))
            @yield('title')
        @else
            {{ __($pageTitle ?? 'labels.title_page') }}
        @endif
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin_dir" content="{{ config('app.admin_dir') }}">
    <meta name="language" content="{{ \App::getLocale() }}">

    {{-- Google Font --}}
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> --}}
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('common/css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/alert.css') }}">

    {{-- JS Header --}}
    <script>
        const SET_SESSION_AJAX_URL = '{{ route('user.ajax.set-session') }}';
        const UPLOAD_MAX_FILESIZE = '{{ config('filesystems.upload_max_filesize') }}';
        const ASSET_URL = @json(trim(asset(''), '/'));
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/jquery-3.6.0.min.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/scroll-hint.min.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/loadingoverlay.min.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/bs3-modal.js') }}"></script>
    {{-- jquery-ui --}}
    <link rel="stylesheet" href="{{ asset('admin_assets/themes/plugins/jquery-ui/jquery-ui.min.css') }}" type="text/css">
    <script src="{{ asset('admin_assets/themes/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    {{-- jquery-validation --}}
    <script type="text/JavaScript" src="{{ asset('common/libs/jquery-validation/jquery.validate.min.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/libs/jquery-validation/additional-methods.min.js') }}"></script>
    {{-- jquery-confirm --}}
    <link href="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.js') }}"></script>
    {{-- multiple-select --}}
    <link href="{{ asset('common/libs/multiple-select/multiple-select.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('common/libs/multiple-select/jquery.multiple.select.js') }}"></script>
    {{-- overlayScrollbars --}}
    <script src="{{ asset('admin_assets/themes/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('common/js/functions.js') }}"></script>
    <style>
        #loading_image {
            background: url('{{ asset('/common/images/loading_image.gif') }}') no-repeat center center;
            background-size: 50px 50px;
        }
    </style>

    @yield('headSection')
    @yield('css')
</head>
<body id="pagetop" class="admin">
    <div id="wrapper">
        @include('admin.layouts.header')
        @yield('main-content')
    </div>
    @include('admin.layouts.footer')

    <div class="pagetop"><a href="#pagetop" title="{{ __('labels.page_top_title') }}">{{ __('labels.page_top') }}</a>
    </div>
    @if (!empty(config('langjs')))
        <script>
            Lang = {};
            @foreach (config('langjs') as $key => $item)
                Lang.{{ $key }} = '@lang($item)';
            @endforeach
        </script>
    @endif

    {{-- Core JS Footer --}}
    <script type="text/JavaScript" src="{{ asset('common/js/common.js') }}"></script>
    @yield('footerSection')
    @yield('script')
</body>
</html>
