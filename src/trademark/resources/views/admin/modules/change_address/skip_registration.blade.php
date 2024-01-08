@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form>
                <h3>{{ __('labels.change_address.text_20') }}</h3>
                <h4 class="eol">{{ __('labels.change_address.text_21') }}</h4>
                <ul class="footerBtn clearfix">
                    <li>
                        <a href="{{ route('admin.registration.document.modification', ['id' => $matchingResult->id, 'register_trademark_id' => $registerTrademark->id]) }}"
                            class="btn_b custom_btn" style="color: white">{{ __('labels.change_address.btn_submit') }}</a>
                    </li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <style>
        .custom_btn {
            padding: 5px 2rem !important;
        }
    </style>
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
