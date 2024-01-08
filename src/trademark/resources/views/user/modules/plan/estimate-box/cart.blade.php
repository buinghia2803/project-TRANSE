<div class="estimateBox">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span
            class="open">{{ __('labels.plan.cart.text_1') }}</span><span
            class="close">{{ __('labels.plan.cart.text_2') }}</span></label>

    <div class="estimateContents">
        <h3>{{ __('labels.plan.cart.text_15') }}</h3>
        <table class="normal_b">
            <tr id="hidden_cost_service_base">
                <td>{{ $flag == PLAG_SELECT_01 ? __('labels.plan.cart.title_select') : __('labels.user_common_payment.cost_service_base_u201simple01') }}</td>
                <td class="right"><span id="cost_service_base"></span> 円</td>
            </tr>
            <tr>
                <td>{{ $flag == PLAG_SELECT_01 ? __('labels.user_common_payment.cost_service_add_prod_u201select01n') : __('labels.plan.cart.text_4') }}<br />
                    {{ __('labels.plan.cart.text_5') }} <span
                        class="amount_product"></span>{{ __('labels.plan.cart.text_16') }}
                    {{ CommonHelper::formatPrice($costServiceAddProd) }}円）
                </td>
                <td class="right"><span id="cost_service_add_prod"></span> 円</td>
            </tr>
            <tr class="hidden_ext_period">
                <td>{{ __('labels.plan.cart.text_6') }}</td>
                <td class="right"><span id="extension_of_period_before_expiry"></span> 円</td>
            </tr>
            <tr class="hidden_ext_period">
                <td>{{ __('labels.plan.cart.text_7') }}</td>
                <td class="right">
                    <font color="red">-{{ CommonHelper::formatPrice($priceDiscount) }} 円</font>
                </td>
            </tr>
            <tr id="hidden_cost_bank_transfer">
                <td>{{ __('labels.plan.cart.bank_transfer') }}</td>
                <td class="right"><span
                        id="cost_bank_transfer">{{ CommonHelper::formatPrice($costBankTransfer) ?? 0 }} </span>円
                </td>
            </tr>
            <tr>
                <th class="right">小計</th>
                <th class="right"><span id="sub_total"></span> 円</th>
            <tr id="hidden_commission_tax">
                <th class="right" colspan="2">
                    {{ __('labels.plan.cart.text_8') }} <span id="commission"></span> 円<br />
                    {{ __('labels.plan.cart.text_9', ['attr' => floor($setting->value * 100)/100]) }}　<span id="tax"></span> 円
                </th>
            </tr>
            <tr class="hidden_ext_period">
                <td>{{ __('labels.plan.cart.text_10') }}</td>
                <td class="right"><span id="print_fee"></span> 円</td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.plan.cart.text_11') }}</th>
                <th class="right" nowrap><strong style="font-size:1.2em;"><span id="total_amount"></span>
                        円</strong></th>
            </tr>
        </table>
        <p class="red mb10">{{ __('labels.plan.cart.text_12') }}</p>

        <ul class="right list">
            <li><input type="button" value="{{ __('labels.plan.cart.text_13') }}" class="btn_a" /></li>
        </ul>
        <ul class="right list">
            <li><input type="submit" value="{{ __('labels.plan.cart.text_14') }}" class="btn_a"
                    data-submit="{{ $quote }}" /></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" value="{{ __('labels.plan.simple.text_20') }}" class="btn_e big"
                    data-submit="{{ $data_submit }}" />
            </li>
        </ul>
    </div>
    <!-- /estimate contents -->
</div>
