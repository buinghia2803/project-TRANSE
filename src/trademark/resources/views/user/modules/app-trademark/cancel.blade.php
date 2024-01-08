@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('messages.apply_trademark.title_cancel') }}</h2>
        @include('admin.components.includes.messages')

        <form action="{{ route('user.apply-trademark.cancel', ['id' => $id]) }}" method="POST">
            @csrf
            {!! __('messages.apply_trademark.message_cancel') !!}
            <ul class="footerBtn clearfix">
{{--                <li><input type="button" onclick="window.location='{{ route('user.apply-trademark.confirm', ['id' => $id]) }}'" value="{{ __('messages.apply_trademark.back') }}" class="btn_a" /></li>--}}
                <li><input type="button" onclick="history.back()" value="{{ __('messages.apply_trademark.back') }}" class="btn_a" /></li>
                <li><input type="submit" value="{{ __('messages.apply_trademark.confirm') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('css')
    <style>
        .btn_a {
            display: inline-block;
            padding: 5px 2em;
            border: 1px solid #999999;
            border-radius: 5px;
            text-decoration: none;
            color: #ffffff;
            cursor: pointer;
            font-size: 1.3em;
            width: 111.17px;
        }
    </style>
@endsection
