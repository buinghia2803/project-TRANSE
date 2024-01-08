<!-- estimate box -->
<div class="estimateBox" id="info_cart_precheck">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span class="open">お見積金額を見る</span><span class="close">お見積金額を閉じる</span></label>

    <div class="estimateContents">

        <h3>{{ __('labels.list_change_address.cart.text_3') }}</h3>
        <table class="normal_b">
            <tr>
                <td>プレチェックサービス：<span class="label_type_precheck_cart">簡易レポート</span>（3商品名まで）</td>
                <td class="right">
                    <span class="cost_service_base"></span>円
                </td>
            </tr>
            <tr>
                <td>{{ __('labels.box_cart.cost_service_add_prod') }}</td>
                <td class="right">
                    <span class="cost_service_add_prod"></span>円
                </td>
            </tr>
            <tr class="tr_cost_bank_transfer">
                <td>{{ __('labels.box_cart.cost_bank_transfer') }}</td>
                <td class="right">
                    <span class="cost_bank_transfer"></span>円
                </td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.box_cart.subtotal') }}</th>
                <th class="right">
                    <strong style="font-size:1.2em;">
                        <span class="subtotal"></span>円
                    </strong>
                </th>
            </tr>
            <tr class="info-tax info-commission">
                <th class="right" colspan="2">
                    <div> {{ __('labels.box_cart.commission') }}　<span class="commission"></span>円<br /></div>
                    <div>{{ __('labels.box_cart.tax_percentage') }}（<span class="tax_percentage"></span>％）　<span class="tax"></span>円</div>
                </th>
            </tr>
        </table>
        <p class="red mb10">{{ __('labels.box_cart.note_box_cart') }}</p>

        <ul class="right list">
            <li><input type="button" value="{{ __('labels.box_cart.get_info_payment') }}" class="btn_a getInfoPayment" /></li>
        </ul>

        <ul class="right list">
            <li><input type="submit" value="{{ __('labels.box_cart.go_to_quotes') }}" class="btn_a saveToShowQuotes" data-code="quotes" /></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" value="{{ __('labels.box_cart.btn_submit') }}" class="btn_e big goToCommonPayment" data-code="payment" /></li>
        </ul>
    </div><!-- /estimate contents -->

</div><!-- /estimate box -->
