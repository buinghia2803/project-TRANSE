<!--payer-info -->
<h3>{{ __('labels.payer_info.title') }}</h3>
<ul class="r_c eol clearfix">
    <li><button type="button"  class="btn_a copyInfoContactOfUser">{{ __('labels.payer_info.copy_info_contact') }}</button></li>
    <li><button type="button" class="btn_a copyInfoMemberOfUser" >{{ __('labels.payer_info.copy_info_member') }}</button></li>
</ul>

<div class="border orange mb20">
    <h4 class="mb10">{{ __('labels.payer_info.title_h4') }} </h4>

    <dl class="w16em clearfix">

        <dt>{{ __('labels.payer_info.method_payment') }} <span class="red">*</span></dt>
        <dd>
            <ul class="r_c clearfix ul_payment_type">
                <li>
                    <label>
                        <input type="radio" name="payment_type" {{ old('payment_type') == 1 ? "selected" : "" }} @if(old('payment_type') == PAYMENT_TYPE_CREDIT) checked @endif value="{{ PAYMENT_TYPE_CREDIT }}" class="payment_type payment_type_credit" />{{ __('labels.payer_info.payment_type_credit') }}
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="payment_type" {{ old('payment_type') == 2 ? "selected" : "" }} @if(old('payment_type') == PAYMENT_TYPE_TRANSFER) checked @endif value="{{ PAYMENT_TYPE_TRANSFER }}" class="payment_type payment_type_transfer" />
                        <input type="hidden" name="cost_bank_transfer" value="{{ $paymentFee['cost_service_base'] ?? 0 }}">
                        {{ __('labels.payer_info.payment_type_transfer', ['payment_fee' => CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0)]) }}</label>
                </li>
            </ul>
            @error('payment_type') <div class="notice">{{ $message }}</div> @enderror
        </dd>
        <dt>{{ __('labels.payer_info.payer_type') }} <span class="red">*</span></dt>
        <dd>
            <ul class="r_c clearfix ul_payer_type">
                <li>
                    <label>
                        <input type="radio" name="payer_type" @if(old('payer_type') == PAYER_TYPE_TAX_AGENT) checked @endif value="{{ PAYER_TYPE_TAX_AGENT }}" class="payer_type payer_type_1"/>{{ __('labels.payer_info.payer_type_1') }}
                    </label>
                </li>
                <li>
                    <label>
                        <input type="radio" name="payer_type" @if(old('payer_type') == PAYER_TYPE_REGIS_ADDRESS_OVERSEAS) checked @endif value="{{ PAYER_TYPE_REGIS_ADDRESS_OVERSEAS }}" class="payer_type payer_type_2"/>{{ __('labels.payer_info.payer_type_2') }}
                    </label><br /></li>
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
                        <option value="{{ $k }}" {{ old('m_nation_id') == $k ? "selected" : "" }}>{{ $nation ?? '' }}</option>
                    @endforeach
                @endif
            </select>
            @error('m_nation_id') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_name') }}<span class="red">*</span></dt>
        <dd><input type="text" name="payer_name" value="{{ old('payer_name') }}" id="payer_name" class="remove_space_input" /><br />{{ __('labels.payer_info.info_name_note') }}</dd>
    </dl>
</div>

