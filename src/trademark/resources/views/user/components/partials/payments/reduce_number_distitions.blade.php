@if(isset($payment->reduce_number_distitions) && $payment->reduce_number_distitions > 0)
    <tr>
        <td class="item">{{ __('labels.payment_table.reduce_number_distitions') }}</td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->reduce_number_distitions ?? 0, '', 0) }}</td>
    </tr>
@endif
