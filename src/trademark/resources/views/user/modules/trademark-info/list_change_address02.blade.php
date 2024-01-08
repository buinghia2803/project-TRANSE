@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('compoments.messages')
        <h2>{{ __('labels.list_change_address.title') }}</h2>
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
            <table class="normal_b">
                <tr>
                    <th></th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_5') }}</th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_6') }}</th>
                    <th class="center" style="width:6em;">{{ __('labels.list_change_address.text_7') }}</th>
                </tr>
                @if ($tradeMarkInfos)
                    @foreach ($tradeMarkInfos as $item)
                        @php
                            $payment = $changeInfoRegisterDraft->payment ?? null;
                            $typeChangeValue = 0;
                            if (isset($changeInfoRegisterDraft) && $changeInfoRegisterDraft->trademark_info_id == $item->id) {
                                if (!empty($payment) && ($payment->cost_change_name != null && $payment->cost_change_address == null)) {
                                    $typeChangeValue = $typeChangeName;
                                } elseif (!empty($payment) && ($payment->cost_change_name == null && $payment->cost_change_address != null)) {
                                    $typeChangeValue = $typeChangeAddress;
                                } elseif (!empty($payment) && ($payment->cost_change_name != null && $payment->cost_change_address != null)) {
                                    $typeChangeValue = $typeChangeDouble;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td class="center"><input type="radio" name="type_change" class="type_change"
                                value="{{ $typeChangeName }}"
                                data-id-trademark-info="{{ $item->id }}"
                                {{ $typeChangeValue == $typeChangeName ? 'checked' : '' }}
                            ></td>
                            <td class="center"><input type="radio" name="type_change" class="type_change"
                                value="{{ $typeChangeAddress }}"
                                data-id-trademark-info="{{ $item->id }}"
                                {{ $typeChangeValue == $typeChangeAddress ? 'checked' : '' }}
                            ></td>
                            <td class="center"><input type="radio" name="type_change" class="type_change"
                                value="{{ $typeChangeDouble }}"
                                data-id-trademark-info="{{ $item->id }}"
                                {{ $typeChangeValue == $typeChangeDouble ? 'checked' : '' }}
                            ></td>
                        </tr>
                    @endforeach
                @endif
            </table>
            <input type="button" class="btn_d small mb15 clear_trademark_info" value="{{ __('labels.clear') }}">
            <div class="error_type_change"></div>
            <p>{{ __('labels.list_change_address.text_8') }}</p>
            <h3 class="title-change-address">{{ __('labels.list_change_address.text_9') }}</h3>
            <table class="normal_b eol" id="table-change-address" style="display: none">
                <tr>
                    <th class="center">{{ __('labels.list_change_address.text_12') }}</th>
                    <th class="center">{{ __('labels.list_change_address.text_13') }}</th>
                </tr>
                <tr>
                    <td>
                        <strong>
                            {{ __('labels.list_change_address.text_14') }}<span id="m_prefectures_id"></span><span id="address_second_2"></span><span
                                id="address_three"></span>
                        </strong><br />
                        {{ __('labels.list_change_address.text_15') }} <span
                            class="input_note">{{ __('labels.list_change_address.text_16') }}</span><br />
                        {{ __('labels.list_change_address.text_17') }} <span class="red">*</span>：
                        <select name="change_info_register_m_nation_id" id="trademark_infos_m_nation_id" class="mb-2">
                            @foreach ($nations as $k => $nation)
                                <option
                                    value="{{ isset($changeInfoRegisterDraft) ? $changeInfoRegisterDraft->m_nation_id : $k }}"
                                    {{ isset($userInfo) && $userInfo['info_nation_id'] == $k ? 'selected' : '' }}>
                                    {{ $nation }}</option>
                            @endforeach
                        </select><br />
                        {{-- @if ($userInfo->info_nation_id == 1) --}}
                        <div id="hidden_prefectures"
                            style="{{ isset($userInfo) && $userInfo->info_nation_id != 1 ? 'display: none' : '' }}">
                            {{ __('labels.list_change_address.address_1') }} <span class="red">*</span>：
                            <select name="change_info_register_m_prefectures_id" id="m_prefectures_id" class="mb-2">
                                @foreach ($prefectures as $k => $prefecture)
                                    <option
                                        value="{{ isset($changeInfoRegisterDraft) ? $changeInfoRegisterDraft->m_prefectures_id : $k }}"
                                        {{ isset($userInfo) &&
                                        isset($userInfo->nation) &&
                                        isset($userInfo->nation->prefecture) &&
                                        $userInfo->nation->prefecture->id == $k
                                            ? 'selected'
                                            : '' }}>
                                        {{ $prefecture }}
                                    </option>
                                @endforeach
                            </select>
                            <div>
                                {{ __('labels.list_change_address.address_2') }} <span class="red">*</span>：<input
                                    type="text" name="trademark_infos_address_second" id="address_second_2"
                                    class="em30 mb-2"
                                    value="{{ $changeInfoRegisterDraft->address_second ?? $userInfo->contact_address_second }}" />
                            </div>
                        </div>
                        <div>
                            {{ __('labels.list_change_address.address_3') }}<input type="text"
                                name="trademark_infos_address_three" id="trademark_infos_address_three"
                                class="em30 mb-2"
                                value="{{ $changeInfoRegisterDraft->address_three ?? $userInfo->contact_address_three }}" />
                        </div>
                        {{-- @endif --}}
                    </td>
                    <td class="right">{{ CommonHelper::formatPrice($priceServiceChangeAddress->base_price ?? 0) }}円</td>
                </tr>
            </table>
            <h3 class="title-change-name-regis">{{ __('labels.list_change_address.text_18') }}</h3>
            <table class="normal_b eol" id="table-change-name" style="display: none">
                <tr>
                    <th style="width:79%;" class="center">{{ __('labels.list_change_address.text_19') }}</th>
                    <th class="center">{{ __('labels.list_change_address.text_20') }}</th>
                </tr>
                <tr>
                    <td><strong>{{ __('labels.list_change_address.text_21') }}<span id="name_change"></span></strong><br />
                        {{ __('labels.list_change_address.text_22') }}<br /><input type="text" class="em30"
                            name="name" id="name"
                            value="{{ isset($changeInfoRegisterDraft) ? $changeInfoRegisterDraft->name : '' }}" /></td>
                    <td class="right">{{ CommonHelper::formatPrice($priceServiceChangeName->base_price ?? 0) }}円</td>
                </tr>
            </table>
            <hr />
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
            @include('user.modules.trademark-info.includes.cart', [
                'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
                'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
                'setting' => $setting,
                'payment' => null,
                'basePriceServiceChangeAddress' => $priceServiceChangeAddress,
                'basePriceServiceChangeName' => $priceServiceChangeName,
                'baseCostBankTransfer' => $costBankTransferBase,
            ])
            <hr />

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="この内容で申込む" class="btn_e big"
                        data-submit="{{ REDIRECT_TO_COMMON_PAYMENT }}" /></li>
            </ul>

            {{-- Input hidden --}}
            <input type="hidden" name="trademark_info_id" value="">
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
            <input type="hidden" name="from_page" value="{{ U000LIST_CHANGE_ADDRESS_02 }}">
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
        const redirectToQuote = '{{ REDIRECT_TO_COMMON_QUOTE }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/trademark_info/list_change_address02.js') }}"></script>
@endsection
