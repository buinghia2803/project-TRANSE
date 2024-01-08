@if(isset($payment->cost_print_name) && $payment->cost_print_name > 0)
    <tr>
        <td class="item">
            @if(in_array($payment->type, [ \App\Models\Payment::CHANG_NAME ]))
                {{ __('labels.payment_table.cost_print_csc') }}<br>
            @endif
            {{ __('labels.payment_table.cost_print_name') }}
        </td>
        <td>&yen; {{ CommonHelper::formatPrice($payment->cost_print_name ?? 0, '', 0) }}</td>
    </tr>
@endif
