@if(!empty($payment->tax_withholding) && $payment->tax_withholding > 0)
    <tr>
        <th class="right">{{ __('labels.payment_table.tax_withholding') }}</th>
        <td><span class="red">&yen; {{ CommonHelper::formatPrice($payment->tax_withholding ?? 0, '', 0) }}</span></td>
    </tr>
@endif
