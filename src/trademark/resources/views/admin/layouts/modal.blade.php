<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <title>AMS オンライン出願サービス</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />

        <link rel="stylesheet" href="{{ asset('common/css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('common/css/alert.css') }}">
        <link href="{{ asset('common/libs/multiple-select/multiple-select.css') }}" rel="stylesheet" type="text/css" />

        <script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
        <script src="{{ asset('common/libs/multiple-select/jquery.multiple.select.js') }}"></script>

        @yield('headSection')
        @yield('css')
    </head>
    <body>
        <div id="wrapper" style="height: 80%">
            @yield('main-content')
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
    <!-- /wrapper inner-->
    </body>
</html>