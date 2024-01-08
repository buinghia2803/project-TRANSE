@if(isset($payment->cost_bank_transfer) && $payment->cost_bank_transfer > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.cost_bank_transfer') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_bank_transfer ?? 0, '', 0) }}</td>
    </tr>
@endif
