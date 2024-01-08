@if(isset($payment->cost_service_base) && $payment->cost_service_base > 0)
    @switch($payment->type)
        @case(\App\Models\Payment::TYPE_TRADEMARK)
        @case(\App\Models\Payment::TYPE_SUPPORT_FIRST_TIME_AMS)
        @case(\App\Models\Payment::TYPE_PRECHECK_AMS)
            <tr>
                <td class="item">
                    {{ $payment->app_trademark->pack_name ?? '' }}<br>
                    {{ __('labels.payment_table.cost_service.app_trademark.pack_info', [
                        'pack_detail' => $payment->app_trademark->pack_detail ?? '',
                        'total_prod' => $payment->total_prod ?? 0,
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_base ?? 0, '', 0) }}</td>
            </tr>
            @if($payment->total_prod > 3)
                <tr>
                    <td class="item">
                        {{ __('labels.payment_table.cost_service.app_trademark.cost_service_total_block', [
                            'total_prod_block' => $payment->total_prod_block ?? 0,
                            'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                        ]) }}
                    </td>
                    <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_total_block ?? 0, '', 0) }}</td>
                </tr>
            @endif
            @break

        @case(\App\Models\Payment::TYPE_SUPPORT_FIRST_TIME)
            <tr>
                <td class="item">{{ __('labels.payment_table.cost_service.support_first_time') }}</td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_base ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::TYPE_PRECHECK)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.precheck.info', [
                        'precheck_type' => $payment->precheck->getTextPrecheckType()
                    ]) }}<br>
                    {{ __('labels.payment_table.cost_service.precheck.cost_service_add_prod', [
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0)
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::TYPE_REASON_REFUSAL)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.matching_result.info') }}<br>
                    {{ __('labels.payment_table.cost_service.matching_result.cost_service_add_prod', [
                        'total_prod' => $payment->total_prod ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_all_prod ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::TYPE_SELECT_POLICY)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.matching_result.addition_fee', [
                        'total_prod' => $payment->total_prod ?? 0,
                    ]) }}<br>
                    {{ __('labels.payment_table.cost_service.matching_result.addition_fee_add_prod', [
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_all_prod ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::TYPE_TRADEMARK_REGIS)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.regis_trademark.title') }}<br>
                    {{ __('labels.payment_table.cost_service.matching_result.addition_fee_add_prod', [
                        'total_prod_block' => $payment->total_prod_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::TYPE_LATE_PAYMENT)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.regis_trademark.late_payment') }}<br>
                    {{ __('labels.payment_table.cost_service.regis_trademark.late_payment_desc', [
                        'total_distinction_block' => $payment->total_distinction_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::RENEWAL_DEADLINE)
            <tr>
                <td class="item">
                    {{ __('labels.payment_table.cost_service.renewal_deadline.title') }}<br>
                    {{ __('labels.payment_table.cost_service.renewal_deadline.desc', [
                        'total_prod_block' => $payment->total_prod_block ?? 0,
                        'cost_service_add_prod' => CommonHelper::formatPrice($payment->cost_service_add_prod  ?? 0, '円', 0),
                    ]) }}
                </td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::CHANG_ADDRESS)
        @case(\App\Models\Payment::CHANG_NAME)
            <tr>
                <td class="item">{{ __('labels.payment_table.app_change_name') }}</td>
                <td>&yen; {{ CommonHelper::formatPrice($payment->cost_service_base ?? 0, '', 0) }}</td>
            </tr>
            @break

        @case(\App\Models\Payment::BEFORE_DUE_DATE)
            @break
    @endswitch
@endif
