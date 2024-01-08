@if(isset($payment->cost_registration_certificate) && $payment->cost_registration_certificate > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.cost_registration_certificate') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_registration_certificate ?? 0, '', 0) }}</td>
    </tr>
@endif
