@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" action="{{ route('admin.registration.change-address.send-session') }}" method="POST">
                @csrf
                <h3>{{ __('labels.change_address.title') }}</h3>
                <h4>{{ __('labels.change_address.title_table') }}</h4>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.change_address.name') }}</dt>
                    <dd>{{ $data['name'] ?? '' }}</dd>

                    <dt>{{ __('labels.change_address.nation_name') }}</dt>
                    <dd>{{ $data['nation_name'] ?? '' }}</dd>
                    @if($data['nation_id'] == NATION_JAPAN_ID)
                    <dt>{{ __('labels.change_address.prefecture_name') }}</dt>
                    <dd>{{ $data['prefecture_name'] ?? '' }}</dd>

                    <dt>{{ __('labels.change_address.address_second') }}</dt>
                    <dd>{{ $data['address_second'] ?? '' }}</dd>
                    @endif
                    <dt>{{ __('labels.change_address.address_three') }}</dt>
                    <dd>{{ $data['address_three'] ?? '' }}</dd>
                </dl>
                <hr />
                <h4>{{ __('labels.change_address.text_1') }}</h4>
                {{ __('labels.change_address.text_2') }}<a
                    href="{{ route('admin.question.answers.from.ams', ['user_id' => $data['user_id']]) }}">{{ __('labels.change_address.link_qa') }}</a>{{ __('labels.change_address.text_3') }}<br />
                {{ __('labels.change_address.text_4') }}
                <p class="eol">

                <ul class="footerBtn clearfix">
                    <li><input type="button" value="{{ __('labels.change_address.btn_enable_name') }}" class="btn_b"
                            id="enable_name" /></li>
                    <li><input type="button" value="{{ __('labels.change_address.btn_enable_address') }}" class="btn_b"
                            id="enable_address" /></li>
                </ul>
                <dl class="w16em eol clearfix">

                    <dt>{{ __('labels.change_address.text_5') }}</dt>
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
                        <input type="text" class="modify_name" value="{{ $data['input_name'] ?? '' }}" name="modify_name" />
                        <input type="hidden" value="{{ $data['input_name'] ?? '' }}" name="name">
                    </dd>
                    <dt> {{ __('labels.change_address.text_7') }}</dt>
                    <dd>
                        <select name="m_nation_id_select" id="m_nation_id" class="modify_address">
                            <option value="" selected> {{ __('labels.change_address.m_nation_id') }}</option>
                            @if (isset($nations) && count($nations))
                                @foreach ($nations as $k => $nation)
                                    <option value="{{ $k }}"
                                        {{ isset($data['input_nation_id']) && $data['input_nation_id'] == $k ? 'selected' : '' }}>
                                        {{ $nation ?? '' }}</option>
                                @endforeach
                            @endif
                        </select>
                        <input type="hidden" value="{{ $data['input_nation_id'] ?? '' }}" name="m_nation_id">
                    </dd>
                    <div id="address_jp">
                        <dt> {{ __('labels.change_address.prefecture_name') }}</dt>
                        <dd>
                            <select name="m_prefecture_id_select" id="m_prefecture_id" class="modify_address">
                                @if (isset($prefectures))
                                    @foreach ($prefectures as $k => $prefecture)
                                        <option value="{{ $k }}"
                                            {{ isset($data['input_m_prefecture_id']) && $data['input_m_prefecture_id'] == $k ? 'selected' : '' }}>{{ $prefecture }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <input type="hidden" name="m_prefecture_id" value="{{$data['input_m_prefecture_id']}}">
                        </dd>

                        <dt> {{ __('labels.change_address.address_second') }}</dt>
                        <dd>
                            <input type="text" value="{{ $data['input_address_second'] ?? ''}}" class="modify_address"
                                name="address_second_input" />
                            <input type="hidden" name="address_second" value="{{$data['input_address_second']}}">
                        </dd>
                    </div>

                    <dt> {{ __('labels.change_address.address_three') }}{{ __('labels.change_address.text_8') }}</dt>
                    <dd>
                        <input type="text" value="{{ $data['input_address_three'] ?? ''}}" class="modify_address"
                            name="address_three_input" />
                        <input type="hidden" name="address_three" value="{{$data['input_address_three']}}">

                    </dd>
                </dl>
                <ul class="footerBtn clearfix">
                    <li><input type="button" value="{{ __('labels.change_address.btn_back') }}" class="btn_a"
                            onclick="history.back()" /></li>
                    <li><input type="submit" value="{{ __('labels.change_address.btn_submit') }}" class="btn_b" /></li>
                </ul>
                <input type="hidden" name="trademark_info_id" value="{{ $data['trademark_info_id'] ?? '' }}">
                <input type="hidden" name="change_info_register_id" value="{{ $data['change_info_register_id'] ?? '' }}">
                <input type="hidden" name="register_trademark_id" value="{{ $data['register_trademark_id'] ?? '' }}">
                <input type="hidden" name="trademark_id" value="{{ $data['trademark_id'] ?? '' }}">
                <input type="hidden" name="from_page" value="{{ $data['from_page'] }}">
                @if(isset($data['matching_result_id']))
                    <input type="hidden" name="matching_result_id" value="{{ $data['matching_result_id'] }}">
                @endif
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessagePaymentRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessagePaymentCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessagePaymentCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
        const errorMessagePaymentInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessagePaymentInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/change_address/registration_change_address.js') }}"></script>
    @if(!empty($data['is_updated']) && $data['is_updated'] == 1)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
