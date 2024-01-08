<!--payer-info -->
<h3>{{ __('labels.payer_info.title') }}</h3>
<ul class="r_c eol clearfix">
    <li><button type="button"  class="btn_a copyInfoContactOfUser" value="{{ __('labels.payer_info.copy_info_contact') }}">{{ __('labels.payer_info.copy_info_contact') }}</button></li>
    <li><button type="button" class="btn_a copyInfoMemberOfUser" value="{{ __('labels.payer_info.copy_info_member') }}">{{ __('labels.payer_info.copy_info_member') }}</button></li>
</ul>

<div class="border orange mb20">
    <h4 class="mb10">{{ __('labels.payer_info.title_h4') }} </h4>

    <dl class="w16em clearfix">

        <dt>{{ __('labels.payer_info.method_payment') }} <span class="red">*</span></dt>
        <dd>
            <ul class="r_c ul_payment_type">
                <li>
                    <label>
                        <input type="radio" name="payment_type" {{ old('payment_type', ($payerInfo && isset($payerInfo['payment_type'])? $payerInfo['payment_type'] : '')) == 1 ? "selected" : "" }} @if(old('payment_type', ($payerInfo && isset($payerInfo['payment_type'])? $payerInfo['payment_type'] : '')) == PAYMENT_TYPE_CREDIT) checked @endif value="{{ PAYMENT_TYPE_CREDIT }}" class="payment_type payment_type_credit" />{{ __('labels.payer_info.payment_type_credit') }}
                    </label>
                </li>
                <li>
                    <label class="{{ isset($disabledBankTransfer) && $disabledBankTransfer ? 'disabled' : '' }}">
                        <input type="radio" name="payment_type" {{ isset($disabledBankTransfer) && $disabledBankTransfer ? 'disabled' : '' }} {{ old('payment_type', ($payerInfo && isset($payerInfo['payment_type'])? $payerInfo['payment_type'] : '')) == 2 ? "selected" : "" }} @if(old('payment_type', ($payerInfo && isset($payerInfo['payment_type'])? $payerInfo['payment_type'] : '')) == PAYMENT_TYPE_TRANSFER) checked @endif value="{{ PAYMENT_TYPE_TRANSFER }}" class="payment_type payment_type_transfer" />
                        <input type="hidden" name="cost_bank_transfer" value="{{ $paymentFee['cost_service_base'] ?? 0 }}">
                        {{ __('labels.payer_info.payment_type_transfer', ['payment_fee' => CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0)]) }}</label>
                </li>
                <br />
            </ul>
            @error('payment_type') <div class="notice">{{ $message }}</div> @enderror
        </dd>
        <dt>{{ __('labels.payer_info.payer_type') }} <span class="red">*</span></dt>
        <dd>
            <ul class="r_c ul_payer_type">
                <li>
                    <label>
                        <input type="radio" name="payer_type" @if(old('payer_type', ($payerInfo && isset($payerInfo['payer_type'])? $payerInfo['payer_type'] : '')) == PAYER_TYPE_TAX_AGENT) checked @endif value="{{ PAYER_TYPE_TAX_AGENT }}" class="payer_type payer_type_1"/>{{ __('labels.payer_info.payer_type_1') }}
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="payer_type" @if(old('payer_type', ($payerInfo && isset($payerInfo['payer_type'])? $payerInfo['payer_type'] : '')) == PAYER_TYPE_REGIS_ADDRESS_OVERSEAS) checked @endif value="{{ PAYER_TYPE_REGIS_ADDRESS_OVERSEAS }}" class="payer_type payer_type_2"/>{{ __('labels.payer_info.payer_type_2') }}
                    </label>
                </li>
                <br />
            </ul>
            @error('payer_type') <div class="notice">{{ $message }}</div> @enderror
        </dd>
        <dt>{{ __('labels.payer_info.nation') }}<span class="red">*</span></dt>
        <dd>
            <input type="hidden" value="{{ NATION_JAPAN_ID }}" id="nation_japan_id" />
            <select name="m_nation_id" id="m_nation_id">
                <option value="" selected>選択</option>
                @if(isset($nations) && count($nations))
                    @foreach ($nations as $k => $nation)
                        <option value="{{ $k }}" {{ old('m_nation_id', ($payerInfo && isset($payerInfo['m_nation_id'])? $payerInfo['m_nation_id'] : null)) == $k ? "selected" : "" }}>{{ $nation ?? '' }}</option>
                    @endforeach
                @endif
            </select>
            @error('m_nation_id') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_name') }}<span class="red">*</span></dt>
        <dd><input type="text" name="payer_name" value="{{ old('payer_name', ($payerInfo && isset($payerInfo['payer_name'])? $payerInfo['payer_name'] : ''))}}" id="payer_name" class="remove_space_input" /><br />{{ __('labels.payer_info.info_name_note') }}</dd>
    </dl>
