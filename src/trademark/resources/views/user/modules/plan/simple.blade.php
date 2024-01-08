@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        @if ($comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SIMPLE01_OVER)
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

            <h2>{{ __('labels.plan.simple.title') }}</h2>

            <h3>{{ __('labels.plan.simple.text_1') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->

            <dl class="w20em clearfix middle">
                <dt>{{ __('labels.plan.simple.text_2') }}{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
                </dt>
                <dd>
                    <input type="button" value="特許庁からの通知書を見る" class="btn_b" id="openAllFileAttach" />
                </dd>
            </dl>
            <p>{{ __('labels.plan.simple.text_3') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}</p>
            <p class="eol">{{ __('labels.plan.simple.text_4') }}<br />
                {{ __('labels.plan.simple.text_5') }}<br />
                {{ __('labels.plan.simple.text_6') }}<br />
                {{ __('labels.plan.simple.text_7') }}<br />
                {{ __('labels.plan.simple.text_8') }}</p>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h3>{{ __('labels.plan.simple.text_9') }}</h3>
            <p>{{ __('labels.plan.simple.text_10', [
                'attr' => CommonHelper::formatPrice($costServiceBase + ($costServiceBase * $setting->value) / 100),
                'attr_prod_add' => CommonHelper::formatPrice($costServiceAddProd),
            ]) }}<br />
                {{ __('labels.plan.simple.text_11') }}</p>
            <table class="normal_b westimate mb20">
                <tr>
                    <th width="12%">{{ __('labels.plan.simple.text_12') }}</th>
                    <th>{{ __('labels.plan.simple.text_13') }}</th>
                </tr>
                @foreach ($dataProdAndDistinct as $keyCode => $products)
                    @foreach ($products as $keyItem => $prod)
                        <tr data-distinction-id="{{ $prod->m_distinction_id }}">
                            @if ($keyItem == 0)
                                <td rowspan="{{ $products->count() > 1 ? $products->count() : '' }}">
                                    {{ __('labels.plan.simple.name_distinct', ['attr' => $keyCode]) }}
                                </td>
                            @endif
                            <td class="boxes">{{ $prod->name }}</td>
                            <input type="hidden" name="prod_ids[]" value="{{ $prod->id }}">
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <th colspan="2" class="right">
                        {{ __('labels.plan.simple.count_product', ['attr' => count(array_unique($productIds))]) }}</th>
                </tr>
            </table>

            @if (
                (!$comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SIMPLE01_OVER) ||
                    $checkRoute == U201_SIMPLE01_ALERT ||
                    $checkRoute == U201_SIMPLE)
                <hr />
                <h3>{{ __('labels.plan.simple.text_14') }}</h3>
                @if ($comparisonTrademarkResult->checkResponseDeadlineAlert() && $checkRoute == U201_SIMPLE01_ALERT)
                    <p class="red">
                        {{ __('labels.plan.simple.text_23') }} <br>
                        {{ __('labels.plan.simple.text_29') }} <br>
                        {{ __('labels.plan.simple.text_30') }} <br>
                        {{ __('labels.plan.simple.text_31') }}
                    </p>
                @else
                    <p>{{ __('labels.plan.simple.text_15') }}</p>
                @endif
                <p><label><input type="checkbox" name="register_before_deadline" id="register_before_deadline"
                            value="1"
                            {{ isset($comparisonTrademarkResultDraft) && isset($comparisonTrademarkResultDraft->planCorrespondence) && $comparisonTrademarkResultDraft->planCorrespondence->is_ext_period == IS_EXT_PERIOD_TRUE ? 'checked' : '' }}>
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
                            data-submit="{{ REDIRECT_TO_COMMON_PAYMENT_SIMPLE }}" />
                    </li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" class="btn_a back_to_anken" value="{{ __('labels.plan.simple.text_21') }}"
                            data-submit="{{ REDIRECT_TO_ANKEN_TOP }}">
                    </li>
                    <li> <input type="submit" value="{{ __('labels.back') }}" class="btn_a"
                            data-submit="{{ BACK_URL }}"></li>
                </ul>
                <!-- estimate box -->
                @include('user.modules.plan.estimate-box.cart', [
                    'data_submit' => REDIRECT_TO_COMMON_PAYMENT_SIMPLE,
                    'quote' => REDIRECT_TO_QUOTE_SIMPLE,
                    'flag' => '',
                ])
                <!-- /estimate box -->
            @endif
            <input type="hidden" value="{{ $id }}" name="comparison_trademark_result_id">
            <input type="hidden" value="{{ $comparisonTrademarkResult->trademark_id }}" name="trademark_id">
            <input type="hidden" value="{{ $planCorrespondence->id ?? '' }}" name="plan_correspondence_id">
            <input type="hidden" value="" id="cost_service_base_input" name="cost_service_base">
            <input type="hidden" id="cost_service_add_prod_input" name="cost_service_add_prod" value="{{ $costServiceAddProd }}">
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
                value="{{ isset($comparisonTrademarkResultDraft) && isset($comparisonTrademarkResultDraft->trademark->payment) && isset($comparisonTrademarkResultDraft->trademark->payment->payerInfo) && $comparisonTrademarkResultDraft ? $comparisonTrademarkResultDraft->trademark->payment->payerInfo->id : '' }}"
                name="payer_info_id">
            <input type="hidden" value="" name="submit_type">
            @if ($comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SIMPLE01_OVER)
                <ul class="footerBtn">
                    <li>
                        <a href="{{ route('user.refusal.extension-period.over', ['id' => $comparisonTrademarkResult->trademark_id]) }}"
                            class="btn_b custom_btn" style="color: white">{{ __('labels.plan.simple.btn_over_1') }}</a>
                    </li>
                </ul>
                <ul class="footerBtn eol">
                    <li>
                        <a href="{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => $comparisonTrademarkResult->id]) }}"
                            class="btn_a custom_btn">{{ __('labels.plan.simple.btn_over_2') }}</a>
                    </li>
                </ul>
            @endif
        </form>
    </div>
    <!-- /contents -->
    <style>
        .custom_btn {
            padding: 5px 2em !important;
        }

        @media only screen and (max-width: 600px) {
            .back_to_anken {
                margin-bottom: 15px;
            }
        }
    </style>
@endsection
@section('footerSection')
    @if (
        (!$comparisonTrademarkResult->checkResponseDeadlineOver() && $checkRoute == U201_SIMPLE01_OVER) ||
            $checkRoute == U201_SIMPLE01_ALERT ||
            $checkRoute == U201_SIMPLE)
        <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
        <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    @endif
    <script type="text/javascript">
        const routeAjaxCalculatorCart = '{{ route('user.refusal.plans.ajax-caculator') }}';
        const dataCost = @json($data);
        const costRegisterBeforeDeadline = @json($costRegisterBeforeDeadline);
        const trademarkDocument = @json($comparisonTrademarkResult->trademark->trademarkDocuments);
        const simple01over = @json(U201_SIMPLE01_OVER);
        const nameScreen = [@json(U201_SIMPLE01_ALERT), @json(U201_SIMPLE)];
        const checkResponseDeadlineOver = @json($comparisonTrademarkResult->checkResponseDeadlineOver());
        const checkRoute = @json($checkRoute);
        const redirectToQuote = '{{ REDIRECT_TO_QUOTE_SIMPLE }}'
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/simple/simple-plan.js') }}"></script>
@endsection
