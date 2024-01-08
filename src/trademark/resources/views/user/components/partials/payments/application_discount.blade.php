@if(isset($payment->application_discount) && $payment->application_discount > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.application_discount') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->application_discount ?? 0, '', 0) }}</td>
    </tr>
@endif
