@extends('user.layouts.app')

@section('main-content')
    <div id="contents" class="normal">
        @include('compoments.messages')

        <h2>{{ __('labels.u402tsuino.title') }}</h2>

        <form id="form" action="{{ route('user.update.notify-procedure.post', $registerTrademark->id) }}" method="post">
            @csrf
            <input type="hidden" name="new_register_trademark" value="{{ $nextRegisterTrademark->id ?? '' }}">
            <input type="hidden" name="id_register_trademark_choice" value="">
            <input type="hidden" name="submit_type" value="">
            <input type="hidden" name="from_page" value="{{ U402TSUINO }}">

            <h3>{{ __('labels.form_trademark_information.title') }}<br>
                @if(now() > $dateUpdateNext6Month)
                    <span class="red">
                        {{ __('labels.u402.expired_text') }}<br>
                        <a href="{{ route('user.apply-trademark-free-input', ['name' => $trademark->name_trademark]) }}" class="no_disabled">{{ __('labels.u402.expired_link_2') }}</a>
                    </span>
                @endif
            </h3>

            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable
            ])

            <p class="blue">
                {{ __('labels.u402tsuino.desc_1') }}<br>
                {{ __('labels.u402tsuino.desc_2') }}
            </p>

            <dl class="w08em eol clearfix">
                <dt>{{ __('labels.u302_402_5yr_kouki.deadline_update') }}</dt>
                <dd>{{ Carbon\Carbon::parse($registerTrademark->deadline_update)->addMonths(6)->format('Y年m月d日') }}</dd>
            </dl>

            <hr>

            <h3>{{ __('labels.u402.product_title') }}</h3>
            <p>
                {{ __('labels.u402.product_desc_1') }}<br>
                {{ __('labels.u402.product_desc_2') }}
            </p>
            <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
                <table class="normal_b westimate" id="product-table">
                    <tbody>
                    <tr>
                        <th class="em08">{{ __('labels.distinction_name') }}</th>
                        <th>{{ __('labels.review_product_name') }}</th>
                        <th class="em04">{{ __('labels.u402.select_prod') }}</th>
                    </tr>
                    @foreach($registerTrademarkProds as $distinctionName => $registerTrademarkProd)
                        @foreach($registerTrademarkProd as $prod)
                            <tr class="product-item" data-prod_id="{{ $prod->id }}">
                                @if($loop->first)
                                    <td rowspan="{{ count($registerTrademarkProd) }}" class="distinction-item">
                                        {{ __('labels.name_distinct', ['attr' => $distinctionName ?? '']) }}
                                        <br>
                                        {{ __('labels.u302_402_5yr_kouki.product_total', ['v' => count($registerTrademarkProd)]) }}
                                    </td>
                                @endif
                                <td>{{ $prod->appTrademarkProd->mProduct->name ?? '' }}</td>
                                @if($loop->first)
                                    <td rowspan="{{ count($registerTrademarkProd) }}" class="center">
                                        <input type="checkbox"
                                               name="distinctions[{{ $prod->appTrademarkProd->mProduct->m_distinction_id }}]"
                                               @if(empty($nextProdIsApply)) checked
                                               @else {{ ($nextProdIsApply[$prod->app_trademark_prod_id] ?? null) == 1 ? 'checked' : '' }}
                                               @endif
                                        >
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="3" class="right">{{ __('labels.u402.distinction_total') }}<span class="total_distinction"></span></td>
                    </tr>
                    <tr>
                        <th colspan="2" class="right">特許庁費用（<span class="total_distinction"></span>区分）　更新期間
                            <select name="period_registration">
                                @foreach($periodRegistrations as $key => $periodRegistration)
                                    <option value="{{ $key ?? '' }}"
                                            @if(!empty($nextRegisterTrademark) && $nextRegisterTrademark->period_registration == $key) selected @endif
                                    >{{ $periodRegistration ?? '' }}</option>
                                @endforeach
                            </select>
                        </th>
                        <td style="width:15%;" class="right cost_year_one_distinction_fee">
                            <div class="price"></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <hr>

            <p>{{ __('labels.u402.info_desc_1') }}</p>

            <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
                <table class="normal_b mw480 mb15" style="max-width: 480px;">
                    <tbody>
                    @foreach($registerTrademarks as $registerTrademark)
                        <tr>
                            <th class="left" style="width:6em;">
                                {{ __('labels.u302_402_5yr_kouki.trademark_info_name') }}
                                {{ $loop->iteration != 1 ? '-' . $loop->iteration : '' }}
                            </th>
                            <td>{{ $registerTrademark->trademark_info_name ?? '' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <p>
                {{ __('labels.u302_402_5yr_kouki.info_desc_2') }}
                {{ __('labels.u302_402_5yr_kouki.info_desc_3') }}
                {{ __('labels.u302_402_5yr_kouki.info_desc_4') }}<a href="{{ route('user.qa.02.qa') }}" target="_blank">Q&A</a>
                {{ __('labels.u302_402_5yr_kouki.info_desc_5') }}
            </p>

            <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
                <table class="normal_b mw480" style="max-width: 480px;">
                    <tbody>
                    <tr>
                        <th></th>
                        <th class="center" style="width:6em;">{{ __('labels.u302_402_5yr_kouki.change_name') }}</th>
                        <th class="center" style="width:6em;">{{ __('labels.u302_402_5yr_kouki.change_address') }}</th>
                        <th class="center" style="width:6em;">{{ __('labels.u302_402_5yr_kouki.change_both_v2') }}</th>
                    </tr>
                    @foreach($registerTrademarks as $registerTrademark)
                        @php
                            $trademarkTypeChange = null;
                            if(!empty($nextRegisterTrademark) && $nextRegisterTrademark->id_register_trademark_choice == $registerTrademark->id) {
                                $trademarkTypeChange = $nextRegisterTrademark->getTypeChange();
                            }
                        @endphp
                        <tr>
                            <td>{{ $registerTrademark->trademark_info_name ?? '' }}</td>
                            <td class="center">
                                <input
                                    type="radio"
                                    name="type_change"
                                    class="type_change"
                                    value="{{ $typeChangeName }}"
                                    data-id="{{ $registerTrademark->id }}"
                                    {{ ($trademarkTypeChange == $typeChangeName) ? 'checked' : '' }}
                                >
                            </td>
                            <td class="center">
                                <input
                                    type="radio"
                                    name="type_change"
                                    class="type_change"
                                    value="{{ $typeChangeAddress }}"
                                    data-id="{{ $registerTrademark->id }}"
                                    {{ ($trademarkTypeChange == $typeChangeAddress) ? 'checked' : '' }}
                                >
                            </td>
                            <td class="center">
                                <input
                                    type="radio"
                                    name="type_change"
                                    class="type_change"
                                    value="{{ $typeChangeDouble }}"
                                    data-id="{{ $registerTrademark->id }}"
                                    {{ ($trademarkTypeChange == $typeChangeDouble) ? 'checked' : '' }}
                                >
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <input type="button" class="btn_d small clear_trademark_info" value="{{ __('labels.clear') }}">
            </div>

            <div class="change-address hidden">
                <h3>{{ __('labels.u302_402_5yr_kouki.change_address_title') }}</h3>
                <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
                    <table class="normal_b mw640 eol" id="table-change-address" style="max-width: 900px;">
                        <tbody>
                        <tr>
                            <th style="width:79%;" class="center">{{ __('labels.u302_402_5yr_kouki.change_address_label') }}</th>
                            <th class="center">{{ __('labels.u302_402_5yr_kouki.change_address_price') }}</th>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('labels.u302_402_5yr_kouki.trademark_info_address') }}：<span class="trademark_info_address"></span></strong><br>
                                {{ __('labels.u302_402_5yr_kouki.change_address_note_title') }}<span class="input_note">{{ __('labels.u302_402_5yr_kouki.change_address_note') }}</span><br>

                                <div class="group-form">
                                    {{ __('labels.u302_402_5yr_kouki.trademark_info_nation_id') }} <span class="red">*</span>：
                                    <select name="trademark_info_nation_id">
                                        @foreach ($nations as $k => $nation)
                                            <option value="{{ $k }}">{{ $nation }}</option>
                                        @endforeach
                                    </select><br>
                                </div>

                                <div id="hidden_prefectures">
                                    <div class="group-form">
                                        {{ __('labels.u302_402_5yr_kouki.trademark_info_address_first') }} <span class="red">*</span>：
                                        <select name="trademark_info_address_first">
                                            @foreach ($prefectures as $k => $prefecture)
                                                <option value="{{ $k }}">{{ $prefecture }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="group-form">
                                        {{ __('labels.u302_402_5yr_kouki.trademark_info_address_second') }} <span class="red">*</span>：
                                        <input type="text" class="em30" name="trademark_info_address_second" nospace>
                                    </div>
                                </div>

                                <div class="group-form">
                                    {{ __('labels.u302_402_5yr_kouki.trademark_info_address_three') }}<input type="text" class="em30" name="trademark_info_address_three" nospace>
                                </div>

                                <label>
                                    <input type="checkbox" name="is_change_address_free"
                                        {{ isset($nextRegisterTrademark) && $nextRegisterTrademark->is_change_address_free ? 'checked' : '' }}
                                    >
                                    {{ __('labels.u302_402_5yr_kouki.is_change_address_free') }}
                                </label>
                            </td>
                            <td class="right">{{ \CommonHelper::formatPrice($priceData['priceServiceChangeAddressFee'], '円') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="change-name hidden">
                <h3>{{ __('labels.u402.change_name_title') }}</h3>
                <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
                    <table class="normal_b mw640 eol" id="table-change-name" style="max-width: 640px;">
                        <tbody>
                        <tr>
                            <th style="width:79%;" class="center">{{ __('labels.u302_402_5yr_kouki.change_name_label') }}</th>
                            <th class="center">{{ __('labels.u302_402_5yr_kouki.change_name_price') }}</th>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ __('labels.u302_402_5yr_kouki.trademark_info_name_input') }}<span class="trademark_info_name"></span></strong><br>
                                {{ __('labels.u302_402_5yr_kouki.trademark_info_name_desc') }}<br>
                                <input type="text" class="em30" name="trademark_info_name" value="">
                            </td>
                            <td class="right">{{ \CommonHelper::formatPrice($priceData['priceServiceChangeNameFee'], '円') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr>

            <div class="representative_name_bock">
                <h3>{{ __('labels.u302_402_5yr_kouki.representative_name_title') }}</h3>
                <p>
                    <span class="representative_name_desc_1">{{ __('labels.u302_402_5yr_kouki.representative_name_desc_1') }}</span><br>
                    <span class="representative_name_desc_2">{{ __('labels.u302_402_5yr_kouki.representative_name_desc_2') }}</span>
                </p>
                <dl class="w16em clearfix">
                    <dt>{{ __('labels.u302_402_5yr_kouki.representative_name') }} <span class="red">*</span></dt>
                    <dd><input type="text" name="representative_name" value="{{ $nextRegisterTrademark->representative_name ?? '' }}" nospace></dd>
                </dl>
                <input type="hidden" name="info_type_acc" value="{{ $nextRegisterTrademark->info_type_acc ?? '' }}">

                <hr>
            </div>

            {{-- Payer info --}}
            @include('user.components.payer-info', [
                'prefectures' => $prefectures ?? [],
                'nations' => $nations ?? [],
                'paymentFee' => [
                    'cost_service_base' => $priceData['priceBankTransferFee'] ?? '',
                ],
                'payerInfo' => $payment->payerInfo ?? null,
                'disabledBankTransfer' => $isDisableBankTransfer ?? false,
            ])

            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_PAYMENT }}" value="{{ ($isPackC == true) ? __('labels.u302_402_5yr_kouki.goto_common_payment_pack_c') : __('labels.u302_402_5yr_kouki.goto_common_payment') }}" class="btn_e big"></li>
            </ul>

            <p class="eol">
                <a data-submit="{{ REDIRECT_TO_KAKUNIN }}" href="#" class="go_to_kakumin">{{ __('labels.u302_402_5yr_kouki.goto_confirm') }}</a>
            </p>

            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ REDIRECT_TO_ANKEN_TOP }}" value="{{ __('labels.u302_402_5yr_kouki.goto_ankentop') }}" class="btn_a"></li>
            </ul>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit"
                           data-back="{{ route('user.register-trademark.cancel-trademark', $registerTrademark->id) }}"
                           value="{{ __('labels.u402.goto_cancel') }}" class="btn_a">
                </li>
            </ul>

            @include('user.modules.register-trademark.partials.cart-u402', [
                'priceData' => $priceData,
            ])
        </form>
    </div>
