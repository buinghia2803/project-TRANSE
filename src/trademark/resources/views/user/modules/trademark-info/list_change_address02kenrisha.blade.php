@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('compoments.messages')
        <h2>{{ __('labels.list_change_address.title_kenrisha') }}</h2>
        <form action="{{ route('user.app-list.create-payment', ['id' => $id]) }}" method="POST" id="form">
            @csrf
            <h3>{{ __('labels.list_change_address.text_1') }}</h3>
            <div class="info">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->
            <hr />
            <p>
                {{ __('labels.list_change_address.text_23') }}{{ __('labels.list_change_address.text_2') }}<a
                    href="{{ route('user.qa.02.qa') }}"
                    target="_blank">{{ __('labels.list_change_address.text_3') }}</a>{{ __('labels.list_change_address.text_4') }}
            </p>
            <table class="normal_b mb-1">
                <tr>
                    <th></th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_24') }}</th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_6') }}</th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_25') }}</th>
                </tr>
                @if (!empty($registerTrademark->trademark_info_name))
                    @php
                        $payment = $changeInfoRegisterDraft->payment ?? null;
                        $typeChangeValue = 0;
                        if (!empty($payment) && ($payment->cost_change_name != null && $payment->cost_change_address == null)) {
                            $typeChangeValue = $typeChangeName;
                        } elseif (!empty($payment) && ($payment->cost_change_name == null && $payment->cost_change_address != null)) {
                            $typeChangeValue = $typeChangeAddress;
                        } elseif (!empty($payment) && ($payment->cost_change_name != null && $payment->cost_change_address != null)) {
                            $typeChangeValue = $typeChangeDouble;
                        }
                    @endphp
                    <tr>
                        <td>{{ $registerTrademark->trademark_info_name ?? '' }}</td>
                        <td class="center"><input type="radio" name="type_change" class="type_change" value="{{ $typeChangeName }}"
                            {{ $typeChangeValue == $typeChangeName ? 'checked' : '' }}
                        ></td>
                        <td class="center"><input type="radio" name="type_change" class="type_change" value="{{ $typeChangeAddress }}"
                            {{ $typeChangeValue == $typeChangeAddress ? 'checked' : '' }}
                        ></td>
                        <td class="center"><input type="radio" name="type_change" class="type_change" value="{{ $typeChangeDouble }}"
                            {{ $typeChangeValue == $typeChangeDouble ? 'checked' : '' }}
                        ></td>
                    </tr>
                @endif
            </table>
            <div class="error_type_change"></div>
            <p class="mt-1">{{ __('labels.list_change_address.text_8') }}</p>
            <div  style="display: none" id="table-change-address">
                <h3 class="title-change-address">{{ __('labels.list_change_address.text_9') }}</h3>
                <table class="normal_b eol" >
                    <tr>
                        <th class="center">{{ __('labels.list_change_address.text_12') }}</th>
                        <th class="center">{{ __('labels.list_change_address.text_13') }}</th>
                    </tr>
                    <tr>
                        <td>
                            <strong>
                                {{ __('labels.list_change_address.text_31') }}<span>{{ $registerTrademark->prefecture->name ?? '' }}</span><span>{{ isset($registerTrademark->trademark_info_address_second) ? $registerTrademark->trademark_info_address_second : '' }}</span><span>{{ isset($registerTrademark->trademark_info_address_three) ? $registerTrademark->trademark_info_address_three : '' }}</span>
                            </strong><br />
                            {{ __('labels.list_change_address.text_15') }} <span
                                class="input_note">{{ __('labels.list_change_address.text_16') }}</span><br />
                            {{ __('labels.list_change_address.text_17') }} <span class="red">*</span>：
                            <select name="change_info_register_m_nation_id" id="trademark_infos_m_nation_id" class="mb-2">
                                @foreach ($nations as $k => $nation)
                                    <option
                                        value="{{ $k }}"
                                        {{ $changeInfoRegisterDraft && $changeInfoRegisterDraft->m_nation_id && $changeInfoRegisterDraft->m_nation_id == $k ? 'selected' : '' }}>
                                        {{ $nation }}</option>
                                @endforeach
                            </select><br />
                            <div id="hidden_prefectures"
                                style="{{ isset($userInfo) && $userInfo->info_nation_id != 1 ? 'display: none' : '' }}">
                                {{ __('labels.list_change_address.address_1') }} <span class="red">*</span>：
                                <select name="change_info_register_m_prefectures_id" id="m_prefectures_id" class="mb-2">
    
                                    @foreach ($prefectures as $k => $prefecture)
                                        <option
                                            value="{{ $k }}"
                                            {{ $changeInfoRegisterDraft && $changeInfoRegisterDraft->m_prefectures_id && $changeInfoRegisterDraft->m_prefectures_id == $k ? 'selected' : ''}}>
                                            {{ $prefecture }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mb-1">
                                    {{ __('labels.list_change_address.address_2') }} <span class="red">*</span>：<input
                                        type="text" name="trademark_infos_address_second" id="address_second_2" class="em30"
                                        value="{{ $changeInfoRegisterDraft->address_second ?? ''}}" />
                                </div>
                            </div>
                            <div class="mb-1">
                                {{ __('labels.list_change_address.address_3') }}<input type="text"
                                    name="trademark_infos_address_three" id="trademark_infos_address_three" class="em30"
                                    value="{{ $changeInfoRegisterDraft->address_three ?? '' }}" />
                            </div>
                            <div id="hidden_change_address_free">
                                <label><input type="checkbox" name="is_change_address_free" value="{{ $isChangeAddressFreeTrue }}" {{ $changeInfoRegisterDraft && $changeInfoRegisterDraft->is_change_address_free == $isChangeAddressFreeTrue ? 'checked' : '' }} id="is_change_address_free">{{ __('labels.list_change_address.text_26') }}</label>
                            </div>
                            {{-- @endif --}}
                        </td>
                        <td class="right"><span
                                id="price_change_address">{{ CommonHelper::formatPrice($priceServiceChangeAddress->base_price ?? 0) }}</span>円
                        </td>
                        <input type="hidden" id="base_address_change_name"
                            value="{{ CommonHelper::formatPrice($priceServiceChangeAddress->base_price ?? 0) }}">
                    </tr>
                </table>
            </div>
            <div style="display: none" id="table-change-name">
                <h3 class="title-change-name-regis">{{ __('labels.list_change_address.text_32') }}</h3>
                <table class="normal_b eol">
                    <tr>
                        <th style="width:79%;" class="center">{{ __('labels.list_change_address.text_19') }}</th>
                        <th class="center">{{ __('labels.list_change_address.text_20') }}</th>
                    </tr>
                    <tr>
                        <td><strong>{{ __('labels.list_change_address.text_33') }}<span
                                    id="name_change">{{ $registerTrademark->trademark_info_name ?? '' }}</span></strong><br />
                            {{ __('labels.list_change_address.text_22') }}<br /><input type="text" class="em30"
                                name="name" id="name"
                                value="{{ isset($changeInfoRegisterDraft) ? $changeInfoRegisterDraft->name : '' }}" /></td>
                        <td class="right">{{ CommonHelper::formatPrice($priceServiceChangeName->base_price ?? 0) }}円</td>
                    </tr>
                </table>
            </div>

            <div id="hidden_represetative_name">
                <hr />
                <h3>{{ __('labels.list_change_address.text_27') }}</h3>
                <p>{{ __('labels.list_change_address.text_28') }}<br />
                    {{ __('labels.list_change_address.text_29') }}</p>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.list_change_address.text_30') }} <span class="red">*</span></dt>
                    <dd><input type="text" name="representative_name" value="{{ $changeInfoRegisterDraft->representative_name ?? null }}"/></dd>
                </dl>
                <hr />
            </div>

            {{-- Payer info --}}
            @include('user.components.payer-info', [
                'prefectures' => $prefectures ?? [],
                'nations' => $nations ?? [],
                'paymentFee' => [
                    'cost_service_base' => $costBankTransfer ?? '',
                ],
                'payerInfo' => $changeInfoRegisterDraft->payment->payerInfo ?? null,
            ])
            {{-- End Payer info --}}

            <!-- estimate box -->
            @include('user.modules.trademark-info.includes.cart-kenrisha', [
                'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
                'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
                'setting' => $setting,
                'payment' => null,
                'basePriceServiceChangeAddress' => $priceServiceChangeAddress,
                'basePriceServiceChangeName' => $priceServiceChangeName,
                'baseCostBankTransfer' => $costBankTransferBase,
                'tradeMark' => $tradeMark,
                'changeInfoRegisterDraft' => $changeInfoRegisterDraft,
                'isChangeAddressFreeTrue' => $isChangeAddressFreeTrue,
            ])
            <hr />

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="この内容で申込む" class="btn_e big"
                        data-submit="{{ REDIRECT_TO_COMMON_PAYMENT_KENRISHA }}" /></li>
            </ul>

            {{-- Input hidden --}}
            <input type="hidden" name="cost_change_name"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->cost_change_name : '' }}"
                id="cost_change_name">
            <input type="hidden" name="cost_change_address"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->cost_change_address : '' }}"
                id="cost_change_address">
            <input type="hidden" name="cost_bank_transfer_input" id="cost_bank_transfer_input"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->cost_bank_transfer : '' }}">
            <input type="hidden" name="commission"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->commission : '' }}"
                id="commission">
            <input type="hidden" name="tax_input"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->tax : '' }}"
                id="tax_input">
            <input type="hidden" name="cost_print_name_input"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->cost_print_name : '' }}"
                id="cost_print_name_input">
            <input type="hidden" name="cost_print_address_input"
                value="{{ isset($changeInfoRegisterDraft) && isset($changeInfoRegisterDraft->payment) ? $changeInfoRegisterDraft->payment->cost_print_address : '' }}"
                id="cost_print_address_input">
            <input type="hidden" name="from_page" value="{{ U000LIST_CHANGE_ADDRESS_02_KENRISHA }}">
            <input type="hidden" name="submit_type" value="">
        </form>

    </div>
    <!-- /contents -->
@endsection
@section('script')
    <script>
        const routeGetInfoUserAjax = '{{ route('user.get-info-user-ajax') }}';
        const routeGetTradeMarkInfo = '{{ route('user.get-trademark-info-ajax') }}';
        const typeChangeName = {{ $typeChangeName }};
        const typeChangeAddress = {{ $typeChangeAddress }};
        const typeChangeDouble = {{ $typeChangeDouble }};
        const costBankTransfer = @json($costBankTransfer);
        const setting = @json($setting);
        const changeInfoRegisterDraft = @json($changeInfoRegisterDraft->payment ?? '');
        const errorMessageRequiredTypeChange = '{{ __('messages.common.errors.Common_E025') }}';
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageFormat = '{{ __('messages.common.errors.Common_E020') }}';
        const errorMessageFormatName = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageFormatRespresentativeName = '{{ __('messages.common.errors.Common_E010') }}';
        const rules = {};
        const messages = {};
        const idNationJP = @json(NATION_JAPAN_ID);
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/trademark_info/list_change_address02.js') }}"></script>
@endsection
