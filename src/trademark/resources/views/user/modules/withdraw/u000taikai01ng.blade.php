@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u000taikai01NG.title') }}</h2>
        <form>
            <p class="eol">{{ __('labels.u000taikai01NG.attention') }}</p>
            <ul class="footerBtn clearfix">
                <li><a style="border:none" href="{{ route('user.withdraw.index') }}"><input type="button" value="{{ __('labels.u000taikai01NG.back') }}" class="btn_a" /></a></li>
                <li>
                    <a style="border:none" href="{{ route('user.withdraw.application-list') }}">
                        <input type="button" value="{{ __('labels.u000taikai01NG.goto_list') }}" class="btn_b" />
                    </a>
                </li>
            </ul>
        </form>
    </div>
<!-- /contents -->
@endsection