@endsection

@section('script')
    <script>
        const registerTrademarks = @json($registerTrademarks);
        const nextRegisterTrademark = @json($nextRegisterTrademark);
        const idNationJP = @json(NATION_JAPAN_ID);
        const TYPE_ACC_COMPANY = {{ \App\Models\RegisterTrademark::TYPE_ACC_COMPANY }};
        const setting = @json($setting);
        const priceData = @json($priceData);
        const prefectureData = @json($prefectures);
        const BANK_TRANSFER = '{{ \App\Models\Payment::BANK_TRANSFER }}';
        const CREDIT_CARD = '{{ \App\Models\Payment::CREDIT_CARD }}';
        const isDisableBankTransfer = @json($isDisableBankTransfer ?? false);

        const PERIOD_REGISTRATION_5_YEAR = {{ \App\Models\RegisterTrademark::PERIOD_REGISTRATION_5_YEAR }};
        const PERIOD_REGISTRATION_10_YEAR = {{ \App\Models\RegisterTrademark::PERIOD_REGISTRATION_10_YEAR }};

        const typeChangeName = {{ $typeChangeName }};
        const typeChangeAddress = {{ $typeChangeAddress }};
        const typeChangeDouble = {{ $typeChangeDouble }};

        const errorMessageRequiredTypeChange = '{{ __('messages.common.errors.Common_E025') }}';
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageFormat = '{{ __('messages.common.errors.Common_E020') }}';
        const errorMessageFormatName = '{{ __('messages.common.errors.Common_E016') }}';
        const errorMessageFormatRespresentativeName = '{{ __('messages.common.errors.Common_E010') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/register-trademark/update_notify_procedure.js') }}"></script>
    @if($isBlock || now() < $dateUpdateCurrent || now() > $dateUpdateNext6Month)
        <script>disabledScreen();</script>
    @endif
@endsection
