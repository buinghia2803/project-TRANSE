<div class="estimateBox">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span class="open">{{ __('labels.list_change_address.cart.text_1') }}</span>
        <span class="close">{{ __('labels.list_change_address.cart.text_2') }}</span></label>
    <div class="estimateContents">
        <h3>{{ __('labels.list_change_address.cart.text_3') }}</h3>
        <table class="normal_b">
            <tr id="hidden_fee_change_name">
                <td>{{ __('labels.list_change_address.cart.text_16') }}</td>
                <td class="right"><span
                        id="fee_change_name">{{ isset($payment) ? CommonHelper::formatPrice($payment->cost_change_name) : CommonHelper::formatPrice($priceServiceChangeNameFee ?? 0) }}</span>円
                </td>
            </tr>
            <tr id="hidden_fee_change_address">
                <td>{{ __('labels.list_change_address.cart.text_5') }}</td>
                <td class="right"><span
                        id="fee_change_address">{{ isset($payment) ? CommonHelper::formatPrice($payment->cost_change_address) : CommonHelper::formatPrice($priceServiceChangeAddressFee ?? 0) }}</span>円
                </td>
                <input type="hidden" id="value_fee_change_address"
                    value="{{ isset($payment) ? CommonHelper::formatPrice($payment->cost_change_address) : CommonHelper::formatPrice($priceServiceChangeAddressFee ?? 0) }}">
            </tr>
            <tr id="hidden_cost_bank_transfer" style="display: none">
                <td>{{ __('labels.list_change_address.cart.text_6') }}</td>
                <td class="right"><span
                        id="cost_bank_transfer">{{ isset($payment) ? CommonHelper::formatPrice($payment->cost_bank_transfer) : CommonHelper::formatPrice($costBankTransfer ?? 0) }}</span>円
                </td>
            </tr>
            <tr>
                <th class="right"><strong>{{ __('labels.list_change_address.cart.text_7') }}</strong></th>
                <th class="right"><strong>
                        <span class="subtotal">
                            {{ isset($payment)
                                ? ($payment->payerInfo->payment_type == 1
                                    ? CommonHelper::formatPrice($payment->cost_change_name + $payment->cost_change_address)
                                    : CommonHelper::formatPrice(
                                        $payment->cost_change_name + $payment->cost_change_address + $payment->cost_bank_transfer,
                                    ))
                                : 0 }}
                        </span>円</strong></th>
            </tr>

            <tr>
                <th class="right" colspan="2" id="hidden_actual_fee">
                    {{ __('labels.list_change_address.cart.text_8') }}<span id="actual_fee">
                        {{ isset($payment) && isset($basePriceServiceChangeAddress) && isset($basePriceServiceChangeName)
                            ? ($payment->payerInfo->payment_type == 1
                                ? CommonHelper::formatPrice(
                                    $basePriceServiceChangeAddress->base_price + $basePriceServiceChangeName->base_price,
                                )
                                : CommonHelper::formatPrice(
                                    $basePriceServiceChangeAddress->base_price +
                                        $basePriceServiceChangeName->base_price +
                                        $baseCostBankTransfer->base_price,
                                ))
                            : 0 }}
                    </span>
                    円<br />
                    {{ __('labels.list_change_address.cart.text_9', ['attr' =>  $setting->value ]) }}<span id="tax">
                        {{ isset($payment) && isset($basePriceServiceChangeAddress) && isset($basePriceServiceChangeName)
                            ? ($payment->payerInfo->payment_type == 1
                                ? CommonHelper::formatPrice(
                                    $payment->cost_change_name +
                                        $payment->cost_change_address -
                                        ($basePriceServiceChangeAddress->base_price + $basePriceServiceChangeName->base_price),
                                )
                                : CommonHelper::formatPrice(
                                    $payment->cost_change_name +
                                        $payment->cost_change_address +
                                        $payment->cost_bank_transfer -
                                        ($basePriceServiceChangeAddress->base_price +
                                            $basePriceServiceChangeName->base_price +
                                            $baseCostBankTransfer->base_price),
                                ))
                            : 0 }}
                    </span>円
                </th>
            </tr>
            <tr id="hidden_cost_print_name">
                <td>{{ __('labels.list_change_address.cart.text_10') }}<br />
                    {{ __('labels.list_change_address.cart.text_11') }}</td>
                <td class="right">
                    <span id="cost_print_address">
                        {{
                        isset($tradeMark->appTrademark) && $tradeMark->appTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR
                        ? CommonHelper::formatPrice($priceServiceChangeAddress->pof_1st_distinction_5yrs)
                        : CommonHelper::formatPrice($priceServiceChangeAddress->pof_1st_distinction_10yrs)
                        }}
                    </span>円
                </td>
                <input type="hidden" id="value_cost_print_address"
                    value="{{ CommonHelper::formatPrice($priceServiceChangeAddress->pof_1st_distinction_5yrs) }}">
            </tr>
            <tr id="hidden_cost_print_address">
                <td>{{ __('labels.list_change_address.cart.text_12') }}<br />
                    {{ __('labels.list_change_address.cart.text_13') }} </td>
                <td class="right">
                    <span id="cost_print_name">
                        @if($changeInfoRegisterDraft && $changeInfoRegisterDraft->is_change_address_free == $isChangeAddressFreeTrue)
                            0
                        @else
                            {{
                                isset($tradeMark->appTrademark) && $tradeMark->appTrademark->period_registration == PERIOD_REGISTRATION_FIVE_YEAR
                                ? CommonHelper::formatPrice($priceServiceChangeName->pof_1st_distinction_5yrs)
                                : CommonHelper::formatPrice($priceServiceChangeName->pof_1st_distinction_10yrs)
                            }}
                        @endif
                    </span>円
                </td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.list_change_address.cart.text_14') }}</th>
                <th class="right" nowrap><strong class="fs12"><span id="total_amount">
                            {{ isset($payment)
                                ? ($payment->payerInfo->payment_type == 1
                                    ? CommonHelper::formatPrice($payment->cost_change_name + $payment->cost_change_address)
                                    : CommonHelper::formatPrice(
                                        $payment->cost_change_name + $payment->cost_change_address + $payment->cost_bank_transfer,
                                    ))
                                : 0 }}</span>
                        円</strong></th>
            </tr>
            <input type="hidden" id="base_price_change_address" value="{{ $priceServiceChangeAddress->base_price }}">
            <input type="hidden" id="base_price_change_name" value="{{ $priceServiceChangeName->base_price }}">
            <input type="hidden" id="base_price_cost_bank_transfer" value="{{ $costBankTransferBase->base_price }}">
        </table>
        <p class="red mb10">{{ __('labels.list_change_address.cart.text_15') }}</p>
        <ul class="right list">
            <li><input type="button" value="再計算" class="btn_a" /></li>
        </ul>
        <ul class="right list">
            <li><input type="submit" value="保存・見積書表示" class="btn_a"
                    data-submit="{{ REDIRECT_TO_COMMON_QUOTE_KENRISHA }}" />
            </li>
        </ul>
        <ul class="footerBtn right clearfix">
            <li><input type="submit" value="この内容で申込む" class="btn_e big"
                    data-submit="{{ REDIRECT_TO_COMMON_PAYMENT_KENRISHA }}" /></li>
        </ul>
    </div>
    <!-- /estimate contents -->
</div>
<!-- /estimate box -->
