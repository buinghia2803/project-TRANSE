<h3>{{ __('labels.support_first_times.regis_period.title') }}</h3>

<p class="eol">
    <input type="checkbox" name="period_registration" id="period_registration" {{ old('period_registration', $appTrademark->period_registration ?? null) == 2 ? 'checked': '' }} value="2" />
    {{ __('labels.support_first_times.regis_period.regis_10_years') }}<br />
    <span class="note">
        {{ __('labels.support_first_times.regis_period.note_1', ['attr' => CommonHelper::formatPrice(($periodRegistration->base_price + $periodRegistration->base_price * $setting->value / 100)) ?? 0]) }}
    </span>
</p>
