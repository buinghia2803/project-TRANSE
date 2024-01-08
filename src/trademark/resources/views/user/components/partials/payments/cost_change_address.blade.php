@if(isset($payment->cost_change_address) && $payment->cost_change_address > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.cost_change_address') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_change_address ?? 0, '', 0) }}</td>
    </tr>
@endif