</div>

<dl class="w16em eol clearfix">
    <dt>{{ __('labels.payer_info.info_name_furigana') }}<span class="red">*</span></dt>
    <dd>
        <input type="text" name="payer_name_furigana" id="payer_name_furigana" class="remove_space_input" value="{{ old('payer_name_furigana', ($payerInfo && isset($payerInfo['payer_name_furigana'])? $payerInfo['payer_name_furigana'] : '')) }}"/>
        @error('payer_name_furigana') <div class="notice">{{ $message }}</div> @enderror
    </dd>

    <!--showHideInfoAddress-->
    <div class="showHideInfoAddress h-adr">
        <dt>{{ __('labels.payer_info.info_postal_code') }} <span class="red">*</span></dt>
        <input type="hidden" class="p-country-name" value="Japan">
        <dd>
            <div class="wp_postal_code">
                <input type="text" name="postal_code" class="p-postal-code remove_space_input" value="{{ old('postal_code', ($payerInfo && isset($payerInfo['postal_code'])? $payerInfo['postal_code'] : '')) }}" id="postal_code"/>
                <input type="button" value="{{ __('labels.payer_info.info_postal_code_button') }} " class="btn_a postal_code_button" />
            </div>
            @error('postal_code') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_prefectures_id') }}<span class="red">*</span></dt>
        <dd>
            <input type="hidden" id="hiddenValuePrefecture" class="p-region" />
            <select name="m_prefecture_id" id="m_prefecture_id">
                @if (isset($prefectures) )
                    @foreach ($prefectures as $k => $item)
                        <option value="{{ $k }}" {{ old('m_prefecture_id', ($payerInfo && isset($payerInfo['m_prefecture_id'])? $payerInfo['m_prefecture_id'] : '')) == $k ? "selected" : "" }}>{{ $item }}</option>
                    @endforeach
                @endif
            </select>
            @error('m_prefecture_id') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_address_second') }}<span class="red">*</span></dt>
        <dd>
            <input type="hidden" id="hiddenAddressSecond" class="p-locality" />
            <input type="hidden" id="hiddenStreetAddressSecond" class="p-street-address" />
            <input type="text" name="address_second" id="address_second" value="{{ old('address_second', ($payerInfo && isset($payerInfo['address_second'])? $payerInfo['address_second'] : '')) }}" class="em30 remove_space_input" /><br />
            @error('address_second') <div class="notice">{{ $message }}</div> @enderror
            <span class="input_note">{{ __('labels.payer_info.info_address_second_note') }}</span>
        </dd>
    </div>
    <dt>{{ __('labels.payer_info.info_address_three') }}</dt>
    <dd>
        <div>
            <input type="text" name="address_three" id="address_three" value="{{ old('address_three', ($payerInfo && isset($payerInfo['address_three'])? $payerInfo['address_three'] : '')) }}" class="em30 remove_space_input" />
        </div>
        @error('address_three') <div class="notice">{{ $message }}</div> @enderror
        <span class="input_note">{{ __('labels.payer_info.info_address_three_note') }}</span>
    </dd>
