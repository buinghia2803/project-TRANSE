@if(isset($payment->cost_print_app) && $payment->cost_print_app > 0)
    <tr>
        <td class="item">
            {{ __('labels.payment_table.cost_print_application.title', [
                'total_distinction' => $payment->total_distinction ?? 0,
            ]) }}<br>
            {{ __('labels.payment_table.cost_print_application.desc', [
                'cost_print_application_one_distintion' => CommonHelper::formatPrice($payment->cost_print_application_one_distintion ?? 0, '', 0),
                'cost_print_application_add_distintion' => CommonHelper::formatPrice($payment->cost_print_application_add_distintion ?? 0, '', 0),
                'total_distinction_block_one' => $payment->total_distinction_block_one ?? 0,
            ]) }}
        </td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_print_app ?? 0, '', 0) }}</td>
    </tr>
@endif
