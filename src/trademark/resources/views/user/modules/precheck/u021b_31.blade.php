@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>
            {{ __('labels.precheck.report.title-application') }}
        </h2>

        <form id="form" action="{{ route('user.precheck.post-application-trademark') }}" method="POST">
            @csrf
            <h3>{{ __('labels.form_trademark_information.title') }}</h3>
            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable
            ])
            {{-- Trademark table --}}

            <hr/>

            {{-- comment --}}
            <h3>{{ __('labels.precheck.precheck_report.title_comment') }}</h3>
            <p>{{ __('labels.precheck.precheck_report.text') }}<br/>
                @if ($precheck->precheckComments->count() > 0)
                    @foreach ($precheck->precheckComments as $item)
                        {{ $item->content }}<br/>
                    @endforeach
                @endif
            </p>
            {{-- comment --}}

            {{-- product --}}
            <div class="collapse-div">
                <p class="hideShowClick">
                    {{ __('labels.precheck.product.title') }}
                    <span class="icon-text" style="cursor: pointer;">+</span>
                </p>
                <p class="toggle-info">
                    {{ __('labels.precheck.product.note1') }}<br/>
                    {{ __('labels.precheck.product.note2') }}<br/>
                    {{ __('labels.precheck.product.note3') }}<br/>
                    {{ __('labels.precheck.product.note4') }}<br/>
                    {{ __('labels.precheck.product.note5') }}<br/>
                    {{ __('labels.precheck.product.note6') }}<br/>
                    {{ __('labels.precheck.product.note7') }}<br/>
                    {{ __('labels.precheck.product.note8') }}<br/>
                    {{ __('labels.precheck.product.note9') }}<br/>
                    {{ __('labels.precheck.product.note10') }}
                </p>
            </div>
            <div class="js-scrollable mb20 highlight">
                <table class="normal_b table_product_choose">
                    <tr>
                        <th rowspan="3" class="em04">{{ __('labels.precheck.table_precheck.th1') }}</th>
                        <th rowspan="3" class="bg_green">{{ __('labels.precheck.table_precheck.th2') }}</th>
                        <th colspan="2">{{ __('labels.precheck.table_precheck.th3') }}</th>
                        <th colspan="6">{{ __('labels.precheck.table_precheck.th4') }}</th>
                        <th rowspan="3" class="bg_green">
                            {{ __('labels.precheck.table_precheck.th5') }}<br />
                            <label>
                                <input type="checkbox" checked class="all-checkbox" />
                                {{ __('labels.precheck.table_precheck.th5_1') }}
                            </label>
                        </th>
                    </tr>
                    <tr>
                        <th>{{ __('labels.precheck.passed') }}</th>
                        <th>{{ __('labels.precheck.this_time') }}</th>
                        <th colspan="3">{{ __('labels.precheck.passed') }}</th>
                        <th colspan="3">{{ __('labels.precheck.this_time') }}</th>
                    </tr>
                    <tr>
                        <th class="em04 bg_whitesmoke">{{ __('labels.precheck.table_precheck.th3_1') }}</th>
                        <th class="em04 bg_whitesmoke">{{ __('labels.precheck.table_precheck.th3_1') }}</th>

                        <th class="em07">{{ __('labels.precheck.table_precheck.th4_1') }}</th>
                        <th class="em07">{{ __('labels.precheck.table_precheck.th4_2') }}</th>
                        <th class="em05 bg_whitesmoke">{{ __('labels.precheck.table_precheck.th4_3') }}

                        <th class="em07">{{ __('labels.precheck.table_precheck.th4_1') }}</th>
                        <th class="em07">{{ __('labels.precheck.table_precheck.th4_2') }}</th>
                        <th class="em05 bg_whitesmoke">{{ __('labels.precheck.table_precheck.th4_3') }}</th>
                    </tr>
                    <div class="error-m-product-id"></div>

                    @error('m_product_ids[]')
                        <div class="notice">{{ $message }}</div>
                    @enderror

                    @foreach ($getProductOfDistinction as $distinction => $products)
                        @foreach ($products as $key => $item)
                            @php
                                $historyPrecheckResult = collect([]);
                                foreach ($precheckBefore as $beforeData) {
                                    $precheckProducts = $beforeData->precheckProduct->where('m_product_id', $item->id);

                                    foreach ($precheckProducts as $precheckProduct) {
                                        $precheckResult = $precheckProduct->precheckResult ?? collect([]);

                                        foreach ($precheckResult as $result) {
                                            $historyPrecheckResult->push($result);
                                        }
                                    }
                                }

                                $precheckProducts = $item->precheckProduct;
                                $lastPrecheckProducts = $precheckProducts->last();
                            @endphp
                            <tr>
                                @if ($key == 0)
                                    <td rowspan="{{ $products->count() > 0 ? $products->count() : '' }}"
                                        class="bg_blue inv_blue">{{ __('labels.precheck.table_precheck.name_distinct', ['attr' => $distinction]) }}</td>
                                @endif
                                <td class="bg_green {{ !empty($item->prechecks[0]) && $item->prechecks[0]->pivot->is_register_product == 1 ? 'red' : '' }}">{{ $item->name }}</td>

                                    {{-- $historyPrecheckResult --}}
                                    <td class="center">
                                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->min('result_similar_simple'))
                                            {{ \App\Models\PrecheckResult::listResultSmilarSimpleOptions()[$historyPrecheckResult->min('result_similar_simple')] }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- $lastPrecheckProducts --}}
                                    <td class="center">
                                        @if (!empty($lastPrecheckProducts)
                                            && $lastPrecheckProducts->precheckResult
                                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                                            && $lastPrecheckProducts->precheckResult->min('result_similar_simple')
                                        )
                                            {{ \App\Models\PrecheckResult::listResultSmilarSimpleOptions()[$lastPrecheckProducts->precheckResult->min('result_similar_simple')] }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- $historyPrecheckResult --}}
                                    <td class="center bg_gray">
                                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_identification_detail'))
                                            {{ \App\Models\PrecheckResult::listResultIdentificationDetailOptions()[$historyPrecheckResult->max('result_identification_detail')] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="center bg_gray">
                                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_similar_detail'))
                                            {{ \App\Models\PrecheckResult::listResultSimilarDetailOptions()[$historyPrecheckResult->max('result_similar_detail')] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if (!empty($historyPrecheckResult) && $historyPrecheckResult->max('result_similar_detail'))
                                            {{ \App\Models\PrecheckResult::getResultDetailPrecheck($historyPrecheckResult->max('result_identification_detail'), $historyPrecheckResult->max('result_similar_detail')) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- $lastPrecheckProducts --}}
                                    <td class="center bg_gray">
                                        @if (!empty($lastPrecheckProducts)
                                            && $lastPrecheckProducts->precheckResult
                                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                                            && $lastPrecheckProducts->precheckResult->max('result_identification_detail')
                                        )
                                            {{ \App\Models\PrecheckResult::listResultIdentificationDetailOptions()[$lastPrecheckProducts->precheckResult->max('result_identification_detail')] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="center bg_gray">
                                        @if (!empty($lastPrecheckProducts)
                                            && $lastPrecheckProducts->precheckResult
                                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                                            && $lastPrecheckProducts->precheckResult->max('result_similar_detail')
                                        )
                                            {{ \App\Models\PrecheckResult::listResultSimilarDetailOptions()[$lastPrecheckProducts->precheckResult->max('result_similar_detail')] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="center">
                                        @if (!empty($lastPrecheckProducts)
                                            && $lastPrecheckProducts->precheckResult
                                            && $lastPrecheckProducts->precheckResult->isNotEmpty($lastPrecheckProducts->precheckResult)
                                            && $lastPrecheckProducts->precheckResult->max('result_similar_detail')
                                        )
                                            {{ \App\Models\PrecheckResult::getResultDetailPrecheck($lastPrecheckProducts->precheckResult->max('result_identification_detail'), $lastPrecheckProducts->precheckResult->max('result_similar_detail')) }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                <td class="center bg_green">
                                    <input type="checkbox" name="is_choice_user[]"
                                           data-foo="is_choice_user[]"
                                           {{ !empty($item->prechecks[0]) &&  $item->prechecks[0]->pivot->is_apply == \App\Models\PrecheckProduct::IS_APPLY_CHECKED ? 'checked' : '' }}
                                           class="single-checkbox single-checkbox-{{ $item->id }}"
                                           value="{{ $item->id }}" data-name-distinction="{{ $distinction }}"/>
                                    <input type="hidden" name="productIds[]" value="{{ $item->id }}"/>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr class="add-product">
                        <td colspan="11" class="right">
                            {{ __('labels.precheck.table_precheck.total_dis') }}：<span
                                class="total-dis"></span>　{{ __('labels.precheck.table_precheck.total_prod') }}：<span
                                class="total-checkbox-checked"></span>
                            <input type="hidden" name="sum_distintion" id="sum_distintion" value="">
                        </td>
                    </tr>
                </table>
            </div>
            {{-- product --}}
            <div id="u031pass-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <iframe src="{{route('user.apply-trademark.show-pass', [
                                 'id' => $id ?? 0,
                                 'from_page' => U021B_31
                                 ])}}" style="width: 100%; height: 70vh;" frameborder="0"></iframe></div>
                        </div>
                    </div>
                </div>
            </div>
            <p>
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="button" name="submitEntry" value="{{ __('labels.support_first_times.rollback_suggest_ai') }}" class="btn_f rollback_suggest_ai" data-route="{{ route('user.precheck.redirect-u020b')}}">
            </p>
            <p>
                <input type="button" name="submitEntry" value="{{ __('labels.support_first_times.show_product_list') }}"
                       class="btn_f" id="redirect_to_u031pass">
            </p>
            {{-- modal --}}
            <div id="mySizeChartModal" class="ebcf_modal">
                <div class="ebcf_modal-content">
                    <span class="ebcf_close">&times;</span>
                    <p>{{ __('labels.support_first_times.answer_1') }}</p>
                    <input type="submit" name="submitEntry" value="{{ __('labels.support_first_times.yes') }}"
                           class="btn_a">
                    <input type="button" value="{{ __('labels.support_first_times.no') }}" class="btn_a">
                </div>
            </div>
            {{-- modal --}}
            <p>
                {{-- <a href="#" class="btn_a" id="mySizeChart">{{ __('labels.support_first_times.add_product') }}</a> --}}
                <a href="javascript:void(0)" class="btn_a" id="btn-redirect-u031_edit_with_number" data-route="{{ route('user.precheck.redirect-u031_edit_with_number')}}">{{ __('labels.support_first_times.add_product') }}</a>
                <br/>
                <span class="note">
                    {{ __('labels.support_first_times.note_1') }}<br/>
                    {{ __('labels.support_first_times.note_24') }}<br/>
                    {{ __('labels.support_first_times.note_3') }}<br/>
                    {{ __('labels.support_first_times.note_4') }}<br/>
                    {{ __('labels.support_first_times.note_5') }}<br/>
                </span>
            </p>

            <p>
                {{ __('labels.support_first_times.note_25') }}<br/>
                {{ __('labels.support_first_times.note_7') }}<br/>
            </p>

            <p>
                <button class="btn_a" id="btn-redirect-u021c"
                        data-route="{{ route('user.precheck.redirect-u021c')}}">{{ __('labels.support_first_times.regis-by-trademark.title_h1') }}</button>
            </p>

            <p class="note">{{ __('labels.precheck.product.note11') }}<br/>
                {{ __('labels.precheck.product.note12') }}
            </p>
            <p class="note">{{ __('labels.precheck.product.note13') }}</p>
            <p class="note">{{ __('labels.precheck.product.note14') }}</p>
            <p class="note eol">{{ __('labels.precheck.product.note15') }}</p>

            <hr/>

            {{-- plan selection --}}
            <h3>{{ __('labels.support_first_times.plan_selection') }}</h3>
            <table class="normal_b mb10">
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" value="1" id="package_a" {{ isset($appTrademark->pack) && $appTrademark->pack == App\Models\AppTrademark::PACK_A ? "checked" : "" }}/>
                        <span id="name_package_a">{{ __('labels.support_first_times.pack_a') }}</span>
                        {{ __('labels.support_first_times.up_to_3_prod') }}
                        <span
                            id="price_package_a">{{  CommonHelper::formatPrice($pricePackage[0][0]['base_price']) }}</span>円<br/>
                        {{ __('labels.support_first_times.note_8') }}<br/>
                        {{ __('labels.support_first_times.note_9') }}
                        <span
                            id="price_product_add_pack_a">{{  CommonHelper::formatPrice($pricePackage[1][0]['base_price']) }}</span>
                        {{ __('labels.support_first_times.note_10') }}<br/>
                        {{ __('labels.precheck.pack.note1') }}
                    </td>
                </tr>
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" value="2" id="package_b" {{ isset($appTrademark->pack) && $appTrademark->pack == App\Models\AppTrademark::PACK_B ? "checked" : "" }}/>
                        <span id="name_package_b">{{ __('labels.support_first_times.pack_b') }}</span>
                        {{ __('labels.support_first_times.up_to_3_prod') }}
                        <span
                            id="price_package_b">{{  CommonHelper::formatPrice($pricePackage[0][1]['base_price']) }}</span>円<br/>
                        {{ __('labels.support_first_times.note_12') }}<br/>
                        {{ __('labels.support_first_times.note_13') }}
                        <span
                            id="price_product_add_pack_b">{{  CommonHelper::formatPrice($pricePackage[1][1]['base_price']) }}</span>
                        {{ __('labels.support_first_times.note_14') }}<br/>
                    </td>
                </tr>
                <tr>
                    <td style="width:34em;">
                        <input type="radio" name="pack" class="package_type" value="3" {{ isset($appTrademark->pack) && $appTrademark->pack == App\Models\AppTrademark::PACK_C ? "checked" : (isset($appTrademark) ? "" : "checked") }} id="package_c"/>
                        <span id="name_package_c">{{ __('labels.support_first_times.pack_c') }}</span>
                        {{ __('labels.support_first_times.up_to_3_prod') }}
                        <span
                            id="price_package_c">{{  CommonHelper::formatPrice($pricePackage[0][2]['base_price']) }}</span>円<br/>
                        {{ __('labels.support_first_times.note_16') }}<br/>
                        {{ __('labels.support_first_times.note_17') }}
                        <span
                            id="price_product_add_pack_c">{{  CommonHelper::formatPrice($pricePackage[1][2]['base_price']) }}</span>
                        {{ __('labels.support_first_times.note_18') }}<br/>
                    </td>
                </tr>
            </table>

            <p class="eol">{{ __('labels.support_first_times.note_27') }}<br/></p>
            {{-- plan selection --}}

            <hr/>

            {{-- mailing regis cert --}}
            <h3>{{ __('labels.support_first_times.mailing_regis_cert.title') }}</h3>
            <p class="eol">
                <input type="checkbox" id="is_mailing_register_cert" value="1" name="is_mailing_register_cert" {{ !empty($trademark) && !empty($trademark->appTrademark) && $trademark->appTrademark->is_mailing_regis_cert ? 'checked': '' }} />{{ __('labels.support_first_times.mailing_regis_cert.note_1') }}<br />
                <span class="note">{{ __('labels.support_first_times.mailing_regis_cert.separate_commission', [ 'attr' => CommonHelper::formatPrice($mailRegisterCert['cost_service_base'] ?? 0) ]) }}<br/>
                    {{ __('labels.support_first_times.mailing_regis_cert.note_2') }}</span>
            </p>
            {{-- mailing regis cert --}}

            <hr/>

            {{-- period registration --}}
            <h3>{{ __('labels.support_first_times.regis_period.title') }}</h3>
            <p class="eol">
                <input type="checkbox" name="period_registration"
                       id="period_registration" value="2" {{ isset($trademark) && isset($appTrademark) && $appTrademark->period_registration == 2 ? 'checked': '' }} />{{ __('labels.support_first_times.regis_period.regis_10_years') }}<br/>
                <span class="note">
                    {{ __('labels.support_first_times.regis_period.note_1', [ 'attr' => CommonHelper::formatPrice(($periodRegistration->base_price  + $periodRegistration->base_price * $setting['value'] / 100) ?? 0)]) }}
                </span>
            </p>
            {{-- period registration --}}

            <hr/>

            {{-- Form trademark-info --}}
            @include('user.components.trademark-info', [
                'nations' => $nations,
                'prefectures' => $prefectures,
                'trademarkInfos' => $trademarkInfos ?? null
            ])
            {{-- End Form trademark-info --}}

            <hr/>

            {{-- Payer info --}}
            @include('user.components.payer-info', [
               'prefectures' => $prefectures ?? [],
               'nations' => $nations ?? [],
               'paymentFee' => $paymentFee ?? null,
               'payerInfo' => $payerInfo ?? null
            ])
            {{-- End Payer info --}}

            <hr/>

            {{-- btn submit --}}
            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" name="submitEntry" value="{{ __('labels.apply-trademark-with-number.btn3') }}" class="btn_e big redirect_to_common_payment" />
                </li>
            </ul>
            <ul class="btn_left eol">
                @if ($routeCancel)
                    <li>
                        <a href="javascript:void(0)" id="stop_applying" class="btn_a">{{ __('labels.support_first_times.stop_applying') }}</a>
                    </li>
                @endif
                <li>
                    <input type="button" id="redirec_to_anken_top" value="{{ __('labels.support_first_times.save_return') }}" class="btn_a"/>
                </li>
            </ul>
            {{-- btn submit --}}

            <!-- payment cart -->
            <div class="estimateBox">
                <input type="checkbox" id="cart"/>
                <label class="button" for="cart">
                    <span class="open">{{ __('labels.support_first_times.cart.see_est_amount') }}</span>
                    <span class="close">{{ __('labels.support_first_times.cart.close_est_amount') }}</span>
                </label>
                <div class="estimateContents">
                    <h3>{{ __('labels.support_first_times.cart.quoted_amount') }}</h3>
                    <table class="normal_b">
                        <tr>
                            <td>
                                <span id="name_package"></span><br/>
                                <span id="name_package_note"> {{ __('labels.support_first_times.cart.note_1') }}
                                </span>
                            </td>
                            <td class="right"><span id="price_package">0</span>円</td>
                        </tr>
                        <tr>
                        <tr>
                            <td>{{ __('labels.support_first_times.addition') }} <span
                                    id="product_selected_count">0</span>{{ __('labels.support_first_times.cart.prod_name_3_prod') }}
                                <span id="each_3_prod_pack"></span>円）
                            </td>
                            <td class="right">
                                <span id="price_product_add">0</span>円
                                <input type="hidden" name="price_product_add" value="">
                            </td>
                        </tr>
                        <tr class="cost_bank_transfer_tr d-none">
                            <td>{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                            <td class="right">
                                <span
                                    id="cost_bank_transfer_span">{{ CommonHelper::formatPrice($paymentFee['cost_service_base'] ?? 0 )}}</span>円
                            </td>
                        </tr>
                        <tr class="tr_change_5yrs_to_10yrs d-none">
                            <td>{{ __('labels.user_common_payment.change_5yrs_to_10yrs') }}</td>
                            <td class="right">
                                <span id="change_5yrs_to_10yrs">{{ CommonHelper::formatPrice($registerTermChange['base_price'] + ( $registerTermChange['base_price'] * $setting['value'] / 100 )) }}</span>円
                            </td>
                        </tr>
                        <tr class="d-content d_none_mailing_regis_cert d-none">
                            <td>{{ __('labels.support_first_times.cart.mailing_regis_cert') }}</td>
                            <td class="right">
                                <span
                                    id="mailing_regis_cert_el">{{ CommonHelper::formatPrice($mailRegisterCert['cost_service_base'] ?? 0) }}</span>円
                            </td>
                        </tr>
                        <tr>
                            <th class="right">
                                <strong><span class="total-checkbox-checked"></span>{{ __('labels.support_first_times.cart.prod_name_small_meter') }}</strong>
                            </th>
                            <th class="right"><strong id="sub_total">0</strong>円</th>
                        </tr>
                        <tr>
                            <th colspan="2" class="right">
                                <span class="breakdown-real-fee">
                                    <span class="commission_is_ja">
                                        {{ __('labels.support_first_times.cart.breakdown_real_fee') }}
                                        <span id="commission">0</span>円<br/>
                                    </span>
                                    {{ __('labels.support_first_times.cart.consumption_tax') }}（{{ floor($setting->value * 100)/100 ?? 0 }}％）
                                    <span id="tax">0</span>円
                                </span>
                            </th>
                        </tr>
                        <tr>
                            <td>{{ __('labels.support_first_times.cart.expense_patent_3_category') }}{{ __('labels.support_first_times.cart.expense_patent_3_category1') }}<br />
                                <span id="one_division">0</span>区分
                                <span id="regis5Ys1">{{ CommonHelper::formatPrice($pricePackage[0][2]['pof_1st_distinction_5yrs'] ?? 0) }}</span>円+
                                <span id="regis5Ys2">{{ CommonHelper::formatPrice($pricePackage[0][2]['pof_2nd_distinction_5yrs'] ?? 0) }}</span>x
                                <span id="mDistintionPayment">{{ $countDistinct - 1 }}</span>区分
                            </td>
                            <td class="right">
                                <span id="fee_submit_register">
                                    {{-- {{ CommonHelper::formatPrice($pricePackage[0][2]['pof_1st_distinction_5yrs'] + $pricePackage[0][2]['pof_2nd_distinction_5yrs']) }} --}}
                                    0
                                </span>円
                            </td>
                        </tr>
                        <tr id="tr_fee_submit_register_year">
                            @php
                                if (isset($appTrademark) && $appTrademark->period_registration == App\Models\AppTrademark::PERIOD_REGISTRATION_TRUE) {
                                    $priceFeeSubmit = $periodRegistration->pof_1st_distinction_5yrs;
                                } else {
                                    $priceFeeSubmit = $periodRegistration->pof_1st_distinction_10yrs;
                                }
                            @endphp
                            <td>
                                {{ __('labels.support_first_times.cart.expense_patent_3_category') }}
                                <span id="sumDistintion2">{{ $countDistinct }}</span>区分
                                <span id="price_fee_submit_5_year">{{ CommonHelper::formatPrice($periodRegistration->pof_1st_distinction_5yrs) }}</span>円
                                <input type="hidden" name="" id="price_fee_submit_5_year_old" value="{{ $periodRegistration->pof_1st_distinction_5yrs }}">
                                <br />
                                <span id="text_5yrs_10yrs2">
                                    {{ __('labels.support_first_times.cart.stamp_fee_5yrs') }}
                                </span>
                            </td>
                            <td class="right">
                                <span id="fee_submit_register_year">{{ CommonHelper::formatPrice($countDistinct * $periodRegistration->pof_1st_distinction_5yrs) }}</span>円
                                <input type="text" name="" id="value_fee_submit_ole" value="{{ $countDistinct * $periodRegistration->pof_1st_distinction_5yrs }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="right">{{ __('labels.support_first_times.cart.total') }}</th>
                            <th class="right" nowrap><strong style="font-size:1.2em;"
                                                             id="total_amount">{{ CommonHelper::formatPrice($countDistinct * $periodRegistration->pof_1st_distinction_5yrs + ($pricePackage[0][2]['pof_1st_distinction_5yrs'] + $pricePackage[0][2]['pof_2nd_distinction_5yrs'])) }}</strong>円
                            </th>
                        </tr>
                    </table>
                    <input type="hidden" name="from_page" id="from_page" value="{{ U021B_31 }}">
                    <input type="hidden" name="cost_service_base" id="cost_service_base">
                    <input type="hidden" name="preheck_id" id="preheck_id"  value="{{ request()->__get('precheck_id')}}">
                    <input type="hidden" name="cost_service_add_prod" id="cost_service_add_prod">
                    <input type="hidden" name="subtotal" id="subtotal">
                    <input type="hidden" name="commission" id="request_commission">
                    <input type="hidden" name="tax" id="request_tax">
                    <input type="hidden" name="cost_print_application_one_distintion"
                           id="cost_print_application_one_distintion">
                    <input type="hidden" name="cost_5_year_one_distintion" id="cost_5_year_one_distintion">
                    <input type="hidden" name="cost_10_year_one_distintion" id="cost_10_year_one_distintion">
                    <input type="hidden" name="total_amount" id="value_total_amount">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <p class="red mb10">※いかなる理由に関わらず、お申込み後の返金は一切ございません。</p>

                    <ul class="right list">
                        <li><input type="button" id="recalculate_money" value="{{ __('labels.support_first_times.recalculation') }}"
                                   class="btn_a"/></li>
                    </ul>

                    <ul class="right list">
                        <li><input type="submit" id="redirect_to_quote" value="{{ __('labels.support_first_times.save_display_quotation') }}"
                                   class="btn_a"/></li>
                        <input type="hidden" name="redirect_to" value="">
                    </ul>

                    <ul class="footerBtn right clearfix">
                        <li><input type="submit" name="submitEntry"
                                   value="{{ __('labels.support_first_times.apply_content') }}" class="btn_e big redirect_to_common_payment"/></li>
                    </ul>
                </div>
            </div>
            <!-- payment cart -->

        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/javascript">
        let trademark_id = @json($id);
        let precheck_id = @json($precheck->id);
        const BANK_TRANSFER = 2;
        let setting = @json($setting);
        let countDistinct = @json($countDistinct);
        let feeSubmit = @json($periodRegistration);
        const nationJPId = '{{ NATION_JAPAN_ID }}';
        const pricePackage = @json($pricePackage);
        let routeCancel = @json($routeCancel);
        const support_U011_E007 = '{{ __('messages.general.support_U011_E007') }}'
        const stampFee5yrs = '{{ __('labels.support_first_times.cart.stamp_fee_5yrs') }}'
        const stampFee10yrs = '{{ __('labels.support_first_times.cart.stamp_fee_10yrs') }}'
        const answer = '{{ __('labels.support_first_times.answer_1') }}'
        const yes = '{{ __('labels.support_first_times.yes') }}'
        const no = '{{ __('labels.support_first_times.no') }}'
        const trademarkInfoRules = {}
        const trademarkInfoMessages = {}
        const errorMessageRegisterPrecheck = '{{ __('messages.support_first_time.support_U011_E008') }}'
    </script>

    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    {{-- <script type="text/JavaScript" src="{{ asset('common/js/simple-modal.js') }}"></script> --}}
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script src="{{ asset('end-user/prechecks/precheck/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/precheck/precheck-cart-product.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/precheck/u021b.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/precheck/redirect_to_u021c.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/prechecks/precheck/redirect_to_u031_edit_with_number.js') }}"></script>
@endsection
