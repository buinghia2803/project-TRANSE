<div class="estimateBox">
    <input type="checkbox" id="cart">
    <label class="button" for="cart">
        <span class="open">{{ __('labels.box_cart.cart_title_open') }}</span>
        <span class="close">{{ __('labels.box_cart.cart_title_close') }}</span>
    </label>

    <div class="estimateContents">
        <h3>【{{ __('labels.box_cart.title') }}】</h3>

        <table class="normal_b" id="estimate-box-table">
            <tbody>
            <tr class="price_service">
                <td class="label">{{ __('labels.user_common_payment.free_histories') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="cost_bank_transfer_fee">
                <td class="label">{{ __('labels.user_common_payment.bank_transfer_fee') }}</td>
                <td class="right price"></td>
            </tr>
            <tr class="subtotal_fee">
                <th class="right label">{{ __('labels.support_first_times.total') }}</th>
                <th class="right">
                    <strong class="price" style="font-size:1.2em;"></strong>
                </th>
            </tr>
            <tr class="subtotal_tax_fee">
                <th class="right label" colspan="2">
                    {{ __('labels.u302_402_5yr_kouki.cart.actual_fee') }}　<span class="actual_fee"></span><br>
                    {{ __('labels.u302_402_5yr_kouki.cart.tax_fee', ['attr' => $setting->value]) }}　<span class="tax_fee"></span>
                </th>
            </tr>
            </tbody>
        </table>

        <p class="red mb10">{{ __('labels.box_cart.note_box_cart') }}</p>

        <input type="hidden" name="payment[cost_service_base]" value="">
        <input type="hidden" name="payment[cost_bank_transfer]" value="">
        <input type="hidden" name="payment[subtotal]" value="">
        <input type="hidden" name="payment[commission]" value="">
        <input type="hidden" name="payment[tax]" value="">
        <input type="hidden" name="payment[total_amount]" value="">

        <ul class="right list">
            <li><input type="button" value="{{ __('labels.box_cart.get_info_payment') }}" class="btn_a"></li>
        </ul>

        <ul class="right list">
            <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_QUOTE }}" value="{{ __('labels.box_cart.go_to_quotes') }}" class="btn_a"></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" data-submit="{{ REDIRECT_TO_COMMON_PAYMENT }}" value="{{ __('labels.support_first_times.apply_content') }}" class="btn_e big"></li>
        </ul>
    </div>
</div>
