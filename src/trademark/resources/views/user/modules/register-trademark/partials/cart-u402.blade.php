<!-- estimate box -->
<div class="estimateBox">
    <input type="checkbox" id="cart"><label class="button" for="cart"><span
            class="open">お見積金額を見る</span><span class="close">お見積金額を閉じる</span></label>

    <div class="estimateContents">

        <h3>【お見積合計金額】</h3>
        <table class="normal_b" id="estimate-box-table">
            <tbody>
            <tr class="price_service">
                <td class="label">{{ __('labels.u402.cart.price_service') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="price_service_add_prod">
                <td class="label" data-text="{{ __('labels.u402.cart.price_service_add_prod') }}"></td>
                <td class="right price"></td>
            </tr>
            <tr class="reduce_distinctions">
                <td class="label">{{ __('labels.u402.cart.reduce_distinctions') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="change_name_fee">
                <td class="label">{{ __('labels.u302_402_5yr_kouki.cart.change_name_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="change_address_fee">
                <td class="label">{{ __('labels.u302_402_5yr_kouki.cart.change_address_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="cost_bank_transfer_fee">
                <td class="label">{{ __('labels.u302_402_5yr_kouki.cart.cost_bank_transfer_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="subtotal_fee">
                <th class="right label" data-text="{{ __('labels.u402.cart.subtotal_fee') }}"></th>
                <th class="right price"></th>
            </tr>
            <tr class="subtotal_tax_fee">
                <th class="right label" colspan="2">
                    {{ __('labels.u302_402_5yr_kouki.cart.actual_fee') }}　<span class="actual_fee"></span><br>
                    {{ __('labels.u302_402_5yr_kouki.cart.tax_fee', ['attr' => $setting->value]) }}　<span class="tax_fee"></span>
                </th>
            </tr>
            <tr class="print_price_service_fee">
                <td style="width:34em;" class="label" data-text="{{ __('labels.u402.cart.print_price_service_fee') }}"></td>
                <td class="right price"></td>
            </tr>
            <tr class="print_change_name_fee">
                <td class="label">{{ __('labels.u302_402_5yr_kouki.cart.print_change_name_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="print_change_address_fee">
                <td class="label">{{ __('labels.u302_402_5yr_kouki.cart.print_change_address_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="total_fee">
                <th class="right label" data-text="{{ __('labels.u402.cart.total_fee') }}"></th>
                <th class="right price"></th>
            </tr>
            <tr class="total_amount_fee">
                <th class="right label">{{ __('labels.u302_402_5yr_kouki.cart.total_amount_fee') }}</th>
                <th class="right" nowrap=""><strong style="font-size:1.2em;" class="price"></strong></th>
            </tr>
            </tbody>
        </table>

        <p class="red mb10">{{ __('labels.box_cart.note_box_cart') }}</p>

        <input type="hidden" name="payment[cost_service_base]" value="">
        <input type="hidden" name="payment[cost_service_add_prod]" value="">
        <input type="hidden" name="payment[reduce_distinctions]" value="">
        <input type="hidden" name="payment[cost_change_name]" value="">
        <input type="hidden" name="payment[cost_change_address]" value="">
        <input type="hidden" name="payment[cost_bank_transfer]" value="">
        <input type="hidden" name="payment[subtotal]" value="">
        <input type="hidden" name="payment[commission]" value="">
        <input type="hidden" name="payment[tax]" value="">
        <input type="hidden" name="payment[cost_5_year_one_distintion]" value="">
        <input type="hidden" name="payment[cost_10_year_one_distintion]" value="">
        <input type="hidden" name="payment[cost_print_name]" value="">
        <input type="hidden" name="payment[cost_print_address]" value="">
        <input type="hidden" name="payment[total_amount]" value="">

        <ul class="right list">
            <li><input type="button" value="{{ __('labels.box_cart.get_info_payment') }}" class="btn_a"></li>
        </ul>

        <ul class="right list">
            <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_QUOTE }}" value="{{ __('labels.box_cart.go_to_quotes') }}" class="btn_a"></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_PAYMENT }}" value="{{ (isset($isPackC) && $isPackC == true) ? __('labels.u302_402_5yr_kouki.goto_common_payment_pack_c') : __('labels.box_cart.go_to_payment') }}" class="btn_e big"></li>
        </ul>
    </div>
</div>
