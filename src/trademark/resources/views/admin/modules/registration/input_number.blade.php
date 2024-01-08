@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form action="{{ route('admin.save_data_registration_input') }}" method="POST" id="form">
                @csrf
                <div class="info eol">
                    {{-- Trademark table --}}
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable,
                    ])
                </div>
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                <h3>{{ __('labels.change_address.text_22') }}</h3>
                <p>{{ __('labels.change_address.text_23') }}</p>
                <dl class="w12em eol clearfix">

                    <dt>{{ __('labels.change_address.text_24') }}</dt>
                    <dd>{{ __('labels.change_address.text_25') }}</dd>

                    <dt>{{ __('labels.change_address.name') }}</dt>
                    <dd>{{ $data['trademark_info_name'] }}</dd>

                    <dt>{{ __('labels.change_address.nation_name') }}</dt>
                    <dd>{{ $data['nation_name'] }}</dd>
                    @if($data['nation_id'] == NATION_JAPAN_ID)
                    <dt>{{ __('labels.change_address.prefecture_name') }}</dt>
                    <dd>{{ $data['prefectures_name'] }}</dd>

                    <dt>{{ __('labels.change_address.address_second') }}</dt>
                    <dd>{{ $data['address_second'] }}</dd>
                    @endif

                    <dt>{{ __('labels.change_address.address_three') }}</dt>
                    <dd>{{ $data['address_three'] }}</dd>
                </dl>
                <dl class="w12em clearfix">
                    <dt>{{ __('labels.change_address.text_26') }}</dt>
                    <dd> <input type="date" name="date_register" id="date_register"
                            value="{{ \CommonHelper::formatTime(old('date_register', $data['pi_dd_date'] ?? ''), 'Y-m-d') ?? '' }}">
                        <div id="erorr_date_register"></div>
                    </dd>
                    <dt>{{ __('labels.change_address.text_27') }}</dt>
                    <dd>
                        <input type="text" value="{{ old('register_number', $data['register_number'] ?? '') }}" name="register_number" />
                        @error('register_number')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                </dl>
                <p class="eol">
                    <a href="{{ route('admin.registration.change-address.index', ['id' => $data['trademark_id'], 'trademark_info_id' => $data['trademark_info_id']]) }}"
                        target="blank" class="btn_b btn_redirect">
                        {{ __('labels.change_address.text_28') }}
                    </a>
                </p>
                <p>{{ __('labels.change_address.text_29') }}</p>
                <h3>{{ __('labels.change_address.text_30') }}</h3>
                <dl class="w12em eol clearfix">
                    <dt>{{ __('labels.change_address.nation_name') }}</dt>
                    <dd>{{ $data['regist_cert_nation_name'] }}</dd>

                    <dt>{{ __('labels.change_address.postal_code') }}</dt>
                    <dd>{{ $data['regist_cert_postal_code'] }}</dd>

                    <dt>{{ __('labels.change_address.cert_address') }}</dt>
                    <dd>{{ $data['regist_cert_address'] }}</dd>

                    <dt>{{ __('labels.change_address.payer_name') }}</dt>
                    <dd>{{ $data['regist_cert_payer_name'] }}</dd>
                </dl>
                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.change_address.text_31') }}" class="btn_a"
                            onclick="history.back()" />
                    </li>
                    <li>
                        <input type="submit" value="{{ __('labels.change_address.text_32') }}" class="btn_c" />
                    </li>
                </ul>
                <input type="hidden" name="register_trademark_id" value="{{ $data['register_trademark_id'] ?? '' }}">
                <input type="hidden" name="trademark_id" value="{{ $data['trademark_id'] ?? '' }}">
                <input type="hidden" name="comparison_trademark_result_id"
                    value="{{ $data['comparison_trademark_result_id'] ?? '' }}">
                <input type="hidden" name="user_id" value="{{ $data['user_id'] ?? '' }}">
                <input type="hidden" name="matching_result_id" value="{{ $data['matching_result_id'] ?? '' }}">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
<style>
    #date_register {
        cursor: pointer;
    }
</style>
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageA303_E001 = '{{ __('messages.general.Register_trademark_A303_E001') }}';
        const errorMessageA303_E002 = '{{ __('messages.general.Register_trademark_A303_E002') }}';
        const errorMessageA303_E003 = '{{ __('messages.general.Register_trademark_A303_E003') }}';
        const errorMessageFormatErorr = '{{ __('messages.general.Register_trademark_A303_E005') }}';
        const errorMessageA303_E006 = '{{ __('messages.general.Register_trademark_A303_E006') }}';

        const sendDate = @json($data['pi_dd_date']);
        const now = @json($now);;
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/input_number/index.js') }}"></script>
    @if($data['checkDisabled'] == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_OFFICE_MANAGER] ])
@endsection
