@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('admin.components.includes.messages')
        @if ($comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SELECT01_OVER)
            <p class="alertBox"><strong>
                    {{ __('labels.plan.simple.text_24') }} <br>
                    {{ __('labels.plan.simple.text_25') }} <br>
                    {{ __('labels.plan.simple.text_26') }} <br>
                    {{ __('labels.plan.simple.text_27') }}
                </strong><br />
                <a href="{{ route('user.refusal.extension-period.over', ['id' => $comparisonTrademarkResult->trademark->id]) }}"
                    class="btn_b">
                    {{ __('labels.plan.simple.text_28') }}
                </a>
            </p>
        @endif

        <form action="{{ route('user.refusal.plans.create-comparison') }}" method="POST" id="form">
            @csrf

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h2>{{ __('labels.plan.select.title') }}</h2>

            <h3>{{ __('labels.plan.select.text_1') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div><!-- /info -->
            <dl class="w20em clearfix middle">
                <dt>{{ __('labels.plan.select.text_2') }}　{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
                </dt>
                <dd>
                    <input type="button" value="特許庁からの通知書を見る" class="btn_b" id="openAllFileAttach" />
                </dd>
            </dl>
            <p>{{ __('labels.plan.select.text_3') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}</p>

            <dl style="display: flex;">
                <dt>
                    <h3 style="margin-right: 10px;"><strong>{{ __('labels.plan.select.text_4') }}</strong></h3>
                </dt>
                <dd>
                    <h3><strong>{{ $comparisonTrademarkResult->parseUserResponseDeadline() }}</strong></h3>
                </dd>
            </dl>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h3>{{ __('labels.plan.select.text_5') }}</h3>
            <p>{{ __('labels.plan.select.text_6') }}<br />
                {{ __('labels.plan.select.text_7', [
                    'attr' => CommonHelper::formatPrice($costServiceBase + ($costServiceBase * $setting->value) / 100),
                    'attr_prod_add' => CommonHelper::formatPrice($costServiceAddProd),
                ]) }}<br />
                {{ __('labels.plan.select.text_8') }}</p>
            <div style="overflow-x:auto;" class="product-container">
                <table class="normal_b westimate" id="product_tbl">
                    <tr>
                        <th width="12%">{{ __('labels.plan.select.text_9') }}</th>
                        <th width="600px">{{ __('labels.plan.select.text_10') }}</th>
                        <th width="28%">{{ __('labels.plan.select.text_11') }}</th>
                        <th width="22%">{{ __('labels.plan.select.text_12') }}</th>
                    </tr>
                    @foreach ($dataProdAndDistinct as $keyCode => $products)
                        @foreach ($products as $keyItem => $prod)
                            <tr>
                                @if ($keyItem == 0)
                                    <td rowspan="{{ $products->count() > 1 ? $products->count() : '' }}">
                                        {{ __('labels.plan.simple.name_distinct', ['attr' => $keyCode]) }}
                                    </td>
                                @endif
                                <td class="boxes">{{ $prod->mProduct->name }}</td>
                                <td>
                                    <ul class="r_c clearfix">
                                        <li>
                                            <label>
                                                <input
                                                    type="radio"
                                                    class="register_prod_rdo register_prod_apply is_register_prod_{{ $prod->mProduct->id }}" id=""
                                                    name="is_register[{{ $prod->mProduct->id }}]" value="1"
                                                    {{ isset($prod->planCorrespondenceProd) && $prod->planCorrespondenceProd->is_register == IS_REGISTER_TRUE ? 'checked' : ( !$prod->planCorrespondenceProd ?'checked': '') }}
                                                />

                                                {{ __('labels.plan.select.text_13') }}
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input
                                                    type="radio"
                                                    class="register_prod_rdo register_prod_not_apply is_register_prod_{{ $prod->mProduct->id }}"
                                                    name="is_register[{{ $prod->mProduct->id }}]" value="0"
                                                    {{ isset($prod->planCorrespondenceProd) && $prod->planCorrespondenceProd->is_register == IS_REGISTER_FALSE ? 'checked' : '' }}
                                                />
                                                {{ __('labels.plan.select.text_14') }}
                                            </label>
                                        </li>
                                    </ul>
                                </td>
                                <td class="bg_gray">
                                    <span class="fee_response_refusal" id="fee_response_refusal_{{ $prod->mProduct->id }}">
                                        {{ __('labels.plan.select.text_15', ['attr' => CommonHelper::formatPrice($costServiceAddProd)]) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <th colspan="2" class="left">
                            {{ __('labels.plan.select.text_16') }}<br />{{ __('labels.plan.select.text_17') }} <span
                                class="amount_product"></span> {{ __('labels.plan.select.text_24') }}
                        </th>
                        <th class="right middle"><strong style="font-size:1.2em;"><span id="number_payment"></span>
                                円</strong>
                        </th>
                        <td class="bg_gray"></td>
                    </tr>
                </table>
                <div class="mb20"></div>
            </div>
            <div class="arrow_flow"><img src="{{ asset('common/images/arrow_orange_b.png') }}" /></div>
            <table class="estimate">
                <tr>
                    <td class="left gray2">{{ __('labels.plan.select.text_18') }}<br /><span
                            class="note">{{ __('labels.plan.select.text_19') }}<br />
                            {{ __('labels.plan.select.text_20') }}</span></td>
                    <td width="22%" class="bg_gray2 gray2 center">0～ <span id="money_take_report"></span> 円</td>
                </tr>
            </table>
            <p class="eol">{{ __('labels.plan.select.text_21') }}<br />
                {{ __('labels.plan.select.text_22') }}</p>

            <p class="eol">
                {{ __('labels.plan.select.text_23') }}
            </p>
            @if (
                (!$comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SELECT01_OVER) ||
                    $checkRoute == U201_SELECT01_ALERT ||
                    $checkRoute == U201_SELECT_01)
                <hr />
                <h3>{{ __('labels.plan.simple.text_14') }}</h3>
                @if ($comparisonTrademarkResult->checkResponseDeadlineAlert() && $checkRoute == U201_SELECT01_ALERT)
                    <p class="red">
                        {{ __('labels.plan.simple.text_23') }} <br>
                        {{ __('labels.plan.simple.text_29') }} <br>
                        {{ __('labels.plan.simple.text_30') }} <br>
                        {{ __('labels.plan.simple.text_31') }}
                    </p>
                @else
                    <p>{{ __('labels.plan.simple.text_15') }}</p>
                @endif
                <p><label><input type="checkbox" name="is_ext_period_2" id="register_before_deadline" value="1"
                            {{ isset($comparisonTrademarkResultDraft) &&
                            isset($comparisonTrademarkResultDraft->planCorrespondence) &&
                            $comparisonTrademarkResultDraft->planCorrespondence->is_ext_period_2 == IS_EXT_PERIOD_TRUE
                                ? 'checked'
                                : '' }}>
                        {{ __('labels.plan.simple.text_16') }}
                        {{ CommonHelper::formatPrice($costRegisterBeforeDeadline) }}円</label></p>

                <table class="eol">
                    <caption>{{ __('labels.plan.simple.text_22') }}</caption>
                    <tr>
                        <th>{{ __('labels.plan.simple.text_17') }}</th>
                        <td class="right"> {{ CommonHelper::formatPrice($costRegisterBeforeDeadline) }}円</td>
                    </tr>
                    <tr>
                        <th>{{ __('labels.plan.simple.text_18') }}</th>
                        <td class="right">
                            {{ CommonHelper::formatPrice($costRegisterBeforeDeadlineBase->pof_1st_distinction_5yrs) }}円
                        </td>
                    </tr>
                    <tr>
                        <th class="red">{{ __('labels.plan.simple.text_19') }}</th>
                        <td class="red right">-{{ CommonHelper::formatPrice($priceDiscount) }}円</td>
                    </tr>
                </table>
                <hr />

                {{-- Payer Info --}}
                @include('user.components.payer-info', [
                    'prefectures' => $prefectures ?? [],
                    'nations' => $nations ?? [],
                    'paymentFee' => [
                        'cost_service_base' => $costBankTransfer ?? '',
                    ],
                    'payerInfo' => $comparisonTrademarkResultDraft->trademark->payment->payerInfo ?? '',
                ])

                <ul class="footerBtn clearfix">
                    <li><input type="submit" value="{{ __('labels.plan.simple.text_20') }}" class="btn_e big"
                            data-submit="{{ REDIRECT_TO_COMMON_PAYMENT_SELECT }}" />
                    </li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="submit" class="btn_a back_to_anken" value="{{ __('labels.plan.simple.text_21') }}"
                            data-submit="{{ REDIRECT_TO_ANKEN_TOP_SELECT }}">
                    </li>
                    <li>
                        <input type="submit" value="{{ __('labels.back') }}" class="btn_a" data-submit="{{ BACK_URL }}">
                    </li>
                </ul>

                @include('user.modules.plan.estimate-box.cart', [
                    'data_submit' => REDIRECT_TO_COMMON_PAYMENT_SELECT,
                    'quote' => REDIRECT_TO_QUOTE_SELECT,
                    'flag' => PLAG_SELECT_01,
                ])
            @endif

            @if ($comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SELECT01_OVER)
                <ul class="footerBtn">
                    <li>
                        <input type="submit" class="btn_b custom_btn" value="{{ __('labels.plan.simple.btn_over_1') }}"
                               data-submit="{{ U210_OVER_02 }}">
                    </li>
                </ul>
                <ul class="footerBtn eol">
                    <li>
                        <a href="{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}"
                           class="btn_a custom_btn">{{ __('labels.plan.simple.btn_over_2') }}</a>
                    </li>
                </ul>
            @endif

            <input type="hidden" value="{{ $id }}" name="comparison_trademark_result_id">
            <input type="hidden" value="{{ $comparisonTrademarkResult->trademark_id }}" name="trademark_id">
            <input type="hidden" value="" id="cost_service_base_input" name="cost_service_base">
            <input type="hidden" value="{{ floor($costServiceAddProd) }}" id="cost_service_add_prod_input" name="cost_service_add_prod">
            <input type="hidden" value="" id="cost_bank_transfer_input" name="cost_bank_transfer">
            <input type="hidden" value="" id="extension_of_period_before_expiry_input" name="extension_of_period_before_expiry">
            <input type="hidden" value="" id="application_discount_input" name="application_discount">
            <input type="hidden" value="" id="subtotal_input" name="subtotal">
            <input type="hidden" value="" id="commission_input" name="commission">
            <input type="hidden" value="" id="tax_input" name="tax">
            <input type="hidden" value="" id="print_fee_input" name="print_fee">
            <input type="hidden" value="" id="total_amount_input" name="total_amount">
            <input type="hidden" value="{{ $planCorrespondence->id ?? ''}}" name="plan_correspondence_id">
            <input type="hidden" name="payer_info_id"
                value="{{ isset($comparisonTrademarkResultDraft) && isset($comparisonTrademarkResultDraft->trademark->payment) && isset($comparisonTrademarkResultDraft->trademark->payment->payerInfo) && $comparisonTrademarkResultDraft ? $comparisonTrademarkResultDraft->trademark->payment->payerInfo->id : '' }}">
            <input type="hidden" value="" name="submit_type">
        </form>
    </div>
    <!-- /contents -->
    <style>
        .custom_btn {
            padding: 5px 2em !important;
        }

        @media only screen and (max-width: 600px) {
            #openAllFileAttach {
                margin-left: 10px;
            }

            table.estimate {
                max-width: none !important;
            }

            .back_to_anken {
                margin-bottom: 15px;
            }
        }
    </style>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/javascript">
        const routeAjaxCalculatorCart = '{{ route('user.refusal.plans.ajax-caculator') }}';
        const MessageRegisterDateNull = '{{ __('messages.plan.register_date_null') }}';
        const labelPriceProduct = '{{ __('labels.plan.select.text_27') }}';
        const dataCost = @json($data);
        const prodIds = @json(array_unique($productIds));
        const comparison_trademark_result_id = @json($id);
        const costServiceAddProd = @json($costServiceAddProd);
        const costRegisterBeforeDeadline = @json($costRegisterBeforeDeadline);
        const trademarkDocument = @json($comparisonTrademarkResult->trademark->trademarkDocuments);
        const flag = 'u201select01';
        const select01over = @json(U201_SELECT01_OVER);
        const nameScreen = [@json(U201_SELECT01_ALERT), @json(U201_SELECT_01)];
        const checkResponseDeadlineOver = @json($comparisonTrademarkResult->checkResponseDeadlineOver());
        const checkRoute = @json($checkRoute);
        const redirectToQuote = '{{ REDIRECT_TO_QUOTE_SELECT }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/simple/simple-plan.js') }}"></script>
@endsection
