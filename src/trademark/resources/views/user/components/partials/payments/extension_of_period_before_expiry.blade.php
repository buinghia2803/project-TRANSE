@if(isset($payment->extension_of_period_before_expiry) && $payment->extension_of_period_before_expiry > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.extension_of_period_before_expiry') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->extension_of_period_before_expiry ?? 0, '', 0) }}</td>
    </tr>
@endif
