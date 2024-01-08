@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')
        <form id="form" action="{{ route('admin.update.change_address.post', ['id' => $trademark->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="register_trademark_id" value="{{ $data['register_trademark_id'] ?? null }}">
            <input type="hidden" name="change_info_register_id" value="{{ $data['change_info_register_id'] ?? null }}">
            <input type="hidden" name="type" value="{{ $data['type'] ?? null }}">
            <h3>{{ __('labels.a700kenrisha01.h3') }}</h3>
            <h4>{{ __('labels.a700kenrisha01.h4_1') }}</h4>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a700kenrisha01.dt_1') }}</dt>
                <dd>{{ $data['trademark_info_name'] ?? null }}</dd>

                <dt>{{ __('labels.a700kenrisha01.dt_2') }}</dt>
                <dd>{{ $data['trademark_info_nation_name'] ?? null }}</dd>
                @if($data['trademark_info_nation_id'] == NATION_JAPAN_ID)
                <dt>{{ __('labels.a700kenrisha01.dt_3') }}</dt>
                <dd>{{ $data['trademark_info_address_first_name'] ?? null }}</dd>

                <dt>{{ __('labels.a700kenrisha01.dt_4') }}</dt>
                <dd>{{ $data['trademark_info_address_second'] ?? null }}</dd>
                @endif
                <dt>{{ __('labels.a700kenrisha01.dt_5') }}</dt>
                <dd>{{ $data['trademark_info_address_three'] ?? null }}</dd>
            </dl>

            <hr />

            <h4>{{ __('labels.a700kenrisha01.h4_2') }}</h4>

            {{ __('labels.a700kenrisha01.text_1') }}<a href="{{ route('admin.question.answers.from.ams', ['user_id' => $trademark->user_id]) }}">{{ __('labels.a700kenrisha01.text_2') }}</a>{{ __('labels.a700kenrisha01.text_3') }}<br />
            {{ __('labels.a700kenrisha01.text_4') }}
            <p class="eol"></p>

            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.a700kenrisha01.btn_1') }}" class="btn_b" id="enable_name"/></li>
                <li><input type="button" value="{{ __('labels.a700kenrisha01.btn_2') }}" class="btn_b" id="enable_address"/></li>
            </ul>

            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.a700kenrisha01.dt_6') }}</dt>
                <dd>
                    <ul class="r_c clearfix">
                        <li>
                            <label>
                                <input type="radio" class="modify_name modify_group" name="type_acc_radio"
                                       value="{{ INFO_TYPE_ACC_GROUP }}"
                                    {{ isset($data['type_acc']) && $data['type_acc'] == INFO_TYPE_ACC_GROUP ? 'checked' : '' }} />
                                {{ __('labels.change_address.type_acc_group') }}
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" class="modify_name modify_user" name="type_acc_radio"
                                       value="{{ INFO_TYPE_ACC_INDIVIDUAL }}"
                                    {{ isset($data['type_acc']) && $data['type_acc'] == INFO_TYPE_ACC_INDIVIDUAL ? 'checked' : '' }} />
                                {{ __('labels.change_address.type_acc_individual') }}
                            </label>
                        </li>
                        <input type="hidden" name="type_acc" value="{{$data['type_acc']}}">
                    </ul>
                </dd>
                <dt> {{ __('labels.change_address.text_6') }}</dt>
                <dd>
                    <input type="text" class="modify_name" value="{{ $data['name'] ?? '' }}" name="modify_name" />
                    <input type="hidden" value="{{ $data['name'] ?? '' }}" name="name">
                </dd>
                <dt> {{ __('labels.change_address.text_7') }}</dt>
                <dd>
                    <select name="m_nation_id_select" id="m_nation_id" class="modify_address">
                        <option value="" selected> {{ __('labels.change_address.m_nation_id') }}</option>
                        @if (isset($nations) && count($nations))
                            @foreach ($nations as $k => $nation)
                                <option value="{{ $nation->id }}"
                                    {{ isset($data['m_nation_id']) && $data['m_nation_id'] == $nation->id ? 'selected' : '' }}>
                                    {{ $nation->name ?? '' }}</option>
                            @endforeach
                        @endif
                    </select>
                    <input type="hidden" value="{{ $data['m_nation_id'] ?? '' }}" name="m_nation_id">
                </dd>
                <div id="address_jp">
                    <dt> {{ __('labels.change_address.prefecture_name') }}</dt>
                    <dd>
                        <select name="m_prefecture_id_select" id="m_prefecture_id" class="modify_address">
                            @if (isset($prefectures))
                                @foreach ($prefectures as $k => $prefecture)
                                    <option value="{{ $prefecture->id }}"
                                        {{ isset($data['m_prefectures_id']) && $data['m_prefectures_id'] == $prefecture->id ? 'selected' : '' }}>{{ $prefecture->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" name="m_prefecture_id" value="{{$data['m_prefectures_id']}}">
                    </dd>

                    <dt> {{ __('labels.change_address.address_second') }}</dt>
                    <dd>
                        <input type="text" value="{{ $data['address_second'] ?? ''}}" class="modify_address"
                               name="address_second_input" />
                        <input type="hidden" name="address_second" value="{{$data['address_second']}}">
                    </dd>
                </div>
                <dt> {{ __('labels.change_address.address_three') }}{{ __('labels.change_address.text_8') }}</dt>
                <dd>
                    <input type="text" value="{{ $data['address_three'] ?? ''}}" class="modify_address"
                           name="address_three_input" />
                    <input type="hidden" name="address_three" value="{{$data['address_three']}}">

                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" value="{{ __('labels.a700kenrisha01.back') }}" class="btn_a" onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'">
                </li>
                <li><input type="submit" value="{{ __('labels.a700kenrisha01.submit') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E016 = '{{ __('messages.general.Common_E016') }}';
        const Common_E020 = '{{ __('messages.general.Common_E020') }}';
        const errorMessagePaymentRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessagePaymentCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessagePaymentCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessagePaymentInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessagePaymentInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        let data = @JSON($data);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/change_address/update_change_address.js') }}"></script>
    @if(isset($data['changeInfoRegister']['is_updated']) && $data['changeInfoRegister']['is_updated'] == true
        || isset($data['registerTrademark']['is_updated']) && $data['registerTrademark']['is_updated'] == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER]]);
@endsection
