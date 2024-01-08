@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        @include('compoments.messages')
        <h2>{{ __('labels.gmo.title') }}</h2>
        <form id="cardForm" action="{{ route('user.payment.GMO.store') }}" method="POST">
            @csrf
            <p>{{ __('labels.gmo.sub_title_1') }}<br>
                {{ __('labels.gmo.sub_title_2') }}<br>
                {{ __('labels.gmo.sub_title_3') }}</p>
            <input type="hidden" name="secret" value="{{ $secret ?? '' }}">
            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.gmo.card_number') }}<span class="red">*</span></dt>
                <dd><input type="tel" name="card_number" value="{{ old('card_number', '') }}" class="em18"></dd>
                <dt>{{ __('labels.gmo.card_name') }}<span class="red">*</span></dt>
                <dd><input type="text" name="card_name" autocapitalize="on" class="em18" value="{{ old('card_name', '') }}"></dd>
                <dt>{{ __('labels.gmo.expire_date') }}<span class="red">*</span></dt>
                <dd class="select-group">
                    <select name="expire_month">
                        @foreach ($months as $month)
                            @if ($currentMonth <= $month)
                                @if(old('expire_month', null) && old('expire_month', null) == $month)
                                    <option selected  value="{{ $month }}">
                                        {{ $month < 10 ? '0' . $month : $month }}</option>
                                @else
                                    <option {{ !old('expire_month', null) && $currentMonth == $month ? 'selected': '' }}  value="{{ $month }}">
                                        {{ $month < 10 ? '0' . $month : $month }}</option>
                                @endif

                            @endif
                        @endforeach
                    </select> /
                    <select name="expire_year" id="select">
                        @foreach ($years as $year)
                        @if(old('expire_year', null) && old('expire_year', null) == $year)
                            <option selected value="{{ $year }}">
                                {{ $year }}</option>
                        @else
                            <option {{ $currentYear == $year ? 'selected' : '' }} value="{{ $year }}">
                                {{ $year }}</option>
                        @endif
                        @endforeach
                    </select>
                </dd>
                <dt>{{ __('labels.gmo.cvc') }}<span class="red">*</span></dt>
                <dd><input type="number" name="card_cvc" maxlength="4" class="em08" value="{{ old('card_cvc', '') }}"></dd>
            </dl>
            <ul class="footerBtn clearfix">
                <li><button type="button" class="btn_a" id="btn-return-back">{{ __('labels.back') }}</button></li>
                <li><button type="button" class="btn_b" id="btn-submit">{{ __('labels.gmo.submit') }}</button></li>
            </ul>
        </form>
    </div>
    <style>
        input[name=card_cvc]::-webkit-outer-spin-button,
        input[name=card_cvc]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }
        #btn-submit,
        #btn-return-back {
            /* width: 111px; */
            font-family: 'Noto Sans JP', "メイリオ", Meiryo, "ヒラギノ角ゴ Pro W3", "Hiragino Kaku Gothic Pro W3", Osaka, "ＭＳ Ｐゴシック", "MS P Gothic", sans-serif;
            height: 38px;
            font-size: 1.3em;
            padding: 5px 2em;
            font-weight: inherit;
        }
    </style>
@endsection
@section('script')
    <script>
        const currentMonth = @json($currentMonth);
        const currentYear = @json($currentYear);
        const months = @json($months);
        const years = @json($years);
        const urlCommonPayment = '{{ route('user.payment.index') }}';
        const errorMessageFormatError = '{{ __('messages.general.Common_E017') }}';
        const errorMessageMaxLength255 = '{{ __('messages.common.errors.Common_E031') }}';
        const errorMessageMaxLength20 = '{{ __('messages.common.errors.Common_E031_2', ['attr' => 20]) }}';
        const errorMessageMaxLength4 = '{{ __('messages.common.errors.Common_E031_2', ['attr' => 4]) }}';
        const errorMessageFormatRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageRequired = '{{ __('messages.registrant_information.Common_E001') }}';
        const errorMessageCardName = '{{ __('messages.general.Common_E056') }}';
        const oldYear = @json(old('expire_year', null));
        const oldYMonth = @json(old('expire_month', null));
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/common/js/gmo-input-card.js') }}"></script>
@endsection
