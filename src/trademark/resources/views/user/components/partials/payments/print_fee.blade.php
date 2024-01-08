@if(isset($payment->print_fee) && $payment->print_fee > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.print_fee') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->print_fee ?? 0, '', 0) }}</td>
    </tr>
@endif
