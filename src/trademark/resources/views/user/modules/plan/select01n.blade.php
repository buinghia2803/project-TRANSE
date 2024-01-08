@extends('user.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('admin.components.includes.messages')

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

            <dl class="w16em clearfix">
                <dt>
                    <h3><strong>{{ __('labels.plan.select.text_4') }}</strong></h3>
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
                {{ __('labels.plan.select.text_8') }}</p>
            <div style="overflow-x:auto;">
                <table class="normal_b westimate mb20" id="product_tbl">
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
                                <td class="boxes">{{ $prod->name }}</td>
                                <td>
                                    <ul class="r_c clearfix">
                                        @php
                                            if ($countReasonNo > 0) {
                                                $planCorrespondenceProd = isset($prod->appTrademarkProd->planCorrespondenceProd) ? $prod->appTrademarkProd->planCorrespondenceProd->where('round', $countReasonNo + 1)->first() : collect([]);
                                            } else {
                                                $planCorrespondenceProd = $prod->appTrademarkProd->planCorrespondenceProd;
                                            }
                                            $reasonRefNumProd = null;
                                            if (isset($planCorrespondenceProd) && $planCorrespondenceProd->count()) {
                                                $planCorrespondenceProd->load('reasonRefNumProds');
                                                $reasonRefNumProd = $planCorrespondenceProd->reasonRefNumProds->last();
                                            }
                                        @endphp
                                        @if (isset($prod->appTrademarkProd->planCorrespondenceProd) &&
                                                $prod->appTrademarkProd->planCorrespondenceProd->completed_evaluation == IS_REGISTER_FALSE)
                                            <li>
                                                <label>
                                                    <input type="radio" class="register_prod_rdo register_prod_apply is_register_prod_{{ $prod->id }}"
                                                        id="" name="is_register[{{ $prod->id }}]"
                                                        {{ isset($prod->appTrademarkProd) && $prod->appTrademarkProd->planCorrespondenceProd->is_register == 1 ? 'checked' : '' }}
                                                        value="1"
                                                        data-completed_evaluation="{{ $prod->appTrademarkProd->planCorrespondenceProd->completed_evaluation }}" />{{ __('labels.plan.select.text_13') }}</label>
                                            </li>
                                            <li><label>
                                                    <input type="radio" class="register_prod_rdo register_prod_apply is_register_prod_{{ $prod->id }}"
                                                        name="is_register[{{ $prod->id }}]"
                                                        {{ $prod->appTrademarkProd->planCorrespondenceProd->is_register == 0 ? 'checked' : '' }}
                                                        value="0"
                                                        data-completed_evaluation="{{ $prod->appTrademarkProd->planCorrespondenceProd->completed_evaluation }}" />{{ __('labels.plan.select.text_14') }}</label>
                                            </li>
                                        @else
                                            <li> {{ isset($reasonRefNumProd) && isset($reasonRefNumProd->rank) ? '申込済み：' . $reasonRefNumProd->rank : '' }}
                                            </li>
                                        @endif
                                    </ul>
                                </td>
                                <td class="bg_gray">
                                    <span class="fee_response_refusal" id="fee_response_refusal_{{ $prod->id }}">
                                        {{ isset($prod->appTrademarkProd->planCorrespondenceProd->reasonRefNumProd) &&
                                        $prod->appTrademarkProd->planCorrespondenceProd->reasonRefNumProd->rank == 'A'
                                            ? $possibilityRegistrationA
                                            : (isset($prod->appTrademarkProd->planCorrespondenceProd) &&
                                            $prod->appTrademarkProd->planCorrespondenceProd->completed_evaluation
                                                ? CommonHelper::formatPrice(floor($possibilityRegistrationOther))
                                                : $possibilityRegistrationA . '円または' . CommonHelper::formatPrice(floor($possibilityRegistrationOther))) }}円
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
            </div>
            <div class="arrow_flow"><img src="{{ asset('common/images/arrow_orange_b.png') }}" /></div>
            <table class="estimate">
                <tr>
                    <td class="left gray2">{{ __('labels.plan.select.text_18') }}<br /><span
                            class="note">{{ __('labels.plan.select.text_19') }}<br />
                            {{ __('labels.plan.select.text_20') }}</span></td>
                    <td width="22%" class="bg_gray2 gray2 center">0～<span id="money_take_report"></span>円
                    </td>
                </tr>
            </table>

            <p class="eol">{{ __('labels.plan.select.text_21') }}<br />
                {{ __('labels.plan.select.text_22') }}</p>
            <p class="eol">
                {{ __('labels.plan.select.text_23') }}
            </p>
            <hr />

            <h3>{{ __('labels.plan.simple.text_14') }}</h3>
            <p>{{ __('labels.plan.select.text_26') }}

            <p><label><input type="checkbox" name="is_ext_period_2" id="register_before_deadline" value="1"
                        {{ isset($comparisonTrademarkResultDraft) &&
                        isset($comparisonTrademarkResultDraft->planCorrespondence) &&
                        $comparisonTrademarkResultDraft->planCorrespondence->is_ext_period_2 == IS_EXT_PERIOD_TRUE
                            ? 'checked'
                            : '' }}>
                    {{ __('labels.plan.simple.text_16') }}
                    {{ CommonHelper::formatPrice(floor($costRegisterBeforeDeadline)) }}円</label></p>

            <table class="eol">
                <caption>{{ __('labels.plan.simple.text_22') }}</caption>
                <tr>
                    <th>{{ __('labels.plan.simple.text_17') }}</th>
                    <td class="right"> {{ CommonHelper::formatPrice(floor($costRegisterBeforeDeadline)) }}円</td>
                </tr>
                <tr>
                    <th>{{ __('labels.plan.simple.text_18') }}</th>
                    <td class="right">
                        {{ CommonHelper::formatPrice(floor($costRegisterBeforeDeadlineBase->pof_1st_distinction_5yrs)) }}円</td>
                </tr>
                <tr>
                    <th class="red">{{ __('labels.plan.simple.text_19') }}</th>
                    <td class="red right">-{{ CommonHelper::formatPrice(floor($priceDiscount)) }}円</td>
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
                'payerInfo' => $paymentDraft->payerInfo ?? '',
            ])
            <hr />
            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_PAYMENT_SELECT_01N }}" value="この内容で申込みに進む"
                        class="btn_e big" />
                </li>
            </ul>

            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ REDIRECT_TO_ANKEN_TOP_SELECT_01_N }}" value="保存して案件トップへ戻る"
                        class="btn_a back_to_anken" /></li>
                <li>
                    <input type="button" value="{{ __('labels.back') }}" class="btn_a" data-back>
                </li>
            </ul>
            <!-- estimate box -->
            @include('user.modules.plan.estimate-box.cart', [
                'data_submit' => REDIRECT_TO_COMMON_PAYMENT_SELECT_01N,
                'quote' => REDIRECT_TO_QUOTE_SELECT_01N,
                'flag' => PLAG_SELECT_01,
            ])
            <!-- /estimate box -->

            <input type="hidden" value="{{ $id }}" name="comparison_trademark_result_id">
            <input type="hidden" value="{{ $comparisonTrademarkResult->trademark_id }}" name="trademark_id">
            <input type="hidden" value="{{ $planCorrespondence->id ?? '' }}" name="plan_correspondence_id">
            <input type="hidden" value="" id="cost_service_base_input" name="cost_service_base">
            <input type="hidden" value="{{ floor($costServiceAddProd) }}" id="cost_service_add_prod_input" name="cost_service_add_prod" >
            <input type="hidden" value="" id="cost_bank_transfer_input" name="cost_bank_transfer">
            <input type="hidden" value="" id="extension_of_period_before_expiry_input"
                name="extension_of_period_before_expiry">
            <input type="hidden" value="" id="application_discount_input" name="application_discount">
            <input type="hidden" value="" id="subtotal_input" name="subtotal">
            <input type="hidden" value="" id="commission_input" name="commission">
            <input type="hidden" value="" id="tax_input" name="tax">
            <input type="hidden" value="" id="print_fee_input" name="print_fee">
            <input type="hidden" value="" id="total_amount_input" name="total_amount">
            <input type="hidden"
                value="{{ isset($paymentDraft->payerInfo) && $paymentDraft ? $paymentDraft->payerInfo->id : '' }}"
                name="payer_info_id">
            <input type="hidden" value="{{ $countReasonNo }}" name="count_reason_no">
            <input type="hidden" value="{{ $reasonNo->id ?? '' }}" name="reason_no_id">
            <input type="hidden" value="" name="submit_type">
        </form>

    </div><!-- /contents -->
    <style>
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
        const labelPriceProduct = '{{ __('labels.plan.select.text_27') }}';
        const dataCost = @json($data);
        const prodIds = @json(array_unique($productIds));
        const comparison_trademark_result_id = @json($id);
        const flag = @json($flag);
        const costServiceAddProd = @json($costServiceAddProd);
        const costRegisterBeforeDeadline = @json($costRegisterBeforeDeadline);
        const trademarkDocument = @json($comparisonTrademarkResult->trademark->trademarkDocuments);
        const checkResponseDeadlineOver = false;
        const nameScreen = [@json(U201_SELECT_01_N)];
        const checkRoute = @json(U201_SELECT_01_N);
        const redirectToQuote = '{{ REDIRECT_TO_QUOTE_SELECT_01N }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/simple/simple-plan.js') }}"></script>
@endsection
