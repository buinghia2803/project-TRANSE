<h3>{{ __('labels.u031b.title_choose_pack') }}</h3>
<table class="normal_b" id="list_pack">
    <tr>
        <td style="width:34em;">
            <input type="radio" name="pack" class="package_type" {{ old('pack', $packSession ?? null) == $packA ? 'checked' : '' }} value="{{ $packA }}" id="package_a" />
            <span
                id="name_package_a">{{ __('labels.support_first_times.pack_a') }}</span>{{ __('labels.support_first_times.up_to_3_prod') }}
            <span id="price_package_a">
                            {{ CommonHelper::formatPrice($dataDefaultPackA['cost_service_base']) }}{{ __('labels.u031b.currency') }} ({{ __('labels.u031b.base_price') }}：{{ CommonHelper::formatPrice($dataDefaultPackA['commission']) }}{{ __('labels.u031b.currency') }})<br />
                        </span>
            {{ __('labels.support_first_times.note_8') }}<br />
            {{ __('labels.support_first_times.note_9') }}
            <span
                id="price_product_add_pack_a">{{ CommonHelper::formatPrice($dataDefaultPackA['cost_service_add_prod']) }}</span>
            {{ __('labels.support_first_times.note_10') }}<br />
            {{ __('labels.support_first_times.note_11') }}
        </td>
    </tr>
    <tr>
        <td style="width:34em;">
            <input type="radio" name="pack" class="package_type" {{ old('pack', $packSession ?? null) == $packB ? 'checked' : '' }} value="{{ $packB }}" id="package_b" />
            <span id="name_package_b">{{ __('labels.support_first_times.pack_b') }}</span>
            {{ __('labels.support_first_times.up_to_3_prod') }}
            <span
                id="price_package_b">{{ CommonHelper::formatPrice($dataDefaultPackB['cost_service_base']) }}</span>{{ __('labels.u031b.currency') }}（{{ __('labels.u031b.base_price') }}：{{ CommonHelper::formatPrice($dataDefaultPackB['commission']) }}{{ __('labels.u031b.currency') }})<br />
            {{ __('labels.support_first_times.note_12') }}<br />
            {{ __('labels.support_first_times.note_13') }}
            <span
                id="price_product_add_pack_b">{{ CommonHelper::formatPrice($dataDefaultPackB['cost_service_add_prod']) }}</span>
            {{ __('labels.support_first_times.note_14') }}<br />
            {{ __('labels.support_first_times.note_15') }}
        </td>
    </tr>
    <tr>
        <td style="width:34em;">
            <input type="radio" name="pack" class="package_type" value="{{ $packC }}" {{ old('pack', $packSession ?? null) == $packC ? 'checked' : '' }} id="package_c" />
            <span id="name_package_c">{{ __('labels.support_first_times.pack_c') }}</span>
            {{ __('labels.support_first_times.up_to_3_prod') }}
            <span
                id="price_package_c">{{ CommonHelper::formatPrice($dataDefaultPackC['cost_service_base']) }}</span>
            {{ __('labels.u031b.currency') }}（{{ __('labels.u031b.base_price') }}：{{ CommonHelper::formatPrice($dataDefaultPackC['commission']) }}{{ __('labels.u031b.currency') }}）<br />
            {{ __('labels.support_first_times.note_16') }}<br />
            {{ __('labels.support_first_times.note_17') }}
            <span id="price_product_add_pack_c">
                            {{ CommonHelper::formatPrice($dataDefaultPackC['cost_service_add_prod']) }}
                        </span>
            {{ __('labels.support_first_times.note_18') }}<br />
            {{ __('labels.support_first_times.note_19') }}
        </td>
    </tr>
</table>
