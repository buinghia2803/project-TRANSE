<div class="estimateBox" id="estimateBox">
    <input type="checkbox" id="cart" /><label class="button" for="cart"><span class="open">{{ __('labels.u210.estimate_1') }}</span><span
            class="close">{{ __('labels.u210.estimate_2') }}</span></label>

    <div class="estimateContents">

        <h3>{{ __('labels.u210.estimate_3') }}</h3>
        <table class="normal_b">
            <tr>
                <td>{{ __('labels.u210.text_6') }}</td>
                <td class="right"><span class="cost_service"></span> 円</td>
            </tr>
            <tr id="hidden_cost_bank_transfer">
                <td>{{ __('labels.plan.cart.bank_transfer') }}</td>
                <td class="right"><span id="cost_bank_transfer"></span> 円<br /></td>
            </tr>
            <tr id="hidden_commission_tax">
                <th class="right">{{ __('labels.u210.estimate_5') }}<br />{{ __('labels.u210.estimate_6') }}<span id="tax_percent"></span> {{ __('labels.u210.estimate_7') }}</th>
                <th class="right"><span id="commission"></span> 円<br /><span id="tax"></span> 円
                </th>
            </tr>
            <tr>
                <td style="width:34em;">{{ __('labels.u210.estimate_8') }}</td>
                <td class="right"><span class="cost_print_fee"></span> 円<br /></td>
            </tr>
            <tr>
                <th class="right">{{ __('labels.u210.estimate_9') }}</th>
                <th class="right" nowrap><strong style="font-size:1.2em;"><span id="total_amount"></span> 円</strong>
                </th>
            </tr>
        </table>
        <p class="red mb10">{{ __('labels.u210.estimate_10') }}</p>

        <ul class="right list">
            <li><input type="button" value="{{ __('labels.u210.estimate_11') }}" class="btn_a" /></li>
        </ul>
        <ul class="right list">
            <li><input type="submit" value="{{ __('labels.u210.estimate_12') }}" class="btn_a" data-submit="{{ QUOTE }}" /></li>
        </ul>

        <ul class="footerBtn right clearfix">
            <li><input type="submit" value="{{ __('labels.u210.estimate_13') }}" class="btn_e" data-submit="{{ SUBMIT }}" /></li>
        </ul>
    </div>
    <!-- /estimate contents -->
</div>