<dl class="w16em eol clearfix">
    <dt>{{ __('labels.payer_info.info_name_furigana') }}<span class="red">*</span></dt>
    <dd>
        <input type="text" name="payer_name_furigana" id="payer_name_furigana" class="remove_space_input" value="{{ old('payer_name_furigana') }}"/>
        @error('payer_name_furigana') <div class="notice">{{ $message }}</div> @enderror
    </dd>

    <!--showHideInfoAddress-->
    <div id="showHideInfoAddress" class="showHideInfoAddress h-adr">
        <dt>{{ __('labels.payer_info.info_postal_code') }} <span class="red">*</span></dt>
        <input type="hidden" class="p-country-name" value="Japan">
        <dd>
            <div class="wp_postal_code">
                <input type="text" name="postal_code" class="p-postal-code remove_space_input" value="{{ old('postal_code') }}" id="postal_code"/>
                <input type="button" class="postal_code_button" value="{{ __('labels.payer_info.info_postal_code_button') }} " class="btn_a" />
            </div>
            @error('postal_code') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_prefectures_id') }}<span class="red">*</span></dt>
        <dd>
            <input type="hidden" id="hiddenValuePrefecture" class="p-region" />
            <select name="m_prefecture_id" id="m_prefecture_id">
                @if (isset($prefectures) )
                    @foreach ($prefectures as $k => $item)
                        <option value="{{ $k }}" {{ old('m_prefecture_id') == $k ? "selected" : "" }}>{{ $item }}</option>
                    @endforeach
                @endif
            </select>
            @error('m_prefecture_id') <div class="notice">{{ $message }}</div> @enderror
        </dd>

        <dt>{{ __('labels.payer_info.info_address_second') }}<span class="red">*</span></dt>
        <dd>
            <input type="hidden" id="hiddenAddressSecond" class="p-locality" />
            <input type="text" name="address_second" id="address_second" value="{{ old('address_second') }}" class="em30 remove_space_input" /><br />
            @error('address_second') <div class="notice">{{ $message }}</div> @enderror
            <span class="input_note">{{ __('labels.payer_info.info_address_second_note') }}</span>
        </dd>
    </div>
    <dt>{{ __('labels.payer_info.info_address_three') }}</dt>
    <dd>
        <input type="text" name="address_three" id="address_three" value="{{ old('address_three') }}" class="em30 remove_space_input" /><br />
        @error('address_three') <div class="notice">{{ $message }}</div> @enderror
        <span class="input_note">{{ __('labels.payer_info.info_address_three_note') }}</span>
    </dd>
</dl>
<!--end payer-info -->

<script>
    let idOfJapan = $('#nation_japan_id').val();
    const errorMessagePaymentRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
    const errorMessagePaymentCharacterPayer = '{{ __('messages.common.errors.Common_E016') }}';
    const errorMessagePaymentCharacterPayerFurigana = '{{ __('messages.common.errors.Common_E018') }}';
    const errorMessagePaymentInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
    const errorMessagePaymentInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';

    const paymentRule = {
        'payment_type': {
            required: true
        },
        'payer_type': {
            required: true
        },
        'm_nation_id': {
            required: true
        },
        'payer_name': {
            required: true,
            isFullwidth: true,
            maxLength: 50,
        },
        'payer_name_furigana': {
            required: true,
            isFullwidth: true,
            maxLength: 50,
        },
        'postal_code': {
            required: () => {
                return $('#m_nation_id').val() == idOfJapan;
            },
            isValidInfoPostalCode: true
        },
        'm_prefecture_id': {
            required: () => {
                return $('#m_nation_id').val() == idOfJapan;
            }
        },
        'address_second': {
            required: () => {
                return $('#m_nation_id').val() == idOfJapan;
            },
            isValidInfoAddress: true
        },
        'address_three': {
            required: true,
            isValidInfoAddress: true
        },
    };

    const paymentMessage = {
        'payment_type': {
            required: errorMessagePaymentRequired
        },
        'payer_type': {
            required: errorMessagePaymentRequired
        },
        'm_nation_id': {
            required: errorMessagePaymentRequired
        },
        'payer_name': {
            required: errorMessagePaymentRequired,
            isFullwidth: errorMessagePaymentCharacterPayer,
            maxLength: errorMessagePaymentCharacterPayer
        },
        'payer_name_furigana': {
            required: errorMessagePaymentRequired,
            isFullwidth: errorMessagePaymentCharacterPayerFurigana,
            maxLength: errorMessagePaymentCharacterPayerFurigana
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
            isValidInfoAddress: errorMessagePaymentInfoAddressFormat
        },
        'address_three': {
            required: errorMessagePaymentRequired,
            isValidInfoAddress: errorMessagePaymentInfoAddressFormat
        },
    };
</script>
