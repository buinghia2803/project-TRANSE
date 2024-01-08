@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        @include('admin.components.includes.messages')

        <form id="form" action="{{ route('user.refusal.select-eval-report-save', ['id' => request()->__get('id')]) }}" method="POST">
            @csrf

            @if (in_array($trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $trademark,
                ])
            @endif

            <h2>{{ __('labels.plan_select02.title') }}</h2>

            <h3>{{ __('labels.plan_select02.trademark_info') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable
                ])
            </div><!-- /info -->

            <dl class="w20em clearfix middle">
                <div class="d-flex f-wrap">
                    <div>
                        {{ __('labels.plan_select02.date_sending') }}
                    </div>
                    <div>
                        {{ Carbon\Carbon::parse($data->sending_noti_rejection_date)->format('Y年m月d日') }}
                    </div>
                </div>
                <dt>
                    <a class="btn_b" id="openAllFileAttach" href="javascript:void(0)">
                        {{ __('labels.plan_select02.notice_frm_PO') }}
                    </a>
                </dt>
            </dl>
            <div class="mt-1 mb20 d-flex f-wrap">
                <div>
                    {{ __('labels.plan_select02.due_date') }}
                </div>
                <div>
                    {{ Carbon\Carbon::parse($data->response_deadline)->format('Y年m月d日') }}
                </div>
            </div>

            @if (!in_array($trademark->block_by, [OVER_03, ALERT_01, OVER_04, OVER_04B, OVER_05]))
                <p class="red">{{ __('labels.plan_select02.des_1') }}<br>
                    {{ __('labels.plan_select02.des_2') }}</p>
                <p class="eol">
                    {{-- Goto u210alert02 --}}
                    <a class="btn_b" href="{{ route('user.refusal.extension-period.alert', ['id' => $trademark->id]) }}">
                        {{ __('labels.plan_select02.goto_u210alert02') }}
                    </a>
                </p>
            @endif

            @if (in_array($trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $trademark,
                ])
            @endif

            <dl class="w16em eol clearfix">
                <dt>
                    <h3><strong>{{ __('labels.plan_select02.deadline_response_title') }}</strong></h3>
                </dt>
                <dd>
                    <h3><strong>{{ !empty($reasonNo->response_deadline) ? Carbon\Carbon::parse($reasonNo->response_deadline)->format('Y年m月d日') : '' }}</strong></h3>
                </dd>
            </dl>

            <h3>{{ __('labels.plan_select02.possibility_eval_report') }}</h3>

            <p>{{ __('labels.plan_select02.des_3') }}<br>
                {{ __('labels.plan_select02.des_4') }}<br>
                {{ __('labels.plan_select02.des_5') }}</p>


            <p class="">{{ __('labels.plan_select02.comment_frm_PO') }}
                @foreach ($reasonRefNumProds as $item)
                    <div>
                        第{{ $item->planCorrespondenceProd->appTrademarkProd->mProduct->mDistinction->name }}類：{{ $item->planCorrespondenceProd->appTrademarkProd->mProduct->name}}　{{ $item->comment_patent_agent ?? '' }}
                    </div>
                @endforeach
            </p>

            <ul class="rank mb10">
                <li><span>A</span>{{ __('labels.plan_select02.des_6') }}</li>
                <li><span>B</span>{{ __('labels.plan_select02.des_7') }}</li>
                <li><span>C</span>{{ __('labels.plan_select02.des_8') }}<br>
                    {{ __('labels.plan_select02.des_9') }}</li>
                <li><span>D</span>{{ __('labels.plan_select02.des_10') }}</li>
                <li><span>E</span>{{ __('labels.plan_select02.des_11') }}
                    {{ __('labels.plan_select02.des_12') }}</li>
            </ul>
            <p class="mb10">{{ __('labels.plan_select02.des_13') }}<br>
                {{ __('labels.plan_select02.des_14') }}<br>
                {{ __('labels.plan_select02.des_15') }}<br>
                {{ __('labels.plan_select02.des_16') }}</p>

            <div class="choose_prod_refusal mb20">
                <table class="normal_b westimate w-100" id="product_tbl">
                    <thead>
                        <tr>
                            <th width="10%">{{ __('labels.plan_select02.division') }}</th>
                            <th>{{ __('labels.plan_select02.product_name') }}</th>
                            <th width="20%">{{ __('labels.plan_select02.registration_possibility') }}</th>
                            <th width="42%">{{ __('labels.plan_select02.response_notice_refusal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($distinctions as $keyCode => $planCorresProds)
                            @foreach ($planCorresProds as $keyItem => $planCorresProd)
                                <tr>
                                    @if ($keyItem == 0)
                                        <td rowspan="{{ count($planCorresProds) }}" class="left">{{ $keyCode }}</td>
                                    @endif
                                    <td>{{ $planCorresProd->appTrademarkProd->mProduct->name ?? '' }}</td>
                                    <td class="center">
                                        <span>
                                            @if ($planCorresProd->is_register > 0 && $planCorresProd->round <= $maxReasonNo)
                                                {{ $planCorresProd->reasonRefNumProd->rank ?? '' }}
                                            @else
                                                ー
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <span>
                                            @if ($planCorresProd->is_register > 0 && $planCorresProd->round <= $maxReasonNo)
                                                <ul class="r_c clearfix">
                                                    <li>
                                                        <label class="lb_choice">
                                                            <input
                                                                data-rank="{{ $planCorresProd->reasonRefNumProd->rank ?? '' }}"
                                                                type="radio"
                                                                class="is_choice"
                                                                value="1"
                                                                    {{ isset($planCorresProd->reasonRefNumProd->is_choice) && $planCorresProd->reasonRefNumProd->is_choice ? 'checked' : '' }}
                                                                name="is_choice[{{ $planCorresProd->reasonRefNumProd->id ?? '' }}]"
                                                                checked="checked"
                                                            >
                                                            {{ __('labels.plan_select02.apply') }}（
                                                            <span class="price_rank">
                                                                {{ isset($planCorresProd->reasonRefNumProd) && $planCorresProd->reasonRefNumProd ? $planCorresProd->reasonRefNumProd->priceWithRank(...[$selectPlanAPrice, $selectPlanBCDEPrice, $setting]) : 0 }}
                                                            </span>円）
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input
                                                                data-rank="{{ $planCorresProd->reasonRefNumProd->rank ?? '' }}"
                                                                type="radio"
                                                                class="is_not_choice"
                                                                value="0"
                                                                {{ isset($planCorresProd->reasonRefNumProd->is_choice) && $planCorresProd->reasonRefNumProd->is_choice == 0 ? 'checked' : '' }}
                                                                name="is_choice[{{ $planCorresProd->reasonRefNumProd->id ?? '' }}]"
                                                            >{{ __('labels.plan_select02.unnecessary') }}
                                                        </label>
                                                    </li>
                                                </ul>
                                            @else
                                                ー
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total_choose_prod_refusal" >
                <table class="normal_b w-100 mb20">
                    <tbody>
                        <tr>
                            <td>{{ __('labels.plan_select02.service_response') }}</td>
                            <td class="right">
                                <span id="prod_count">0</span>件
                            </td>
                            <td class="right">
                                <span id="total_prod_price">0</span>円
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p>{{ __('labels.plan_select02.des_17') }}</p>

            <div class="payer-info-box">
                <hr>

                {{-- Payer info --}}
                @include('user.components.payer-info', [
                    'prefectures' => $prefectures ?? [],
                    'nations' => $nations ?? [],
                    'paymentFee' => $paymentFee ?? null,
                    'payerInfo' => $payerInfo ?? null,
                ])
                {{-- End Payer info --}}
            </div>

            <hr>
            @if(isset($productIsNotRegister) && $productIsNotRegister > 0)
                @if ($planCorrespondence && $planCorrespondence->type == App\Models\PlanCorrespondence::TYPE_SELECTION)
                {{-- ToDo --}}
                    <ul class="footerBtn clearfix">
                        <li style="margin-bottom:1em;">
                            <a style="background: #39a03e; color:#ffffff;padding: 5px 2em;" href="{{ route('user.refusal.plans.select-eval-report-re', [
                                'comparison_trademark_result_id' => request()->__get('id'),
                                'reason_no_id' => Request::get('reason_no_id')
                                ]) }}">
                                {{ __('labels.plan_select02.goto_u201select01n') }}
                            </a>
                        </li>
                    </ul>
                @endif
            @endif
            <ul class="footerBtn clearfix">
                <li style="margin-bottom:1em;">
                    <button type="submit" class="btn_e btn_submit_form"> {{ __('labels.plan_select02.submit') }} </button>
                </li>
            </ul>
            <ul class="footerBtn clearfix">
                <li>
                    <a style="padding:0;text-align:center;line-height:38px;height: 38px; width:293px" class="btn_a" href="{{ route('user.refusal.notification.cancel', ['comparison_trademark_result_id' => request()->__get('id')] )}}">
                            {{ __('labels.plan_select02.cancel') }}
                    </a>
                </li>
            </ul>
            <ul class="footerBtn clearfix">
            <li>
                <a style="padding:0;text-align:center;line-height:38px;height: 38px; width:112px" class="btn_a" data-back>{{ __('labels.back') }}</a>
            </ul>

            <!-- estimate box -->
            <div class="estimateBox">
                <input type="checkbox" id="cart"><label class="button" for="cart"><span class="open">{{ __('labels.plan_select02.view_quote') }}</span><span
                        class="close">{{ __('labels.plan_select02.close_quote') }}</span></label>

                <div class="estimateContents">

                    <h3>{{ __('labels.plan_select02.est_total_title') }}</h3>
                    <table class="normal_b">
                        <tbody>
                            <tr>
                                <td>{{ __('labels.plan_select02.service_response') }}<br>{{ __('labels.plan_select02.A_rating') }}
                                    <span id="est_box_prod_count_A">0</span>
                                    件x
                                    <span id="base_price_select_plan_A">{{ CommonHelper::formatPrice($selectPlanAPrice['base_price'] + $selectPlanAPrice['base_price'] * $setting->value/100 ?? 0 ) }}</span>
                                    円）</td>
                                <td class="right">
                                    <span id="select_plan_A_price">
                                        0
                                    </span>円
                                </td>
                            </tr>
                            <tr>
                                <td>{{ __('labels.plan_select02.service_response') }}<br>{{ __('labels.plan_select02.B_rating') }}
                                    <span id="est_box_prod_count_B_E">0</span>件x
                                    <span id="base_price_select_plan_B_E">{{ CommonHelper::formatPrice($selectPlanBCDEPrice['base_price'] + $selectPlanBCDEPrice['base_price'] * $setting->value/100 ?? 0 ) }}</span>円）
                                </td>
                                <td class="right">
                                    <span id="select_plan_B_E_price">0</span>円
                                </td>
                            </tr>
                            <tr class="cost_bank_transfer_tr d-none">
                                <td class="em16">{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                                <td class="right">
                                    <span id="cost_bank_transfer_span">{{ CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0 )}}</span>円
                                </td>
                            </tr>
                            <tr>
                                <th class="right">{{ __('labels.plan_select02.total') }}</th>
                                <th class="right" nowrap=""><strong style="font-size:1.2em;">
                                    <span id="sub_total_text">0</span>
                                    円</strong></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="right">
                                    <span class="breakdown-real-fee d-none">
                                        {{ __('labels.support_first_times.cart.breakdown_real_fee') }}
                                        <span id="commission">0</span>円
                                        <br />
                                        <span class="consumption_tax">
                                            {{ __('labels.support_first_times.cart.consumption_tax') }}（{{ floor($setting->value*100)/100 ?? 0 }}%)
                                            <span id="tax">0</span>円
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    <p class="red mb10">{{ __('labels.plan_select02.des_17') }}</p>

                    <ul class="right list">
                        <li><button type="button" class="btn_a">{{ __('labels.plan_select02.recalculation') }}</button></li>
                    </ul>

                    <ul class="right list">
                        <li><button type="submit" id="redirect_to_quote" class="btn_a">{{ __('labels.plan_select02.go_to_quotes') }}</button></li>
                    </ul>

                    <ul class="footerBtn right clearfix">
                        <li><button type="submit" class="btn_e big btn_payment">{{ __('labels.plan_select02.submit') }}</button></li>
                    </ul>

                    <input type="hidden" name="redirect_to" value="{{ App\Models\SupportFirstTime::REDIRECT_TO_COMMON_PAYMENT }}">
                    <input type="hidden" name="id" value="{{ request()->__get('id') }}">
                    <input type="hidden" name="from_page" id="from_page" value="{{ U201SELECT02 .'_'. request()->__get('reason_no_id')  }}">
                    <input type="hidden" name="subtotal" id="subtotal" value="0">
                    <input type="hidden" name="commission" id="commission_val" value="0">
                    <input type="hidden" name="tax" id="tax_val" value="0">
                    <input type="hidden" name="choose_a_count" id="chooseACount" value="0">
                    <input type="hidden" name="choose_b_e_count" id="chooseBECount" value="0">
                    <input type="hidden" name="cost_service_base" id="cost_service_base" value="{{ $selectPlanAPrice['base_price'] + $selectPlanAPrice['base_price'] * $setting->value/100 ?? 0  }}">
                    <input type="hidden" name="cost_service_add_prod" id="cost_service_add_prod" value="{{ $selectPlanBCDEPrice['base_price'] + $selectPlanBCDEPrice['base_price'] * $setting->value/100 ?? 0  }}">
                    <input type="hidden" name="trademark_id" id="trademark_id" value="{{ $trademark->id }}">
                </div><!-- /estimate contents -->
            </div>
            <!-- /estimate box -->
        </form>
    </div>
    <style>
        .d-flex {
            display: flex;
        }
        .f-wrap {
            flex-wrap: wrap;
        }
        #openAllFileAttach {
            padding: 0;
            width: 220px;
            height: 35px;
            line-height: 35px;
            text-align: center;
        }
        .btn_submit_form {
            padding: 0 !important;
            height: 38px;
            width: 347px;
            line-height: 38px;
            text-align: center;
        }
        .btn_payment {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        .d-none {
            display: none;
        }
        .d-block {
            display: block;
        }
        .d-content {
            display: contents;
        }
    </style>
@endsection
@section('footerSection')
<script type="text/JavaScript">
    const errorMessageCommon_E025 = '{{ __('messages.general.Common_E025') }}';
    const setting = @json($setting);
    const selectPlanAPrice = @json($selectPlanAPrice);
    const selectPlanBCDEPrice = @json($selectPlanBCDEPrice);
    const paymentFee = @json($paymentFee);
    const distinctions = @json($distinctions);
    const statusRegister = @json($statusRegister);
    const trademarkDocument = @json($trademarkDoc);
    const routeTop = '{{ route('user.top') }}';
    const constQuote = 'QUOTE';
</script>
<script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
<script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
<script type="text/JavaScript" src="{{ asset('end-user/select/select02.js') }}"></script>
@if($isBlockScreen)
    <script>disabledScreen()</script>
@endif
@endsection
