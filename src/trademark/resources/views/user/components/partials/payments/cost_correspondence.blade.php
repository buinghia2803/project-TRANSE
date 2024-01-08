@if(isset($payment->cost_correspondence_of_all_prod) && $payment->cost_correspondence_of_all_prod > 0)
    <tr>
        <td class="item">
            {{ __('labels.payment_table.cost_correspondence.title', [
                'total_distinction' => $payment->total_distinction ?? 0,
            ]) }}<br>
            {{ __('labels.payment_table.cost_correspondence.desc', [
                'costs_correspondence_of_one_prod' => CommonHelper::formatPrice($payment->costs_correspondence_of_one_prod ?? 0, '', 0),
                'total_distinction' => $payment->total_distinction ?? 0,
            ]) }}
        </td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_correspondence_of_all_prod ?? 0, '', 0) }}</td>
    </tr>
@endif
