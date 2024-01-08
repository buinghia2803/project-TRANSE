<div class="estimateBox">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span class="open">{{ __('labels.u031b.open_cart') }}</span><span class="close">{{ __('labels.u031b.close_cart') }}</span></label>

    <div class="estimateContents">

        <h3>{{ __('labels.u031b.title_cart') }}</h3>
        <table class="normal_b">
            <tr>
                <td>
                    <span class="label_text_by_pack">{{ __('labels.u031b.cost_service_base') }}</span><br>
                    <span class="total_product_choose">0</span>{{ __('labels.u031b.product_name') }} <br>
                    <span>{{__('labels.user_common_payment.breakdown')}}</span><br>
                    <span>{{__('labels.u031d.label_cost_service_base')}} </span> <span class="cost_service_base">0</span>円）<br>
                    <span class="count_service_add_prod">0</span>{{ __('labels.u031d.prod_add_more') }}
                    {{__('labels.u031d.add_3_prod')}}<span class="cost_service_add_prod"></span>円）
                    <br />
                    <span class="cost_prod_price">0</span>円
                </td>
                <td class="right"><span class="sum_prod_price"></span>円<br /></td>
            </tr>
            <tr class="tr_cost_registration_certificate">
                <td>{{ __('labels.payment_table.cost_registration_certificate') }}</td>
                <td class="right"><span class="cost_registration_certificate">0</span>円</td>
            </tr>
            <tr class="tr_cost_bank_transfer">
                <td style="width:34em;">{{ __('labels.u031b.cost_bank_transfer') }}</td>
                <td class="right"><span class="cost_bank_transfer"></span>円<br /></td>
            </tr>
            <tr class="tr_change_5yrs_to_10yrs">
                <td>{{ __('labels.u031b.change_5yrs_to_10yrs') }}</td>
                <td class="right">
                    <span id="change_5yrs_to_10yrs">0</span>円
                </td>
            </tr>
{{--            <tr class="tr_cost_registration_certificate">--}}
{{--                <td>{{ __('labels.u031b.cost_registration_certificate') }}</td>--}}
{{--                <td class="right"><span class="cost_registration_certificate">0</span>円</td>--}}
{{--            </tr>--}}
            <tr>
                <th class="right"><strong><span class="total_product_choose">0</span>{{ __('labels.u031b.total_product_choose') }}</strong></th>
                <th class="right"><strong><span class="subtotal"></span>円</strong></th>
            </tr>
            <tr class="tr_commission">
                <th class="right" colspan="2">
                    {{ __('labels.u031b.commission') }}　<span class="commission"></span>円<br />
                    {{ __('labels.u031b.tax') }}（<span class="tax_percentage">0</span>％）　<span class="tax">0</span>円</th>
            </tr>
            <tr>
                <td style="width:34em;">{{ __('labels.u031b.count_distintion_choose') }}　<span class="count_distintion_choose">0</span>区分<br />
                    <span class="count_cost_print_application_one_distintion">0</span>区分
                    <span class="cost_print_application_one_distintion">0</span>円+
                    <span class="cost_print_application_add_distintion">0</span>円x
                    <span class="count_cost_print_application_add_distintion">0</span>区分
                </td>
                <td class="right"><span class="total_fee_register_for_csc">0</span>円</td>
            </tr>
            <tr id="tr_fee_submit_register_year">
                <td>
                    {{ __('labels.u031b.count_distintion_choose') }}
                    <span id="sumDistintion2">0</span>区分
                    <span id="cost_year_one_distintion">0</span>円<br>
                    <span>（<span id="text_5yrs_10yrs2"></span>{{ __('labels.u031b.text_5yrs_10yrs2') }}）</span>
                </td>
                <td class="right">
                    <span id="fee_submit_register_year">0</span>円
                    <input type="text" name="value_fee_submit_ole" id="value_fee_submit_ole" value="0">
                </td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.u031b.total') }}）：</th>
                <th class="right" nowrap>
                    <strong class="fs12">
                        <span id="total_amount">0</span>円
                    </strong>
                </th>
            </tr>
        </table>
        <p class="red mb10">{{ __('labels.u031b.note_cart') }}</p>

        <ul class="right list">
            <li><input type="button" value="{{__('labels.support_first_times.recalculation')}}" class="btn_a recalculationCart" /></li>
        </ul>

        <ul class="right list">
            <li><input type="submit" value="{{__('labels.support_first_times.save_display_quotation')}}" class="btn_a submitRedirectToQuoute" /></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" value="{{__('labels.support_first_times.apply_content')}}" class="btn_e big submitRedirectToCommonPayment"/>
            </li>
        </ul>

    </div><!-- /estimate contents -->
</div><!-- /estimate box -->
