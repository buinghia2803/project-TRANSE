@if(isset($payment->cost_change_name) && $payment->cost_change_name > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.cost_change_name') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_change_name ?? 0, '', 0) }}</td>
    </tr>
@endif
