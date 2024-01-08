<!-- detail 1 -->
<table class="detail">
    <tr>
        <th class="item">{{ __('labels.payment_table.project') }}</th>
        <th>{{ __('labels.payment_table.amount') }}</th>
    </tr>

    @include('user.components.partials.payments.cost_service')
    @include('user.components.partials.payments.cost_registration_certificate')
    @include('user.components.partials.payments.extension_of_period_before_expiry')
    @include('user.components.partials.payments.application_discount')
    @include('user.components.partials.payments.reduce_number_distitions')
    @include('user.components.partials.payments.cost_change_name')
    @include('user.components.partials.payments.cost_change_address')
    @include('user.components.partials.payments.cost_bank_transfer')

    @include('user.components.partials.payments.subtotal')

    @include('user.components.partials.payments.cost_print_application')
    @include('user.components.partials.payments.cost_5_year')
    @include('user.components.partials.payments.cost_10_year')
    @include('user.components.partials.payments.print_fee')
    @include('user.components.partials.payments.cost_correspondence')
    @include('user.components.partials.payments.cost_print_name')
    @include('user.components.partials.payments.cost_print_address')

    @include('user.components.partials.payments.total_amount')
    @include('user.components.partials.payments.tax_withholding')
    @include('user.components.partials.payments.payment_amount')
</table>
<!-- /detail 1 -->
