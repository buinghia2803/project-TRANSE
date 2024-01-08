<tr class="total">
    <th class="right">{{ __('labels.payment_table.payment_amount') }}</th>
    <td><strong>&yen; {{ CommonHelper::formatPrice($payment->payment_amount ?? 0, '', 0) }}</strong></td>
</tr>
