@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    <h2>{{ __('labels.refusal_plans.u203b02.title') }}</h2>
    <form id="choose_plan__confirm" method="POST">
        @csrf
        @include('admin.components.includes.messages')
        <p class="eol">{{ __('labels.refusal_plans.u203b02.title_2') }}<br />
            {{ __('labels.refusal_plans.u203b02.title_3') }}</p>

        @foreach ($trademarkPlan->plans as $keyPlan => $plan)
            <h3>{{ __('labels.refusal_plans.u203.key_plan', ['attr' => $keyPlan + 1]) }}
                <span>
                    {{ $plan->reasons->unique('id')->pluck('reason_name')->implode(',') ?? '' }}
                </span>
                {{ __('labels.refusal_plans.u203.reason') }}
            </h3>
            <div class="container__plan_correspondence">
                <table class="normal_b westimate mb20 planCorrespondenceTbl">
                    <tr>
                        <th>{{ __('labels.refusal_plans.u203.is_choice') }}</th>
                        <th style="width:50%;">{{ __('labels.refusal_plans.u203.plan_description') }}</th>
                        <th>{{ __('labels.refusal_plans.u203.possibility_resolution_1') }}<br />{{ __('labels.refusal_plans.u203.possibility_resolution_2') }}
                        </th>
                        <th style="width:25%;">{{ __('labels.refusal_plans.u203.m_type_plan_doc_name') }}</th>
                        <th style="width:10%;" class="{{ $keyPlan ? 'd-none': '' }} hidden_m_distinct" id="">
                            {{ __('labels.refusal_plans.u203.m_distinct_1') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_2') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_3') }}
                        </th>
                        <th class="{{ $keyPlan ? 'd-none': '' }} hidden_m_distinct">
                            {{ __('labels.refusal_plans.u203.m_distinct_4') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_5') }}<br />{{ __('labels.refusal_plans.u203.m_distinct_6') }}
                        </th>
                    </tr>
                    @php
                        if(!$keyPlan) {
                            $firstPlanDetail = $plan->planDetails->where('is_choice', IS_CHOICE)->first();
                        }
                    @endphp
                    @foreach ($plan->planDetails as $keyPlanDetail => $planDetail)
                        @if ($planDetail->is_choice == IS_CHOICE)
                            @php
                                $planDetailDistincts = $planDetail->planDetailDistincts->filter(function ($item) {
                                    $planDetailProducts = $item->planDetailProducts;
                                    $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                                        \App\Models\PlanDetailProduct::LEAVE_STATUS_7,
                                        \App\Models\PlanDetailProduct::LEAVE_STATUS_3,
                                    ]);
                                    $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);
                                    return $planDetailProductIsDeleted->count() == 0 && $planDetailProductNotAdd->count() == 0;
                                });

                                $planDetailDistinctUnique = $planDetailDistincts
                                    ->where('is_add', IS_ADD_TRUE)
                                    ->pluck('mDistinction.name')
                                    ->unique();
                                $distinctionsAddOn = $planDetailDistincts
                                    ->where('is_add', IS_ADD_TRUE)
                                    ->where('is_distinct_settlement', IS_DISTINCT_SETTLEMENT)
                                    ->pluck('mDistinction.name')
                                    ->unique();
                            @endphp
                            <tr>
                                <th>
                                    {{ __('labels.refusal_plans.u203.content_7') }} ({{ $keyPlanDetail + 1 }})
                                </th>
                                <td>
                                    <div class="plan_detail_description">
                                        {{ $planDetail->plan_description }}
                                    </div>
                                </td>
                                <td class="center td_revolution" data-possibility_resolution="{{ $planDetail->possibility_resolution }}"> {{ $planDetail->getTextRevolution() }}</td>
                                <td>
                                    <div class="doc_type_name">
                                        @php
                                            $conditionShowing = in_array($planDetail->type_plan_id, [2, 4, 5, 7, 8]);
                                        @endphp
                                        @if ($conditionShowing)
                                            @foreach ($planDetail->mTypePlan->mTypePlanDocs as $mTypeDoc)
                                                @if ($mTypeDoc->url)
                                                    <div class="mb-2">
                                                        <a href="{{ url($mTypeDoc->url) }}" target="_blank" rel="noopener noreferrer">
                                                            {{ $mTypeDoc->name }}
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="mb-2">
                                                        {{ $mTypeDoc->name }}
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            {{ __('labels.refusal_plans.u203c.none') }}
                                        @endif
                                    </div>
                                </td>
                                <td class="{{ $keyPlan ? 'd-none': '' }} hidden_m_distinct">
                                    @if ($planDetailDistinctUnique->count())
                                        @foreach ($planDetailDistinctUnique as $name)
                                                {{ __('labels.refusal_plans.u203.content_8', ['attr' => $name]) }}<br>
                                        @endforeach
                                    @else
                                        {{ __('labels.refusal_plans.u203c.no_payment_type') }}
                                    @endif
                                </td>
                                <td class="{{ $keyPlan ? 'd-none': '' }} hidden_m_distinct number_distinct_{{ $planDetail->id }}">
                                    @if ($distinctionsAddOn->count())
                                        {{ $distinctionsAddOn->count() }}類
                                    @else
                                        {{ __('labels.refusal_plans.u203c.no_payment_type') }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endforeach

        <p>{{ __('labels.refusal_plans.u203b02.title_4') }}</p>
        @if ($planDetailProducts->count())
            <table class="normal_b eol select-motto__product_table">
                <tr>
                    <th style="width: 6em;">{{ __('labels.refusal_plans.estimate-box.distinct_name') }}</th>
                    <th style="width: 20em;">{{ __('labels.refusal_plans.estimate-box.product_name') }}</th>
                    <th class="center em05">{{ __('labels.refusal_plans.u203b02.registrability_based') }}</th>
                </tr>
                @foreach ($planDetailProducts as $key => $planDetailProd)
                    <tr>
                        <td class="right {{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : '' }}">
                            {{ $planDetailProd->first()->mProduct->mDistinction->name ?? '' }}
                        </td>
                        <td class="{{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : '' }}">
                            {{ $planDetailProd->first()->mProduct->name ?? '' }}
                        </td>
                        <td class="center {{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : 'bg_orange' }}">
                            {{ $planDetailProd->first()->getLeaveStsResolutionEvaluation() ?? '' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        @endif

        <h3>
            <strong>
                @php
                    $registrationPrice = $trademark->appTrademark->period_registration ? $costAdditional['pof_2nd_distinction_10yrs'] : $costAdditional['pof_2nd_distinction_5yrs'];

                    $firstPlanDetailDistincts = $firstPlanDetail->planDetailDistincts->filter(function ($item) {
                        $planDetailProducts = $item->planDetailProducts;
                        $planDetailProductNotAdd = $planDetailProducts->whereIn('leave_status', [
                            \App\Models\PlanDetailProduct::LEAVE_STATUS_7,
                            \App\Models\PlanDetailProduct::LEAVE_STATUS_3,
                        ]);
                        $planDetailProductIsDeleted = $planDetailProducts->where('is_deleted', true);
                        return $planDetailProductIsDeleted->count() == 0 && $planDetailProductNotAdd->count() == 0;
                    });
                    $distinctionsAddOn = $firstPlanDetailDistincts
                        ->where('is_add', IS_ADD_TRUE)
                        ->where('is_distinct_settlement', IS_DISTINCT_SETTLEMENT)
                        ->pluck('mDistinction.name')
                        ->unique()
                        ->count();
                @endphp
                <span>
                    {{ $distinctionsAddOn }}{{ __('labels.refusal_plans.u203b02.add_category') }}
                </span>
                @if ($distinctionsAddOn)
                    {{ CommonHelper::formatPrice($registrationPrice, '円', 0) }}　
                @else
                    {{ CommonHelper::formatPrice($distinctionsAddOn, '円', 0) }}　
                @endif
                合計：
                <span>
                    {{ CommonHelper::formatPrice($registrationPrice * $distinctionsAddOn, '円', 0) }}
                </span>
            </strong>
        </h3>


        <ul class="footerBtn clearfix">
            <li>
                <button type="button" id="redirect_top" class="btn_a mb-2" >{{ __('labels.refusal_plans.u203stop.input_2') }}</button>
            </li>
            <li>
                <button type="button" class="btn_b mb-2 redirect_common_payment" >{{ __('labels.refusal_plans.u203b02.submit') }}</button>
            </li>
        </ul>
        <input type="hidden" id="input_redirect" name="redirect_to" value="">

        @if ($hasFee)
            <!-- estimate box -->
            <div class="estimateBox">
                <input type="checkbox" id="cart" />

                <label class="button" for="cart">
                    <span class="open">{{ __('labels.box_cart.cart_title_open') }}</span>
                    <span class="close">{{ __('labels.box_cart.cart_title_close') }}</span>
                </label>

                <div class="estimateContents">
                    <h3>{{ __('labels.refusal_plans.estimate-box.title') }}</h3>
                    <p>{{ __('labels.refusal_plans.estimate-box.content_1') }}
                    <br />
                    <span id="worst_result" class="fs15">{{ __('labels.refusal_plans.estimate-box.content_18') }}</span>
                    </p>

                    <p>{{ __('labels.refusal_plans.estimate-box.content_2') }}</p>
                    <p>{{ __('labels.refusal_plans.estimate-box.content_3') }}</p>

                    <p><input type="submit" value="{{ __('labels.refusal_plans.u203.btn.btn_7') }}" class="btn_b goto_u203c" /></p>
                    @if (count($planDetailProducts))
                        <table class="normal_b eol">
                            <thead>
                                <tr>
                                    <th>{{ __('labels.refusal_plans.estimate-box.distinct_name') }}</th>
                                    <th>{{ __('labels.refusal_plans.estimate-box.product_name') }}</th>
                                    <th class="center em05">{{ __('labels.refusal_plans.u203c.text_2') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($planDetailProducts as $key => $planDetailProd)
                                    <tr>
                                        <td class="right {{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : '' }}">{{ $planDetailProd->first()->mProduct->mDistinction->name ?? '' }}</td>
                                        <td class="{{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : '' }}">{{ $planDetailProd->first()->mProduct->name ?? '' }}</td>
                                        <td class="center {{ isset($planDetailProd->first()->leave_status) && $planDetailProd->first()->leave_status == LEAVE_STATUS_2 ? 'bg_gray' : 'bg_orange' }}">
                                            {{ $planDetailProd->first()->getLeaveStsResolutionEvaluation() ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <h3>{{ __('labels.plan_select02.est_total_title') }}</h3>
                    <table class="normal_b">
                    <tr>
                        <td>{{ __('labels.refusal_plans.estimate-box.content_5') }}{{ $numberPlanDetailProducts }} 商品名<br />
                            {{ __('labels.refusal_plans.estimate-box.content_7') }}{{ CommonHelper::formatPrice(($costAdditional['base_price'] ?? 0) + ($costAdditional['base_price'] ?? 0) * ($setting->value/100)) }}円）
                        </td>
                        <td class="right" id="base_price">
                            <span>
                                {{ CommonHelper::formatPrice($dataSession['cost_service_add_prod'] ?? 0) }}
                            </span>円
                        </td>
                    </tr>
                    @if(isset($dataSession['payment_type']) && $dataSession['payment_type'] == App\Models\Payment::BANK_TRANSFER)
                    <tr>
                        <td>{{ __('labels.refusal_plans.estimate-box.content_9') }}</td>
                        <td class="right" id="cost_bank_transfer"> <span>{{ CommonHelper::formatPrice($dataSession['cost_bank_transfer'] ?? 0 ) }}</span>円</td>
                    </tr>
                    @endif
                    <tr>
                        <th class="right">{{ __('labels.refusal_plans.estimate-box.content_10') }}</th>
                        <th class="right"><span id="subtotal">{{ CommonHelper::formatPrice($dataSession['subtotal'] ?? 0) }}</span>円</th>
                    </tr>
                    @if($payerInfo && $payerInfo->m_nation_id == NATION_JAPAN_ID)
                        <tr>
                            <th class="right" colspan="2">
                            {{ __('labels.refusal_plans.estimate-box.content_11') }}<span id="commission">{{ CommonHelper::formatPrice($dataSession['commission'] ?? 0) }}</span>円<br />
                            {{ __('labels.refusal_plans.estimate-box.content_12') }}{{ floor($setting->value *100)/100 }}{{ __('labels.refusal_plans.estimate-box.content_13') }}
                            <span id="tax">{{ CommonHelper::formatPrice($dataSession['tax'] ?? 0) }}</span>円</th>
                        </tr>
                    @endif
                    <tr>
                        <td>{{ __('labels.refusal_plans.estimate-box.content_14') }}<span class="total_distinction">{{ $totalDistinction ?? 0 }}</span>{{ __('labels.refusal_plans.estimate-box.number_distinct_add') }}<br />1{{ __('labels.refusal_plans.estimate-box.number_distinct_add') }}・
                            <span id="price_one_category">
                                {{ CommonHelper::formatPrice(isset($trademark->appTrademark->period_registration) && $trademark->appTrademark->period_registration ?  $costAdditional->pof_2nd_distinction_10yrs : $costAdditional->pof_2nd_distinction_5yrs) }}
                            </span>{{ __('labels.refusal_plans.estimate-box.content_16') }} <span class="total_distinction">{{ $totalDistinction ?? 0 }}</span>{{ __('labels.refusal_plans.estimate-box.number_distinct_add') }}
                        </td>
                        <td class="right">
                            <span id="patent_office_fees">
                                {{
                                    CommonHelper::formatPrice(
                                        $totalDistinction * (isset($trademark->appTrademark->period_registration)
                                            && $trademark->appTrademark->period_registration
                                            ?  $costAdditional->pof_2nd_distinction_10yrs
                                            : $costAdditional->pof_2nd_distinction_5yrs
                                        ) ?? 0
                                    )
                                }}
                            </span>円
                        </td>
                    </tr>
                    <tr>
                        <th class="right"><strong style="font-size:1.2em;">{{ __('labels.refusal_plans.estimate-box.content_19') }}</strong></th>
                        <th class="right"><strong style="font-size:1.2em;"> <span id="total">{{ CommonHelper::formatPrice($dataSession['total_amount'] ?? 0) }}</span>円</strong></th>
                    </tr>
                    </table>
                    <input type="hidden" name="s" value="{{ request()->__get('s') ?? '' }}">
                    <p class="red mb10">{{ __('labels.refusal_plans.estimate-box.content_17') }}</p>
                    <ul class="right list">
                        <li>
                            <button type="button" id="redirect_quote" class="btn_a" >{{ __('labels.refusal_plans.u203.btn.btn_8') }}</button>
                        </li>
                    </ul>
                    <p class="center mb20">
                        @if (isset($dataSession['from_page']) && $dataSession['from_page'] == U203)
                            <a class="btn_b" href="{{ route('user.refusal.response-plan.refusal_product', [
                                    'comparison_trademark_result_id' => request()->__get('comparison_trademark_result_id'),
                                    'trademark_plan_id' => request()->__get('trademark_plan_id')
                            ])}}">
                                {{ __('labels.refusal_plans.u203.btn.btn_9') }}
                            </a>
                        @else
                            <a class="btn_b" href="{{ route('user.refusal.response-plan.refusal_product_re', [
                                    'comparison_trademark_result_id' => request()->__get('comparison_trademark_result_id'),
                                    'trademark_plan_id' => request()->__get('trademark_plan_id')
                            ])}}">
                                {{ __('labels.refusal_plans.u203.btn.btn_9') }}
                            </a>
                        @endif
                    </p>
                    <ul class="footerBtn clearfix">
                        <li style="margin-bottom:1em"></li>
                        <li>
                            <input type="button" value="{{ __('labels.refusal_plans.u203.btn.btn_10') }}" class="btn_e redirect_common_payment" />
                        </li>
                    </ul>
                </div><!-- /estimate contents -->
            </div><!-- /estimate box -->
        @endif
    </form>
</div><!-- /contents -->
<style>
    #redirect_top {
        width: 293px;
        height: 38px;
        font-size: 1.3em;
    }
    .redirect_common_payment {
        height: 38px;
        font-size: 1.3em;
    }
    .doc_type_name {

    }
    .plan_detail_description {
        white-space: pre-line;
    }
</style>
@endsection
@section('script')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const QUOTE = @json(QUOTE);
        const COMMON_PAYMENT = @json(COMMON_PAYMENT);
        const U000ANKEN_TOP = @json(U000ANKEN_TOP);
        const U203C = @json(U203C);
        const IS_CHOICE = @json(IS_CHOICE);
        const plans = @json($trademarkPlan->plans);
        const revolutionTypes = @json(App\Models\PlanDetail::getRevolutionTypes());
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/refusal/choose-plan-confirm.js') }}"></script>
    @if ($trademarkPlan->is_register == TRADEMARK_PLAN_IS_REGISTER_TRUE)
        <script>disabledScreen();</script>
    @endif
@endsection
