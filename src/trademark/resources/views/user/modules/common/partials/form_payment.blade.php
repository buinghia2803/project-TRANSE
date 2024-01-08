<table class="normal_b mb10">
    @if (isset($contentPay['cost_service_text']) && isset($contentPay['cost_service_text']))
        <tr>
            <td>
                <span id="name_package">{{ $contentPay['cost_service_text'] }}</span><br />
                {{-- Todo waiting for test again --}}
                {{-- @if (isset($contentPay['total_product']) && $contentPay['total_product'])
                    <span>
                        {{ $contentPay['total_product'] }}
                    </span>
                @endif
                @if (isset($contentPay['breakdown']) && $contentPay['breakdown'])
                <br /><span>
                        {{ $contentPay['breakdown'] }}
                    </span>
                @endif
                @if (isset($contentPay['basic_fee_up_to_3_prod']) && $contentPay['basic_fee_up_to_3_prod'])
                <br /><span>
                        {{ $contentPay['basic_fee_up_to_3_prod'] }}
                    </span>
                @endif
                @if (isset($contentPay['addition_prod']) && $contentPay['addition_prod'])
                <br /><span>
                        {{ $contentPay['addition_prod'] }}
                    </span>
                @endif --}}
            </td>
            <td class="right">
                <span id="price_package">{{ CommonHelper::formatPrice($contentPay['cost_service_value']) }}</span>円
            </td>
        </tr>
    @endif
    @if (isset($contentPay['cost_service_a_rating_text']) && isset($contentPay['cost_service_a_rating_text']))
        <tr>
            <td>{{ __('labels.plan_select02.service_response') }}<br>{{ $contentPay['cost_service_a_rating_text'] }}
                <span id="est_box_prod_count_A">{{ $contentPay['count_a_rating'] }}</span>
                件x
                <span id="base_price_select_plan_A">{{ CommonHelper::formatPrice($contentPay['select_A_price']) }}</span>
                円）
            </td>
            <td class="right">
                <span id="select_plan_A_price">{{ CommonHelper::formatPrice($contentPay['cost_service_a_rating_value']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_service_other_rating_text']) &&
        isset($contentPay['cost_service_other_rating_value']))
        <tr>
            <td>{{ __('labels.plan_select02.service_response') }}<br>{{ $contentPay['cost_service_other_rating_text'] }}
                <span id="est_box_prod_count_B_E">{{ $contentPay['count_other_rating'] }}</span>件x
                <span
                    id="base_price_select_plan_B_E">{{ CommonHelper::formatPrice($contentPay['select_other_price']) }}</span>円）
            </td>
            <td class="right">
                <span id="select_plan_B_E_price">{{ CommonHelper::formatPrice($contentPay['cost_service_other_rating_value']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['product_selected_text']) &&
        isset($contentPay['product_selected_count']) &&
        isset($contentPay['each_3_prod_pack']) &&
        isset($contentPay['price_product_add']) &&
        $contentPay['product_selected_count'] &&
        $contentPay['price_product_add'])
        <tr>
            <td>{{ $contentPay['product_selected_text'] }}
                <span id="product_selected_count">{{ $contentPay['product_selected_count'] }}</span>
                {{ $contentPay['prod_name_3_prod_text'] }}
                <input type="hidden" name="reduce_number_distitions" value="" />
                <span id="each_3_prod_pack">{{ CommonHelper::formatPrice($contentPay['each_3_prod_pack']) }}</span>円）
            </td>
            <td class="right">
                <span id="price_product_add">{{ CommonHelper::formatPrice($contentPay['price_product_add']) }}円</span>
                <input type="hidden" name="reduce_number_distitions" value="">
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_service_base_text']) &&
        isset($contentPay['cost_service_base_value']) &&
        $contentPay['cost_service_base_value'])
        <tr>
            <td>{{ $contentPay['cost_service_base_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_service_base_value']) }}円
                <input type="hidden" name="cost_service_base" value="{{ $contentPay['cost_service_base_value'] }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['change_5yrs_to_10yrs_text']) && isset($contentPay['change_5yrs_to_10yrs']))
        <tr class="tr_change_5yrs_to_10yrs">
            <td>{{ $contentPay['change_5yrs_to_10yrs_text'] }}</td>
            <td class="right">
                <span id="change_5yrs_to_10yrs">{{ CommonHelper::formatPrice($contentPay['change_5yrs_to_10yrs']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_service_add_prod_text']) &&
        isset($contentPay['cost_service_add_prod_value']) &&
        $contentPay['cost_service_add_prod_value'])
        <tr>
            <td>{{ $contentPay['cost_service_add_prod_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_service_add_prod_value']) }}円
                <input type="hidden" name="cost_service_add_prod"
                    value="{{ $contentPay['cost_service_add_prod_value'] }}" />
            </td>
        </tr>
    @endif

    @if(isset($contentPay['reduce_number_distitions_text'])
        && isset($contentPay['reduce_number_distitions_value'])
        && $contentPay['reduce_number_distitions_value']
    )
        <tr>
            <td>
                {{ $contentPay['reduce_number_distitions_text'] }}
            </td>
            <td class="right">
                <span id="select_plan_B_E_price">{{ CommonHelper::formatPrice($contentPay['reduce_number_distitions_value']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_change_name_text']) &&
        isset($contentPay['cost_change_name_value']) &&
        $contentPay['cost_change_name_value'])
        <tr>
            <td>{{ $contentPay['cost_change_name_text'] }}
            </td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_change_name_value'] ?? 0) }}円
                <input type="hidden" name="cost_change_name"
                    value="{{ $contentPay['cost_change_name_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_change_address_text']) &&
        isset($contentPay['cost_change_address_value']) &&
        $contentPay['cost_change_address_value'])
        <tr>
            <td>{{ $contentPay['cost_change_address_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_change_address_value'] ?? 0) }}円
                <input type="hidden" name="cost_change_address"
                    value="{{ $contentPay['cost_change_address_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if(isset($contentPay['regis_period_change_fee_text']) &&
        isset($contentPay['regis_period_change_fee']) &&
        $contentPay['regis_period_change_fee']
    )
         <tr class="tr_regis_period_change_fee">
            <td>{{ $contentPay['regis_period_change_fee_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['regis_period_change_fee'] ?? 0) }}円
                <input type="hidden" name="regis_period_change_fee"
                    value="{{ $contentPay['regis_period_change_fee'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['mailing_regis_cert_el']) && $contentPay['mailing_regis_cert_el'])
        <tr class="tr_mailing_regis_cert_el">
            <td>{{ $contentPay['mailing_regis_cert_el_text'] }}</td>
            <td class="right">
                <span id="mailing_regis_cert_el">
                    {{ CommonHelper::formatPrice($contentPay['mailing_regis_cert_el']) }}円
                </span>
            </td>
        </tr>
    @endif

    @if (isset($contentPay['extension_of_period_before_expiry_text']) &&
        isset($contentPay['extension_of_period_before_expiry_value']) &&
        $contentPay['extension_of_period_before_expiry_value'])
        <tr>
            <td>{{ $contentPay['extension_of_period_before_expiry_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['extension_of_period_before_expiry_value']) }}円
                <input type="hidden" name="extension_of_period_before_expiry"
                    value="{{ $contentPay['extension_of_period_before_expiry_value'] }}" />
            </td>
        </tr>
    @endif
    @if (isset($contentPay['application_discount_text']) &&
        isset($contentPay['application_discount_value']) &&
        $contentPay['application_discount_value'])
        <tr>
            <td>{{ $contentPay['application_discount_text'] }}</td>
            <td class="right red">
                -{{ CommonHelper::formatPrice($contentPay['application_discount_value']) }}円
                <input type="hidden" name="application_discount"
                    value="{{ $contentPay['application_discount_value'] }}" />
            </td>
        </tr>
    @endif
    @if (isset($contentPay['cost_bank_text']) &&
        isset($contentPay['cost_bank_value']) &&
        $contentPay['cost_bank_value'])
        <tr>
            <td>{{ $contentPay['cost_bank_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_bank_value']) }}円
                <input type="hidden" name="cost_bank_transfer" value="{{ $contentPay['cost_bank_value'] }}" />
            </td>
        </tr>
    @endif
    @if (isset($contentPay['subtotal_text']) && isset($contentPay['subtotal_value']))
        <tr>
            <th style="min-width: 318px;" class="right">
                <strong style="font-size:1.2em;">{{ $contentPay['subtotal_text'] }}</strong>
            </th>
            <th class="right">
                <strong
                    style="font-size:1.2em;">{{ CommonHelper::formatPrice($contentPay['subtotal_value']) }}円</strong>
                <input type="hidden" name="subtotal" value="{{ $contentPay['subtotal_value'] ?? 0 }}" />
            </th>
        </tr>
    @endif

    @if ((isset($contentPay['commission_text']) && isset($contentPay['commission_value'])) ||
    (isset($contentPay['tax_value']) && isset($contentPay['tax_text'])))
            <tr>
            <th colspan="2" class="right">
                {{ $contentPay['commission_text'] . ' ' . CommonHelper::formatPrice($contentPay['commission_value'] ?? 0) }}
                円<br/>
                {{ $contentPay['tax_text'] . CommonHelper::formatPrice($contentPay['tax_value']) }}円
                <input type="hidden" name="tax" value="{{ $contentPay['tax_value'] ?? 0 }}"/>
                <input type="hidden" name="commission" value="{{ $contentPay['commission_value'] ?? 0 }}"/>
            </th>
        </tr>
    @endif

    @if (isset($contentPay['cost_5_year_one_distintion_text']) &&
    isset($contentPay['cost_5_year_one_distintion_value']) &&
    $contentPay['cost_5_year_one_distintion_value'])
        <tr>
            <td class="">{!! $contentPay['cost_5_year_one_distintion_text'] !!}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_5_year_one_distintion_value'] ?? 0) }}円
                 <input type="hidden" name="cost_5_year_one_distintion" value="{{ $contentPay['cost_5_year_one_distintion_value'] ?? 0 }}"/>
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_print_name_text']) &&
        isset($contentPay['cost_print_name_value']) &&
        $contentPay['cost_print_name_value'])
        <tr>
            <td class="">{{ $contentPay['cost_print_name_text'] }}</td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_print_name_value'] ?? 0) }}円
                <input type="hidden" name="cost_print_name" value="{{ $contentPay['cost_print_name_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_print_address_text']) &&
        isset($contentPay['cost_print_address_value']) &&
        $contentPay['cost_print_address_value'])
        <tr>
            <td class="">{{ $contentPay['cost_print_address_text'] }}
            </td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['cost_print_address_value'] ?? 0) }}円
                <input type="hidden" name="cost_print_address"
                    value="{{ $contentPay['cost_print_address_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_patent_office_text_1']) &&
        isset($contentPay['sum_distinction']) &&
        isset($contentPay['fee_submit_register']) &&
        $contentPay['fee_submit_register'])
        <tr>
            <td>{{ $contentPay['cost_patent_office_text_1'] }}
                <span id="sumDistintion">{{ $contentPay['sum_distinction'] }}</span>区分<br />
                <span id="one_division">1</span>区分
                <span
                    id="regis5Ys1">{{ CommonHelper::formatPrice($contentPay['pof_1st_distinction_5yrs'] ?? 0) }}</span>円+
                <span
                    id="regis5Ys2">{{ CommonHelper::formatPrice($contentPay['pof_2nd_distinction_5yrs'] ?? 0) }}</span>
                x<span id="mDistintionPayment">{{ $contentPay['sum_distinction'] - 1 }}</span>区分
                {{ __('labels.support_first_times.cart.stamp_fee') }}
            </td>
            <td class="right">
                <span id="fee_submit_register">{{ CommonHelper::formatPrice($contentPay['fee_submit_register']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_patent_office_text_1']) &&
        isset($contentPay['sum_distinction']) &&
        isset($contentPay['price_fee_submit_5_year']) &&
        isset($contentPay['fee_submit_register_year']) &&
        $contentPay['fee_submit_register_year'])
        <tr>
            <td>
                {{ $contentPay['cost_patent_office_text_1'] }}{{ $contentPay['stamp_fee_5yrs_text_v2'] ?? '' }}
                <span id="sumDistintion2">{{ $contentPay['sum_distinction'] }}</span>区分<br>
                <span
                    id="price_fee_submit_5_year">{{ CommonHelper::formatPrice($contentPay['price_fee_submit_5_year']) }}</span>
                円
            </td>
            <td class="right">
                <span
                    id="fee_submit_register_year">{{ CommonHelper::formatPrice($contentPay['fee_submit_register_year']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_patent_office_text_1']) &&
        isset($contentPay['sum_distinction']) &&
        isset($contentPay['price_fee_submit_5_year_v2']) &&
        isset($contentPay['fee_submit_register_year']) &&
        $contentPay['fee_submit_register_year'])
        <tr>
            <td>
                {{ $contentPay['cost_patent_office_text_1'] }}{{ $contentPay['stamp_fee_5yrs_text_v2'] ?? '' }}
                <span id="sumDistintion">{{ $contentPay['sum_distinction'] }}</span>区分<br />
                <span id="">{{ $contentPay['price_fee_submit_5_year_v2'] ?? '' }}</span>
            </td>
            <td class="right">
                <span
                    id="fee_submit_register_year">{{ CommonHelper::formatPrice($contentPay['fee_submit_register_year']) }}</span>円
            </td>
        </tr>
    @endif

    @if (isset($contentPay['print_fee_text']) &&
        isset($contentPay['print_fee_value']) &&
        $contentPay['print_fee_value'])
        <tr>
            <td class="">{{ $contentPay['print_fee_text'] }}
            </td>
            <td class="right">
                {{ CommonHelper::formatPrice($contentPay['print_fee_value'] ?? 0) }}円
                <input type="hidden" name="print_fee" value="{{ $contentPay['print_fee_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif

    @if (isset($contentPay['cost_patent_office_text']) &&
        isset($contentPay['price_fee_submit_5_year']))
        <tr>
            <td>
                <div>
                    {{ $contentPay['cost_patent_office_text'] }}
                </div>
                <div>
                    {{ $contentPay['cost_patent_office_text_2'] }}
                </div>
            </td>
            <td class="right">
                <span id="fee_submit_register_year">{{ CommonHelper::formatPrice($contentPay['fee_submit_register_year']) }}</span>円
            </td>
        </tr>
    @endif
    @if (isset($contentPay['expenses_to_PO_text']) &&
        $contentPay['expenses_to_PO_text'] &&
        isset($contentPay['expenses_to_PO_value']) &&
        $contentPay['expenses_to_PO_value']
    )
        <tr>
            <td>
                <span id="expenses_to_PO_text">
                    {{ $contentPay['expenses_to_PO_text'] ?? '' }}
                </span>
            </td>
            <td class="right">
                <span id="expenses_to_PO_value">
                    {{ CommonHelper::formatPrice($contentPay['expenses_to_PO_value'] ?? 0, '円', 0) }}
                </span>
            </td>
        </tr>
    @endif
    @if (isset($contentPay['total_text']) && isset($contentPay['total_value']))
        <tr>
            <th class="right">
                <strong style="font-size:1.2em;">{{ $contentPay['total_text'] }}</strong>
            </th>
            <th class="right">
                <strong
                    style="font-size:1.2em;">{{ CommonHelper::formatPrice($contentPay['total_value'] ?? 0) }}円</strong>
                <input type="hidden" name="total_amount" value="{{ $contentPay['total_value'] ?? 0 }}" />
            </th>
        </tr>
    @endif

    @if (isset($contentPay['tax_withholding_text']) &&
        isset($contentPay['tax_withholding_value']) &&
        isset($payerInfo->payer_type) &&
        $payerInfo->payer_type == App\Models\User::INFO_TYPE_ACC_GROUP &&
        isset($payerInfo->m_nation_id) &&
        $payerInfo->m_nation_id == NATION_JAPAN_ID)
        <tr>
            <td class="right">{{ $contentPay['tax_withholding_text'] }}</td>
            <td class="right">
                <span
                    class="red">{{ CommonHelper::formatPrice($contentPay['tax_withholding_value'] ?? 0) }}円</span>
                <input type="hidden" name="tax_withholding"
                    value="{{ $contentPay['tax_withholding_value'] ?? 0 }}" />
            </td>
        </tr>
    @endif
    @if (isset($contentPay['payment_amount_text']) && isset($contentPay['payment_amount_value']))
        <tr>
            <th class="right"><strong style="font-size:1.2em;">{{ $contentPay['payment_amount_text'] }}</strong>
            </th>
            <th class="right">
                <strong
                    style="font-size:1.2em;">{{ CommonHelper::formatPrice($contentPay['payment_amount_value'] ?? 0) }}円</strong>
                <input type="hidden" name="payment_amount"
                    value="{{ $contentPay['payment_amount_value'] ?? 0 }}" />
            </th>
        </tr>
    @endif
</table>
