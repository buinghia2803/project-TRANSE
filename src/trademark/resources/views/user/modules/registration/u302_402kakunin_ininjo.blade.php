
@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u302_402kakunin_ininjo.title') }}</h2>
        <form id="form" action="{{ route('user.registration.save-attorney-letter-confirm', ['id' => $registerTrademark->id, 's' => $sessionKey]) }}" method="POST">
            @csrf
            <p>{{ __('labels.u302_402kakunin_ininjo.title_2') }}</p>
            <div class="ininjo_wrap eol">
            <h3>{{ __('labels.u302_402kakunin_ininjo.title_3') }}</h3>
            <p class="right">{{ __('labels.u302_402kakunin_ininjo.title_4', ['month' => now()->month, 'day' => now()->day]) }}</p>
            <p class="eol">{{ __('labels.u302_402kakunin_ininjo.title_5') }}<br />

            @foreach ($agents as $key => $agent)
                {{ __('labels.u302_402kakunin_ininjo.title_6') }}{{ $agent->identification_number ?? '' }}{{ __('labels.u302_402kakunin_ininjo.title_7') }}{{ $agent->name ?? ''}}  、
            @endforeach
            {{ __('labels.u302_402kakunin_ininjo.title_10') }}
            <h4>{{ __('labels.u302_402kakunin_ininjo.title_8') }}</h4>
            <p class="num">{{ __('labels.u302_402kakunin_ininjo.title_9', ['num' => $registerTrademark->register_number ?? '']) }}</p>
            <p class="center">{{ __('labels.u302_402kakunin_ininjo.remember') }}</p>
            <ul class="eol">
                <li>{{ __('labels.u302_402kakunin_ininjo.content_1') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_2') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_3') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_4') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_5') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_6') }}</li>
                <li>{{ __('labels.u302_402kakunin_ininjo.content_7') }}</li>
            </ul>
            <p class="right eol">{{ __('labels.u302_402kakunin_ininjo.that_all') }}</p>
            <dl class="w08em clearfix">
                <dt>{{ __('labels.u302_402kakunin_ininjo.address') }}</dt>
                <dd>{{ ($prefectures[$registerTrademark->trademark_info_address_first] ?? '') . $registerTrademark->trademark_info_address_second . $registerTrademark->trademark_info_address_three  }}</dd>
                <dt>{{ __('labels.u302_402kakunin_ininjo.name') }}</dt>
                <dd>{{ $registerTrademark->trademark_info_name ?? '' }}</dd>
                <dt>{{ __('labels.u302_402kakunin_ininjo.representative') }}</dt>
                <dd>{{ $registerTrademark->representative_name ?? '' }}</dd>
            </dl>
            </div><!-- /ininjo -->
            <p class="eol">
                <label>{{ __('labels.u302_402kakunin_ininjo.attention') }}
                    <input type="checkbox" name="is_confirm" />
                </label>
            </p>
            <ul class="footerBtn clearfix">
                <li><input type="button" data-back="{{ $backUrl ?? '' }}" value="{{ __('labels.back') }}" class="btn_a"/></li>
                <li><input type="submit" value="{{ __('labels.signup.form.submit_code') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div><!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E025') }}';
        new clsValidation('#form', {
            rules: {
                is_confirm: {
                    required: true
                }
            }, messages:{
                is_confirm: {
                    required: errorMessageRequired
                }
            }
        })
    </script>
@endsection
