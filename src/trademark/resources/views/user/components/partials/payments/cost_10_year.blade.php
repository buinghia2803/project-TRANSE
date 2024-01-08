@if(isset($payment->cost_10_year_all_distintion) && $payment->cost_10_year_all_distintion > 0)
    @php $payment->type = 10 @endphp
    @switch($payment->type)
        @case(\App\Models\Payment::RENEWAL_DEADLINE)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_10_year.title_2', [
                        'total_distinction' => $payment->total_distinction ?? 0,
                        'cost_10_year_one_distintion' => CommonHelper::formatPrice($payment->cost_10_year_one_distintion ?? 0, '円', 0),
                    ]) }}<br>
                    {{ __('labels.payment_table.cost_10_year.desc_2', [
                        'total_distinction' => $payment->total_distinction ?? 0,
                        'cost_10_year_one_distintion' => CommonHelper::formatPrice($payment->cost_10_year_one_distintion ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_10_year_all_distintion ?? 0, '', 0) }}</td>
            </tr>
            @break
        @default
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_10_year.title', [
                        'total_distinction' => $payment->total_distinction ?? 0,
                        'cost_10_year_one_distintion' => CommonHelper::formatPrice($payment->cost_10_year_one_distintion ?? 0, '円', 0),
                    ]) }}<br>
                    {{ __('labels.payment_table.cost_10_year.desc') }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_10_year_all_distintion ?? 0, '', 0) }}</td>
            </tr>
    @endswitch
@endif
