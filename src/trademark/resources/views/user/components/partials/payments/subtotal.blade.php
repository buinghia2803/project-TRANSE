<tr>
    <th class="right">
        @if (!empty($payment->total_prod) && $payment->total_prod > 0)
            {{ $payment->total_prod }}商品名
        @endif
        {{ __('labels.payment_table.subtotal') }}
    </th>
    <th class="right">&yen; {{ CommonHelper::formatPrice($payment->subtotal ?? 0, '', 0) }}<br /></th>
</tr>
<tr>
    <th class="right" colspan="2">
        @if (!empty($payment->commission) && $payment->commission > 0)
            {{ __('labels.payment_table.commission') }}　{{ CommonHelper::formatPrice($payment->commission ?? 0, '円', 0) }}<br />
        @endif

        @if (!empty($payment->tax) && $payment->tax > 0)
            {{ __('labels.payment_table.tax', ['attr' => number_format(($payment->tax / $payment->subtotal) * 100, 0)]) }}　{{ CommonHelper::formatPrice($payment->tax ?? 0, '円', 0) }}
        @endif
    </th>
</tr>
