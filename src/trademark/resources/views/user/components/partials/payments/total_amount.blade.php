@if(!empty($payment->total_amount) && $payment->total_amount > 0)
    <tr>
        <th class="right">{{ __('labels.payment_table.total_amount') }}</th>
        <td>&yen; {{ CommonHelper::formatPrice($payment->total_amount ?? 0, '', 0) }}</td>
    </tr>
@endif