</dl>
<!--end payer-info -->
<style>
    @media only screen and (max-width: 640px)  {
        #m_nation_id {
            width: 100%;
        }
    }
    @media only screen and (max-width: 440px)  {
        .p-postal-code,
        .copyInfoContactOfUser{
            margin-bottom: 10px;
        }
    }
</style>
<script>
    const payerInfo = @json($payerInfo);
    let callClear = true
    if(payerInfo) {
        callClear = false
    }
    let JapanID = $('#nation_japan_id').val();
    const AjaxGetInfoUser = '{{ route('user.get-info-user-ajax') }}';
    const errorMessagePaymentRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
    const errorMessagePaymentCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
    const errorMessagePaymentCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
    const errorMessagePaymentInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
    const errorMessagePaymentInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
    const errorMessageSelectRequired = '{{ __('messages.common.errors.Common_E025') }}';

    const paymentRule = {
        'payment_type': {
            required: () => {
                return $('[name=payment_type]:visible').length > 0;
            },
        },
        'payer_type': {
            required: () => {
                return $('[name=payer_type]:visible').length > 0;
            },
        },
        'm_nation_id': {
            required: () => {
                return $('[name=m_nation_id]:visible').length > 0;
            },
        },
        'payer_name': {
            required: () => {
                return $('[name=payer_name]:visible').length > 0;
            },
            isFullwidthSpecial: true,
            maxlength: 50,
        },
        'payer_name_furigana': {
            required: () => {
                return $('[name=payer_name_furigana]:visible').length > 0;
            },
            isValidFurigana: true,
            maxlength: 50,
        },
        'postal_code': {
            required: () => {
                return $('[name=postal_code]:visible').length > 0
                    && $('[name=m_nation_id]:visible').length > 0
                    && $('select[name=m_nation_id]').val() == JapanID;
            },
            isValidInfoPostalCode: true
        },
        'm_prefecture_id': {
            required: () => {
                return $('[name=m_prefecture_id]:visible').length > 0
                    && $('[name=m_nation_id]:visible').length > 0
                    && $('select[name=m_nation_id]').val() == JapanID;
            }
        },
        'address_second': {
            required: () => {
                return $('[name=address_second]:visible').length > 0
                    && $('[name=m_nation_id]:visible').length > 0
                    && $('select[name=m_nation_id]').val() == JapanID;
            },
            isValidInfoAddress: true,
            maxlength: 100
        },
        'address_three': {
            isValidInfoAddress: () => {
                return $('[name=address_three]:visible').length > 0
                    && $('[name=m_nation_id]:visible').length > 0
                    && $('select[name=m_nation_id]').val() == JapanID;
            },
            maxlength: 100
        },
    };

    const paymentMessage = {
        'payment_type': {
            required: errorMessageSelectRequired
        },
        'payer_type': {
            required: errorMessageSelectRequired
        },
        'm_nation_id': {
            required: errorMessageSelectRequired
        },
        'payer_name': {
            required: errorMessagePaymentRequired,
            isFullwidthSpecial: errorMessagePaymentCharacterPayer,
            maxlength: errorMessagePaymentCharacterPayer
        },
        'payer_name_furigana': {
            required: errorMessagePaymentRequired,
            isValidFurigana: errorMessagePaymentCharacterPayerFurigana,
            maxlength: errorMessagePaymentCharacterPayerFurigana
        },
        'postal_code': {
            required: errorMessagePaymentRequired,
            isValidInfoPostalCode: errorMessagePaymentInfoPostalCode
        },
        'm_prefecture_id': {
            required: errorMessagePaymentRequired
        },
        'address_second': {
            required: errorMessagePaymentRequired,
            isValidInfoAddress: errorMessagePaymentInfoAddressFormat,
            maxlength: errorMessagePaymentInfoAddressFormat
        },
        'address_three': {
            isValidInfoAddress: errorMessagePaymentInfoAddressFormat,
            maxlength: errorMessagePaymentInfoAddressFormat
        },
    };
</script>
<script src="{{ asset('common/js/yubinbango.js') }}" charset="UTF-8"></script>
<script src="{{ asset('end-user/common/js/payer-info.js') }}" charset="UTF-8"></script>
